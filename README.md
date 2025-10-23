<<<<<<< HEAD
# README

Mini e-commerce (Laravel) — **Controllers** + **Giải thích & Tác dụng của code**

---

## Tổng quan

Repo này gồm 6 controller chính tạo thành luồng **duyệt sản phẩm → giỏ hàng → thanh toán → lịch sử đơn → quản trị**.
Bên dưới là **tác dụng** của từng file và **toàn bộ code** tương ứng (để bạn tiện đọc/đối chiếu).

---

## 1) `Controller.php` — Lớp cơ sở

**Tác dụng:** Là lớp cha cho toàn bộ controller khác. Kế thừa sẵn năng lực **uỷ quyền** (authorization) và **kiểm tra dữ liệu** (validation) của Laravel.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
```

Nguồn: 

---

## 2) `HomeController.php` — Trang chủ (người dùng đã đăng nhập)

**Tác dụng:** Bảo vệ bằng `auth`, lấy danh sách **sản phẩm đang active**, eager-load quan hệ `category`, `images`, sắp xếp mới nhất, **phân trang 12 sản phẩm**, và trả về view `home`.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $products = \App\Models\Product::with('category', 'images')
            ->where('is_active', true)
            ->latest()
            ->paginate(12);
        
        return view('home', compact('products'));
    }
}
```

Nguồn: 

---

## 3) `AdminController.php` — Dashboard quản trị

**Tác dụng:** Tính **tổng số sản phẩm**, **đơn hàng**, **người dùng** và **doanh thu** từ các đơn `completed`; trả về view `admin.dashboard`.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalOrders   = Order::count();
        $totalUsers    = User::count();
        $revenue       = Order::where('status', 'completed')->sum('total_price');

        return view('admin.dashboard', compact('totalProducts', 'totalOrders', 'totalUsers', 'revenue'));
    }
}
```

Nguồn: 

---

## 4) `CartController.php` — Giỏ hàng

**Tác dụng:** Hiển thị, thêm, cập nhật số lượng, xoá từng dòng, và xoá toàn bộ giỏ hàng. Sử dụng facade `Cart`.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Cart;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::content();
        $total = 0;
        foreach($cartItems as $item) {
            $total += $item->price * $item->qty;
        }
        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        Cart::add([
            'id' => $product->id,
            'name' => $product->name,
            'qty' => $request->quantity ?? 1,
            'price' => $product->price,
            'options' => [
                'image' => $product->images->first()->image_path ?? $product->image ?? null
            ]
        ]);

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng!');
    }

    public function update(Request $request, $rowId)
    {
        Cart::update($rowId, $request->quantity);
        return redirect()->route('cart.index')->with('success', 'Cập nhật giỏ hàng thành công!');
    }

    public function remove($rowId)
    {
        Cart::remove($rowId);
        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ!');
    }

    public function clear()
    {
        Cart::destroy();
        return redirect()->route('cart.index')->with('success', 'Đã xóa toàn bộ giỏ hàng!');
    }
}
```

Nguồn: 

---

## 5) `ProductController.php` — Quản trị sản phẩm (CRUD + ảnh)

**Tác dụng:**

* **Index**: phân trang + eager-load `category`.
* **Create/Edit**: nạp danh sách category.
* **Store/Update**: validate đầu vào; tạo/cập nhật `slug`, `sku`, tồn kho; **upload ảnh** vào disk `public/products` và gắn với quan hệ `images()`.
* **Destroy**: xoá sản phẩm.

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->description = $request->description;
        $product->price = $request->price;
        $product->sale_price = $request->sale_price;
        $product->sku = Str::upper(Str::random(8));
        $product->stock_quantity = $request->stock_quantity ?? 0;
        $product->category_id = $request->category_id;
        $product->save();

        // Upload ảnh chính
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->images()->create(['image_path' => $path]);
        }

        return redirect()->route('products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'stock_quantity' => $request->stock_quantity,
            'category_id' => $request->category_id
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->images()->create(['image_path' => $path]);
        }

        return redirect()->route('products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Xóa sản phẩm thành công!');
    }
}
```

Nguồn: 

---

## 6) `OrderController.php` — Đặt hàng & lịch sử đơn

**Tác dụng:**

* **Create**: lấy giỏ; nếu trống → quay lại giỏ; nếu có → tính tổng và hiển thị form checkout.
* **Store**: validate địa chỉ/điện thoại; tạo `Order` và các `OrderItem` từ giỏ; **xoá giỏ**; chuyển hướng về danh sách đơn.
* **Index**: liệt kê các đơn của **chính user đang đăng nhập**.
* **Show**: kiểm tra quyền `authorize('view', $order)` trước khi hiển thị chi tiết.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Cart;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Hiển thị form checkout
    public function create()
    {
        $cartItems = Cart::content();
        if ($cartItems->count() == 0) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }
        $total = 0;
        foreach($cartItems as $item) {
            $total += $item->price * $item->qty;
        }
        return view('orders.create', compact('cartItems', 'total'));
    }

    // Lưu đơn hàng
    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required',
            'phone' => 'required',
        ]);

        // Tính tổng tiền
        $total = 0;
        foreach(Cart::content() as $item) {
            $total += $item->price * $item->qty;
        }

        // Tạo đơn hàng
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_price' => $total,
            'status' => 'pending',
            'shipping_address' => $request->shipping_address,
            'phone' => $request->phone,
        ]);

        // Thêm chi tiết đơn hàng
        foreach (Cart::content() as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->id,
                'quantity' => $item->qty,
                'price' => $item->price,
            ]);
        }

        // Xóa giỏ hàng
        Cart::destroy();

        return redirect()->route('orders.index')->with('success', 'Đặt hàng thành công!');
    }

    // Hiển thị danh sách đơn hàng của user
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->get();
        return view('orders.index', compact('orders'));
    }

    // Chi tiết đơn hàng
    public function show(Order $order)
    {
        $this->authorize('view', $order); // chỉ cho phép user xem đơn của chính mình
        return view('orders.show', compact('order'));
    }
}
```

Nguồn: 

---

## Sơ đồ luồng (tóm tắt nhanh)

1. **Home** → duyệt sản phẩm (đã đăng nhập).  
2. **Cart** → thêm/cập nhật/xoá giỏ.  
3. **Checkout** → `OrderController@create` → `store` (tạo `Order` + `OrderItem`, xoá giỏ).  
4. **Lịch sử/Chi tiết đơn** → `index`/`show` (giới hạn theo user + authorize). 
5. **Admin** → Dashboard + CRUD Sản phẩm.    

