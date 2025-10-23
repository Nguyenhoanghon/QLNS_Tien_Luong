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
    $showData = "SELECT id, ma_thuong_phat, nhanvien_id, ngay_thuong_phat, loai_thuong_phat, so_tien, ghi_chu, nguoi_tao, ngay_tao, nguoi_sua, ngay_sua FROM thuong_phat WHERE id = $id ORDER BY ngay_tao DESC";
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
    $rewardPunishDate = $_POST['rewardPunishDate'];
    $rewardPunishType = $_POST['rewardPunishType'];
    $rewardPunishAmount = $_POST['rewardPunishAmount'];
    $description = $_POST['description'];
    $personEdit = $_POST['personCreate'];
    $dateEdit = date("Y-m-d H:i:s");

    // validate
    if(empty($rewardPunishDate))
      $error['rewardPunishDate'] = 'Vui lòng nhập <b> ngày thưởng phạt </b>';

    if(empty($rewardPunishType))
      $error['rewardPunishType'] = 'Vui lòng nhập <b> loại thưởng phạt </b>';

    if(empty($rewardPunishAmount) || !is_numeric($rewardPunishAmount))
      $error['rewardPunishAmount'] = 'Vui lòng nhập <b> số tiền thưởng phạt hợp lệ </b>';

    if(!$error)
    {
      $showMess = true;
      $update = "UPDATE thuong_phat SET 
                  ngay_thuong_phat = '$rewardPunishDate',
                  loai_thuong_phat = '$rewardPunishType',
                  so_tien = '$rewardPunishAmount',
                  ghi_chu = '$description',
                  nguoi_sua = '$personEdit',
                  ngay_sua = '$dateEdit'
                  WHERE id = $id";
      mysqli_query($conn, $update);
      $success['success'] = 'Lưu lại thành công';
      echo '<script>setTimeout("window.location=\'thuong-phat.php?p=staff&a=rewardPunish\'",2000);</script>';
    }
  }

?>
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Thưởng phạt
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> Tổng quan</a></li>
        <li><a href="thuong-phat.php?p=staff&a=rewardPunish">Thưởng phạt</a></li>
        <li class="active">Chỉnh sửa thưởng phạt</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Chỉnh sửa thưởng phạt</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
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
                      <label for="exampleInputEmail1">Mã thưởng phạt: </label>
                      <input type="text" class="form-control" id="exampleInputEmail1" name="rewardPunishCode" value="<?php echo $row['ma_thuong_phat']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Ngày thưởng phạt: </label>
                      <input type="date" class="form-control" id="exampleInputEmail1" value="<?php echo $row['ngay_thuong_phat']; ?>" name="rewardPunishDate">
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Loại thưởng phạt: </label>
                      <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Nhập loại thưởng phạt" value="<?php echo $row['loai_thuong_phat']; ?>" name="rewardPunishType" >
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Số tiền: </label>
                      <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Nhập số tiền" value="<?php echo $row['so_tien']; ?>" name="rewardPunishAmount">
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Ghi chú: </label>
                      <textarea id="editor1" rows="10" cols="80" name="description"><?php echo $row['ghi_chu']; ?>
                      </textarea>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Người sửa: </label>
                      <input type="text" class="form-control" id="exampleInputEmail1" value="<?php echo $row_acc['ho']; ?> <?php echo $row_acc['ten']; ?>" name="personCreate" readonly>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Ngày sửa: </label>
                      <input type="text" class="form-control" id="exampleInputEmail1" value="<?php echo date('d-m-Y H:i:s'); ?>" name="dateCreate" readonly>
                    </div>
                    <!-- /.form-group -->
                    <?php 
                      if($_SESSION['level'] == 1)
                        echo "<button type='submit' class='btn btn-warning' name='save'><i class='fa fa-save'></i> Lưu lại</button>";
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
