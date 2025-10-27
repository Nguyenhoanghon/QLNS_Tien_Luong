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
      echo "<script>location.href='sua_cham_cong.php?p=staff&a=chamcong&id=".$id."'</script>";
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
        echo '<script>setTimeout("window.location=\'chamcong_list.php?p=staff&a=chamcong\'",2000);</script>';
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
      echo '<script>setTimeout("window.location=\'chamcong_list.php?p=staff&a=chamcong\'",1000);</script>';
    }
    
  // Khởi tạo bảng chấm công
  if (isset($_POST['khoitao'])) {
  // Nhận dữ liệu từ form
  $thang = intval($_POST['thang']);
  $nam = intval($_POST['nam']);
  $nguoi_tao = $row_acc['ho'] . ' ' . $row_acc['ten'];

  // ====== BƯỚC 1: KIỂM TRA DỮ LIỆU TỒN TẠI ======
  $check_sql = "SELECT COUNT(*) AS total FROM cham_cong WHERE thang = $thang AND nam = $nam";
  $result = $conn->query($check_sql);
  $row = $result->fetch_assoc();

  if ($row['total'] > 0) {
    // Nếu đã có dữ liệu => báo lỗi và dừng
    echo "
    <div style='font-family: Arial; text-align:center; margin-top:50px;'>
      <h3 style='color:red;'>⚠️ Dữ liệu chấm công tháng $thang / $nam đã tồn tại!</h3>
      <p>Vui lòng kiểm tra lại trước khi khởi tạo mới.</p>
    </div>";
    echo '<script>setTimeout("window.location=\'chamcong_list.php?p=staff&a=chamcong\'",3000);</script>';
   /* $conn->close();
     exit; */
  }
  else
  {
  // ====== BƯỚC 2: THỰC HIỆN KHỞI TẠO CHẤM CÔNG ======
    $sql = "
    SET @THANG = $thang;
    SET @NAM = $nam;
    SET @rownum = 0;

    INSERT INTO cham_cong 
    (`ma_cham_cong`, `nhanvien_id`, `thang`, `nam`, `so_ngay_cong`, `so_gio_lam_them`, 
    `ghi_chu`, `nguoi_tao`, `ngay_tao`, `nguoi_sua`, `ngay_sua`)
    SELECT 
      CONCAT('CC',
      LPAD(@THANG, 2, '0'),
      LPAD(@NAM % 100, 2, '0'),
      LPAD(@rownum := @rownum + 1, 3, '0')
      ) AS ma_cham_cong,
      nv.id AS nhanvien_id,
      @THANG AS thang,
      @NAM AS nam,
      0 AS so_ngay_cong,
      0 AS so_gio_lam_them,
      '' AS ghi_chu,
      '$nguoi_tao' AS nguoi_tao,
      NOW() AS ngay_tao,
      '$nguoi_tao' AS nguoi_sua,
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
      </div>";;
      echo '<script>setTimeout("window.location=\'chamcong_list.php?p=staff&a=chamcong\'",3000);</script>';
    }else {
        echo "
            <div style='font-family: Arial; text-align:center; margin-top:50px;'>
              <h3 style='color:red;'>❌ Lỗi khi khởi tạo: " . $conn->error . "</h3>
              <a href='index.php' style='margin-top:20px; display:inline-block; text-decoration:none; background:#6c757d; color:#fff; padding:10px 20px; border-radius:5px;'>Thử lại</a>
            </div>";
        echo '<script>setTimeout("window.location=\'chamcong_list.php?p=staff&a=chamcong\'",1000);</script>';
    }
  }
}
//Dữ liệu cho bộ lọc
  if (isset($_GET['loc'])) {
  // ====== BƯỚC 4: HIỂN THỊ DỮ LIỆU THEO BỘ LỌC ======
  // Lấy giá trị tháng và năm từ form lọc (nếu có)
    $thang_filter = isset($_GET['thang']) ? $_GET['thang'] : date('n');
    $nam_filter   = isset($_GET['nam']) ? $_GET['nam'] : date('Y');

    // Lấy dữ liệu theo bộ lọc
    $sql = "SELECT * FROM cham_cong WHERE thang = '$thang_filter' AND nam = '$nam_filter' ORDER BY id ASC";
    $query = mysqli_query($conn, $sql);
    $arrShow = [];
    while ($row = mysqli_fetch_assoc($query)) {
      $arrShow[] = $row;
    }
  }
