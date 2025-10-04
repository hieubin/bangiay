# 👟 Hieu Hai Shop - Website Bán Giày Cao Cấp

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-red.svg" alt="Laravel Version">
  <img src="https://img.shields.io/badge/PHP-8.2+-blue.svg" alt="PHP Version">
  <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
</p>

## 🏪 Giới thiệu về Hieu Hai Shop

**Hieu Hai Shop** là một website thương mại điện tử chuyên bán giày cao cấp, được xây dựng bằng Laravel với thiết kế hiện đại theo phong cách Adidas. Chúng tôi cam kết mang đến cho khách hàng những sản phẩm giày chất lượng cao với giá cả hợp lý.

### 🌟 Đặc điểm nổi bật

- **Thiết kế Adidas Style**: Giao diện hiện đại với slogan "IMPOSSIBLE IS NOTHING"
- **Sản phẩm cao cấp**: Bộ sưu tập giày từ các thương hiệu nổi tiếng
- **Trải nghiệm mua sắm**: Giao diện thân thiện, dễ sử dụng
- **Responsive Design**: Tương thích hoàn hảo trên mọi thiết bị
- **Hệ thống quản lý**: Admin panel đầy đủ tính năng

### 🎯 Sản phẩm chính

- **Giày thể thao**: Nike, Adidas, Converse
- **Giày cao cấp**: Louis Vuitton, Jimmy Choo, Christian Louboutin
- **Giày công sở**: Cole Haan, Timberland
- **Giày đặc biệt**: Các mẫu limited edition và collaboration

## 🚀 Demo Website

### 🌐 Truy cập Website
**URL**: http://localhost:8000

### 👤 Tài khoản Demo

#### 🔑 Admin Account
```
Email: admin@shoeshop.com
Password: password
```
**Quyền hạn**: Quản lý toàn bộ hệ thống, sản phẩm, đơn hàng

#### 👥 User Account
```
Email: user@shoeshop.com
Password: password
```
**Quyền hạn**: Mua sắm, quản lý giỏ hàng, theo dõi đơn hàng

### 📱 Tính năng Demo

#### 👨‍💼 Admin Features
- **Dashboard**: Tổng quan hệ thống và thống kê
- **Quản lý sản phẩm**: Thêm/sửa/xóa sản phẩm với upload ảnh
- **Quản lý danh mục**: Phân loại sản phẩm theo loại
- **Quản lý đơn hàng**: Xem và cập nhật trạng thái đơn hàng
- **Quản lý người dùng**: Xem danh sách khách hàng

#### 🛒 User Features
- **Trang chủ**: Hero slider với ảnh giày cao cấp
- **Sản phẩm**: Lưới sản phẩm với filter và sorting
- **Chi tiết sản phẩm**: Thông tin đầy đủ với ảnh zoom
- **Giỏ hàng**: Thêm/sửa/xóa sản phẩm, tính tổng tiền
- **Đặt hàng**: Tạo đơn hàng với thông tin giao hàng
- **Lịch sử**: Theo dõi các đơn hàng đã đặt

## 🛠️ Cài đặt và Chạy

### Yêu cầu hệ thống
- PHP >= 8.2
- Composer >= 2.0
- Node.js >= 16.0
- MySQL 5.7+ hoặc SQLite

### Cài đặt nhanh

```bash
# Clone repository
git clone <repository-url>
cd shoe-shop

# Cài đặt dependencies
composer install
npm install

# Cấu hình môi trường
cp .env.example .env
php artisan key:generate

# Cấu hình database (SQLite)
touch database/database.sqlite

# Chạy migrations và seeders
php artisan migrate:fresh --seed

# Tạo storage link
php artisan storage:link

# Build assets
npm run build

# Chạy server
php artisan serve
```

## 🎨 Thiết kế Adidas Style

### Hero Section
- **Slider tự động**: 3 ảnh giày cao cấp với hiệu ứng chuyển đổi mượt mà
- **Slogan nổi bật**: "IMPOSSIBLE IS NOTHING" với typography đậm
- **Overlay gradient**: Hiệu ứng mờ đẹp mắt với màu indigo/purple

### Brand Showcase
- **3 bộ sưu tập**: Luxury, Sports, Limited Edition
- **Glassmorphism cards**: Hiệu ứng kính mờ hiện đại
- **Hover effects**: Scale và transform mượt mà

### Product Grid
- **Filter buttons**: Lọc sản phẩm theo danh mục
- **Sort dropdown**: Sắp xếp theo giá, tên
- **Product cards**: Với overlay actions và hover effects
- **Quick actions**: Yêu thích, xem nhanh, thêm giỏ hàng

## 📊 Cấu trúc Database

### Tables chính
- **users**: Thông tin người dùng và admin
- **categories**: Danh mục sản phẩm
- **products**: Thông tin sản phẩm
- **product_images**: Ảnh sản phẩm
- **orders**: Đơn hàng
- **order_items**: Chi tiết đơn hàng
- **carts**: Giỏ hàng (session-based)

## 🔧 Công nghệ sử dụng

### Backend
- **Laravel 12.x**: Framework PHP
- **MySQL/SQLite**: Database
- **Eloquent ORM**: Database abstraction
- **Laravel UI**: Authentication scaffolding

### Frontend
- **Bootstrap 5**: CSS framework
- **Vite**: Build tool
- **SCSS**: CSS preprocessor
- **Font Awesome**: Icons

### Packages
- **hardevine/shoppingcart**: Shopping cart functionality
- **intervention/image**: Image processing
- **stripe/stripe-php**: Payment processing

## 📱 Responsive Design

Website được thiết kế responsive hoàn toàn:
- **Mobile**: Tối ưu cho điện thoại
- **Tablet**: Giao diện thân thiện cho máy tính bảng
- **Desktop**: Trải nghiệm đầy đủ trên máy tính

## 🎯 Roadmap

### Tính năng sắp tới
- [ ] **Payment Gateway**: Tích hợp Stripe/PayPal
- [ ] **Email Notifications**: Thông báo đơn hàng
- [ ] **Product Reviews**: Đánh giá sản phẩm
- [ ] **Wishlist**: Danh sách yêu thích
- [ ] **Coupon System**: Hệ thống mã giảm giá
- [ ] **Multi-language**: Đa ngôn ngữ
- [ ] **API**: RESTful API cho mobile app

## 🤝 Đóng góp

Chúng tôi hoan nghênh mọi đóng góp từ cộng đồng:

1. Fork dự án
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📄 License

Dự án này được phân phối dưới MIT License. Xem file `LICENSE` để biết thêm chi tiết.

## 👨‍💻 Tác giả

- **Nguyen Duc Hieu**: Full-stack Developer
- **Pham Thanh Hai**: UI/UX Designer
- **Email**: contact@shoeshop.com
- **Phone**: 0123-456-789

## 🙏 Lời cảm ơn

- Laravel Framework Team
- Bootstrap Community
- Adidas Design Inspiration
- Tất cả contributors và users

---

## ⚠️ Lưu ý quan trọng

**Đây là dự án học tập và demo**. Vui lòng không sử dụng cho mục đích thương mại mà không có sự cho phép của tác giả.

**Tài khoản demo chỉ để test tính năng**. Trong môi trường production, vui lòng thay đổi mật khẩu mặc định.

---

<p align="center">
  <strong>Hieu Hai Shop - Chuyên cung cấp giày chất lượng cao với giá cả hợp lý</strong>
</p>