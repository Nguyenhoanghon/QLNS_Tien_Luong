<?php 

// create session
session_start();

if(isset($_SESSION['username']) && isset($_SESSION['level']))
{
  // include file
  include('../layouts/header.php');
  include('../layouts/topbar.php');
  include('../layouts/sidebar.php');

    if(isset($_POST['edit']))
    {
      $id = $_POST['idLuong'];
      echo "<script>location.href='sua-luong-vp.php?p=staff&a=luong&id=".$id."'</script>";
    }

    // show data
    $showData = "SELECT id, ma_luong, nhanvien_id, thang, nam, luong_co_ban, tong_phu_cap, tong_thuong, tong_phat, tong_tam_ung, luong_theo_ngay_cong, tien_lam_them, thuc_linh, ngay_tinh_luong, ghi_chu, nguoi_tao_id, ngay_tao, nguoi_sua_id, ngay_sua FROM luong_vp ORDER BY ngay_tao DESC";
    $result = mysqli_query($conn, $showData);
    $arrShow = array();
    while ($row = mysqli_fetch_array($result)) {
      $arrShow[] = $row;
    }

    // create code luong
    $luongCode = "MLVP" . time();

    // Khoi tạo bảng lương
    if (isset($_POST['save'])) {
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
          echo '<script>setTimeout("window.location=\'luong-vp.php?p=staff&a=luongvp\'",3000);</script>';
      } else {
          echo "
          <div style='font-family: Arial; text-align:center; margin-top:50px;'>
            <h3 style='color:red;'>❌ Lỗi khi khởi tạo: " . $conn->error . "</h3>
            <a href='index.php' style='margin-top:20px; display:inline-block; text-decoration:none; background:#6c757d; color:#fff; padding:10px 20px; border-radius:5px;'>Thử lại</a>
          </div>";
          echo '<script>setTimeout("window.location=\'luong-vp.php?p=staff&a=luongvp\'",1000);</script>';
      }
          }
          // delete record
          if(isset($_POST['delete']))
          {
            $showMess = true;

            $id = $_POST['idLuong'];
            $delete = "DELETE FROM luong_vp WHERE id = $id";
            mysqli_query($conn, $delete);
            $success['success'] = 'Xóa lương thành công.';
            echo '<script>setTimeout("window.location=\'luong-vp.php?p=staff&a=luongvp\'",1000);</script>';
    }

  ?>


