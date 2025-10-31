## code trang  website thương mại điện tử

# Trang chủ 
- Trang giới thiệu
- Trang cửa hàng (mua bán danh sách sản phẩm)
-- tìm kiếm , lọc (giá , thương hiệu , danh sách ,...)
- Bản tin 
-- tìm kiếm , lọc (danh mục ,..) 
- Liên hệ
# Giỏ hàng
# Danh sách yêu thích 
# Thanh toán
- Phương thức giao hàng (3 phương thức - hiện đang được set up - nếu đơn hàng giá trị trên 500k sẽ được miễn phí ship)
- Người dùng thanh toán 2 phương thức COD và MOMO
- Nhập voucher 
- Xuất hoá đơn - thông báo thành công  

## Chức năng khách hành 
# Đăng nhập/Đăng ký- Đăng xuất
- Xác thực tài khoản bằng OTP (bằng Email)
- Đăng nhập qua Google
- Lấy lại Mật khẩu
# Quản lý tài khoản 
# Thông tin cá nhân 
-- Ảnh đại diện, Họ và tên , Email , SĐt , địa chỉ , 
# Đổi mật khẩu 
# lịch sử đặt hàng
- Chi tiết đơn hàng : thông tin đơn hàng (sản phẩm , số lượng ,..)
- Lịch sử giao hàng ()
- Đánh giá đơn hàng  (khi đơn hàng đã hoàn tất)
- huỷ đơn hàng (chỉ khi đơn hàng chưa được xác nhận)


## Chức năng admin
# Dashboard (Thống kê)
- Tổng thu nhập
- Số lượng khách hàng
- Số lượng sản phẩm đang bán 
- Tổng số đơn hàng
- Phân tích các chỉ số : doanh thu các tháng , Top sản phẩm, trạng thái đơn hàng,...

# Quản lý phân quyền
- Quản trị chức vụ
- Quản trị quyền
- Quản trị phân quyền
- Quản trị tài khoản nhân viên
- Quản trị tài khoản khách hàng

# Quản lý kinh doanh
- Quản trị sản phẩm 
- Quản trị thương hiệu 
- Quản trị Danh mục
- Danh sách đơn hàng (Chi tiết đơn hàng, In hoá đơn)
# Quản lý dịch vụ 
- Quản trị Mã giảm giá
-- Tạo mã đơn hàng : theo % hoặc giá tiền
-- Lựa chọn Áp dụng cho 1 số sản phẩm (Qua mã SKU) hoặc Áp dụng cho tất cả sản phẩm 
-- Tự động tạo mã giảm giá (copy từ 1 mã bất kỳ đang có)
-- Bật tắt trạng thái hoạt động của mã giảm giá
- Quản trị bản tin
-- tạo bản tin dạng lưu trữ 
-- Xuất bản tin theo thời gian đã cài đặt
-- Nột bật bài viết
- Quản trị bình luận


## Hướng dẫn cài đặt project 
# bước 1 : chuẩn bị database- code 
- git clone project về 
- Đưa dữ liệu (file ci.ecommerce.sql) vào mysql
- cài composer 
-- composer install
-- composer require google/apiclient:^2.15
- Chuyển file env thành .env 

## Thông tin tài khoản 
Tài Khoản : Admin 
email : admin@example.com
Mk : 123456

Tài Khoản : nhân viên
email : nhanvien@gmail.com
Mk : 555555
