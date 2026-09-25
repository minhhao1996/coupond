# Triển khai VibeTechCoupons

Gói này có sẵn `vendor` dành cho production, `public/build`, ảnh local và dữ liệu quốc gia DB-IP. Không cần Node.js/npm trên hosting. `node_modules`, các thư viện PHP chỉ dùng để phát triển, `.env` thật, log, session và cache của máy phát triển không được đóng gói.

## Yêu cầu

- PHP 8.4.1 trở lên và các extension theo `vendor/composer/platform_check.php`; cần PDO MySQL, GD hỗ trợ WebP và EXIF để xử lý ảnh local.
- MySQL/MariaDB; kết nối HTTPS ra Cloudinary.
- Domain có HTTPS. Đặt document root của website vào thư mục `public` bên trong gói, không trỏ vào thư mục gốc của ứng dụng.
- Cho tiến trình PHP quyền ghi vào `storage` và `bootstrap/cache`; không cần cấp quyền 777.

## Cài mới

1. Giải nén gói ra thư mục ứng dụng trên hosting.
2. Sao chép `.env.production.example` thành `.env`. Điền thông tin database và Cloudinary API key/secret. Nếu dùng domain khác, sửa cả `APP_URL` và `SEO_URL`.
3. Trong thư mục ứng dụng, chạy:

```sh
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
php artisan admin:create
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Lệnh `admin:create` hỏi email, tên và mật khẩu quản trị. Không chạy demo seeder trên production.

4. Thêm lịch chạy sau vào cron của hosting, thay đường dẫn PHP và ứng dụng cho đúng:

```cron
* * * * * cd /path/to/vibetechcoupons && /path/to/php artisan schedule:run >> /dev/null 2>&1
```

5. Mở trang chủ và `/login`, đăng nhập rồi thử tìm kiếm, xem review, copy mã và tải ảnh. Thử Analytics trong cửa sổ ẩn danh vì tài khoản admin không được tính lượt truy cập. Kiểm tra `/sitemap.xml` và `/robots.txt` dùng đúng domain.

## Giữ nội dung đang có hoặc nâng cấp website

Gói ZIP không chứa bản sao database. Nếu muốn giữ các store, coupon, bài review và tài khoản hiện tại, cần xuất database từ máy cũ và nhập vào database trên hosting trước khi chạy migrate. Giữ `APP_KEY` của hệ thống cũ khi chuyển/nâng cấp; KHÔNG chạy lại `key:generate` trên một website đã có cấu hình. Giữ `.env` và ảnh trong `storage/app/public` của hệ thống đang chạy. Sao lưu database và uploads trước khi nâng cấp.

Sau khi cập nhật code, chạy migrate và tạo lại config/route/view cache ngay trên hosting. Không chép cache đã tạo trên một máy khác. Khi thay thông tin Cloudinary, chạy lại `php artisan config:cache`.

Ảnh Cloudinary đã lưu trong database sẽ tiếp tục dùng URL cũ. API credentials không có trong ZIP và cần điền riêng. Dữ liệu DB-IP được tra tại chỗ; giấy phép và hướng dẫn cập nhật nằm trong `storage/app/geoip/README.md`.

## Tiếp tục phát triển giao diện

Source và các file khóa phiên bản vẫn được giữ. Chỉ khi cần sửa CSS/JS mới cần cài Node.js ở máy phát triển:

```sh
npm ci
npm run build
```

Sau đó đưa `public/build` mới lên hosting. Không đưa `public/hot` hoặc `node_modules` lên production. JavaScript đã build vẫn cần thiết cho tìm kiếm, copy mã, Livewire và trình soạn thảo.

## Lỗi 419 khi đăng nhập trên Hostinger

419 thường là CSRF token không khớp phiên đăng nhập. Bản ứng dụng mới gửi header không cache HTML (gồm header cho LiteSpeed) và form login dùng cùng origin. Không tắt CSRF để chữa lỗi này.

1. Truy cập lại trang `/login` bằng HTTPS, dùng nhất quán một hostname (www hoặc không www). Trong `.env`, kiểm tra:

```dotenv
APP_URL=https://vibetechcoupons.com
SEO_URL=https://vibetechcoupons.com
SESSION_DRIVER=file
SESSION_DOMAIN=null
SESSION_PATH=/
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

Nếu dùng domain tạm, thay APP_URL và SEO_URL bằng domain thực tế. `SESSION_DOMAIN=null` tạo cookie theo hostname đang truy cập; không điền URL có `https://` vào SESSION_DOMAIN. Cookie secure yêu cầu HTTPS.

2. Giữ nguyên APP_KEY. Không tạo lại key để xử lý lỗi 419. Sau khi sửa `.env` hoặc cập nhật code, chạy trong thư mục có `artisan`:

```sh
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Nếu không có terminal, dùng File Manager xóa các file cache được sinh trong `bootstrap/cache` (giữ thư mục); ứng dụng sẽ đọc cấu hình lại. Không xóa source trong `bootstrap`.

3. Bảo đảm tiến trình PHP có quyền ghi `storage/framework/sessions`, `storage/framework/views`, `storage/framework/cache/data` và `bootstrap/cache`. Kiểm tra ownership đúng tài khoản hosting; thường thư mục dùng quyền 755 hoặc 775 tùy hosting, không đặt 777. Không xóa file session đang hoạt động nếu không cần.
4. Xóa cache website/CDN trong hPanel nếu đang bật; không cache HTML có CSRF token, `/login`, `/admin/*`, `/livewire/*` hoặc các request POST. Header mới không thể sửa một trang login cũ vẫn nằm trong cache ngoài ứng dụng cho đến khi cache đó được xóa.
5. Mở cửa sổ ẩn danh, tải lại trang login và thử đăng nhập. Nếu vẫn 419, kiểm tra cookie session có được trả về trong request POST `/login` không và xem `storage/logs/laravel.log` có lỗi ghi session không. Chỉ chia sẻ thông báo lỗi, không chia sẻ cookie, CSRF token, APP_KEY hay mật khẩu.