---

## Ghi chú & gợi ý mở rộng

* **Middleware bảo vệ**: Home đang yêu cầu `auth`; cân nhắc áp dụng guard/middleware riêng cho `AdminController`. 
* **Service hoá** phần tính tổng cart để tái sử dụng (hiện có ở `CartController@index` và `OrderController@store`).  
* **Quản lý ảnh**: khi xoá sản phẩm, cân nhắc xoá file vật lý (tránh ảnh mồ côi). 
* **Phân quyền**: `authorize('view', $order)` đã có; thêm policy/role cho thao tác quản trị. 

---

> Cần mình đóng gói README này thành file `README.md` để tải về, hay muốn bổ sung phần **cài đặt thư viện Cart**, **route mẫu**, hoặc **migrations/model** để chạy end-to-end?
# README — Auth Controllers (Laravel)

Bộ controller **xác thực người dùng** bao gồm: đăng nhập, đăng ký, xác minh email, quên mật khẩu, đặt lại mật khẩu, và xác nhận mật khẩu. Bên dưới có **tác dụng** của từng file và **toàn bộ code** để bạn dễ tra cứu/đối chiếu.

---

## 1) `Auth/LoginController.php` — Đăng nhập

**Tác dụng:** Xử lý đăng nhập người dùng bằng trait `AuthenticatesUsers`, cấu hình **điểm đến sau đăng nhập** và middleware để chỉ cho khách (`guest`) truy cập trang login; cho phép đã đăng nhập gọi `logout`.

* Dùng trait `AuthenticatesUsers` cung cấp sẵn form/login, attempt, throttle, logout… .
* Sau đăng nhập chuyển tới `/home` qua thuộc tính `$redirectTo` .
* Áp dụng middleware: `guest` (trừ `logout`), và `auth` cho `logout` .

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
```

Nguồn: 

---

## 2) `Auth/RegisterController.php` — Đăng ký

**Tác dụng:** Xử lý đăng ký tài khoản mới bằng trait `RegistersUsers`; xác thực dữ liệu đầu vào, tạo người dùng (hash mật khẩu) và điều hướng sau đăng ký.

* Trait `RegistersUsers` gói sẵn các bước show form → validate → create → login → redirect .
* `$redirectTo = '/home'` sau khi đăng ký thành công .
* Chỉ cho **khách** truy cập (middleware `guest`) .
* `validator()` định nghĩa rule: tên bắt buộc, email hợp lệ & unique, mật khẩu tối thiểu 8 ký tự và confirm .
* `create()` tạo user và **hash** mật khẩu bằng `Hash::make` .

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
```

Nguồn: 

---

## 3) `Auth/VerificationController.php` — Xác minh email

**Tác dụng:** Quản lý **xác minh email** sau đăng ký bằng trait `VerifiesEmails`; điều hướng sau xác minh; middleware bảo vệ các bước verify/resend.

* Trait `VerifiesEmails` xử lý gửi/verify token xác minh email .
* `$redirectTo = '/home'` sau verify thành công .
* Middleware: `auth`, chữ ký URL (`signed`) cho route `verify`, và giới hạn tần suất (`throttle:6,1`) cho `verify`, `resend` .

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
}
```

Nguồn: 

---

## 4) `Auth/ForgotPasswordController.php` — Gửi email đặt lại mật khẩu

**Tác dụng:** Nhận email người dùng và **gửi liên kết** đặt lại mật khẩu bằng trait `SendsPasswordResetEmails`.

* Trait `SendsPasswordResetEmails` lo validate email, tạo token và gửi notification mail reset .

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
}
```

Nguồn: 

---

## 5) `Auth/ResetPasswordController.php` — Đặt lại mật khẩu (qua link)

**Tác dụng:** Xử lý form **đặt lại mật khẩu** khi người dùng bấm vào link trong email (có token). Dùng trait `ResetsPasswords`, cấu hình **điểm đến sau khi reset**.

* Trait `ResetsPasswords` kiểm tra token/email, validate password mới, cập nhật & đăng nhập user, rồi redirect .
* `$redirectTo = '/home'` sau khi reset thành công .

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';
}
```

Nguồn: 

---

## 6) `Auth/ConfirmPasswordController.php` — Xác nhận lại mật khẩu

**Tác dụng:** Yêu cầu người dùng **xác nhận lại mật khẩu** (re-auth) trước khi thực hiện hành động nhạy cảm; dùng trait `ConfirmsPasswords`; yêu cầu đăng nhập và cấu hình redirect khi URL intended thất bại.

* Trait `ConfirmsPasswords` cung cấp form xác nhận & logic xác nhận lại credentials .
* `$redirectTo = '/'` nếu intended URL fail .
* Middleware `auth` đảm bảo chỉ người đã đăng nhập mới vào bước xác nhận lại .

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ConfirmsPasswords;

class ConfirmPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Confirm Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password confirmations and
    | uses a simple trait to include the behavior. You're free to explore
    | this trait and override any functions that require customization.
    |
    */

    use ConfirmsPasswords;

    /**
     * Where to redirect users when the intended url fails.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
}
```

Nguồn: 

---

## Luồng xác thực tổng quát

1. **Đăng ký** → `RegisterController` (validate, tạo user, login) → **Xác minh email** có thể được bắt buộc qua middleware `verified` → chuyển tới `/home`.  
2. **Đăng nhập** → `LoginController` (throttle/remember/redirect) → `/home`. 
3. **Quên mật khẩu** → `ForgotPasswordController` gửi email chứa link reset. 
4. **Đặt lại mật khẩu** → `ResetPasswordController` (xác thực token, cập nhật mật khẩu, login, redirect).  
5. **Xác nhận lại mật khẩu** (re-auth) trước thao tác nhạy cảm → `ConfirmPasswordController`. 
6. **Xác minh email** → `VerificationController` (verify/resend, bảo vệ bằng signed + throttle). 

---

## Gợi ý cấu hình thêm

* **Routes**: Dùng `Auth::routes(['verify' => true]);` để bật xác minh email và các route cho reset/confirm (tuỳ phiên bản Laravel).
* **Middleware `verified`**: Thêm vào các route cần người dùng đã xác minh email.
* **Throttle** đăng nhập/quên mật khẩu để giảm brute force (đã có sẵn trong traits, có thể tinh chỉnh).
* **Localization**: Tùy biến thông điệp lỗi/validation theo ngôn ngữ của bạn.