<!-- Modal Xác thực xoá-->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <span style="font-size: 18px;">Thông báo</span>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="idLuong">
                    Bạn có thực sự muốn xóa bản ghi lương này?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary" name="delete">Xóa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Lương Văn Phòng
        </h1>
        <ol class="breadcrumb">
            <li><a href="index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> Tổng quan</a></li>
            <li><a href="luong-vp.php?p=staff&a=luong">Lương Văn Phòng</a></li>
            <li class="active">Thêm lương</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Khởi tạo bảng lương</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                    class="fa fa-remove"></i></button>
                        </div>

                        <form method="POST">
                            <div class="form-group">
                                <label for="thang">Tháng</label>
                                <select class="form-control" id="thang" name="thang" required>
                                    <option value="" disabled selected>Chọn tháng</option>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nam">Năm</label>
                                <select class="form-control" id="nam" name="nam" required>
                                    <option value="" disabled selected>Chọn năm</option>
                                    <?php for ($i = 2020; $i <= 2030; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="luongcoban">Lương cơ bản</label>
                                <input type="number" class="form-control" id="luongcoban" name="luongcoban"
                                    placeholder="Nhập lương cơ bản" required>
                            </div>
                            <button type="submit" class="btn btn-primary" name="save">Khởi tạo</button>
                        </form>
                    </div>

                    <!-- Danh sách lương  -->
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Danh sách lương</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Mã lương</th>
                                            <th>Nhân viên</th>
                                            <th>Tháng</th>
                                            <th>Năm</th>
                                            <th>Lương cơ bản</th>
                                            <th>Tổng phụ cấp</th>
                                            <th>Tổng thưởng</th>
                                            <th>Tổng phạt</th>
                                            <th>Tổng tạm ứng</th>
                                            <th>Lương theo ngày công</th>
                                            <th>Tiền làm thêm</th>
                                            <th>Thực lĩnh</th>
                                            <th>Ngày tính lương</th>
                                            <th>Ghi chú</th>
                                            <th>Người tạo</th>
                                            <th>Ngày tạo</th>
                                            <th>Người sửa</th>
                                            <th>Ngày sửa</th>
                                            <th>Sửa</th>
                                            <th>Xóa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                  $count = 1;
                  foreach ($arrShow as $arrS) 
                  {
                ?>
                                        <tr>
                                            <td><?php echo $count; ?></td>
                                            <td><?php echo $arrS['ma_luong']; ?></td>
                                            <td>
                                                <?php 
                      $queryEmployee = "SELECT ten_nv FROM nhanvien WHERE id = ".$arrS['nhanvien_id'];
                      $resultEmployee = mysqli_query($conn, $queryEmployee);
                      $rowEmployee = mysqli_fetch_array($resultEmployee);
                      echo $rowEmployee['ten_nv'];
                    ?>
                                            </td>
                                            <td><?php echo $arrS['thang']; ?></td>
                                            <td><?php echo $arrS['nam']; ?></td>
                                            <td><?php echo number_format($arrS['luong_co_ban']); ?></td>
                                            <td><?php echo number_format($arrS['tong_phu_cap']); ?></td>
                                            <td><?php echo number_format($arrS['tong_thuong']); ?></td>
                                            <td><?php echo number_format($arrS['tong_phat']); ?></td>
                                            <td><?php echo number_format($arrS['tong_tam_ung']); ?></td>
                                            <td><?php echo number_format($arrS['luong_theo_ngay_cong']); ?></td>
                                            <td><?php echo number_format($arrS['tien_lam_them']); ?></td>
                                            <td><?php echo number_format($arrS['thuc_linh']); ?></td>
                                            <td><?php echo $arrS['ngay_tinh_luong']; ?></td>
                                            <td><?php echo $arrS['ghi_chu']; ?></td>
                                            <td><?php echo $arrS['nguoi_tao_id']; ?></td>
                                            <td><?php echo $arrS['ngay_tao']; ?></td>
                                            <td><?php echo $arrS['nguoi_sua_id']; ?></td>
                                            <td><?php echo $arrS['ngay_sua']; ?></td>
                                            <th>
                                                <?php 
                      if($row_acc['quyen'] == 1)
                      {
                      echo "<form method='POST'>";
                      echo "<input type='hidden' value='".$arrS['id']."' name='idLuong'/>";
                      echo "<button type='submit' class='btn bg-orange btn-flat'  name='edit'><i class='fa fa-edit'></i></button>";
                      echo "</form>";
                      }
                      else
                      {
                      echo "<button type='button' class='btn bg-orange btn-flat' disabled><i class='fa fa-edit'></i></button>";
                      }
                    ?>
                                            </th>
                                            <th>
                                                <?php 
                      if($row_acc['quyen'] == 1)
                      {
                      echo "<button type='button' class='btn bg-maroon btn-flat' data-toggle='modal' data-target='#exampleModal' data-whatever='".$arrS['id']."'><i class='fa fa-trash'></i></button>";
                      }
                      else
                      {
                      echo "<button type='button' class='btn bg-maroon btn-flat' disabled><i class='fa fa-trash'></i></button>";
                      }
                    ?>
                                            </th>
                                        </tr>
                                        <?php
                  $count++;
                  }
                ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                    <!-- /.col -->
                </div>
                <!-- /.row -->
    </section>
    <!-- /.content -->
</div>

<?php
  // include
  include('../layouts/footer.php');
}
else
{
  // go to pages login
  header('Location: dang-nhap.php');
}

?>