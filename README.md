# QlNhanSu

Quản lý nhân sự tiền lương
admin@gmail.com  12345678 / nhanvien@gmail.com  12345678

1. Export / Import Nhân viên
2. Thêm table Phụ cấp, Đã thực hiện được CRDU 22/10/2025 ; csdl quanly_nhansu_V1_phuCap
3. Thêm table Tạm ứng, Đã thực hiện được CRDU 22/10/2025 ; CSDL quanly_nhansu_V2_TamUng_ChamCong
4. Thêm table Chấm công, Đã thực hiện được CRDU 22/10/2025; csdl quanly_nhansu_V2_TamUng_ChamCong
5. Thêm table Thưởng phạt, Đã thực hiện được CRDU 23/10/2025 ; csdl V3_thuongPhat
6. Thêm table Lương_VP, Hiển thị được bản lương (phương án mở rộng)

   1. Khởi tạo dữ liệu bảng chấm công cho tháng năm hiện tại, với dữ liệu lấy từ bảng Nhân viên điều kiện nhân viên có trạng thái đang làm việc
   2. --> cần Xử lý bảng lương
7. Khởi tạo bảng chấm công cho tháng năm hiện tại, với dữ liệu lấy từ bảng Nhân viên điều kiện nhân viên có trạng thái đang làm việc.

   1. Có chức năng khởi tạo bảng chấm công theo tháng, năm --> ok
   2. Tiến hành chấm công lọc theo tháng, năm -->ok
   3. --> mở rộng (Sửa code bảng dữ liệu chấm công theo mẫu, chức năng trên form có thể nhập được số ngày công, số giờ làm thêm trực tiếp trên form, có chức năng lưu tất cả dữ liệu chấm công đã nhập)
   4. Xuất csdl_V4_chamcongLuong_vpLuong_nvvp, Update git: Thực hiện quy trình chấm công chamcong_list.php, CRUD được Luong-vp.php (dùng cho phương án mở rộng).
8. Tạo Bảng lương Nhân viên văn phòng theo biểu mẫu cung cấp. Thực hiện Qui trình  tính lương cho Nhân viên văn phòng. Chấm công --> tính lương --> xuất bảng lương (excel)

   1. Khởi tạo bảng lương có các điều kiện như sau: Không trùng tháng, năm; Chỉ tạo nhân viên đang làm việc, chỉ tạo nhân viên chính thức
   2. Cải thiệt chức năng cập nhật lương khi sửa bảng chấm công. Khi bảng tạm ứng và chấm công thay đổi thì click vào update để cập nhật lại bảng lương
   3. --> Export bản lương
9. Hỏi / đáp

   1. Cách tính lương cơ bản
   2. Quy trình tính lương
   3. Lương cơ bản cho chức vụ? ngày lương cơ bản hay tháng lương cơ bản

==> Việc cần làm tiếp theo

Cần tạo bộ lọc Xử lý chấm công

Nghiên cứu UX/UI chấm công, tính lương VP

Xem lại CSDL để tính lương nhân viên văn phòng

Thiết kế UI Tính lương nhân viên văn phòng
