<?php
// Tên file: import.php
require_once('../config.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

// Kiểm tra xem thư viện PhpSpreadsheet đã được cài đặt chưa
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die("Lỗi: Vui lòng cài đặt thư viện PhpSpreadsheet bằng Composer (composer require phpoffice/phpspreadsheet) để sử dụng chức năng này.");
}

require __DIR__ . '/../vendor/autoload.php';


// Hàm xử lý chuỗi đầu vào (trim và escape)
function clean_input($data) {
    global $conn;
    $data = trim($data);
    // Sử dụng mysqli_real_escape_string để ngăn chặn SQL Injection
    $data = $conn->real_escape_string($data);
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file'];

    // 1. Kiểm tra lỗi upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = "Lỗi khi upload file: " . $file['error'];
    } else {
        $inputFileName = $file['tmp_name'];

        try {
            // 2. Tải file vào PhpSpreadsheet
            $spreadsheet = IOFactory::load($inputFileName);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();

            $inserted_count = 0;
            $updated_count = 0;
            $error_rows = [];

            // Bắt đầu từ hàng thứ 2 vì hàng đầu tiên là tiêu đề (A1, B1, C1, D1)
            for ($row = 2; $row <= $highestRow; $row++) {
                // Lấy dữ liệu từ các cột tương ứng
                $id_nv = (int) clean_input($worksheet->getCell("A$row")->getValue()); // Cột A
                $manv = clean_input($worksheet->getCell("B$row")->getValue()); // Cột B
                $ten_nv = clean_input($worksheet->getCell("C$row")->getValue()); // Cột C
                $so_cccd = clean_input($worksheet->getCell("D$row")->getValue()); // Cột D
                $gioi_tinh_raw = clean_input($worksheet->getCell("E$row")->getValue()); // Cột E
                $gioi_tinh = ($gioi_tinh_raw === 'Nam' || $gioi_tinh_raw === 'nam') ? 0 : 1; // Nam = 0, Nữ = 1
                $ngaysinh_raw = $worksheet->getCell("F$row")->getValue(); // Cột F
                $quoctich_id = (int) clean_input($worksheet->getCell("G$row")->getValue()); // Cột G
                $tongiao_id = (int) clean_input($worksheet->getCell("H$row")->getValue()); // Cột H
                $dantoc_id = (int) clean_input($worksheet->getCell("I$row")->getValue()); // Cột I
                $loainv_id = (int) clean_input($worksheet->getCell("J$row")->getValue()); // Cột J
                $trinhdo_id = (int) clean_input($worksheet->getCell("K$row")->getValue()); // Cột K
                $chuyenmon_id = (int) clean_input($worksheet->getCell("L$row")->getValue()); // Cột L
                $bangcap_id = (int) clean_input($worksheet->getCell("M$row")->getValue()); // Cột M
                $phongban_id = (int) clean_input($worksheet->getCell("N$row")->getValue()); // Cột N
                $chucvu_id = (int) clean_input($worksheet->getCell("O$row")->getValue()); // Cột O

                // Xử lý định dạng ngày sinh
                $ngaysinh = null;
                if (!empty($ngaysinh_raw)) {
                    if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $ngaysinh_raw)) {
                        $ngaysinh = $ngaysinh_raw;
                    } else if (is_numeric($ngaysinh_raw)) {
                        try {
                            $ngaysinh_obj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($ngaysinh_raw);
                            $ngaysinh = $ngaysinh_obj->format('Y-m-d');
                        } catch (\Exception $e) {
                            $error_rows[] = "Hàng $row (Mã NV: $manv): Ngày sinh không hợp lệ.";
                            continue;
                        }
                    } else {
                        $ngaysinh = $ngaysinh_raw;
                    }
                }

                // Kiểm tra và Validate dữ liệu cơ bản
                if (empty($id_nv) || empty($ten_nv)) {
                    $error_rows[] = "Hàng $row: ID và Tên nhân viên không được để trống.";
                    continue;
                }

                // Kiểm tra ID đã tồn tại chưa hoặc mã nhân viên
                $check_sql = "SELECT id FROM nhanvien WHERE id = '$id_nv' OR ma_nv = '$manv'";
                $check_res = $conn->query($check_sql);

                if ($check_res->num_rows > 0) {
                    // Nếu Mã NV đã tồn tại, bỏ qua và không cập nhật
                    $error_rows[] = "Hàng $row (Mã NV: $manv): Đã tồn tại, không cập nhật.";
                } else {
                    // Thêm mới (INSERT) nếu Mã NV chưa tồn tại
                    $sql = "INSERT INTO nhanvien (
                                id, ma_nv, hinh_anh, ten_nv, biet_danh, gioi_tinh, ngay_sinh, 
                                noi_sinh, hon_nhan_id, so_cmnd, noi_cap_cmnd, ngay_cap_cmnd, 
                                nguyen_quan, quoc_tich_id, ton_giao_id, dan_toc_id, ho_khau, 
                                tam_tru, loai_nv_id, trinh_do_id, chuyen_mon_id, bang_cap_id, 
                                phong_ban_id, chuc_vu_id, trang_thai, nguoi_tao_id, ngay_tao, 
                                nguoi_sua_id, ngay_sua
                            ) VALUES (
                                '$id_nv', '$manv', 'demo-3x4.jpg', '$ten_nv', 'Không có', '$gioi_tinh', " . ($ngaysinh ? "'$ngaysinh'" : "NULL") . ", 
                                '', 1, '$so_cccd', 'Bộ công an', NOW(), 
                                '', 24, 0, 1, 'Bạc Liêu', 
                                'Bạc liêu', 2, 17, 2, 1, 
                                20, 33, 1, 1, NOW(), 
                                1, NOW()
                            )";
                    if ($conn->query($sql) === TRUE) {
                        $inserted_count++;
                    } else {
                        $error_rows[] = "Hàng $row (Mã NV: $manv): Lỗi thêm mới - " . $conn->error;
                    }
                }
            }

            // 8. Thông báo kết quả
            $message = "Hoàn tất Import dữ liệu!<br>";
            $message .= "Đã thêm mới: **$inserted_count** bản ghi.<br>";
            $message .= "Đã cập nhật: **$updated_count** bản ghi.<br>";

 /*            if (!empty($error_rows)) {
                $message .= "---<br>Lỗi xảy ra ở các hàng sau:<br>" . implode("<br>", $error_rows);
            } */

        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            $message = "Lỗi khi đọc file: " . $e->getMessage();
        } catch (\Exception $e) {
            $message = "Lỗi không xác định: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Import Dữ liệu Nhân viên</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .message { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; }
        .success { border-color: green; background-color: #e6ffe6; color: green; }
        .error { border-color: red; background-color: #ffe6e6; color: red; }
    </style>
</head>
<body>

    <h1>Import Dữ liệu Nhân viên từ Excel/CSV</h1>
    
    <?php if (isset($message)): ?>
        <div class="message <?php echo strpos($message, 'Lỗi') !== false ? 'error' : 'success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <p>Chọn file Excel (.xlsx, .xls) hoặc CSV để import dữ liệu nhân viên. Dữ liệu sẽ được chèn mới hoặc cập nhật nếu **Mã NV** đã tồn tại.</p>

    <form method="POST" enctype="multipart/form-data">
        <label for="excel_file">Chọn File:</label>
        <input type="file" name="excel_file" id="excel_file" accept=".xlsx, .xls, .csv" required>
        <button type="submit">Import Dữ liệu</button>
    </form>

    <hr>
    
    <h2>Yêu cầu Định dạng File Import</h2>
    <p>File cần có định dạng cột theo thứ tự sau (giống file export của bạn, với hàng đầu tiên là tiêu đề):</p>
    
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Cột A</th>
                <th>Cột B</th>
                <th>Cột C</th>
                <th>Cột D</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>**Mã NV** (manv) - Bắt buộc</td>
                <td>**Họ tên** (hoten) - Bắt buộc</td>
                <td>**Ngày sinh** (ngaysinh)</td>
                <td>**Phòng ban** (phongban)</td>
            </tr>
            <tr>
                <td>*VD: NV004*</td>
                <td>*VD: Doan Thi D*</td>
                <td>*VD: 1992-11-25 (hoặc định dạng ngày của Excel)*</td>
                <td>*VD: Marketing*</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
