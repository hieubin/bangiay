@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card glass">
                <div class="card-header">
                    <h3 class="text-gradient mb-0">Tạo đơn hàng mới</h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        
                        <!-- Thông tin giao hàng -->
                        <div class="mb-4">
                            <h5 class="mb-3">Thông tin giao hàng</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="shipping_address" class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('shipping_address') is-invalid @enderror" 
                                              id="shipping_address" 
                                              name="shipping_address" 
                                              rows="3" 
                                              required>{{ old('shipping_address') }}</textarea>
                                    @error('shipping_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}" 
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Tóm tắt giỏ hàng -->
                        <div class="mb-4">
                            <h5 class="mb-3">Sản phẩm trong giỏ hàng</h5>
                            @if($cartItems->count() > 0)
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
                                            @foreach($cartItems as $item)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3 bg-light d-flex align-items-center justify-content-center" 
                                                                 style="width: 50px; height: 50px; border-radius: 8px;">
                                                                <i class="fas fa-image text-muted"></i>
                                                            </div>
                                                            <div>
                                                                <strong>{{ $item->name }}</strong><br>
                                                                <small class="text-muted">SKU: {{ $item->options->sku ?? 'N/A' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $item->qty }}</td>
                                                    <td>{{ number_format($item->price) }}đ</td>
                                                    <td class="fw-bold">{{ number_format($item->price * $item->qty) }}đ</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Giỏ hàng trống. Vui lòng thêm sản phẩm vào giỏ hàng trước khi tạo đơn hàng.
                                </div>
                            @endif
                        </div>

                        <!-- Tổng tiền -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Tạm tính:</span>
                                            <span>{{ number_format(Cart::subtotal()) }}đ</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Phí vận chuyển:</span>
                                            <span>Miễn phí</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <strong>Tổng cộng:</strong>
                                            <strong class="text-primary">{{ number_format(Cart::total()) }}đ</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nút xác nhận -->
                        <div class="d-flex gap-3">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Quay lại giỏ hàng
                            </a>
                            @if($cartItems->count() > 0)
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check me-2"></i>
                                    Xác nhận đặt hàng
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
