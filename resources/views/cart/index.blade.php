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
                        <img src="{{ asset('storage/'.$item->options->image) }}" width="50" height="50" style="object-fit: cover; border-radius: 5px; cursor: pointer;" alt="{{ $item->name }}" onclick="showImageModal('{{ asset('storage/'.$item->options->image) }}', '{{ $item->name }}')">
                    @else
                        <img src="{{ asset('images/shoes/images.jpg') }}" width="50" height="50" style="object-fit: cover; border-radius: 5px; cursor: pointer;" alt="{{ $item->name }}" onclick="showImageModal('{{ asset('images/shoes/images.jpg') }}', '{{ $item->name }}')">
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

    <div class="row mt-4">
        <div class="col-md-6">
            <h4 class="text-primary">Tổng tiền: <span class="fw-bold">{{ number_format($total) }} đ</span></h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('cart.clear') }}" class="btn btn-warning me-2" onclick="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')">Xóa giỏ hàng</a>
            <a href="{{ route('orders.create') }}" class="btn btn-success">Thanh toán</a>
        </div>
    </div>
    @else
    <div class="text-center py-5">
        <h3 class="text-muted">Giỏ hàng trống!</h3>
        <p class="text-muted">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm.</p>
        <a href="{{ route('home') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
    </div>
    @endif
</div>

<!-- Modal để hiển thị ảnh lớn -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Ảnh sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="">
            </div>
        </div>
    </div>
</div>

<script>
function showImageModal(imageSrc, productName) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('modalImage').alt = productName;
    document.getElementById('imageModalLabel').textContent = productName;
    var modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}
</script>
@endsection
