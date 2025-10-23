@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-gradient mb-0">Đơn hàng của tôi</h2>
                <a href="{{ route('orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Tạo đơn hàng mới
                </a>
            </div>

            @if($orders->count() > 0)
                <div class="row g-4">
                    @foreach($orders as $order)
                        <div class="col-lg-6 col-xl-4">
                            <div class="card glass hover-lift">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Đơn hàng #{{ $order->id }}</h5>
                                    <span class="badge 
                                        @if($order->status == 'pending') bg-warning
                                        @elseif($order->status == 'processing') bg-info
                                        @elseif($order->status == 'completed') bg-success
                                        @elseif($order->status == 'cancelled') bg-danger
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <strong>Ngày đặt:</strong><br>
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="col-6">
                                            <strong>Tổng tiền:</strong><br>
                                            <span class="text-primary fw-bold">{{ number_format($order->total_price) }}đ</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Địa chỉ giao hàng:</strong><br>
                                        <small class="text-muted">{{ $order->shipping_address }}</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Số điện thoại:</strong><br>
                                        <small class="text-muted">{{ $order->phone }}</small>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>
                                            Chi tiết
                                        </a>
                                        @if($order->status == 'pending')
                                            <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                        onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                                                    <i class="fas fa-times me-1"></i>
                                                    Hủy đơn
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="row mt-4">
                    <div class="col-12 d-flex justify-content-center">
                        {{ $orders->links() }}  {{-- Đảm bảo $orders là instance của LengthAwarePaginator --}}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="alert alert-info glass">
                        <i class="fas fa-shopping-bag fa-3x mb-3 text-muted"></i>
                        <h4>Chưa có đơn hàng nào</h4>
                        <p class="text-muted">Hãy thêm sản phẩm vào giỏ hàng và tạo đơn hàng đầu tiên của bạn!</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Mua sắm ngay
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

