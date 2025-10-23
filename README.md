# 👟 Premium Shoe Shop - Laravel E-commerce Website

Website bán giày cao cấp được xây dựng bằng Laravel với thiết kế hiện đại theo phong cách Adidas, tích hợp 10 tấm ảnh giày cao cấp và giao diện responsive đẹp mắt.

## 📋 Mục lục

- [Tính năng](#-tính-năng)
- [Yêu cầu hệ thống](#-yêu-cầu-hệ-thống)
- [Cài đặt](#-cài-đặt)
- [Cấu hình](#-cấu-hình)
- [Chạy ứng dụng](#-chạy-ứng-dụng)
- [Cấu trúc dự án](#-cấu-trúc-dự-án)
- [Hướng dẫn sử dụng](#-hướng-dẫn-sử-dụng)
- [Troubleshooting](#-troubleshooting)

## ✨ Tính năng

### 👥 Người dùng
- **Đăng ký/Đăng nhập**: Hệ thống xác thực người dùng
- **Hero Slider**: Trình chiếu ảnh giày cao cấp với hiệu ứng đẹp mắt
- **Brand Showcase**: Trưng bày các bộ sưu tập đặc biệt
- **Product Grid**: Lưới sản phẩm với filter và sorting
- **Product Cards**: Thẻ sản phẩm với hover effects và quick actions
- **Giỏ hàng**: Thêm/sửa/xóa sản phẩm trong giỏ hàng
- **Đặt hàng**: Tạo đơn hàng và theo dõi lịch sử mua hàng
- **Giao diện responsive**: Tương thích với mọi thiết bị

### 👨‍💼 Admin
- **Dashboard**: Tổng quan hệ thống
- **Quản lý sản phẩm**: CRUD sản phẩm với upload ảnh
- **Quản lý danh mục**: Phân loại sản phẩm
- **Quản lý đơn hàng**: Xem và cập nhật trạng thái đơn hàng

### 🎨 Giao diện Adidas Style
- **Hero Section**: Slider ảnh nền với slogan "IMPOSSIBLE IS NOTHING"
- **Glassmorphism**: Hiệu ứng kính mờ hiện đại
- **Gradient Design**: Màu sắc gradient indigo/purple
- **Modern Typography**: Font chữ đậm, uppercase
- **Animations**: Hiệu ứng mượt mà và chuyên nghiệp
- **Responsive**: Tương thích mobile và desktop
- **Dark Mode**: Hỗ trợ chế độ sáng/tối
- **Interactive Elements**: Hover effects, transitions

## 🔧 Yêu cầu hệ thống

- **PHP**: >= 8.2
- **Composer**: >= 2.0
- **Node.js**: >= 16.0
- **NPM**: >= 8.0
- **Database**: MySQL 5.7+ hoặc SQLite
- **Web Server**: Apache hoặc Nginx

## 🚀 Cài đặt

### Bước 1: Clone dự án

```bash
git clone <repository-url>
cd shoe-shop
```

### Bước 2: Cài đặt dependencies

```bash
# Cài đặt PHP dependencies
composer install

# Cài đặt Node.js dependencies
npm install
```

### Bước 3: Cấu hình môi trường

```bash
# Copy file cấu hình
cp .env.example .env

# Tạo application key
php artisan key:generate
```

### Bước 4: Cấu hình database

Mở file `.env` và cấu hình database:

```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=shoe_shop_db
# DB_USERNAME=root
# DB_PASSWORD=
```

Hoặc sử dụng MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shoe_shop_db
DB_USERNAME=root
DB_PASSWORD=
```

### Bước 5: Chạy migrations và seeders

```bash
# Tạo database (nếu dùng MySQL)
mysql -u root -p -e "CREATE DATABASE shoe_shop_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Chạy migrations và seeders
php artisan migrate:fresh --seed
```

### Bước 6: Tạo storage link

```bash
php artisan storage:link
```

### Bước 7: Build assets

```bash
# Build CSS và JS
npm run build

# Hoặc chạy development server
npm run dev
```

## ⚙️ Cấu hình

### Cấu hình Mail (tùy chọn)

Trong file `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Cấu hình File Storage

```env
FILESYSTEM_DISK=public
```

## 🏃‍♂️ Chạy ứng dụng

### Development Mode

```bash
# Terminal 1: Chạy Laravel server
php artisan serve

# Terminal 2: Chạy Vite dev server (nếu cần)
npm run dev
```

Truy cập: http://localhost:8000

### Production Mode

```bash
# Build assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📁 Cấu trúc dự án

```
shoe-shop/
├── app/
│   ├── Http/Controllers/     # Controllers
│   ├── Models/              # Eloquent Models
│   └── Providers/           # Service Providers
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   ├── css/                # Modern CSS với Adidas style
│   ├── js/                 # JavaScript files
│   └── views/              # Blade templates
├── public/
│   ├── images/
│   │   └── shoes/          # 10 tấm ảnh giày cao cấp
│   └── build/              # Compiled assets
├── routes/
│   └── web.php             # Web routes
├── storage/
│   └── app/public/         # Public storage
├── .env                    # Environment config
├── composer.json           # PHP dependencies
├── package.json            # Node.js dependencies
└── README.md              # Documentation
```

### 🖼️ Ảnh tích hợp
- `doi-giay-dat-nhat.jpg` - Đôi giày đắt nhất
- `giay-dat-nhat-the-gioi-2023.jpg` - Giày đắt nhất thế giới 2023
- `giay-dat-nhat-the-gioi.jpg` - Giày đắt nhất thế giới
- `giay-dat-nhat.jpg` - Giày đắt nhất
- `Louis-Vuitton-Kanye-West-Jasper.jpg` - Louis Vuitton x Kanye West
- `nhung-mau-giay-dat-nhat-the-gioi.jpg` - Những mẫu giày đắt nhất thế giới
- `nhung-mau-giay-dat-nhat.jpg` - Những mẫu giày đắt nhất
- `top-10-doi-giay-bong-da-adidas-dat-nhat-the-gioi-3.jpg` - Top 10 giày bóng đá Adidas
- `top-nhung-doi-giay-dat-nhat-the-gioi.jpg` - Top những đôi giày đắt nhất thế giới
- `images.jpg` - Ảnh tổng hợp

## 📖 Hướng dẫn sử dụng

### Tài khoản mặc định

Sau khi chạy seeders, bạn có thể đăng nhập với:

**Admin:**
- Email: `admin@shoeshop.com`
- Password: `password`

**User thường:**
- Email: `user@shoeshop.com`
- Password: `password`

### Quy trình mua hàng

1. **Truy cập trang chủ** với hero slider và brand showcase
2. **Đăng ký/Đăng nhập** tài khoản
3. **Duyệt sản phẩm** với filter và sorting
4. **Xem chi tiết** sản phẩm với hover effects
5. **Thêm sản phẩm** vào giỏ hàng
6. **Kiểm tra giỏ hàng** và cập nhật số lượng
7. **Tạo đơn hàng** và xác nhận
8. **Theo dõi đơn hàng** trong phần "Đơn hàng"

### Quản lý Admin

1. **Đăng nhập** với tài khoản admin
2. **Truy cập Dashboard** để xem tổng quan
3. **Quản lý sản phẩm**: Thêm/sửa/xóa sản phẩm
4. **Quản lý danh mục**: Phân loại sản phẩm
5. **Xem đơn hàng**: Theo dõi và cập nhật trạng thái

## 🐛 Troubleshooting

### Lỗi thường gặp

**1. Lỗi "Class not found"**
```bash
composer dump-autoload
```

**2. Lỗi "Permission denied"**
```bash
# Linux/Mac
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows (chạy với quyền admin)
icacls storage /grant Everyone:F /T
icacls bootstrap/cache /grant Everyone:F /T
```

**3. Lỗi "Storage link not found"**
```bash
php artisan storage:link
```

**4. Lỗi "Migration failed"**
```bash
php artisan migrate:fresh --seed
```

**5. Lỗi "Assets not loading"**
```bash
npm run build
# hoặc
npm run dev
```

### Kiểm tra cấu hình

```bash
# Kiểm tra cấu hình Laravel
php artisan config:show

# Kiểm tra routes
php artisan route:list

# Kiểm tra migrations
php artisan migrate:status
```

### Debug mode

Trong file `.env`:
```env
APP_DEBUG=true
APP_ENV=local
```

## 🎨 Thiết kế Adidas Style

### Hero Section
- **Slider ảnh nền**: 3 ảnh giày cao cấp tự động chuyển
- **Slogan**: "IMPOSSIBLE IS NOTHING"
- **Overlay gradient**: Hiệu ứng mờ đẹp mắt
- **Navigation dots**: Điều khiển slider

### Brand Showcase
- **3 bộ sưu tập**: Luxury, Sports, Limited Edition
- **Glassmorphism cards**: Hiệu ứng kính mờ
- **Hover effects**: Scale và transform

### Product Grid
- **Filter buttons**: Lọc theo danh mục
- **Sort dropdown**: Sắp xếp theo giá
- **Product cards**: Với overlay actions
- **Quick actions**: Yêu thích, xem nhanh, so sánh

### Modern Features
- **Glassmorphism**: Backdrop-filter effects
- **Gradient design**: Indigo/purple color scheme
- **Typography**: Bold, uppercase fonts
- **Animations**: Smooth transitions
- **Responsive**: Mobile-first design

## 📚 Tài liệu tham khảo

- [Laravel Documentation](https://laravel.com/docs)
- [Bootstrap Documentation](https://getbootstrap.com/docs)
- [Vite Documentation](https://vitejs.dev/guide/)
- [Adidas Design System](https://www.adidas.com.vn/vi/giay)

## 🤝 Đóng góp

1. Fork dự án
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📄 License

Dự án này được phân phối dưới MIT License. Xem file `LICENSE` để biết thêm chi tiết.

## 👨‍💻 Tác giả

- **Tên**: [Nguyen Duc Hieu, Pham Thanh Hai]
- **Email**: [2310677@st.phenikaa-uni.edu.vn, 23010614@st.phenikaa-uni.edu.vn]
- **GitHub**: [@hieubin](https://github.com/hieubin/bangiay.git)

## 🙏 Lời cảm ơn

- Laravel Framework
- Bootstrap Team
- Adidas Design Inspiration
- Tất cả contributors và community

---

## 🚀 Demo Website

**Truy cập**: http://localhost:8000

**Tài khoản demo**:
- Admin: `admin@shoeshop.com` / `password`
- User: `user@shoeshop.com` / `password`

**Lưu ý**: Đây là dự án học tập với thiết kế Adidas style. Vui lòng không sử dụng cho mục đích thương mại mà không có sự cho phép.
