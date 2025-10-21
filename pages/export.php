<?php
require_once('../config.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    // Use PhpSpreadsheet if installed
    require __DIR__ . '/../vendor/autoload.php';

   /*  $sql = 'SELECT id, hoten, ngaysinh, phongban FROM nhanvien';
    $res = $conn->query($sql); */
    // Lấy dữ liệu từ bảng nhanvien
    #$sql = "SELECT id, ma_nv, ten_nv, biet_danh, gioi_tinh, ngay_sinh, noi_sinh, so_cmnd, noi_cap_cmnd, ngay_cap_cmnd, ho_khau, tam_tru, trang_thai, ngay_tao FROM nhanvien";
    $sql = "SELECT * FROM nhanvien";
    $res = $conn->query($sql);

// Tiêu đề chính
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Danh sách nhân viên');

    $sheet->mergeCells('A1:N1');
    $sheet->setCellValue('A1', 'DANH SÁCH NHÂN VIÊN');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    // Dòng tiêu đề cột
    $header = [
        'ID', 'Mã NV', 'Tên nhân viên', 'Biệt danh', 'Giới tính', 'Ngày sinh', 
        'Nơi sinh', 'Số CMND', 'Nơi cấp CMND', 'Ngày cấp CMND', 
        'Hộ khẩu', 'Tạm trú', 'Trạng thái', 'Ngày tạo'
    ];
    $sheet->fromArray($header, NULL, 'A2');
    $sheet->getStyle('A3:N3')->getFont()->setBold(true);
    $sheet->getStyle('A3:N3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A3:N3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEFEFEF');

    // Ghi dữ liệu nhân viênS
    
    $rowCount = 4;
    $sheet->setCellValue('A' . $rowCount, 'STT');
    $sheet->setCellValue('B' . $rowCount, 'Mã nhân viên');
    $sheet->setCellValue('C' . $rowCount, 'Tên nhân viên');
    $sheet->setCellValue('D' . $rowCount, 'Biệt danh');
    $sheet->setCellValue('E' . $rowCount, 'Giới tính');
    $sheet->setCellValue('F' . $rowCount, 'Ngày sinh');
    $sheet->setCellValue('G' . $rowCount, 'Nơi sinh');
    $sheet->setCellValue('H' . $rowCount, 'Tình trạng hôn nhân');
    $sheet->setCellValue('I' . $rowCount, 'Số CMND');
    $sheet->setCellValue('J' . $rowCount, 'Ngày cấp');
    $sheet->setCellValue('K' . $rowCount, 'Nơi cấp');
    $sheet->setCellValue('L' . $rowCount, 'Nguyên quán');
    $sheet->setCellValue('M' . $rowCount, 'Quốc tịch');
    $sheet->setCellValue('N' . $rowCount, 'Dân tộc');
    $sheet->setCellValue('O' . $rowCount, 'Tôn giáo');
    $sheet->setCellValue('P' . $rowCount, 'Hộ khẩu');
    $sheet->setCellValue('Q' . $rowCount, 'Tạm trú');
    $sheet->setCellValue('R' . $rowCount, 'Loại nhân viên');
    $sheet->setCellValue('S' . $rowCount, 'Trình độ');
    $sheet->setCellValue('T' . $rowCount, 'Chuyên môn');
    $sheet->setCellValue('U' . $rowCount, 'Bằng cấp');
    $sheet->setCellValue('V' . $rowCount, 'Phòng ban');
    $sheet->setCellValue('W' . $rowCount, 'Chức vụ');
    $sheet->setCellValue('X' . $rowCount, 'Trạng thái');

    $row = 2;
    while ($r = $res->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $r['id']);
        $sheet->setCellValue('B' . $row, $r['ma_nv']);
        $sheet->setCellValue('C' . $row, $r['ten_nv']);
        $sheet->setCellValue('D' . $row, $r['biet_danh']);
        $sheet->setCellValue('E' . $row, $r['gioi_tinh'] == 1 ? 'Nam' : 'Nữ');
        $sheet->setCellValue('F' . $row, $r['ngay_sinh']);
        $sheet->setCellValue('G' . $row, $r['noi_sinh']);
        $sheet->setCellValue('H' . $row, $r['hon_nhan_id']);
        $sheet->setCellValue('I' . $row, $r['so_cmnd']);
        $sheet->setCellValue('J' . $row, $r['ngay_cap_cmnd']);
        $sheet->setCellValue('K' . $row, $r['noi_cap_cmnd']);
        $sheet->setCellValue('L' . $row, $r['quoc_tich_id']);
        $sheet->setCellValue('M' . $row, $r['ton_giao_id']);
        $sheet->setCellValue('N' . $row, $r['ho_khau']);
        $sheet->setCellValue('O' . $row, $r['tam_tru']);
        $sheet->setCellValue('P' . $row, $r['loai_nv_id']);
        $sheet->setCellValue('Q' . $row, $r['trinh_do_id']);
        $sheet->setCellValue('R' . $row, $r['chuyen_mon_id']);
        $sheet->setCellValue('S' . $row, $r['bang_cap_id']);
        $sheet->setCellValue('T' . $row, $r['phong_ban_id']);
        $sheet->setCellValue('U' . $row, $r['chuc_vu_id']);
        $sheet->setCellValue('V' . $row, $r['trang_thai'] == 1 ? 'Đang làm việc' : 'Nghỉ việc');
        $sheet->setCellValue('W' . $row, $r['nguoi_tao']);
        $sheet->setCellValue('X' . $row, $r['ngay_tao']);
        $sheet->setCellValue('Y' . $row, $r['nguoi_sua']);
        $sheet->setCellValue('Z' . $row, $r['ngay_sua']);
        $row++;
    }

// Kẻ khung dữ liệu
$sheet->getStyle('A3:N' . ($row - 1))
    ->getBorders()->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN);

// Căn giữa một số cột
$sheet->getStyle('A3:E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Tự động căn độ rộng cột
foreach (range('A', 'N') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

    $filename = 'DanhSachNhanVien_' . date('Dmy/His') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //header('Content-Disposition: attachment;filename="nhanvien.xlsx"');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

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
