<?php
// ===============================
// EXPORT DỮ LIỆU BẢNG NHÂN VIÊN RA EXCEL
// ===============================
// connect database
require_once('../config.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    // Use PhpSpreadsheet if installed
    require __DIR__ . '/../vendor/autoload.php';

    // Lấy dữ liệu từ bảng nhanvien
    $sql = "SELECT * FROM nhanvien";
    $res = $conn->query($sql);

// Tiêu đề chính
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Danh sách nhân viên');

    $sheet->mergeCells('A1:O1');
    $sheet->setCellValue('A1', 'DANH SÁCH NHÂN VIÊN');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension(1)->setRowHeight(30);
    
    // Dòng tiêu đề cột
/*     $header = [
        'ID', 'Mã NV','Tên nhân viên', 'Hình ảnh', 'Biệt danh', 'Giới tính', 'Ngày sinh', 
        'Nơi sinh','Hôn nhân ID', 'Số CMND', 'Nơi cấp CMND', 'Ngày cấp CMND',
        'Nguyên quán','Quốc tịch ID', 'Tôn giáo ID', 'Dân tộc ID','Hộ khẩu','Tạm trú',
        'Loại NV ID', 'Trình độ ID', 'Chuyên môn ID', 'Bằng cấp ID', 
        'Phòng ban ID', 'Chức vụ ID','Trạng thái', 'Người tạo', 'Ngày tạo', 'Người sửa', 'Ngày sửa'
    ];
     */
    $header = [
        'id NV', 'Mã NV','Tên nhân viên','Số CCCD', 'Giới tính', 'Ngày sinh', 
        'Quốc tịch ID', 'Tôn giáo ID', 'Dân tộc ID', 
        'Loại NV ID', 'Trình độ ID', 'Chuyên môn ID', 'Bằng cấp ID', 
        'Phòng ban ID', 'Chức vụ ID'
    ];
    $sheet->fromArray($header, NULL, 'A2');
    $sheet->getStyle('A2:O2')->getFont()->setBold(true);
    $sheet->getStyle('A2:O2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A2:O2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEFEFEF');

    // Ghi dữ liệu nhân viênS
   
    $row = 3;
    while ($r = $res->fetch_assoc()) {
        //$sheet->setCellValue('A' . $row, $row - 2);
        $sheet->setCellValue('A' . $row, $r['id']);
        $sheet->setCellValue('B' . $row, $r['ma_nv']);
        $sheet->setCellValue('C' . $row, $r['ten_nv']);
        $sheet->setCellValue('D' . $row, $r['so_cmnd']);
        $sheet->setCellValue('E' . $row, $r['gioi_tinh'] == 1 ? 'Nam' : 'Nữ');
        $sheet->setCellValue('F' . $row, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(new DateTime($r['ngay_sinh'])));
        $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
        $sheet->setCellValue('G' . $row, $r['quoc_tich_id']);
        $sheet->setCellValue('H' . $row, $r['ton_giao_id']);
        $sheet->setCellValue('I' . $row, $r['dan_toc_id']);
        $sheet->setCellValue('J' . $row, $r['loai_nv_id']);
        $sheet->setCellValue('K' . $row, $r['trinh_do_id']);
        $sheet->setCellValue('L' . $row, $r['chuyen_mon_id']);
        $sheet->setCellValue('M' . $row, $r['bang_cap_id']);
        $sheet->setCellValue('N' . $row, $r['phong_ban_id']);
        $sheet->setCellValue('O' . $row, $r['chuc_vu_id']);
        $row++;
    }

// Kẻ khung dữ liệu
$sheet->getStyle('A2:O' . ($row - 1))
    ->getBorders()->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN);

// Căn giữa một số cột
$sheet->getStyle('A3:O' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

// Tự động căn độ rộng cột
foreach (range('A', 'O') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

    $filename = 'DanhSachNhanVien_' . date('Dmy/His') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //header('Content-Disposition: attachment;filename="nhanvien.xlsx"');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');
    
    // Định dạng trang in A4 ngang
    $sheet->getPageSetup()
        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

    // Căn chỉnh lề trang
    $sheet->getPageMargins()->setTop(0.5);
    $sheet->getPageMargins()->setRight(0.5);
    $sheet->getPageMargins()->setLeft(0.5);
    $sheet->getPageMargins()->setBottom(0.5);


    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} else {
    // Fallback: simple tab-separated XLS
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename=nhanvien.xls');
    echo "ID\tHọ tên\tNgày sinh\tPhòng ban\n";
    $res = $conn->query('SELECT id, hoten, ngaysinh, phongban FROM nhanvien');
    while ($r = $res->fetch_assoc()) {
        echo $r['id'] . "\t" . $r['hoten'] . "\t" . $r['ngaysinh'] . "\t" . $r['phongban'] . "\n";
    }
    exit;
}
?>