> Bạn muốn mình đóng gói phần này thành file `README-AUTH.md` để tải về không? Hoặc mình có thể thêm mẫu **routes/web.php** và **view** cơ bản cho các form đăng nhập/đăng ký/đặt lại mật khẩu.
# README — `AdminMiddleware` (Laravel)

Middleware kiểm tra quyền **Admin** trước khi cho phép truy cập các tuyến (routes) quản trị.

---

## 🧩 Tác dụng

* **Buộc đăng nhập:** nếu **chưa đăng nhập**, chuyển về trang đăng nhập. 
* **Ràng buộc quyền Admin:** nếu đã đăng nhập **nhưng không phải admin** (thuộc tính `is_admin` trên model User = `false`), trả về **403 Forbidden** với thông báo tiếng Việt. 
* **Cho phép đi tiếp**: khi đạt cả hai điều kiện (đăng nhập + admin), request tiếp tục đi vào controller đích. 

---

## 📄 Mã nguồn đầy đủ

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->is_admin) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
```

Nguồn: 

---

## 🔧 Cách dùng (gợi ý tích hợp)

> Phần này là hướng dẫn triển khai thông dụng trong Laravel (tham khảo).

1. **Đăng ký middleware** trong `app/Http/Kernel.php` (mảng `$routeMiddleware`):

```php
'admin' => \App\Http\Middleware\AdminMiddleware::class,
```

2. **Áp dụng cho routes** quản trị, ví dụ trong `routes/web.php`:

```php
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'index'])
        ->name('admin.dashboard');

    // Các route CRUD sản phẩm, user, đơn hàng...
});
```

3. **Yêu cầu dữ liệu người dùng có cột `is_admin`** (kiểu boolean) trên bảng `users`.

   * Nếu bạn dùng Policy hoặc vai trò phức tạp hơn, có thể thay `is_admin` bằng `role`/`permissions`.

---

## ✅ Tóm tắt

* `AdminMiddleware` bảo vệ khu vực admin bằng **2 lớp kiểm tra**: đăng nhập → quyền admin.
* Không phải admin → **403**; chưa đăng nhập → **redirect login**.
* Dùng kèm `auth` middleware để tối ưu trải nghiệm & bảo mật.
# README — Eloquent Models (Laravel)

**Mô tả tác dụng & kèm toàn bộ mã nguồn**

Bộ **model** dưới đây định nghĩa cấu trúc dữ liệu, quan hệ và thuộc tính có thể gán hàng loạt cho ứng dụng e-commerce của bạn.

---

## 1) `app/Models/User.php` — Người dùng

**Tác dụng:**

* Kế thừa `Authenticatable`, dùng trait `HasFactory`, `Notifiable` cho factory & notification. Thuộc tính có thể gán: `name`, `email`, `password`, `is_admin`  .
* Ẩn `password`, `remember_token` khi serialize .
* Kiểu dữ liệu tự ép (casts): `email_verified_at` datetime, `password` hashed, `is_admin` boolean .

```php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }
}
```

Nguồn: 

---

## 2) `app/Models/Product.php` — Sản phẩm

**Tác dụng:**

* Khai báo các thuộc tính gán hàng loạt: tên, slug, mô tả, giá, giá giảm, SKU, tồn kho, cờ hiển thị, danh mục, ảnh chính .
* Quan hệ:

  * `belongsTo(Category)` (mỗi sản phẩm thuộc một danh mục) .
  * `hasMany(ProductImage)` (nhiều ảnh) .
  * `hasMany(OrderItem)` (xuất hiện trong nhiều dòng đơn hàng) .

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'sale_price',
        'sku', 'stock_quantity', 'is_active', 'is_featured', 'category_id', 'image'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
```

Nguồn: 

---

## 3) `app/Models/Category.php` — Danh mục

**Tác dụng:**

* Cho phép gán `name`, `slug`, `description`, `is_active` .
* Quan hệ `hasMany(Product)` để truy ra toàn bộ sản phẩm thuộc danh mục đó .

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
```

Nguồn: 

---

## 4) `app/Models/ProductImage.php` — Ảnh sản phẩm

**Tác dụng:**

* Thuộc tính gán hàng loạt: `image_path`, `product_id`, `is_primary` .
* Quan hệ `belongsTo(Product)` (ảnh thuộc một sản phẩm) .

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = ['image_path', 'product_id', 'is_primary'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
```

Nguồn: 

---

## 5) `app/Models/Order.php` — Đơn hàng

**Tác dụng:**

* Thuộc tính gán: `user_id`, `total_price`, `status`, `shipping_address`, `phone` .
* Quan hệ:

  * `belongsTo(User)` — đơn hàng thuộc về một người dùng .
  * `hasMany(OrderItem)` — tập các dòng sản phẩm trong đơn .

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_price', 'status', 'shipping_address', 'phone'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
```

Nguồn: 

---

## 6) `app/Models/OrderItem.php` — Dòng sản phẩm trong đơn

**Tác dụng:**

* Thuộc tính gán: `order_id`, `product_id`, `quantity`, `price` .
* Quan hệ:

  * `belongsTo(Order)` — dòng này thuộc đơn hàng nào .
  * `belongsTo(Product)` — dòng này tham chiếu sản phẩm nào .

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'quantity', 'price'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
```

Nguồn: 

---

## 7) `app/Models/Cart.php` — Bảng giỏ hàng (lưu DB)

> Lưu ý: **khác** với facade `Cart` dùng trong controller (thư viện giỏ hàng phiên làm việc). File này là **model Eloquent** để lưu trạng thái giỏ trong DB.

**Tác dụng:**

* Thuộc tính gán: `user_id`, `product_id`, `quantity` .
* Quan hệ:

  * `belongsTo(User)` — giỏ gắn với người dùng .
  * `belongsTo(Product)` — mỗi dòng tham chiếu sản phẩm cụ thể .

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id', 'quantity'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
```

Nguồn: 

---

## 8) `app/Models/Color.php` — Màu sắc (thuộc tính phụ)

**Tác dụng:** Lưu danh mục màu sắc; cho phép gán `name`, `hex_code` (mã màu) .
*(Hiện chưa khai báo quan hệ; có thể mở rộng quan hệ N-N với Product nếu cần.)*

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'hex_code'];
}
```

Nguồn: 

---

## 9) `app/Models/Size.php` — Kích thước (thuộc tính phụ)

