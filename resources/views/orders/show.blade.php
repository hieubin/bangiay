@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-gradient mb-0">Chi tiết đơn hàng #{{ $order->id }}</h2>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Quay lại
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Thông tin đơn hàng -->
                    <div class="card glass mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Mã đơn hàng:</strong> #{{ $order->id }}<br>
                                    <strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}<br>
                                    <strong>Trạng thái:</strong> 
                                    <span class="badge 
                                        @if($order->status == 'pending') bg-warning
                                        @elseif($order->status == 'processing') bg-info
                                        @elseif($order->status == 'completed') bg-success
                                        @elseif($order->status == 'cancelled') bg-danger
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Tổng tiền:</strong> <span class="text-primary fw-bold">{{ number_format($order->total_price) }}đ</span><br>
                                    <strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address }}<br>
                                    <strong>Số điện thoại:</strong> {{ $order->phone }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Danh sách sản phẩm -->
                    <div class="card glass">
                        <div class="card-header">
                            <h5 class="mb-0">Sản phẩm trong đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            @if($order->orderItems->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th>Số lượng</th>
                                                <th>Giá</th>
                                                <th>Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->orderItems as $item)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if($item->product->image && file_exists(public_path($item->product->image)))
                                                                <img src="{{ asset($item->product->image) }}" 
                                                                     alt="{{ $item->product->name }}" 
                                                                     class="me-3" 
                                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                                            @else
                                                                <div class="me-3 bg-light d-flex align-items-center justify-content-center" 
                                                                     style="width: 50px; height: 50px; border-radius: 8px;">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <strong>{{ $item->product->name }}</strong><br>
                                                                <small class="text-muted">{{ $item->product->category->name ?? 'Chưa phân loại' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ number_format($item->price) }}đ</td>
                                                    <td class="fw-bold">{{ number_format($item->price * $item->quantity) }}đ</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <p class="text-muted">Không có sản phẩm nào trong đơn hàng này.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Tóm tắt đơn hàng -->
                    <div class="card glass">
                        <div class="card-header">
                            <h5 class="mb-0">Tóm tắt đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính:</span>
                                <span>{{ number_format($order->total_price) }}đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển:</span>
                                <span>Miễn phí</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Tổng cộng:</strong>
                                <strong class="text-primary">{{ number_format($order->total_price) }}đ</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Hành động -->
                    <div class="card glass mt-3">
                        <div class="card-body">
                            <h6 class="card-title">Hành động</h6>
                            @if($order->status == 'pending')
                                <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="d-inline w-100">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100" 
                                            onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                                        <i class="fas fa-times me-2"></i>
                                        Hủy đơn hàng
                                    </button>
                                </form>
                            @elseif($order->status == 'completed')
                                <button class="btn btn-success w-100" disabled>
                                    <i class="fas fa-check me-2"></i>
                                    Đơn hàng đã hoàn thành
                                </button>
                            @else
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-clock me-2"></i>
                                    Đơn hàng đang xử lý
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
