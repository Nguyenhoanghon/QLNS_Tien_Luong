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
    $showData = "SELECT id, ma_phu_cap, chuc_vu_id, ten_phu_cap, tien_phu_cap, ghi_chu, nguoi_tao, ngay_tao, nguoi_sua, ngay_sua FROM phu_cap WHERE id = $id ORDER BY ngay_tao DESC";
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
    $titleAllowance = $_POST['titlePosition'];
    $allowanceAmount = $_POST['salary'];
    $description = $_POST['description'];
    $personEdit = $_POST['personCreate'];
    $dateEdit = date("Y-m-d H:i:s");

    // validate
    if(empty($titleAllowance))
      $error['titleAllowance'] = 'Vui lòng nhập <b> tên phụ cấp </b>';

    if(empty($allowanceAmount) || !is_numeric($allowanceAmount))
      $error['allowanceAmount'] = 'Vui lòng nhập <b> số tiền phụ cấp hợp lệ </b>';

    if(!$error)
    {
      $showMess = true;
      $update = "UPDATE phu_cap SET 
                  ten_phu_cap = '$titleAllowance',
                  tien_phu_cap = '$allowanceAmount',
                  ghi_chu = '$description',
                  nguoi_sua = '$personEdit',
                  ngay_sua = '$dateEdit'
                  WHERE id = $id";
      mysqli_query($conn, $update);
      $success['success'] = 'Lưu lại thành công';
      //điều hướng về trang phụ cấp
/*       echo '<script>setTimeout("window.location=\'sua-phu-cap.php?p=staff&a=allowance&id='.
      $id.'\'",1000);</script>'; */
      echo '<script>setTimeout("window.location=\'phu-cap.php?p=staff&a=allowance\'",2000);</script>';
    }
  }

?>
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Phụ cấp
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> Tổng quan</a></li>
        <li><a href="phu-cap.php?p=staff&a=allowance">Phụ cấp</a></li>
        <li class="active">Chỉnh sửa phụ cấp</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Chỉnh sửa phụ cấp</h3>
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
                      <label for="exampleInputEmail1">Mã phụ cấp: </label>
                      <input type="text" class="form-control" id="exampleInputEmail1" name="allowanceCode" value="<?php echo $row['ma_phu_cap']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Tên phụ cấp: </label>
                      <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Nhập tên phụ cấp" value="<?php echo $row['ten_phu_cap']; ?>" name="titlePosition">
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Tiền phụ cấp: </label>
                      <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Nhập số tiền phụ cấp" value="<?php echo $row['tien_phu_cap']; ?>" name="salary">
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Mô tả: </label>
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