**Tác dụng:** Lưu danh mục size; cho phép gán `name` .
*(Tương tự Color, có thể mở rộng quan hệ với Product.)*

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
}
```

Nguồn: 

---

## Gợi ý mở rộng mô hình dữ liệu

* **Biến thể sản phẩm (SKU con):** tạo bảng `product_variants` liên kết `product_id` + `color_id` + `size_id` + `stock_quantity` để quản lý tồn kho theo thuộc tính.
* **Cart bền vững:** nếu muốn đồng bộ giỏ giữa các thiết bị, sử dụng model `Cart` hiện có với session key để gắn cả **guest**; đồng bộ sau khi login.
* **Chỉ mục DB:** thêm index cho các khóa ngoại (`user_id`, `product_id`, `order_id`, `category_id`) để tăng tốc truy vấn.
* **Ràng buộc toàn vẹn:** sử dụng foreign keys và cascade phù hợp (`onDelete('cascade')` cho `OrderItem` khi xóa `Order`, v.v.).

---

Bạn muốn mình đóng gói README này thành file `README-MODELS.md` để tải về, hoặc vẽ **ERD**/sơ đồ quan hệ từ các model trên không?
# README — `AppServiceProvider` (Laravel)

Service provider gốc của ứng dụng. Dùng để **đăng ký (register)** các service/binding vào IoC container và **khởi động (boot)** các hành vi toàn cục khi app chạy.

---

## 🧩 Tác dụng chính trong mã nguồn

* **`register()`**: nơi khai báo binding, singletons, hoặc đăng ký service provider khác ở runtime. (Hiện để trống) 
* **`boot()`**: nơi cấu hình/tùy biến hành vi ở thời điểm app khởi động (VD: view composers, schema, policies…). (Hiện để trống) 

> Tóm lại: File này **chưa có tùy biến**—đang giữ khung chuẩn của Laravel và sẵn sàng cho bạn thêm logic ứng dụng toàn cục khi cần.

---

## 📄 Mã nguồn đầy đủ

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
```

Nguồn: 

---

## 🔧 Gợi ý sử dụng thực tế (có thể thêm vào ngay)

> Phần dưới đây là **mẫu** phổ biến bạn có thể đưa vào `boot()`/`register()` khi dự án cần:

1. **Cấu hình phân trang dùng Bootstrap** (nếu frontend là Bootstrap):

```php
use Illuminate\Pagination\Paginator;

public function boot(): void
{
    Paginator::useBootstrapFive(); // hoặc useBootstrapFour()
}
```

2. **Giới hạn độ dài mặc định cho chuỗi trong Schema** (một số DB cũ cần):

```php
use Illuminate\Support\Facades\Schema;

public function boot(): void
{
    Schema::defaultStringLength(191);
}
```

3. **View Composer**: chia sẻ dữ liệu dùng chung cho mọi view (VD: danh mục sản phẩm, thông báo…):

```php
use Illuminate\Support\Facades\View;
use App\Models\Category;

public function boot(): void
{
    View::composer('*', function ($view) {
        $view->with('sharedCategories', Category::query()->where('is_active', true)->get());
    });
}
```

4. **Binding vào container** (service/repo pattern):

```php
use App\Services\CartService;
use App\Services\Contracts\CartServiceContract;

public function register(): void
{
    $this->app->bind(CartServiceContract::class, CartService::class);
}
```

---

## ✅ Kết luận

`AppServiceProvider` là **điểm trung tâm cấu hình** của ứng dụng Laravel. Dù hiện tại rỗng, bạn có thể dùng nó để:

* Đăng ký service/binding (DI) trong `register()`.
* Cấu hình hành vi toàn cục, view composers, pagination, schema… trong `boot()`.

Bạn muốn mình thêm một phiên bản `AppServiceProvider` đã cài sẵn **Paginator Bootstrap + View Composer danh mục** phù hợp với các model của bạn không?
# README — Cấu hình `config/*.php` (Laravel)

Tài liệu này mô tả **tác dụng** của từng file cấu hình bạn vừa cung cấp, kèm **đoạn code thực tế** để đối chiếu nhanh.

---

## 1) `config/app.php` — Thông số ứng dụng

**Tác dụng chính:**

* Tên app, môi trường, chế độ debug, URL gốc. 
* Múi giờ, locale, bản địa hoá Faker. 
* Khoá mã hoá (`APP_KEY`), cipher, previous keys. 
* Cấu hình **maintenance mode** (file / cache). 

```php
<?php

return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'UTC',
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(explode(',', (string) env('APP_PREVIOUS_KEYS', ''))),
    ],
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store'  => env('APP_MAINTENANCE_STORE', 'database'),
    ],
];
```

Nguồn:     

---

## 2) `config/auth.php` — Xác thực

**Tác dụng chính:**

* Guard mặc định `web`, broker reset password `users`. 
* Cấu hình guard `web` sử dụng `session` + provider `users`. 
* Provider `users` dùng Eloquent `App\Models\User`. 
* Thiết lập reset password: bảng token, hạn 60 phút, throttle 60s. 
* Thời gian timeout xác nhận lại mật khẩu (mặc định 10800s). 

```php
<?php

return [
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],
    'guards' => [
        'web' => ['driver' => 'session','provider' => 'users'],
    ],
    'providers' => [
        'users' => ['driver' => 'eloquent','model' => env('AUTH_MODEL', App\Models\User::class)],
    ],
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60, 'throttle' => 60,
        ],
    ],
    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];
```

Nguồn:     

---

## 3) `config/cache.php` — Bộ nhớ đệm

**Tác dụng chính:**

* Store mặc định là `database`. 
* Khai báo các store: `array`, `database`, `file`, `memcached`, `redis`, `dynamodb`, `octane`.       
* Prefix key: theo `APP_NAME`. 

```php
<?php

use Illuminate\Support\Str;

return [
    'default' => env('CACHE_STORE', 'database'),
    'stores' => [
        'array' => ['driver' => 'array','serialize' => false],
        'database' => [
            'driver' => 'database',
            'connection' => env('DB_CACHE_CONNECTION'),
            'table' => env('DB_CACHE_TABLE', 'cache'),
            'lock_connection' => env('DB_CACHE_LOCK_CONNECTION'),
            'lock_table' => env('DB_CACHE_LOCK_TABLE'),
        ],
        'file' => ['driver' => 'file','path' => storage_path('framework/cache/data'),'lock_path' => storage_path('framework/cache/data')],
        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [env('MEMCACHED_USERNAME'), env('MEMCACHED_PASSWORD')],
            'options' => [],
            'servers' => [[
                'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                'port' => env('MEMCACHED_PORT', 11211),
                'weight' => 100,
            ]],
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
        ],
        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],
        'octane' => ['driver' => 'octane'],
    ],
    'prefix' => env('CACHE_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-cache-'),
];
```

Nguồn:        

---

## 4) `config/database.php` — Kết nối CSDL & Redis

**Tác dụng chính:**

* Kết nối mặc định là `sqlite` (có cấu hình ví dụ cho `mysql`, `mariadb`, `pgsql`, `sqlsrv`).     
* Bảng `migrations` và tùy chọn cập nhật ngày publish. 
* Cấu hình Redis (client, prefix, backoff…).   

```php
<?php

use Illuminate\Support\Str;

return [
    'default' => env('DB_CONNECTION', 'sqlite'),
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'transaction_mode' => 'DEFERRED',
        ],
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '', 'prefix_indexes' => true, 'strict' => true, 'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        // mariadb, pgsql, sqlsrv ... (xem file đầy đủ)
    ],
    'migrations' => ['table' => 'migrations','update_date_on_publish' => true],
    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix'  => env('REDIS_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-database-'),
            'persistent' => env('REDIS_PERSISTENT', false),
        ],
        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
        'cache' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],
    ],
];
```

Nguồn:      

---

## 5) `config/filesystems.php` — Lưu trữ tệp

**Tác dụng chính:**

* Disk mặc định: theo `FILESYSTEM_DISK` (mặc định `local`). 
* Định nghĩa các **disk**: `local` (lưu `storage/app/private` + `serve`), `public` (URL `/storage`), `s3`.   
* Cấu hình symbolic link cho `storage:link`. 

```php
<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),
    'disks' => [
        'local' => ['driver' => 'local','root' => storage_path('app/private'),'serve' => true,'throw' => false,'report' => false],
        'public' => ['driver' => 'local','root' => storage_path('app/public'),'url' => env('APP_URL').'/storage','visibility' => 'public','throw' => false,'report' => false],
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,'report' => false,
        ],
    ],
    'links' => [ public_path('storage') => storage_path('app/public'), ],
];
```

Nguồn:     

---

## 6) `config/logging.php` — Ghi log

**Tác dụng chính:**

* Kênh mặc định `stack` (gộp nhiều kênh). 
* Kênh deprecations (mặc định `null`). 
* Danh sách kênh: `single`, `daily`, `slack`, `papertrail`, `stderr`, `syslog`, `errorlog`, `null`, `emergency`.       

```php
<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [
    'default' => env('LOG_CHANNEL', 'stack'),
    'deprecations' => ['channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),'trace' => env('LOG_DEPRECATIONS_TRACE', false)],
    'channels' => [
        'stack' => ['driver' => 'stack','channels' => explode(',', (string) env('LOG_STACK', 'single')),'ignore_exceptions' => false],
        'single' => ['driver' => 'single','path' => storage_path('logs/laravel.log'),'level' => env('LOG_LEVEL', 'debug'),'replace_placeholders' => true],
        'daily'  => ['driver' => 'daily','path' => storage_path('logs/laravel.log'),'level' => env('LOG_LEVEL', 'debug'),'days' => env('LOG_DAILY_DAYS', 14),'replace_placeholders' => true],
        'slack'  => ['driver' => 'slack','url' => env('LOG_SLACK_WEBHOOK_URL'),'username' => env('LOG_SLACK_USERNAME', 'Laravel Log'),'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),'level' => env('LOG_LEVEL', 'critical'),'replace_placeholders' => true],
        'papertrail' => ['driver' => 'monolog','level' => env('LOG_LEVEL', 'debug'),'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),'handler_with' => ['host' => env('PAPERTRAIL_URL'),'port' => env('PAPERTRAIL_PORT'),'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT')],'processors' => [PsrLogMessageProcessor::class]],
        'stderr' => ['driver' => 'monolog','level' => env('LOG_LEVEL', 'debug'),'handler' => StreamHandler::class,'handler_with' => ['stream' => 'php://stderr'],'formatter' => env('LOG_STDERR_FORMATTER'),'processors' => [PsrLogMessageProcessor::class]],
        'syslog' => ['driver' => 'syslog','level' => env('LOG_LEVEL', 'debug'),'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),'replace_placeholders' => true],
        'errorlog' => ['driver' => 'errorlog','level' => env('LOG_LEVEL', 'debug'),'replace_placeholders' => true],
        'null' => ['driver' => 'monolog','handler' => NullHandler::class],
        'emergency' => ['path' => storage_path('logs/laravel.log')],
    ],
];
```

Nguồn:        

---

## 7) `config/mail.php` — Gửi email

**Tác dụng chính:**

* Mailer mặc định là `log` (ghi log thay vì gửi thật). 
* Khai báo các mailer: `smtp`, `ses`, `postmark`, `resend`, `sendmail`, `log`, `array`, `failover`, `roundrobin`.      
* Địa chỉ “From” toàn cục. 

```php
<?php

return [
    'default' => env('MAIL_MAILER', 'log'),
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],
        'ses' => ['transport' => 'ses'],
        'postmark' => ['transport' => 'postmark'],
        'resend' => ['transport' => 'resend'],
        'sendmail' => ['transport' => 'sendmail','path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i')],
        'log' => ['transport' => 'log','channel' => env('MAIL_LOG_CHANNEL')],
        'array' => ['transport' => 'array'],
        'failover' => ['transport' => 'failover','mailers' => ['smtp','log'],'retry_after' => 60],
        'roundrobin' => ['transport' => 'roundrobin','mailers' => ['ses','postmark'],'retry_after' => 60],
    ],
    'from' => ['address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),'name' => env('MAIL_FROM_NAME', 'Example')],
];
```

Nguồn:       

---

## 8) `config/queue.php` — Hàng đợi

**Tác dụng chính:**

* Kết nối hàng đợi mặc định `database`. 
* Các connection: `sync`, `database`, `beanstalkd`, `sqs`, `redis`.    
* Job batching & failed jobs (driver `database-uuids` mặc định).  

```php
<?php

return [
    'default' => env('QUEUE_CONNECTION', 'database'),
    'connections' => [
        'sync' => ['driver' => 'sync'],
        'database' => [
            'driver' => 'database',
            'connection' => env('DB_QUEUE_CONNECTION'),
            'table' => env('DB_QUEUE_TABLE', 'jobs'),
            'queue' => env('DB_QUEUE', 'default'),
            'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90),
            'after_commit' => false,
        ],
        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => env('BEANSTALKD_QUEUE_HOST', 'localhost'),
            'queue' => env('BEANSTALKD_QUEUE', 'default'),
            'retry_after' => (int) env('BEANSTALKD_QUEUE_RETRY_AFTER', 90),
            'block_for' => 0,
            'after_commit' => false,
        ],
        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'default'),
            'suffix' => env('SQS_SUFFIX'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'after_commit' => false,
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => (int) env('REDIS_QUEUE_RETRY_AFTER', 90),
            'block_for' => null,
            'after_commit' => false,
        ],
    ],
    'batching' => ['database' => env('DB_CONNECTION', 'sqlite'),'table' => 'job_batches'],
    'failed' => ['driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),'database' => env('DB_CONNECTION', 'sqlite'),'table' => 'failed_jobs'],
];
```

