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
      $id = $_POST['idChamCong'];
      echo "<script>location.href='sua-cham-cong.php?p=staff&a=chamcong&id=".$id."'</script>";
    }

    // show data
    $showData = "SELECT id, ma_cham_cong, nhanvien_id, thang, nam, so_ngay_cong, so_gio_lam_them, ghi_chu, nguoi_tao, ngay_tao, nguoi_sua, ngay_sua FROM cham_cong ORDER BY ngay_tao DESC";
    $result = mysqli_query($conn, $showData);
    $arrShow = array();
    while ($row = mysqli_fetch_array($result)) {
      $arrShow[] = $row;
    }

    // create code cham cong
    $chamCongCode = "MCC" . time();

    // add record
    if(isset($_POST['save']))
    {
      // create array error
      $error = array();
      $success = array();
      $showMess = false;

      // get data from form
      $employeeId = $_POST['employeeId'];
      $month = $_POST['month'];
      $year = $_POST['year'];
      $workingDays = $_POST['workingDays'];
      $overtimeHours = $_POST['overtimeHours'];
      $description = $_POST['description'];
      $personCreate = $_POST['personCreate'];
      $dateCreate = date("Y-m-d H:i:s");
      $personEdit = $_POST['personCreate'];
      $dateEdit = date("Y-m-d H:i:s");      
      // validate
      if(empty($employeeId))
        $error['employeeId'] = 'Vui lòng chọn <b> nhân viên </b>';

      if(empty($month) || empty($year))
        $error['date'] = 'Vui lòng nhập <b> tháng và năm </b>';

      if(empty($workingDays))
        $error['workingDays'] = 'Vui lòng nhập <b> số ngày công </b>';

      if(!$error)
      {
        $showMess = true;
        $insert = "INSERT INTO cham_cong(ma_cham_cong, nhanvien_id, thang, nam, so_ngay_cong, so_gio_lam_them, ghi_chu, nguoi_tao, ngay_tao, nguoi_sua, ngay_sua) VALUES('$chamCongCode', '$employeeId', '$month', '$year', '$workingDays', '$overtimeHours', '$description', '$personCreate', '$dateCreate', '$personEdit', '$dateEdit')";
        mysqli_query($conn, $insert);
        $success['success'] = 'Thêm chấm công thành công';
        echo '<script>setTimeout("window.location=\'cham-cong.php?p=staff&a=chamcong\'",2000);</script>';
      }
    }

    // delete record
    if(isset($_POST['delete']))
    {
      $showMess = true;

      $id = $_POST['idChamCong'];
      $delete = "DELETE FROM cham_cong WHERE id = $id";
      mysqli_query($conn, $delete);
      $success['success'] = 'Xóa chấm công thành công.';
      echo '<script>setTimeout("window.location=\'cham-cong.php?p=staff&a=chamcong\'",1000);</script>';
    }

  ?>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
              <input type="hidden" name="idChamCong">
              Bạn có thực sự muốn xóa bản ghi chấm công này?
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
          Chấm công
        </h1>
        <ol class="breadcrumb">
          <li><a href="index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> Tổng quan</a></li>
          <li><a href="cham-cong.php?p=staff&a=chamcong">Chấm công</a></li>
          <li class="active">Thêm chấm công</li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-xs-12">

            <div class="box">
              <div class="box-header">
                <h3 class="box-title">Danh sách chấm công</h3>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <div class="table-responsive">
                  <table id="example1" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>STT</th>
                      <th>Mã chấm công</th>
                      <th>Nhân viên</th>
                      <th>Tháng</th>
                      <th>Năm</th>
                      <th>Số ngày công</th>
                      <th>Số giờ làm thêm</th>
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
                          <td><?php echo $arrS['ma_cham_cong']; ?></td>
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
                          <td><?php echo $arrS['so_ngay_cong']; ?></td>
                          <td><?php echo $arrS['so_gio_lam_them']; ?></td>
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
                                echo "<input type='hidden' value='".$arrS['id']."' name='idChamCong'/>";
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
          <!-- Thêm châm công -->
          <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">Thêm chấm công</h3>
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
                        <label for="exampleInputEmail1">Mã chấm công: </label>
                        <input type="text" class="form-control" id="exampleInputEmail1" name="chamCongCode" value="<?php echo $chamCongCode; ?>" readonly>
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
                        <label for="month">Tháng: </label>
                        <input type="number" class="form-control" id="month" placeholder="Nhập tháng" name="month" value="<?php echo date('m'); ?>">
                      </div>
                      <div class="form-group">
                        <label for="year">Năm: </label>
                        <input type="number" class="form-control" id="year" placeholder="Nhập năm" name="year" value="<?php echo date('Y'); ?>">
                      </div>
                      <div class="form-group">
                        <label for="workingDays">Số ngày công: </label>
                        <input type="number" class="form-control" id="workingDays" placeholder="Nhập số ngày công" name="workingDays">
                      </div>
                      <div class="form-group">
                        <label for="overtimeHours">Số giờ làm thêm: </label>
                        <input type="text" class="form-control" id="overtimeHours" placeholder="Nhập số giờ làm thêm (số thập phân)" name="overtimeHours" pattern="^\d+(\.\d+)?$" title="Vui lòng nhập số hợp lệ (có thể bao gồm số thập phân)">
                      </div>
                      <div class="form-group">
                        <label for="description">Ghi chú: </label>
                        <textarea id="description" rows="5" class="form-control" name="description"></textarea>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputEmail1">Người tạo: </label>
                        <input type="text" class="form-control" id="exampleInputEmail1" value="<?php echo $row_acc['ho']; ?> <?php echo $row_acc['ten']; ?>" name="personCreate" readonly>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputEmail1">Ngày tạo: </label>
                        <input type="text" class="form-control" id="exampleInputEmail1" value="<?php echo date('d-m-Y H:i:s'); ?>" name="dateCreate" readonly>
                      </div>
                      <!-- /.form-group -->
                      <?php 
                        if($_SESSION['level'] == 1)
                          echo "<button type='submit' class='btn btn-primary' name='save'><i class='fa fa-plus'></i> Thêm chấm công</button>";
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