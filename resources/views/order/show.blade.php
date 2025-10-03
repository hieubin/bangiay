@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Chi tiết đơn hàng #{{ $order->id }}</h2>
    <p><strong>Địa chỉ giao:</strong> {{ $order->shipping_address }}</p>
    <p><strong>SĐT:</strong> {{ $order->phone }}</p>
    <p><strong>Trạng thái:</strong> {{ $order->status }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price) }} đ</td>
                <td>{{ number_format($item->price * $item->quantity) }} đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Tổng: {{ number_format($order->total_price) }} đ</h4>
</div>
@endsection
