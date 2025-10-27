<?php
// Nhận dữ liệu từ form
$thang = intval($_POST['thang']);
$nam = intval($_POST['nam']);
$luongcoban = intval($_POST['luongcoban']);

// ====== BƯỚC 1: KIỂM TRA DỮ LIỆU TỒN TẠI ======
$check_sql = "SELECT COUNT(*) AS total FROM luong_VP WHERE thang = $thang AND nam = $nam";
$result = $conn->query($check_sql);
$row = $result->fetch_assoc();

if ($row['total'] > 0) {
    // Nếu đã có dữ liệu => báo lỗi và dừng
    echo "
    <div style='font-family: Arial; text-align:center; margin-top:50px;'>
      <h3 style='color:red;'>⚠️ Dữ liệu lương tháng $thang / $nam đã tồn tại!</h3>
      <p>Vui lòng kiểm tra lại trước khi khởi tạo mới.</p>
      <a href='index.php' style='margin-top:20px; display:inline-block; text-decoration:none; background:#6c757d; color:#fff; padding:10px 20px; border-radius:5px;'>Quay lại</a>
    </div>";
    $conn->close();
    exit;
}

// ====== BƯỚC 2: THỰC HIỆN KHỞI TẠO LƯƠNG ======
$sql = "
SET @THANG = $thang;
SET @NAM = $nam;
SET @LUONGCOBAN = $luongcoban;
SET @rownum = 0;

INSERT INTO luong_VP 
(`ma_luong`, `nhanvien_id`, `thang`, `nam`, `luong_co_ban`, `tong_phu_cap`, `tong_thuong`, `tong_phat`, 
 `tong_tam_ung`, `luong_theo_ngay_cong`, `tien_lam_them`, `thuc_linh`, `ngay_tinh_luong`, `ghi_chu`, 
 `nguoi_tao_id`, `ngay_tao`, `nguoi_sua_id`, `ngay_sua`)
SELECT 
  CONCAT('ML', LPAD(@THANG, 2, '0'), LPAD(@NAM % 100, 2, '0'), LPAD(@rownum := @rownum + 1, 3, '0')) AS ma_luong,
  nv.id AS nhanvien_id,
  @THANG AS thang,
  @NAM AS nam,
  @LUONGCOBAN AS luong_co_ban,
  0 AS tong_phu_cap,
  0 AS tong_thuong,
  0 AS tong_phat,
  0 AS tong_tam_ung,
  0 AS luong_theo_ngay_cong,
  0 AS tien_lam_them,
  @LUONGCOBAN AS thuc_linh,
  CURDATE() AS ngay_tinh_luong,
  '' AS ghi_chu,
  1 AS nguoi_tao_id,
  NOW() AS ngay_tao,
  1 AS nguoi_sua_id,
  NOW() AS ngay_sua
FROM nhanvien nv
WHERE nv.trang_thai = 1
ORDER BY nv.id;
";

// ====== BƯỚC 3: THỰC THI ======
if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());

    echo "
    <div style='font-family: Arial; text-align:center; margin-top:50px;'>
      <h3 style='color:green;'>✅ Đã tạo bảng lương tháng $thang / $nam thành công!</h3>
      <a href='index.php' style='margin-top:20px; display:inline-block; text-decoration:none; background:#0d6efd; color:#fff; padding:10px 20px; border-radius:5px;'>Quay lại</a>
    </div>";
    echo '<script>setTimeout("window.location=\'http://localhost/QUANLYNHANSU/pages/luong-vp.php?p=staff&a=luongvp\'",1000);</script>';
} else {
    echo "
    <div style='font-family: Arial; text-align:center; margin-top:50px;'>
      <h3 style='color:red;'>❌ Lỗi khi khởi tạo: " . $conn->error . "</h3>
      <a href='index.php' style='margin-top:20px; display:inline-block; text-decoration:none; background:#6c757d; color:#fff; padding:10px 20px; border-radius:5px;'>Thử lại</a>
    </div>";
    echo '<script>setTimeout("window.location=\'luong-vp.php?p=staff&a=luongvp\'",1000);</script>';
}
?>
