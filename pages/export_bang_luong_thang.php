<?php
// ===============================
// EXPORT DỮ LIỆU BẢNG LƯƠNG NVVP RA EXCEL
// ===============================
// connect database
require_once('../config.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';

    $thang = isset($_POST['thang']) ? (int)$_POST['thang'] : date('m');
    $nam = isset($_POST['nam']) ? (int)$_POST['nam'] : date('Y');

    $sql = "SELECT luong_nvvp.*, nhanvien.ten_nv 
            FROM luong_nvvp 
            JOIN nhanvien ON luong_nvvp.nhanvien_id = nhanvien.id 
            WHERE luong_nvvp.thang = ? AND luong_nvvp.nam = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $thang, $nam);
    $stmt->execute();
    $res = $stmt->get_result();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Bảng lương NVVP');

    // Tiêu đề công ty và MST
    $sheet->mergeCells('A1:N1');
    $sheet->setCellValue('A1', 'CÔNG TY TNHH TMDV XNK TS QUÂN PHÁT');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('A2:N2');
    $sheet->setCellValue('A2', 'MST: 1900656558');
    $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Tiêu đề bảng
    $sheet->mergeCells('A3:N3');
    $sheet->setCellValue('A3', 'BẢNG THANH TOÁN TIỀN LƯƠNG');
    $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('A4:N4');
    $sheet->setCellValue('A4', "Tháng $thang năm $nam");
    $sheet->getStyle('A4')->getFont()->setItalic(true)->setSize(12);
    $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Tiêu đề cột
    $header = [
        'STT', 'Họ và tên', 'CCCD', 'Chức vụ', 'Lương cơ bản', 
        'Làm thêm ngoài giờ', 'Tiền cơm', 'Hỗ trợ tiền xe, tiền xăng, ở nhà', 
        'Tổng cộng', 'Ký nhận'
    ];
    $sheet->fromArray($header, NULL, 'A5');
    $sheet->getStyle('A5:J5')->getFont()->setBold(true);
    $sheet->getStyle('A5:J5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A5:J5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEFEFEF');

    // Ghi dữ liệu lương
    $row = 6;
    $stt = 1;
    $tong_luong_co_ban = 0;
    $tong_lam_them = 0;
    $tong_tien_com = 0;
    $tong_ho_tro = 0;
    $tong_tong_cong = 0;

    while ($r = $res->fetch_assoc()) {
        $tong_cong = $r['luong_co_ban'] + $r['tien_lam_them'] + $r['phu_cap_com'] + $r['ho_tro_tien_xe'];
        $sheet->setCellValue('A' . $row, $stt++);
        $sheet->setCellValue('B' . $row, $r['ten_nv']);
        $sheet->setCellValue('C' . $row, $r['so_cmnd']);
        $sheet->setCellValue('D' . $row, $r['ten_chuc_vu']);
        $sheet->setCellValue('E' . $row, number_format($r['luong_co_ban'], 0, ',', '.') . ' VND');
        $sheet->setCellValue('F' . $row, number_format($r['tien_lam_them'], 0, ',', '.') . ' VND');
        $sheet->setCellValue('G' . $row, number_format($r['phu_cap_com'], 0, ',', '.') . ' VND');
        $sheet->setCellValue('H' . $row, number_format($r['phu_cap_xe'], 0, ',', '.') . ' VND');
        $sheet->setCellValue('I' . $row, number_format($tong_cong, 0, ',', '.') . ' VND');

        $tong_luong_co_ban += $r['luong_co_ban'];
        $tong_lam_them += $r['tien_lam_them'];
        $tong_tien_com += $r['phu_cap_com'];
        $tong_ho_tro += $r['phu_cap_xe'];
        $tong_tong_cong += $tong_cong;

        $row++;
    }

    // Dòng tổng
    $sheet->setCellValue('D' . $row, 'Tổng cộng');
    $sheet->setCellValue('E' . $row, number_format($tong_luong_co_ban, 0, ',', '.') . ' VND');
    $sheet->setCellValue('F' . $row, number_format($tong_lam_them, 0, ',', '.') . ' VND');
    $sheet->setCellValue('G' . $row, number_format($tong_tien_com, 0, ',', '.') . ' VND');
    $sheet->setCellValue('H' . $row, number_format($tong_ho_tro, 0, ',', '.') . ' VND');
    $sheet->setCellValue('I' . $row, number_format($tong_tong_cong, 0, ',', '.') . ' VND');
    $sheet->getStyle('D' . $row . ':I' . $row)->getFont()->setBold(true);

    // Dòng ký nhận
    $sheet->mergeCells('A' . ($row + 2) . ':E' . ($row + 2));
    $sheet->setCellValue('A' . ($row + 2), 'Người lập biểu');
    $sheet->mergeCells('F' . ($row + 2) . ':J' . ($row + 2));
    $sheet->setCellValue('F' . ($row + 2), 'Giám Đốc');

    // Kẻ khung dữ liệu
    $sheet->getStyle('A5:J' . $row)
        ->getBorders()->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);

    // Căn giữa một số cột
    $sheet->getStyle('A6:J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Tự động căn độ rộng cột
    foreach (range('A', 'J') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $filename = 'BangLuongNVVP_' . date('Dmy_His') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} else {
    echo "Library PhpSpreadsheet chưa được cài đặt.";
    exit;
}
?>