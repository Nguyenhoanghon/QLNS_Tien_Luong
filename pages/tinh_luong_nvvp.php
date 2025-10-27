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
      echo "<script>location.href='sua_tinh_luong_nvvp.php?p=staff&a=luong&id=".$id."'</script>";
    }

    // show data
    $showData = "SELECT lnv.id, lnv.ma_luong, lnv.nhanvien_id, nv.ten_nv AS ten_nhan_vien, cv.ten_chuc_vu AS chuc_vu, 
              lnv.thang, lnv.nam, lnv.so_cmnd, lnv.luong_co_ban, lnv.phu_cap_com, lnv.phu_cap_xe, 
              lnv.tong_tam_ung, lnv.tien_lam_them, lnv.thuc_linh, lnv.ngay_tinh_luong, lnv.ghi_chu, 
              lnv.nguoi_tao_id, lnv.ngay_tao, lnv.nguoi_sua_id, lnv.ngay_sua 
           FROM luong_nvvp lnv
           LEFT JOIN nhanvien nv ON lnv.nhanvien_id = nv.id
           LEFT JOIN chuc_vu cv ON nv.chuc_vu_id = cv.id
           ORDER BY lnv.ngay_tao DESC";
    $result = mysqli_query($conn, $showData);
    $arrShow = array();
    while ($row = mysqli_fetch_array($result)) {
      $arrShow[] = $row;
    }

    // create code cham cong
    //$chamCongCode = "MCC" . time();

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
      $personCreateId = $row_acc['id']; // Assuming $row_acc contains the logged-in user's info
      $dateCreate = date("Y-m-d H:i:s");
      $personEditId = $row_acc['id'];
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
      // Fetch employee details
      $queryEmployee = "SELECT nv.so_cmnd, cv.ten_chuc_vu, cv.luong_ngay 
                FROM nhanvien nv 
                LEFT JOIN chuc_vu cv ON nv.chuc_vu_id = cv.id 
                WHERE nv.id = '$employeeId'";
      $resultEmployee = mysqli_query($conn, $queryEmployee);
      $employeeData = mysqli_fetch_assoc($resultEmployee);

      if ($employeeData) {
        $so_cmnd = $employeeData['so_cmnd'];
        $ten_chuc_vu = $employeeData['ten_chuc_vu'];
        $luong_ngay = $employeeData['luong_ngay'];

        // Calculate salary components
        $luong_co_ban = $workingDays * $luong_ngay;
        $phu_cap_com = 900000; // Fixed value
        $phu_cap_xe = 600000; // Fixed value
        $tong_tam_ung = 0; // Default value, can be updated later
        $tien_lam_them = $overtimeHours * ($luong_ngay / 8);
        $thuc_linh = $luong_co_ban + $phu_cap_com + $phu_cap_xe + $tien_lam_them - $tong_tam_ung;

        // Generate salary code
        $ma_luong = "ML" . str_pad($month, 2, '0', STR_PAD_LEFT) . str_pad($year % 100, 2, '0', STR_PAD_LEFT) . str_pad($employeeId, 3, '0', STR_PAD_LEFT);

        // Insert into luong_nvvp table
        $insert = "INSERT INTO luong_nvvp(ma_luong, nhanvien_id, thang, nam, so_cmnd, ten_chuc_vu, 
              luong_co_ban, phu_cap_com, phu_cap_xe, tong_tam_ung, tien_lam_them, thuc_linh, 
              ngay_tinh_luong, ghi_chu, nguoi_tao_id, ngay_tao, nguoi_sua_id, ngay_sua) 
              VALUES('$ma_luong', '$employeeId', '$month', '$year', '$so_cmnd', '$ten_chuc_vu', 
              '$luong_co_ban', '$phu_cap_com', '$phu_cap_xe', '$tong_tam_ung', '$tien_lam_them', '$thuc_linh', 
              CURDATE(), '$description', '$personCreateId', '$dateCreate', '$personEditId', '$dateEdit')";
        mysqli_query($conn, $insert);

        $showMess = true;
        $success['success'] = 'Thêm bảng lương thành công';
        echo '<script>setTimeout("window.location=\'chamcong_list.php?p=staff&a=chamcong\'",2000);</script>';
      } else {
        $error['employeeData'] = 'Không tìm thấy thông tin nhân viên.';
      }
      }
    }

    // delete record
    if(isset($_POST['delete']))
    {
      $showMess = true;

      $id = $_POST['idChamCong'];
      $delete = "DELETE FROM luong_nvvp WHERE id = $id";
      mysqli_query($conn, $delete);
      $success['success'] = 'Xóa bản ghi lương thành công.';
      echo '<script>setTimeout("window.location=\'tinh_luong_nvvp.php?p=salary&a=tinh_luong_nvvp\'",1000);</script>';
    }
    
  // Khởi tạo bảng tính lương
  if (isset($_POST['khoitao'])) {
    // Nhận dữ liệu từ form
    $thang = intval($_POST['thang']);
    $nam = intval($_POST['nam']);
    $nguoi_tao = $row_acc['ho'] . ' ' . $row_acc['ten'];

    // ====== BƯỚC 1: KIỂM TRA DỮ LIỆU TỒN TẠI ======
    $check_sql = "SELECT COUNT(*) AS total FROM luong_nvvp WHERE thang = $thang AND nam = $nam";
    $result = $conn->query($check_sql);
    $row = $result->fetch_assoc();

    if ($row['total'] > 0) {
      // Nếu đã có dữ liệu => báo lỗi và dừng
      echo "
      <div style='font-family: Arial; text-align:center; margin-top:50px;'>
        <h3 style='color:red;'>⚠️ Dữ liệu lương tháng $thang / $nam đã tồn tại!</h3>
        <p>Vui lòng kiểm tra lại trước khi khởi tạo mới.</p>
      </div>";
      echo '<script>setTimeout("window.location=\'tinh_luong_nvvp.php?p=salary&a=tinh_luong_nvvp\'",3000);</script>';
    }
    else
    {
    // ====== BƯỚC 2: THỰC HIỆN KHỞI TẠO TÍNH LƯƠNG ======
      $sql = "
      SET @THANG = $thang;
      SET @NAM = $nam;
      SET @PHUCAP_COM = 900000;
      SET @PHUCAP_XE = 600000;
      SET @rownum = 0;

      INSERT INTO luong_nvvp
      (`ma_luong`, `nhanvien_id`, `thang`, `nam`, `so_cmnd`, `ten_chuc_vu`, 
      `luong_co_ban`, `phu_cap_com`, `phu_cap_xe`, 
      `tong_tam_ung`, `tien_lam_them`, `thuc_linh`, 
      `ngay_tinh_luong`, `ghi_chu`, 
      `nguoi_tao_id`, `ngay_tao`, `nguoi_sua_id`, `ngay_sua`)

      SELECT 
        CONCAT('ML',
          LPAD(@THANG, 2, '0'),
          LPAD(@NAM % 100, 2, '0'),
          LPAD(@rownum := @rownum + 1, 3, '0')
        ) AS ma_luong,
        
        nv.id AS nhanvien_id,
        @THANG AS thang,
        @NAM AS nam,
        nv.so_cmnd AS so_cmnd,
        cv.ten_chuc_vu AS ten_chuc_vu,

        -- Lương cơ bản = số ngày công * lương ngày
        COALESCE(cc.so_ngay_cong, 0) * COALESCE(cv.luong_ngay, 0) AS luong_co_ban,

        @PHUCAP_COM AS phu_cap_com,
        @PHUCAP_XE AS phu_cap_xe,

        -- Tổng tạm ứng theo nhân viên (tính theo tháng & năm)
        COALESCE(tu.tong_tam_ung, 0) AS tong_tam_ung,

        -- Tiền làm thêm = số giờ làm thêm * lương ngày / 8
        COALESCE(cc.so_gio_lam_them, 0) * COALESCE(cv.luong_ngay, 0) / 8 AS tien_lam_them,

        -- Thực lĩnh = lương cơ bản + phụ cấp + tiền làm thêm - tạm ứng
        (COALESCE(cc.so_ngay_cong, 0) * COALESCE(cv.luong_ngay, 0)
        + @PHUCAP_COM + @PHUCAP_XE
        + COALESCE(cc.so_gio_lam_them, 0) * COALESCE(cv.luong_ngay, 0) / 8
        - COALESCE(tu.tong_tam_ung, 0)) AS thuc_linh,

        CURDATE() AS ngay_tinh_luong,
        '' AS ghi_chu,
        1 AS nguoi_tao_id,
        NOW() AS ngay_tao,
        1 AS nguoi_sua_id,
        NOW() AS ngay_sua

      FROM nhanvien nv
      LEFT JOIN chuc_vu cv ON nv.chuc_vu_id = cv.id

      -- Chấm công theo tháng/năm
      LEFT JOIN (
        SELECT nhanvien_id, SUM(so_ngay_cong) AS so_ngay_cong, SUM(so_gio_lam_them) AS so_gio_lam_them
        FROM cham_cong
        WHERE thang = @THANG AND nam = @NAM
        GROUP BY nhanvien_id
      ) cc ON cc.nhanvien_id = nv.id

      -- Tổng tạm ứng trong tháng/năm (tính theo ngày tạm ứng)
      LEFT JOIN (
        SELECT nhanvien_id, SUM(so_tien) AS tong_tam_ung
        FROM tam_ung
        WHERE MONTH(ngay_tam_ung) = @THANG AND YEAR(ngay_tam_ung) = @NAM
        GROUP BY nhanvien_id
      ) tu ON tu.nhanvien_id = nv.id

      -- Chỉ thêm nhân viên chính thức, đang hoạt động và chưa có bản ghi lương tháng này
      WHERE nv.trang_thai = 1
        AND nv.loai_nv_id = 2
        AND NOT EXISTS (
          SELECT 1 FROM luong_nvvp l 
          WHERE l.nhanvien_id = nv.id AND l.thang = @THANG AND l.nam = @NAM
        )

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
        </div>";
        echo '<script>setTimeout("window.location=\'tinh_luong_nvvp.php?p=salary&a=tinh_luong_nvvp\'",3000);</script>';
      } else {
          echo "
              <div style='font-family: Arial; text-align:center; margin-top:50px;'>
                <h3 style='color:red;'>❌ Lỗi khi khởi tạo: " . $conn->error . "</h3>
                <a href='index.php' style='margin-top:20px; display:inline-block; text-decoration:none; background:#6c757d; color:#fff; padding:10px 20px; border-radius:5px;'>Thử lại</a>
              </div>";
          echo '<script>setTimeout("window.location=\'tinh_luong_nvvp.php?p=salary&a=tinh_luong_nvvp\'",1000);</script>';
      }
    }
  }
  // Cập nhật bảng lương
  if (isset($_GET['update'])) {
    // Nhận dữ liệu từ form
    $thang = intval($_GET['thang']);
    $nam = intval($_GET['nam']);

    // Câu lệnh SQL cập nhật bảng lương
    $sql = "
    SET @THANG = $thang;
    SET @NAM = $nam;

    UPDATE luong_nvvp l
    JOIN nhanvien nv ON l.nhanvien_id = nv.id
    JOIN chuc_vu cv ON nv.chuc_vu_id = cv.id
    LEFT JOIN cham_cong cc 
      ON cc.nhanvien_id = nv.id 
      AND cc.thang = @THANG 
      AND cc.nam = @NAM
    LEFT JOIN (
      SELECT 
        nhanvien_id, 
        MONTH(ngay_tam_ung) AS thang, 
        YEAR(ngay_tam_ung) AS nam, 
        SUM(so_tien) AS tong_tam_ung
      FROM tam_ung
      GROUP BY nhanvien_id, thang, nam
    ) t ON t.nhanvien_id = nv.id 
       AND t.thang = @THANG 
       AND t.nam = @NAM
    SET
      l.luong_co_ban = IFNULL(cc.so_ngay_cong, 0) * IFNULL(cv.luong_ngay, 0),
      l.tien_lam_them = IFNULL(cc.so_gio_lam_them, 0) * IFNULL(cv.luong_ngay, 0) / 8,
      l.tong_tam_ung = IFNULL(t.tong_tam_ung, 0),
      l.phu_cap_com = IFNULL(l.phu_cap_com, 900000),
      l.phu_cap_xe  = IFNULL(l.phu_cap_xe, 600000),
      l.thuc_linh = (
          IFNULL(cc.so_ngay_cong, 0) * IFNULL(cv.luong_ngay, 0)
        + IFNULL(cc.so_gio_lam_them, 0) * IFNULL(cv.luong_ngay, 0) / 8
        + IFNULL(l.phu_cap_com, 900000)
        + IFNULL(l.phu_cap_xe, 600000)
        - IFNULL(t.tong_tam_ung, 0)
      ),
      l.nguoi_sua_id = 1,
      l.ngay_sua = NOW()
    WHERE l.thang = @THANG AND l.nam = @NAM;
    ";

    // Thực thi câu lệnh SQL
    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());

        echo "
        <div style='font-family: Arial; text-align:center; margin-top:50px;'>
          <h3 style='color:green;'>✅ Cập nhật bảng lương tháng $thang / $nam thành công!</h3>
        </div>";
        echo '<script>setTimeout("window.location=\'tinh_luong_nvvp.php?thang=' . $thang . '&nam=' . $nam . '&loc=\'",3000);</script>';
    } else {
        echo "
        <div style='font-family: Arial; text-align:center; margin-top:50px;'>
          <h3 style='color:red;'>❌ Lỗi khi cập nhật: " . $conn->error . "</h3>
          <a href='index.php' style='margin-top:20px; display:inline-block; text-decoration:none; background:#6c757d; color:#fff; padding:10px 20px; border-radius:5px;'>Thử lại</a>
        </div>";
        echo '<script>setTimeout("window.location=\'tinh_luong_nvvp.php?thang=' . $thang . '&nam=' . $nam . '&loc=\'",3000);</script>';
    }
  }
