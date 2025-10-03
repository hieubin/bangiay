@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Đơn hàng của tôi</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Mã</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ number_format($order->total_price) }} đ</td>
                <td>{{ $order->status }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td><a href="{{ route('orders.show', $order) }}" class="btn btn-info btn-sm">Xem</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
