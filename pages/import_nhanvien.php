<?php
// ===============================
// IMPORT NHÂN VIÊN TỪ FILE EXCEL (CÓ KIỂM TRA TRÙNG)
// ===============================
// require 'vendor/autoload.php';

require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;



// Kiểm tra upload
if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] != 0) {
    die('⚠️ Vui lòng chọn file Excel hợp lệ.');
}

$filePath = $_FILES['file_excel']['tmp_name'];

try {
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    $added = 0;
    $skipped = 0;
    $errors = 0;
    $errorLog = [];

    // Bỏ qua dòng 1-3 (tiêu đề)
    for ($i = 3; $i < count($rows); $i++) {
        $row = $rows[$i];
        if (empty($row[1])) continue; // Bỏ dòng trống

        // Lấy dữ liệu cột
        $ma_nv = trim($row[1]);
        $ten_nv = trim($row[2]);
        $biet_danh = trim($row[3]);
        $gioi_tinh = ($row[4] == 'Nam') ? 1 : 0;
        $ngay_sinh = !empty($row[5]) ? date('Y-m-d', strtotime($row[5])) : null;
        $noi_sinh = trim($row[6]);
        $so_cmnd = trim($row[7]);
        $noi_cap_cmnd = trim($row[8]);
        $ngay_cap_cmnd = !empty($row[9]) ? date('Y-m-d', strtotime($row[9])) : null;
        $ho_khau = trim($row[10]);
        $tam_tru = trim($row[11]);
        $trang_thai = ($row[12] == 'Đang làm việc') ? 1 : 0;
        $ngay_tao = !empty($row[13]) ? date('Y-m-d H:i:s', strtotime($row[13])) : date('Y-m-d H:i:s');

        // Bỏ qua nếu thiếu dữ liệu bắt buộc
        if (empty($ma_nv) || empty($ten_nv)) {
            $errors++;
            $errorLog[] = "⚠️ Dòng " . ($i + 1) . ": Thiếu mã NV hoặc tên NV.";
            continue;
        }

        // Kiểm tra trùng mã nhân viên
        $check = $conn->prepare("SELECT id FROM nhanvien WHERE ma_nv = ?");
        $check->bind_param("s", $ma_nv);
        $check->execute();
        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {
            $skipped++;
            $errorLog[] = "⏭ Dòng " . ($i + 1) . ": Mã NV '{$ma_nv}' đã tồn tại — bỏ qua.";
            continue;
        }

        // Thêm bản ghi mới
        $sql = "INSERT INTO nhanvien (
                    ma_nv, ten_nv, biet_danh, gioi_tinh, ngay_sinh, noi_sinh,
                    so_cmnd, noi_cap_cmnd, ngay_cap_cmnd, ho_khau, tam_tru,
                    trang_thai, ngay_tao,
                    hinh_anh, hon_nhan_id, quoc_tich_id, ton_giao_id, dan_toc_id,
                    loai_nv_id, trinh_do_id, chuyen_mon_id, bang_cap_id, phong_ban_id, chuc_vu_id,
                    nguoi_tao_id, nguoi_sua_id, ngay_sua
                )
                VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                    '', 1, 24, 1, 1,
                    2, 17, 1, 1, 20, 33,
                    1, 1, NOW()
                )";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssissssssiss",
            $ma_nv, $ten_nv, $biet_danh, $gioi_tinh, $ngay_sinh, $noi_sinh,
            $so_cmnd, $noi_cap_cmnd, $ngay_cap_cmnd, $ho_khau, $tam_tru,
            $trang_thai, $ngay_tao
        );

        if ($stmt->execute()) {
            $added++;
        } else {
            $errors++;
            $errorLog[] = "❌ Dòng " . ($i + 1) . ": Lỗi SQL (" . $conn->error . ")";
        }
    }

    // Hiển thị kết quả
    echo "<div style='font-family:Arial; margin:20px'>";
    echo "<h2>📊 KẾT QUẢ IMPORT NHÂN VIÊN</h2>";
    echo "<ul>";
    echo "<li>✅ Thêm mới: <b style='color:green'>{$added}</b> dòng</li>";
    echo "<li>⏭ Bỏ qua (trùng mã NV): <b style='color:orange'>{$skipped}</b> dòng</li>";
    echo "<li>❌ Lỗi dữ liệu: <b style='color:red'>{$errors}</b> dòng</li>";
    echo "</ul>";

    if (!empty($errorLog)) {
        echo "<h4>Chi tiết lỗi:</h4><ul>";
        foreach ($errorLog as $msg) {
            echo "<li>{$msg}</li>";
        }
        echo "</ul>";
    }

    echo "<a href='import_form.php' class='btn btn-secondary'>⬅ Quay lại</a>";
    echo "</div>";

} catch (Exception $e) {
    die('⚠️ Lỗi khi đọc file Excel: ' . $e->getMessage());
}
?>