Nguồn:      

---

## 9) `config/services.php` — Third-party & kênh thông báo

**Tác dụng chính:**

* Khoá Postmark, Resend, SES và cấu hình Slack notifications.  

```php
<?php

return [
    'postmark' => ['token' => env('POSTMARK_TOKEN')],
    'resend'   => ['key' => env('RESEND_KEY')],
    'ses'      => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
];
```

Nguồn:  

---

## 10) `config/session.php` — Phiên đăng nhập

**Tác dụng chính:**

* Driver mặc định `database`, thời lượng 120 phút, có tuỳ chọn expire on close. 
* Vị trí file (nếu dùng `file`), tên bảng `sessions` (nếu `database`).  
* Tên cookie, path/domain, `secure`, `http_only`, `same_site`, `partitioned`.      

```php
<?php

use Illuminate\Support\Str;

return [
    'driver' => env('SESSION_DRIVER', 'database'),
    'lifetime' => (int) env('SESSION_LIFETIME', 120),
    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),
    'encrypt' => env('SESSION_ENCRYPT', false),
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table' => env('SESSION_TABLE', 'sessions'),
    'store' => env('SESSION_STORE'),
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', Str::slug((string) env('APP_NAME', 'laravel')).'-session'),
    'path' => env('SESSION_PATH', '/'),
    'domain' => env('SESSION_DOMAIN'),
    'secure' => env('SESSION_SECURE_COOKIE'),
    'http_only' => env('SESSION_HTTP_ONLY', true),
    'same_site' => env('SESSION_SAME_SITE', 'lax'),
    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),
];
```

Nguồn:         

---

## 11) (tham chiếu) `bootstrap/app.php` — Khởi tạo + alias middleware

> Bạn đã gửi file này trước đó; nhắc lại điểm chính để gắn với config:

* Gắn route `web`, `console`, liveness `/up`. 
* Tạo **alias** `admin` → `App\Http\Middleware\AdminMiddleware`. 

