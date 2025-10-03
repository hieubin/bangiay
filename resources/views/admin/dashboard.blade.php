@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-gradient mb-0">Dashboard Quản lý</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Thêm sản phẩm
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>
                        Quản lý sản phẩm
                    </a>
                </div>
            </div>

            <!-- Thống kê tổng quan -->
            <div class="row g-4 mb-5">
                <div class="col-lg-3 col-md-6">
                    <div class="card glass hover-lift">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-box fa-3x text-primary"></i>
                            </div>
                            <h3 class="text-gradient">{{ $totalProducts }}</h3>
                            <p class="text-muted mb-0">Tổng sản phẩm</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card glass hover-lift">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-shopping-cart fa-3x text-success"></i>
                            </div>
                            <h3 class="text-gradient">{{ $totalOrders }}</h3>
                            <p class="text-muted mb-0">Tổng đơn hàng</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card glass hover-lift">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-users fa-3x text-info"></i>
                            </div>
                            <h3 class="text-gradient">{{ $totalUsers }}</h3>
                            <p class="text-muted mb-0">Tổng người dùng</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card glass hover-lift">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-dollar-sign fa-3x text-warning"></i>
                            </div>
                            <h3 class="text-gradient">{{ number_format($revenue) }}đ</h3>
                            <p class="text-muted mb-0">Doanh thu</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quản lý nhanh -->
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card glass">
                        <div class="card-header">
                            <h5 class="mb-0">Quản lý sản phẩm</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-grid">
                                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-list me-2"></i>
                                            Xem tất cả sản phẩm
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-grid">
                                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>
                                            Thêm sản phẩm mới
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card glass">
                        <div class="card-header">
                            <h5 class="mb-0">Thống kê nhanh</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Sản phẩm hoạt động:</span>
                                    <span class="fw-bold text-success">{{ $totalProducts }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Đơn hàng mới:</span>
                                    <span class="fw-bold text-info">{{ $totalOrders }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Người dùng:</span>
                                    <span class="fw-bold text-primary">{{ $totalUsers }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Doanh thu:</span>
                                    <span class="fw-bold text-warning">{{ number_format($revenue) }}đ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hướng dẫn sử dụng -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card glass">
                        <div class="card-header">
                            <h5 class="mb-0">Hướng dẫn sử dụng</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center mb-3">
                                        <i class="fas fa-box fa-2x text-primary mb-2"></i>
                                        <h6>Quản lý sản phẩm</h6>
                                        <p class="text-muted small">Thêm, sửa, xóa sản phẩm và quản lý danh mục</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center mb-3">
                                        <i class="fas fa-shopping-cart fa-2x text-success mb-2"></i>
                                        <h6>Quản lý đơn hàng</h6>
                                        <p class="text-muted small">Xem và cập nhật trạng thái đơn hàng</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center mb-3">
                                        <i class="fas fa-chart-bar fa-2x text-info mb-2"></i>
                                        <h6>Thống kê</h6>
                                        <p class="text-muted small">Theo dõi doanh thu và hiệu suất bán hàng</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection