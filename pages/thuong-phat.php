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
    $id = $_POST['idReward'];
    echo "<script>location.href='sua-thuong-phat.php?p=staff&a=thuongphat&id=".$id."'</script>";
  }

    // show data
    $showData = "SELECT id, ma_thuong_phat, nhanvien_id, ngay_thuong_phat, loai_thuong_phat, so_tien, ghi_chu, nguoi_tao, ngay_tao, nguoi_sua, ngay_sua FROM thuong_phat ORDER BY ngay_tao DESC";
    $result = mysqli_query($conn, $showData);
    $arrShow = array();
    while ($row = mysqli_fetch_array($result)) {
      $arrShow[] = $row;
    }

    // Thêm thưởng phạt-------------------------------------
    // create code reward
    $rewardCode = "MTP" . time();
    
    if(isset($_POST['save']))
    {
      // create array error
      $error = array();
      $success = array();
      $showMess = false;

      // get data from form
      $employeeId = $_POST['employeeId'];
      $rewardDate = $_POST['rewardDate'];
      $rewardType = $_POST['rewardType'];
      $amount = $_POST['amount'];
      $description = $_POST['description'];
      $personCreate = $_POST['personCreate'];
      $dateCreate = date("Y-m-d H:i:s");
      $personEdit = $_POST['personCreate'];
      $dateEdit = date("Y-m-d H:i:s");      
      // validate
      if(empty($employeeId))
        $error['employeeId'] = 'Vui lòng chọn <b> nhân viên </b>';

      if(empty($rewardDate))
        $error['rewardDate'] = 'Vui lòng nhập <b> ngày thưởng phạt </b>';

      if(empty($rewardType))
        $error['rewardType'] = 'Vui lòng chọn <b> loại thưởng phạt </b>';

      if(empty($amount))
        $error['amount'] = 'Vui lòng nhập <b> số tiền </b>';

      if(!$error)
      {
        $showMess = true;
        $insert = "INSERT INTO thuong_phat(ma_thuong_phat, nhanvien_id, ngay_thuong_phat, loai_thuong_phat, so_tien, ghi_chu, nguoi_tao, ngay_tao, nguoi_sua, ngay_sua) 
        VALUES('$rewardCode', '$employeeId', '$rewardDate', '$rewardType', '$amount', '$description', '$personCreate', '$dateCreate', '$personEdit', '$dateEdit')";
        mysqli_query($conn, $insert);
        $success['success'] = 'Thêm thưởng phạt thành công';
        echo '<script>setTimeout("window.location=\'thuong-phat.php?p=staff&a=reward\'",2000);</script>';
      }
    }

    // Xoá thưởng phạt-------------------------------------
    if(isset($_POST['delete']))
    {
      $showMess = true;

      $id = $_POST['idReward'];
      $delete = "DELETE FROM thuong_phat WHERE id = $id";
      mysqli_query($conn, $delete);
      $success['success'] = 'Xóa thưởng phạt thành công.';
      echo '<script>setTimeout("window.location=\'thuong-phat.php?p=staff&a=reward\'",1000);</script>';
    }

  ?>
<!-- Modal -->
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
                    <input type="hidden" name="idReward">
                    Bạn có thực sự muốn xóa thưởng phạt này?
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
            Thưởng phạt
        </h1>
        <ol class="breadcrumb">
            <li><a href="index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> Tổng quan</a></li>
            <li><a href="thuong-phat.php?p=staff&a=reward">Thưởng phạt</a></li>
            <li class="active">Thêm thưởng phạt</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Danh sách thưởng phạt</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã thưởng phạt</th>
                                        <th>Nhân viên</th>
                                        <th>Ngày thưởng phạt</th>
                                        <th>Loại</th>
                                        <th>Số tiền</th>
                                        <th>Mô tả</th>
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
                                        <td><?php echo $arrS['ma_thuong_phat']; ?></td>
                                        <td>
                                            <?php 
                                                $queryEmployee = "SELECT ten_nv FROM nhanvien WHERE id = ".$arrS['nhanvien_id'];
                                                $resultEmployee = mysqli_query($conn, $queryEmployee);
                                                $rowEmployee = mysqli_fetch_array($resultEmployee);
                                                echo $rowEmployee['ten_nv'];
                                              ?>
                                        </td>
                                        <td><?php echo $arrS['ngay_thuong_phat']; ?></td>
                                        <td><?php echo $arrS['loai_thuong_phat']; ?></td>
                                        <td><?php echo number_format($arrS['so_tien']). "vnđ"; ?></td>
                                        <td><?php echo $arrS['ghi_chu']; ?></td>
                                        <td><?php echo $arrS['nguoi_tao']; ?></td>
                                        <td><?php echo $arrS['ngay_tao']; ?></td>
                                        <td><?php echo $arrS['nguoi_sua']; ?></td>
                                        <td><?php echo $arrS['ngay_sua']; ?></td>
                                        <th>
                                            <?php 
                                                if($row_acc['quyen'] == 1)
                                                {
                                                  echo "<form method='POST'>";
                                                  echo "<input type='hidden' value='".$arrS['id']."' name='idReward'/>";
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
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Thêm thưởng phạt</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                    class='fa fa-remove'></i></button>
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
                                        <input type="text" class="form-control" id="exampleInputEmail1"
                                            name="rewardCode" value="<?php echo $rewardCode; ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="employeeId">Nhân viên: </label>
                                        <select class="form-control" id="employeeId" name="employeeId">
                                            <option value="">-- Chọn nhân viên --</option>
                                            <?php
                          $queryEmployee = "SELECT id, ma_nv, ten_nv FROM nhanvien";
                          $resultEmployee = mysqli_query($conn, $queryEmployee);
                          while ($rowEmployee = mysqli_fetch_array($resultEmployee)) {
                            echo "<option value='".$rowEmployee['id']."'>".$rowEmployee['ma_nv']." - ".$rowEmployee['ten_nv']."</option>";
                          }
                          ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Ngày thưởng phạt: </label>
                                        <input type="date" class="form-control" id="exampleInputEmail1"
                                            name="rewardDate">
                                    </div>
                                    <div class="form-group">
                                        <label for="rewardType">Loại thưởng phạt: </label>
                                        <select class="form-control" id="rewardType" name="rewardType">
                                            <option value="">-- Chọn loại --</option>
                                            <option value="Thưởng">Thưởng</option>
                                            <option value="Phạt">Phạt</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Số tiền: </label>
                                        <input type="text" class="form-control" id="exampleInputEmail1"
                                            placeholder="Nhập số tiền" name="amount">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Mô tả: </label>
                                        <textarea id="editor1" rows="10" cols="80" name="description">
                        </textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Người tạo: </label>
                                        <input type="text" class="form-control" id="exampleInputEmail1"
                                            value="<?php echo $row_acc['ho']; ?> <?php echo $row_acc['ten']; ?>"
                                            name="personCreate" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Ngày tạo: </label>
                                        <input type="text" class="form-control" id="exampleInputEmail1"
                                            value="<?php echo date('d-m-Y H:i:s'); ?>" name="dateCreate" readonly>
                                    </div>
                                    <!-- /.form-group -->
                                    <?php 
                        if($_SESSION['level'] == 1)
                          echo "<button type='submit' class='btn btn-primary' name='save'><i class='fa fa-plus'></i> Thêm thưởng phạt</button>";
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