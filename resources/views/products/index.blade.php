@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-gradient mb-0">Quản lý sản phẩm</h2>
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Thêm sản phẩm mới
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($products->count() > 0)
                <div class="card glass">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Danh mục</th>
                                        <th>Giá</th>
                                        <th>Giá sale</th>
                                        <th>Tồn kho</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                @if($product->image && file_exists(public_path($product->image)))
                                                    <img src="{{ asset($product->image) }}" 
                                                         alt="{{ $product->name }}" 
                                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px; border-radius: 8px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $product->name }}</strong><br>
                                                <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $product->category->name ?? 'Chưa phân loại' }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ number_format($product->price) }}đ</span>
                                            </td>
                                            <td>
                                                @if($product->sale_price)
                                                    <span class="text-success fw-bold">{{ number_format($product->sale_price) }}đ</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    @if($product->stock_quantity > 10) bg-success
                                                    @elseif($product->stock_quantity > 0) bg-warning
                                                    @else bg-danger
                                                    @endif">
                                                    {{ $product->stock_quantity }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    @if($product->is_active) bg-success
                                                    @else bg-danger
                                                    @endif">
                                                    {{ $product->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('products.edit', $product->id) }}" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('products.destroy', $product->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="row mt-4">
                    <div class="col-12 d-flex justify-content-center">
                        {{ $products->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="alert alert-info glass">
                        <i class="fas fa-box fa-3x mb-3 text-muted"></i>
                        <h4>Chưa có sản phẩm nào</h4>
                        <p class="text-muted">Hãy thêm sản phẩm đầu tiên để bắt đầu quản lý!</p>
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Thêm sản phẩm đầu tiên
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