```php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

Nguồn: 

---

## Gợi ý cấu hình nhanh cho dự án của bạn

* **Env tối thiểu để chạy**: `APP_KEY`, `APP_URL`, `DB_CONNECTION` (+ thông số DB), `FILESYSTEM_DISK`, `SESSION_DRIVER`, `CACHE_STORE`. (Tham chiếu từng mục ở trên.)
* **Gửi mail thật**: đổi `MAIL_MAILER=log` → `smtp`/`ses`/`postmark` và cung cấp thông tin tương ứng trong `.env`. 
* **Giỏ hàng lưu ảnh public**: bảo đảm đã chạy `php artisan storage:link` (khớp `filesystems.links`). 
* **Queue**: nếu dùng queue cho email/đơn hàng, hãy bật `QUEUE_CONNECTION=database` và chạy migration cho `jobs` & `failed_jobs`.   

---

Bạn muốn mình gộp **toàn bộ các README** trước đó (Controllers, Auth, Middleware, Models, Providers, Config) thành **một file `README.md` duy nhất** để bạn tải về không?
# README — `.gitignore`, `database.sqlite`, `UserFactory`

Tài liệu này mô tả **tác dụng** của từng tệp bạn vừa thêm và kèm **code thực tế** (đối với `UserFactory`).

---

## 1) `.gitignore` — Bỏ qua file khi commit Git

**Tác dụng:** Quy định các đường dẫn **không đưa lên Git** (build artifacts, cache, secrets).
**Vì sao quan trọng:** Giữ repo sạch, tránh lộ `.env`, khoá, file tạm/thư mục lớn như `vendor/`, `node_modules/`, `storage/`, `bootstrap/cache/`.

> Nội dung cụ thể của `.gitignore` trong repo hiện **không được hiển thị** qua công cụ tìm nội dung; nếu bạn muốn mình ghi lại chính xác, hãy gửi nội dung tệp. Dưới đây là **mẫu chuẩn** (tham khảo, *không phải* trích nguyên bản từ file của bạn):

```gitignore
/vendor/
/node_modules/
composer.lock
npm-debug.log*
yarn.lock
/.env
/.env.*
/public/storage
/storage/*.key
/storage/logs/
/storage/framework/cache/
/storage/framework/sessions/
/storage/framework/views/
/bootstrap/cache/
/.idea/
/.vscode/
/.DS_Store
```

---

## 2) `database.sqlite` — CSDL SQLite nhúng

**Tác dụng:** Là **tệp cơ sở dữ liệu** SQLite dùng bởi ứng dụng (khi `DB_CONNECTION=sqlite`).
**Đặc điểm:**

* Tự chứa, không cần server MySQL/PG.
* Các bảng như `users`, `password_reset_tokens`, `jobs`, `failed_jobs`, `sessions`… sẽ nằm trong tệp này sau khi chạy migration/seed.
* Được tham chiếu trong `config/database.php` dưới kết nối **`sqlite`** (database trỏ về `database_path('database.sqlite')`) .

**Mẹo vận hành nhanh:**

* Tạo file rỗng (nếu chưa có): `touch database/database.sqlite`.
* Cấu hình `.env`:

  ```
  DB_CONNECTION=sqlite
  DB_DATABASE=/absolute/path/to/database/database.sqlite
  ```
* Chạy migration & seed:

  ```
  php artisan migrate
  php artisan db:seed
  ```

---

## 3) `database/factories/UserFactory.php` — Tạo dữ liệu giả cho User

**Tác dụng:** Cung cấp **factory** tạo bản ghi người dùng phục vụ seed/test.

### Code thực tế

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
```

Nguồn: 

### Tác dụng của từng phần trong code

* **`definition()`**: sinh dữ liệu giả hợp lệ (tên, email unique, đã verify, mật khẩu mặc định `'password'` được **hash** bằng `Hash::make`) .
* **`unverified()`**: trạng thái biến thể đặt `email_verified_at = null` để mô phỏng user **chưa xác minh** email .
* **`protected static ?string $password`**: giữ sẵn hash để **tối ưu** (không hash lại nhiều lần) .

### Cách dùng nhanh

* Tạo 10 người dùng:

  ```php
  \App\Models\User::factory()->count(10)->create();
  ```
* Tạo 1 admin (nếu model User có cột `is_admin`):

  ```php
  \App\Models\User::factory()->create([
      'email' => 'admin@example.com',
      'is_admin' => true,
  ]);
  ```
* Tạo user **chưa xác minh**:

  ```php
  \App\Models\User::factory()->unverified()->create();
  ```

---

## Liên quan cấu hình

* `config/auth.php`: guard `web`, provider `users` trỏ `App\Models\User`, thông số reset password & timeout xác nhận lại mật khẩu     .
* `config/database.php`: mặc định **`sqlite`**; có sẵn cấu hình `mysql`, `pgsql`, `sqlsrv`, `mariadb` để chuyển đổi khi cần    .

---

## Tóm tắt nhanh

| File              | Tác dụng chính                                                          |
| ----------------- | ----------------------------------------------------------------------- |
| `.gitignore`      | Loại trừ file/thư mục không cần track, tránh lộ bí mật & rác build      |
| `database.sqlite` | CSDL nhúng cho môi trường dev/test, lưu toàn bộ bảng & dữ liệu ứng dụng |
| `UserFactory.php` | Sinh dữ liệu giả (user) để seed/test; có biến thể `unverified()`        |

---

Bạn muốn mình gộp các README trước (Controllers, Auth, Middleware, Models, Providers, Config, và phần này) thành **một file `README.md` duy nhất** để tiện tải về không?
# README — Migrations (CSDL) cho dự án

Dưới đây là mô tả **tác dụng** của từng migration bạn đã gửi, kèm **mã nguồn đầy đủ** để đối chiếu nhanh.

---

## 1) `0001_01_01_000000_create_users_table.php` — Bảng người dùng, token reset & sessions

**Tác dụng:** Tạo các bảng nền tảng:

* `users` (thông tin người dùng, đã có cột remember token, email unique),
* `password_reset_tokens` (lưu token reset theo email),
* `sessions` (lưu phiên nếu driver là `database`). 

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
```

Nguồn: 

> Ghi chú: `config/session.php` mặc định dùng driver `database`, bảng `sessions`, khớp với migration này.  

---

## 2) `0001_01_01_000001_create_cache_table.php` — Cache & khóa cache

**Tác dụng:** Tạo 2 bảng phục vụ **cache store `database`**:

* `cache` (key, value, expiration),
* `cache_locks` (dùng lock theo key/owner). 

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
```

Nguồn: 

> Khớp với `config/cache.php` khi bạn đặt `CACHE_STORE=database`. (Tham chiếu ở README config.)

---

## 3) `0001_01_01_000002_create_jobs_table.php` — Queue jobs, batches & failed jobs

**Tác dụng:** Tạo các bảng nền tảng cho **hàng đợi**:

* `jobs` (hàng đợi thực thi),
* `job_batches` (batch info),
* `failed_jobs` (job thất bại). 

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
```

Nguồn: 

---

## 4) `2025_10_03_030007_create_categories_table.php` — Danh mục

**Tác dụng:** Tạo bảng `categories` (name, slug unique, timestamps). 

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

Nguồn: 

---

## 5) `2025_10_03_030429_create_products_table.php` — Sản phẩm

**Tác dụng:** Tạo bảng `products` với các trường thương mại cơ bản (giá, giá khuyến mãi, SKU unique, tồn kho, cờ kích hoạt/nổi bật) và **khóa ngoại** đến `categories`. 

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('sku')->unique();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

Nguồn: 

---

## 6) `2025_10_03_030513_create_product_images_table.php` — Ảnh sản phẩm

**Tác dụng:** Bảng `product_images` lưu nhiều ảnh cho một sản phẩm (1-n), **cascade delete** khi xóa sản phẩm. 

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
```

Nguồn: 

---

## 7) `2025_10_03_030521_create_sizes_table.php` — Size

**Tác dụng:** Bảng `sizes` lưu các kích cỡ (ví dụ 38, 39, 40). 

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ví dụ: 38, 39, 40
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sizes');
    }
};
```

Nguồn: 

---

## 8) `2025_10_03_030527_create_colors_table.php` — Màu sắc

**Tác dụng:** Bảng `colors` lưu tên và mã màu (HEX) tuỳ chọn. 

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ví dụ: Red, Black
            $table->string('hex_code')->nullable(); // mã màu HEX
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colors');
    }
};
```

Nguồn: 

---

## 9) `2025_10_03_030531_create_orders_table.php` — Đơn hàng

**Tác dụng:** Bảng `orders` liên kết **user → orders**, tổng tiền, trạng thái (pending/paid/shipped/completed), địa chỉ giao & điện thoại. 

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total_price', 10, 2);
            $table->string('status')->default('pending'); // pending, paid, shipped, completed
            $table->string('shipping_address');
            $table->string('phone');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
```

Nguồn: 

---

## 10) `2025_10_03_030536_create_order_items_table.php` — Dòng sản phẩm trong đơn

**Tác dụng:** Bảng `order_items` liên kết **order ↔ product**, lưu **số lượng** và **đơn giá** tại thời điểm mua; cascade khi xóa order/product. 

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
```

Nguồn: 

---

## Quan hệ & luồng dữ liệu gợi ý

* **Category 1-n Products**: `products.category_id` → `categories.id`. 
* **Product 1-n Images**: `product_images.product_id` → `products.id` (cascade). 
* **User 1-n Orders**: `orders.user_id` → `users.id` (cascade). 
* **Order 1-n OrderItems** + **Product 1-n OrderItems**: liên kết nhiều-một qua `order_id`, `product_id`. 

---

## Gợi ý vận hành nhanh

* **Kết nối CSDL mặc định** đang là `sqlite` (có thể đổi sang `mysql/pgsql/sqlsrv`).  
* **Queue**: cấu hình `QUEUE_CONNECTION=database`, các bảng ở mục (3) sẽ được dùng; `failed_jobs` dùng driver `database-uuids`.   
* **Sessions**: driver `database`, bảng `sessions` đã có; tên cookie đặt theo `APP_NAME`.  

---

Bạn muốn mình gộp toàn bộ README (Controllers, Auth, Middleware, Models, Providers, Config, Database & Migrations) thành **một file `README.md` duy nhất** để bạn tải về không?
# README — Các migration bổ sung

Dưới đây là **tác dụng** của từng migration bạn vừa thêm, kèm **mã nguồn đầy đủ** để đối chiếu.

---

## 1) `2025_10_03_030541_create_carts_table.php` — Bảng giỏ hàng (lưu DB)

**Tác dụng:** Tạo bảng `carts` để lưu các dòng giỏ hàng theo người dùng & sản phẩm.

* Ràng buộc: `user_id` (nullable) & `product_id` đều **foreign key** và **cascade delete**.
* Mỗi dòng có `quantity` và timestamps. 

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
```

Nguồn: 

---

## 2) `2025_10_03_041718_add_is_admin_to_users_table.php` — Cờ quyền Admin cho User

**Tác dụng:** Thêm cột `is_admin` (boolean, mặc định `false`) sau `email_verified_at` để phân quyền quản trị. 

```php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_admin')->default(false)->after('email_verified_at');
});
```

Nguồn: 

---

## 3) `2025_10_03_041737_add_description_and_is_active_to_categories_table.php` — Mô tả & kích hoạt danh mục

**Tác dụng:** Bổ sung 2 cột cho `categories`:

* `description` (text, nullable),
* `is_active` (boolean, mặc định `true`). 

```php
Schema::table('categories', function (Blueprint $table) {
    $table->text('description')->nullable()->after('slug');
    $table->boolean('is_active')->default(true)->after('description');
});
```

Nguồn: 

---

## 4) `2025_10_03_041803_add_is_primary_to_product_images_table.php` — Đánh dấu ảnh chính

**Tác dụng:** Thêm cột `is_primary` (boolean, mặc định `false`) cho `product_images` để đánh dấu **ảnh đại diện** của sản phẩm. 

```php
Schema::table('product_images', function (Blueprint $table) {
    $table->boolean('is_primary')->default(false)->after('image_path');
});
```

Nguồn: 

---

## 5) `2025_10_03_050715_add_image_to_products_table.php` — Ảnh chính trực tiếp trên `products`

**Tác dụng:** Thêm cột `image` (string, nullable) vào bảng `products` (đặt sau `description`).
Hữu ích khi muốn lưu **ảnh chính** trực tiếp trên sản phẩm bên cạnh bảng `product_images`. 

```php
Schema::table('products', function (Blueprint $table) {
    $table->string('image')->nullable()->after('description');
});
```

Nguồn: 

---

## Gợi ý liên kết với code hiện tại

* **Model & Controller đã tương thích:**

  * `ProductController` khi upload ảnh sẽ **tạo bản ghi ở `product_images`**; nếu bạn muốn **đồng thời** set `products.image` làm ảnh đại diện, có thể bổ sung đoạn gán sau khi upload. 
  * `CartController` khi thêm vào giỏ đang ưu tiên lấy `images->first()->image_path` hoặc fallback `products.image` nếu có, phù hợp với 2 migration ảnh ở trên. 
* **Phân quyền admin:** middleware `admin` sẽ dựa vào `users.is_admin` (đã có migration thêm cột này).
* **Giỏ hàng DB vs. thư viện session:** Migration `carts` cho phép bạn **lưu bền** giỏ trong DB. Thư viện `Cart::...` (session) có thể đồng bộ sang bảng `carts` nếu bạn muốn lưu cross-device.

---

Bạn muốn mình gộp **tất cả các README** đã soạn (Controllers, Auth, Middleware, Models, Providers, Config, Migrations phần 1 & phần 2) thành **một file `README.md` duy nhất** để tải về không?
# README — Routes (`routes/web.php`, `routes/console.php`)

Dưới đây là **tác dụng** của từng file routes, kèm **mã nguồn đầy đủ** để bạn đối chiếu.

---

## 1) `routes/web.php` — Tuyến HTTP cho web

**Tác dụng chính:**

* Khai báo trang chủ `/` dùng `HomeController@index` và đặt tên route `home`. 
* Nạp **auth routes** mặc định của Laravel (login, register, reset...). 
* Redirect `/home` về `/` (giữ tương thích sau đăng nhập). 
* Nhóm **giỏ hàng & đơn hàng** bắt buộc đăng nhập (`auth`). Gồm xem giỏ, thêm/cập nhật/xoá, checkout, lưu đơn, lịch sử & chi tiết đơn. 
* Nhóm **admin** yêu cầu `auth` + middleware `admin`; có dashboard `/admin` và **resource** `products` (CRUD). 

### Mã nguồn đầy đủ

```php
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;

// 🌐 Trang chủ - Adidas Style
Route::get('/', [HomeController::class, 'index'])->name('home');

// 🔑 Auth routes (login, register, forgot password...)
Auth::routes();

// 🏠 Trang home sau khi đăng nhập (redirect về trang chủ)
Route::get('/home', function () {
    return redirect('/');
});

// 🛒 Giỏ hàng (user phải đăng nhập mới dùng được)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{rowId}', [CartController::class, 'update'])->name('cart.update');
    Route::get('/cart/remove/{rowId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // 📦 Đơn hàng (Checkout & lịch sử đơn)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

// 👨‍💼 Admin routes (chỉ admin mới vào được)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('products', ProductController::class);
});
```

Nguồn: 

**Liên quan:** alias middleware `admin` đã được gắn trong `bootstrap/app.php`, nên có thể gọi trực tiếp theo tên `'admin'` trong nhóm route. 

---

## 2) `routes/console.php` — Lệnh Artisan tuỳ biến

**Tác dụng chính:**

* Khai báo lệnh `php artisan inspire` in **câu nói truyền cảm hứng** (mặc định của Laravel), có mô tả purpose. 

### Mã nguồn đầy đủ

```php
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
```

Nguồn: 

---

## Gợi ý sử dụng nhanh

* **Xem danh sách routes:**

  ```bash
  php artisan route:list
  ```

  (Sẽ thấy các route `home`, `cart.*`, `orders.*`, `admin.dashboard`, `products.*`…)

* **Bảo vệ admin:**
  Đảm bảo người dùng có `is_admin = true`; middleware đã kiểm tra và chặn truy cập nếu không phải admin (xem README `AdminMiddleware` trước đó).

* **Auth views:**
  `Auth::routes()` bật toàn bộ route xác thực mặc định. Bạn có thể chạy `php artisan ui:auth` (nếu dùng laravel/ui) hoặc tự tạo view theo stack hiện có.

---

Bạn muốn mình gộp phần **Routes** này vào bản **README tổng** cùng các phần trước để bạn tải về một file duy nhất không?
=======
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
>>>>>>> 9c2e89acc4b2103bbbfa35662f804a78f7245834
