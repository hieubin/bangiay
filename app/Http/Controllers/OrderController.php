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
        $orders = Order::where('user_id', auth()->id())
                       ->orderBy('created_at', 'desc')
                       ->paginate(10); // Phân trang 10 đơn hàng mỗi trang
        
        return view('orders.index', compact('orders'));
    }

    // Chi tiết đơn hàng
    public function show(Order $order)
    {
        $this->authorize('view', $order); // chỉ cho phép user xem đơn của chính mình
        return view('orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        // Kiểm tra quyền xóa đơn hàng
        if($order->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Không có quyền xóa đơn hàng này!');
        }
        
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Đã xóa đơn hàng thành công!');
    }
}
