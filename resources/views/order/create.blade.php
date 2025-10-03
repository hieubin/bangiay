@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Thanh toán</h2>
    <form action="{{ route('orders.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Địa chỉ giao hàng</label>
            <input type="text" name="shipping_address" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Số điện thoại</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <button class="btn btn-success">Đặt hàng</button>
    </form>
</div>
@endsection
