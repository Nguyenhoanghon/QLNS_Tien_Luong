<?php 

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
    $showData = "SELECT id, ma_cham_cong, nhanvien_id, thang, nam, so_ngay_cong, so_gio_lam_them, ghi_chu, nguoi_tao, ngay_tao, nguoi_sua, ngay_sua FROM cham_cong WHERE id = $id ORDER BY ngay_tao DESC";
    $result = mysqli_query($conn, $showData);
    $arrShow = array();
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
    $workingDays = $_POST['workingDays'];
    $overtimeHours = $_POST['overtimeHours'];
    $description = $_POST['description'];
    $personEdit = $_POST['personEdit'];
    $dateEdit = date("Y-m-d H:i:s");
    $thang = $_POST['thang'];
    $nam = $_POST['nam'];

    // validate
    if(empty($workingDays) || !is_numeric($workingDays))
      $error['workingDays'] = 'Vui lòng nhập <b> số ngày công hợp lệ </b>';
    //empty($overtimeHours) || 
    if(!is_numeric($overtimeHours))
      $error['overtimeHours'] = 'Vui lòng nhập <b> số giờ làm thêm hợp lệ </b>';

    if(!$error)
    {
      $showMess = true;
      $update = "UPDATE cham_cong SET 
                  so_ngay_cong = '$workingDays',
                  so_gio_lam_them = '$overtimeHours',
                  ghi_chu = '$description',
                  nguoi_sua = '$personEdit',
                  ngay_sua = '$dateEdit'
                  WHERE id = $id";
      mysqli_query($conn, $update);
      $success['success'] = 'Lưu lại thành công';
      echo '<script>setTimeout(function() { window.location.href = "chamcong_list.php?thang=' . $thang . '&nam=' . $nam . '&loc="; }, 2000);</script>';
    }
  }

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Chấm công
        </h1>
        <ol class="breadcrumb">
            <li><a href="index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> Tổng quan</a></li>
            <li><a href="cham-cong.php?p=staff&a=attendance">Chấm công</a></li>
            <li class="active">Chỉnh sửa chấm công</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Chỉnh sửa chấm công</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                    class="fa fa-remove"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <?php 
                // show error
                if($row_acc['quyen'] != 1) 
                {
                  echo "<div class='alert alert-warning alert-dismissible'>";
                  echo "<h4><i class='icon fa fa-ban'></i> Thông báo!</h4>";
                  echo "Bạn <b> không có quyền </b> thực hiện chức năng này.";
                  echo "</div>";
                }
              ?>

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
                                        <label for="exampleInputEmail1">Mã chấm công: </label>
                                        <input type="text" class="form-control" id="exampleInputEmail1"
                                            name="attendanceCode" value="<?php echo $row['ma_cham_cong']; ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Số ngày công: </label>
                                        <input type="number" class="form-control" id="exampleInputEmail1"
                                            placeholder="Nhập số ngày công" value="<?php echo $row['so_ngay_cong']; ?>"
                                            name="workingDays" min="0" max="31" oninput="validateWorkingDays(this)">
                                        <script>
                                        function validateWorkingDays(input) {
                                            const value = parseInt(input.value, 10);
                                            if (value < input.min || value > input.max) {
                                                alert('Vui lòng nhập số ngày công trong khoảng từ ' + input.min +
                                                    ' đến ' + input.max + '.');
                                                input.value = '';
                                            }
                                        }
                                        </script>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Số giờ làm thêm: </label>
                                        <input type="text" class="form-control" id="exampleInputEmail1"
                                            placeholder="Nhập số giờ làm thêm"
                                            value="<?php echo $row['so_gio_lam_them']; ?>" name="overtimeHours">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Mô tả: </label>
                                        <textarea id="editor1" rows="10" cols="80" name="description"><?php echo $row['ghi_chu']; ?>
                      </textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Người sửa: </label>
                                        <input type="text" class="form-control" id="exampleInputEmail1"
                                            value="<?php echo $row_acc['ho']; ?> <?php echo $row_acc['ten']; ?>"
                                            name="personEdit" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Ngày sửa: </label>
                                        <input type="text" class="form-control" id="exampleInputEmail1"
                                            value="<?php echo date('d-m-Y H:i:s'); ?>" name="dateEdit" readonly>
                                    </div>
                                    <!-- /.form-group -->
                                    <?php 
                      if($_SESSION['level'] == 1){
                        echo "<input type='hidden' value='".$row['thang']."' name='thang'/>";
                        echo "<input type='hidden' value='".$row['nam']."' name='nam'/>";
                        echo "<button type='submit' class='btn btn-warning' name='save'><i class='fa fa-save'></i> Lưu lại</button>";
                      }
                        
                    ?>
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