//Dữ liệu cho bộ lọc
  if (isset($_GET['loc'])) {
    // ====== BƯỚC 4: HIỂN THỊ DỮ LIỆU THEO BỘ LỌC ======
    // Lấy giá trị tháng và năm từ form lọc (nếu có)
    $thang_filter = isset($_GET['thang']) ? $_GET['thang'] : date('n');
    $nam_filter   = isset($_GET['nam']) ? $_GET['nam'] : date('Y');

    // Lấy dữ liệu theo bộ lọc từ bảng luong_nvvp
    $sql = "SELECT 
          lnv.id,
          lnv.ma_luong,
          nv.ten_nv AS ten_nhan_vien,
          cv.ten_chuc_vu AS chuc_vu,
          lnv.thang,
          lnv.nam,
          lnv.so_cmnd,
          lnv.luong_co_ban,
          lnv.phu_cap_com,
          lnv.phu_cap_xe,
          lnv.tong_tam_ung,
          lnv.tien_lam_them,
          lnv.thuc_linh,
          lnv.ngay_tinh_luong,
          lnv.ghi_chu,
          lnv.ngay_tao,
          lnv.ngay_sua
        FROM luong_nvvp lnv
        LEFT JOIN nhanvien nv ON lnv.nhanvien_id = nv.id
        LEFT JOIN chuc_vu cv ON nv.chuc_vu_id = cv.id
        WHERE lnv.thang = '$thang_filter' AND lnv.nam = '$nam_filter'
        ORDER BY lnv.id ASC";
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
            Tính lương nhân viên văn phòng
        </h1>
        <ol class="breadcrumb">
            <li><a href="index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> Tổng quan</a></li>
            <li><a href="tinh_luong_nvvp.php?p=staff&a=luong">Tính lương</a></li>
            <li class="active">Thêm bảng lương</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <!-- Form Khởi tạo bảng lương -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Khởi tạo bảng lương</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                    class="fa fa-remove"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php 
                          if ($row_acc['quyen'] != 1) {
                          echo "<div class='alert alert-warning alert-dismissible'>";
                          echo "<h4><i class='icon fa fa-ban'></i> Thông báo!</h4>";
                          echo "Bạn <b> không có quyền </b> thực hiện chức năng này.";
                          echo "</div>";
                          }
                        ?>
                        <?php 
                          if (isset($error) && !$showMess) {
                          echo "<div class='alert alert-danger alert-dismissible'>";
                          echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
                          echo "<h4><i class='icon fa fa-ban'></i> Lỗi!</h4>";
                          foreach ($error as $err) {
                            echo $err . "<br/>";
                          }
                          echo "</div>";
                          }
                        ?>
                        <?php 
                          if (isset($success) && $showMess) {
                          echo "<div class='alert alert-success alert-dismissible'>";
                          echo "<h4><i class='icon fa fa-check'></i> Thành công!</h4>";
                          foreach ($success as $suc) {
                            echo $suc . "<br/>";
                          }
                          echo "</div>";
                          }
                        ?>
                        <form method="POST">
                            <div class="form-group">
                                <label for="thang">Tháng</label>
                                <select class="form-control" id="thang" name="thang" required>
                                    <option value="" disabled>Chọn tháng</option>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?php echo $i; ?>"
                                        <?php echo ($i == date('n')) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nam">Năm</label>
                                <select class="form-control" id="nam" name="nam" required>
                                    <option value="" disabled>Chọn năm</option>
                                    <?php for ($i = 2020; $i <= 2030; $i++): ?>
                                    <option value="<?php echo $i; ?>"
                                        <?php echo ($i == date('Y')) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" name="khoitao">Khởi tạo bảng lương</button>
                        </form>
                    </div>
                </div>

                <!-- Bộ lọc bảng lương -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Bộ lọc bảng lương</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                    class="fa fa-remove"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
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
                                    <option value="<?php echo $i; ?>"
                                        <?php echo ($i == date('Y')) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" name="loc">Xem bảng lương</button>
                            <button type="submit" class="btn btn-primary" name="update">Cập nhật bảng lương</button>
                            <a href="tinh_luong_nvvp.php" class="btn btn-secondary" style="float: right;">
                                <i class="fa fa-refresh"></i> Làm mới
                            </a>
                        </form>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã lương</th>
                                        <th>Nhân viên</th>
                                        <th>CCCD</th>
                                        <th>Chức vụ</th>
                                        <th>Lương cơ bản</th>
                                        <th>Phụ cấp cơm</th>
                                        <th>Phụ cấp xe</th>
                                        <th>Tổng tạm ứng</th>
                                        <th>Tiền làm thêm</th>
                                        <th>Thực lĩnh</th>
                                        <th>Ghi chú</th>
                                        <th>Sửa</th>
                                        <th>Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                    $count = 1;
                    foreach ($arrShow as $arrS) {
                  ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo $arrS['ma_luong']; ?></td>
                                        <td><?php echo $arrS['ten_nhan_vien']; ?></td>
                                        <td><?php echo $arrS['so_cmnd']; ?></td>
                                        <td><?php echo $arrS['chuc_vu']; ?></td>
                                        <td><?php echo number_format($arrS['luong_co_ban']); ?></td>
                                        <td><?php echo number_format($arrS['phu_cap_com']); ?></td>
                                        <td><?php echo number_format($arrS['phu_cap_xe']); ?></td>
                                        <td><?php echo number_format($arrS['tong_tam_ung']); ?></td>
                                        <td><?php echo number_format($arrS['tien_lam_them']); ?></td>
                                        <td><?php echo number_format($arrS['thuc_linh']); ?></td>
                                        <td><?php echo $arrS['ghi_chu']; ?></td>
                                        <td>
                                            <form method="POST">
                                                <input type="hidden" value="<?php echo $arrS['id']; ?>"
                                                    name="idChamCong" />
                                                <button type="submit" class="btn bg-orange btn-flat" name="edit"><i
                                                        class="fa fa-edit"></i></button>
                                            </form>
                                        </td>
                                        <td>
                                            <button type="button" class="btn bg-maroon btn-flat" data-toggle="modal"
                                                data-target="#exampleModal"
                                                data-whatever="<?php echo $arrS['id']; ?>"><i
                                                    class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <?php
                    $count++;
                    }
                  ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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