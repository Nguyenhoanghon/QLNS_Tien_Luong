<?php 
/* Ghi chú còn lỗi
Mục đích: Sửa lỗi chỉnh sửa lương nhân viên văn phòng
Người tạo: Nguyễn Hoàng Hôn
Ngày tạo: 27/10/2025

*/
// create session
session_start();

if(isset($_SESSION['username']) && isset($_SESSION['level']))
{
  // include file
  include('../layouts/header.php');
  include('../layouts/topbar.php');
  include('../layouts/sidebar.php');

  // show data
  if(isset($_GET['id']))
  {
    $id = $_GET['id'];
    $showData = "SELECT * FROM luong_nvvp WHERE id = $id";
    $result = mysqli_query($conn, $showData);
    $row = mysqli_fetch_array($result);
  }

  // update record
  if(isset($_POST['save']))
  {
    // create array error
    $error = array();
    $success = array();
    $showMess = false;

    // get data from form
    $luongCoBan = $_POST['luongCoBan'];
    $phuCapCom = $_POST['phuCapCom'];
    $phuCapXe = $_POST['phuCapXe'];
    $tongTamUng = $_POST['tongTamUng'];
    $tienLamThem = $_POST['tienLamThem'];
    $thucLinh = $_POST['thucLinh'];
    $ghiChu = $_POST['ghiChu'];
    $nguoiSuaId = $_SESSION['user_id'];
    $ngaySua = date("Y-m-d H:i:s");

    // validate
    if(empty($luongCoBan) || !is_numeric($luongCoBan))
      $error['luongCoBan'] = 'Vui lòng nhập <b> lương cơ bản hợp lệ </b>';
    if(empty($phuCapCom) || !is_numeric($phuCapCom))
      $error['phuCapCom'] = 'Vui lòng nhập <b> phụ cấp cơm hợp lệ </b>';
    if(empty($phuCapXe) || !is_numeric($phuCapXe))
      $error['phuCapXe'] = 'Vui lòng nhập <b> phụ cấp xe hợp lệ </b>';
    if(!is_numeric($tongTamUng))
      $error['tongTamUng'] = 'Vui lòng nhập <b> tổng tạm ứng hợp lệ </b>';
    if(!is_numeric($tienLamThem))
      $error['tienLamThem'] = 'Vui lòng nhập <b> tiền làm thêm hợp lệ </b>';
    if(!is_numeric($thucLinh))
      $error['thucLinh'] = 'Vui lòng nhập <b> thực lĩnh hợp lệ </b>';

    if(!$error)
    {
      $showMess = true;
      $update = "UPDATE luong_nvvp SET 
                  luong_co_ban = '$luongCoBan',
                  phu_cap_com = '$phuCapCom',
                  phu_cap_xe = '$phuCapXe',
                  tong_tam_ung = '$tongTamUng',
                  tien_lam_them = '$tienLamThem',
                  thuc_linh = '$thucLinh',
                  ghi_chu = '$ghiChu',
                  nguoi_sua_id = '$nguoiSuaId',
                  ngay_sua = '$ngaySua'
                  WHERE id = $id";
      mysqli_query($conn, $update);
      $success['success'] = 'Lưu lại thành công';
      echo '<script>setTimeout(function() { window.location.href = "luong_nvvp_list.php"; }, 2000);</script>';
    }
  }

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Lương nhân viên văn phòng
        </h1>
        <ol class="breadcrumb">
            <li><a href="index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> Tổng quan</a></li>
            <li><a href="luong_nvvp.php?p=staff&a=salary">Lương NVVP</a></li>
            <li class="active">Chỉnh sửa lương</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Chỉnh sửa lương</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <?php 
                // show error
                if(isset($error)) 
                {
                  if($showMess == false)
                  {
                    echo "<div class='alert alert-danger alert-dismissible'>";
                    echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
                    echo "<h4><i class='icon fa fa-ban'></i> Lỗi!</h4>";
                    foreach ($error as $err) 
                    {
                      echo $err . "<br/>";
                    }
                    echo "</div>";
                  }
                }
              ?>
                        <?php 
                // show success
                if(isset($success)) 
                {
                  if($showMess == true)
                  {
                    echo "<div class='alert alert-success alert-dismissible'>";
                    echo "<h4><i class='icon fa fa-check'></i> Thành công!</h4>";
                    foreach ($success as $suc) 
                    {
                      echo $suc . "<br/>";
                    }
                    echo "</div>";
                  }
                }
              ?>
                        <form action="" method="POST">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="luongCoBan">Lương cơ bản: </label>
                                        <input type="number" class="form-control" id="luongCoBan"
                                            placeholder="Nhập lương cơ bản" value="<?php echo $row['luong_co_ban']; ?>"
                                            name="luongCoBan">
                                    </div>
                                    <div class="form-group">
                                        <label for="phuCapCom">Phụ cấp cơm: </label>
                                        <input type="number" class="form-control" id="phuCapCom"
                                            placeholder="Nhập phụ cấp cơm" value="<?php echo $row['phu_cap_com']; ?>"
                                            name="phuCapCom">
                                    </div>
                                    <div class="form-group">
                                        <label for="phuCapXe">Phụ cấp xe: </label>
                                        <input type="number" class="form-control" id="phuCapXe"
                                            placeholder="Nhập phụ cấp xe" value="<?php echo $row['phu_cap_xe']; ?>"
                                            name="phuCapXe">
                                    </div>
                                    <div class="form-group">
                                        <label for="tongTamUng">Tổng tạm ứng: </label>
                                        <input type="number" class="form-control" id="tongTamUng"
                                            placeholder="Nhập tổng tạm ứng" value="<?php echo $row['tong_tam_ung']; ?>"
                                            name="tongTamUng">
                                    </div>
                                    <div class="form-group">
                                        <label for="tienLamThem">Tiền làm thêm: </label>
                                        <input type="number" class="form-control" id="tienLamThem"
                                            placeholder="Nhập tiền làm thêm"
                                            value="<?php echo $row['tien_lam_them']; ?>" name="tienLamThem">
                                    </div>
                                    <div class="form-group">
                                        <label for="thucLinh">Thực lĩnh: </label>
                                        <input type="number" class="form-control" id="thucLinh"
                                            placeholder="Nhập thực lĩnh" value="<?php echo $row['thuc_linh']; ?>"
                                            name="thucLinh">
                                    </div>
                                    <div class="form-group">
                                        <label for="ghiChu">Ghi chú: </label>
                                        <textarea class="form-control" id="ghiChu" rows="3"
                                            name="ghiChu"><?php echo $row['ghi_chu']; ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-warning" name="save"><i class="fa fa-save"></i>
                                        Lưu lại</button>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </form>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
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