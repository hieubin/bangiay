@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Giỏ hàng</h2>

    @if($cartItems->count() > 0)
    <table class="table">
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Ảnh</th>
                <th>Số lượng</th>
                <th>Giá</th>
                <th>Thành tiền</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cartItems as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>
                    @if($item->options->image)
                        <img src="{{ asset('storage/'.$item->options->image) }}" width="50">
                    @endif
                </td>
                <td>
                    <form action="{{ route('cart.update', $item->rowId) }}" method="POST">
                        @csrf
                        <input type="number" name="quantity" value="{{ $item->qty }}" min="1" style="width: 60px;">
                        <button type="submit" class="btn btn-sm btn-primary">Cập nhật</button>
                    </form>
                </td>
                <td>{{ number_format($item->price) }} đ</td>
                <td>{{ number_format($item->price * $item->qty) }} đ</td>
                <td>
                    <a href="{{ route('cart.remove', $item->rowId) }}" class="btn btn-sm btn-danger">Xóa</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Tổng: {{ Cart::total() }} đ</h4>
    <a href="{{ route('cart.clear') }}" class="btn btn-warning">Xóa giỏ hàng</a>
    <a href="{{ route('orders.create') }}" class="btn btn-success">Thanh toán</a>
    @else
    <p>Giỏ hàng trống!</p>
    @endif
</div>
@endsection