?>
<!-- Modal xác thực xoá-->
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
            <li><a href="chamcong_list.php?p=staff&a=chamcong">Chấm công</a></li>
            <li class="active">Thêm chấm công</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <!-- Form Khởi tạo chấm công -->
                <div class="box box-primary">
                    <!--box-header -->
                    <div class="box-header with-border">
                        <h3 class="box-title">Khởi tạo chấm công</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                    class="fa fa-remove"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->

                    <!--box-body  -->
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
                        <form method="POST">
                            <div class="form-group">
                                <label for="thang">Tháng</label>
                                <select class="form-control" id="thang" name="thang" required>
                                    <option value="" disabled>Chọn tháng</option>
                                    <option value="<?php echo date('n'); ?>" selected><?php echo date('n'); ?></option>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nam">Năm</label>
                                <select class="form-control" id="nam" name="nam" required>
                                    <option value="" disabled>Chọn năm</option>
                                    <?php for ($i = 2020; $i <= 2030; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($i == 2025) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" name="khoitao">Khởi tạo</button>
                        </form>
                    </div>
                    <!--/.box-body -->
                </div>
                <!--./Form Khởi tạo chấm công  -->

                <!--Bộ lọc chấm công -->
                <div class="box box-primary">
                    <!--box-header -->
                    <div class="box-header with-border">
                        <h3 class="box-title">Chấm công</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                    class="fa fa-remove"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <!--box-body form bô lọc  -->
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
                        <form method="GET" class="form-inline">
                            <div class="form-group me-3">
                                <label for="thang" class="me-2 fw-bold">Chọn tháng:</label>
                                <select name="thang" id="thang" class="form-control">
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= $i ?>" <?= ($i == $thang_filter) ? 'selected' : '' ?>>
                                        <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="nam">Năm</label>
                                <select class="form-control" id="nam" name="nam" required>
                                    <option value="" disabled>Chọn năm</option>
                                    <?php for ($i = 2020; $i <= 2030; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($i == 2025) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary" name="loc">Xem</button>

                            <a href="chamcong_list.php" class="btn btn-secondary" style="float: right;">
                                <i class="fa fa-refresh"></i> Làm mới
                            </a>
                        </form>
                    </div>
                    <!--/.box-body form bộ lọc -->
                    <!--box-body table chấm công -->
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
                                                echo "<input type='hidden' value='".$arrS['thang']."' name='thang'/>";
                                                echo "<input type='hidden' value='".$arrS['nam']."' name='nam'/>";
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
                    <!--./box-body table chấm công -->
                </div>
                <!--./Bộ lọc chấm công -->

                <!--box Thêm châm công -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Thêm chấm công</h3>
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
                                            name="chamCongCode" value="<?php echo $chamCongCode; ?>" readonly>
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
                                        <input type="number" class="form-control" id="month" placeholder="Nhập tháng"
                                            name="month" value="<?php echo date('m'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="year">Năm: </label>
                                        <input type="number" class="form-control" id="year" placeholder="Nhập năm"
                                            name="year" value="<?php echo date('Y'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="workingDays">Số ngày công: </label>
                                        <input type="number" class="form-control" id="workingDays"
                                            placeholder="Nhập số ngày công" name="workingDays">
                                    </div>
                                    <div class="form-group">
                                        <label for="overtimeHours">Số giờ làm thêm: </label>
                                        <input type="text" class="form-control" id="overtimeHours"
                                            placeholder="Nhập số giờ làm thêm (số thập phân)" name="overtimeHours"
                                            pattern="^\d+(\.\d+)?$"
                                            title="Vui lòng nhập số hợp lệ (có thể bao gồm số thập phân)">
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Ghi chú: </label>
                                        <textarea id="description" rows="5" class="form-control"
                                            name="description"></textarea>
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
                <!--/.box Thêm châm công-->
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