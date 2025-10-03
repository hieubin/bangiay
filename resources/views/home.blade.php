@extends('layouts.app')

@section('content')
<!-- Hero Section - Luffy One Piece Background -->
<div class="hero-section position-relative overflow-hidden">
    <!-- Video Background -->
    <div class="hero-video-background">
        <video autoplay muted loop playsinline class="hero-video" preload="auto">
            <source src="{{ asset('videos/luffy-background.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    
    <!-- Image Slides (Fallback) -->
    <div class="hero-background">
        <div class="hero-slide active" style="background-image: url('/images/shoes/giay-dat-nhat-the-gioi.jpg');"></div>
        <div class="hero-slide" style="background-image: url('/images/shoes/Louis-Vuitton-Kanye-West-Jasper.jpg');"></div>
        <div class="hero-slide" style="background-image: url('/images/shoes/top-10-doi-giay-bong-da-adidas-dat-nhat-the-gioi-3.jpg');"></div>
    </div>
    
    <div class="hero-overlay"></div>
    
    <div class="container position-relative">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6">
                <div class="hero-content fade-in">
                    <h1 class="hero-title text-gradient mb-4">
                        IMPOSSIBLE IS NOTHING
                    </h1>
                    <p class="hero-subtitle mb-4">
                        Khám phá bộ sưu tập giày cao cấp với thiết kế đột phá và công nghệ tiên tiến
                    </p>
                    <div class="hero-buttons">
                        <a href="#products" class="btn btn-primary btn-lg me-3 hover-lift">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Mua ngay
                        </a>
                        <a href="#collections" class="btn btn-outline-light btn-lg hover-lift">
                            <i class="fas fa-eye me-2"></i>
                            Xem bộ sưu tập
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image-container scale-in">
                    <img src="/images/shoes/doi-giay-dat-nhat.jpg" 
                         alt="Premium Shoes" 
                         class="hero-image float">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hero Navigation Dots -->
    <div class="hero-dots">
        <span class="dot active" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
        <span class="dot" onclick="currentSlide(3)"></span>
    </div>
</div>

<!-- Brand Showcase Section -->
<div class="brand-showcase py-5" id="collections">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="section-title text-gradient mb-3">BỘ SƯU TẬP</h2>
                <p class="section-subtitle">Khám phá những dòng sản phẩm đặc biệt</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="collection-card glass hover-lift">
                    <div class="collection-image">
                        <img src="/images/shoes/nhung-mau-giay-dat-nhat-the-gioi.jpg" alt="Luxury Collection">
                    </div>
                    <div class="collection-content">
                        <h4>LUXURY COLLECTION</h4>
                        <p>Những đôi giày cao cấp nhất</p>
                        <a href="#" class="btn btn-outline">Xem thêm</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="collection-card glass hover-lift">
                    <div class="collection-image">
                        <img src="/images/shoes/giay-dat-nhat-the-gioi-2023.jpg" alt="Sports Collection">
                    </div>
                    <div class="collection-content">
                        <h4>SPORTS COLLECTION</h4>
                        <p>Giày thể thao hiệu suất cao</p>
                        <a href="#" class="btn btn-outline">Xem thêm</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="collection-card glass hover-lift">
                    <div class="collection-image">
                        <img src="/images/shoes/top-nhung-doi-giay-dat-nhat-the-gioi.jpg" alt="Limited Edition">
                    </div>
                    <div class="collection-content">
                        <h4>LIMITED EDITION</h4>
                        <p>Phiên bản giới hạn đặc biệt</p>
                        <a href="#" class="btn btn-outline">Xem thêm</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Products Section - Adidas Style -->
<div class="products-section py-5" id="products">
    <div class="container">
        <!-- Section Header -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="section-title text-gradient mb-3">SẢN PHẨM NỔI BẬT</h2>
                <p class="section-subtitle">Khám phá những mẫu giày được yêu thích nhất</p>
            </div>
        </div>
        
        <!-- Filter and Sort Bar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-bar glass p-3 rounded-modern">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="filter-buttons">
                                <button class="btn btn-filter active" data-filter="all">Tất cả</button>
                                <button class="btn btn-filter" data-filter="sneakers">Sneakers</button>
                                <button class="btn btn-filter" data-filter="sports">Thể thao</button>
                                <button class="btn btn-filter" data-filter="luxury">Cao cấp</button>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="sort-dropdown">
                                <select class="form-select glass" id="sortSelect">
                                    <option value="latest">Mới nhất</option>
                                    <option value="price-low">Giá thấp đến cao</option>
                                    <option value="price-high">Giá cao đến thấp</option>
                                    <option value="popular">Phổ biến</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
            <div class="row g-4" id="productsGrid">
                @foreach($products as $product)
                    <div class="col-lg-3 col-md-4 col-sm-6 product-item" data-category="{{ strtolower($product->category->name ?? 'all') }}">
                        <div class="product-card glass hover-lift">
                            <div class="product-image-container">
                                @if($product->image && file_exists(public_path($product->image)))
                                    <img src="{{ asset($product->image) }}" 
                                         class="product-image" 
                                         alt="{{ $product->name }}">
                                @elseif($product->images->count() > 0)
                                    <img src="{{ asset($product->images->first()->image_path) }}" 
                                         class="product-image" 
                                         alt="{{ $product->name }}">
                                @else
                                    <img src="/images/shoes/giay-dat-nhat.jpg" 
                                         class="product-image" 
                                         alt="{{ $product->name }}">
                                @endif
                                
                                <div class="product-overlay">
                                    <div class="product-actions">
                                        <button class="btn btn-action" title="Yêu thích">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                        <button class="btn btn-action" title="Xem nhanh">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-action" title="So sánh">
                                            <i class="fas fa-balance-scale"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                @if($product->sale_price && $product->sale_price < $product->price)
                                    <div class="product-badge sale">SALE</div>
                                @endif
                            </div>
                            
                            <div class="product-content">
                                <div class="product-category">{{ $product->category->name ?? 'Chưa phân loại' }}</div>
                                <h5 class="product-title">{{ $product->name }}</h5>
                                <p class="product-description">{{ Str::limit($product->description, 80) }}</p>
                                
                                <div class="product-price">
                                    @if($product->sale_price && $product->sale_price < $product->price)
                                        <span class="price-old">{{ number_format($product->price) }}đ</span>
                                        <span class="price-new">{{ number_format($product->sale_price) }}đ</span>
                                    @else
                                        <span class="price-current">{{ number_format($product->price) }}đ</span>
                                    @endif
                                </div>

                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="product-form">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100 hover-lift">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        Thêm vào giỏ
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="row mt-5">
                <div class="col-12 d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-12 text-center py-5">
                    <div class="alert alert-info glass">
                        <i class="fas fa-info-circle me-2"></i>
                        Hiện tại chưa có sản phẩm nào. Vui lòng quay lại sau!
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
/* Luffy One Piece Video Background */
.hero-video-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    overflow: hidden;
}

.hero-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    min-width: 100%;
    min-height: 100%;
}

/* Hide image slides when video is playing */
.hero-section:has(.hero-video) .hero-background {
    display: none;
}

/* Show image slides when video fails */
.hero-section:not(:has(.hero-video)) .hero-background {
    display: block;
}

/* Adidas Style Hero Section */
.hero-section {
    min-height: 100vh;
    position: relative;
    overflow: hidden;
    background: #000;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.hero-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    opacity: 0;
    transition: opacity 1s ease-in-out;
}

.hero-slide.active {
    opacity: 1;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.4) 100%);
    z-index: 2;
}

.hero-content {
    position: relative;
    z-index: 3;
    color: white;
}

.hero-title {
    font-size: 4rem;
    font-weight: 900;
    line-height: 1.1;
    margin-bottom: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
}

.hero-subtitle {
    font-size: 1.25rem;
    font-weight: 300;
    opacity: 0.9;
    line-height: 1.6;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
}

.hero-image-container {
    position: relative;
    z-index: 3;
}

.hero-image {
    max-width: 100%;
    height: auto;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
}

.hero-dots {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 3;
    display: flex;
    gap: 10px;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    cursor: pointer;
    transition: all 0.3s ease;
}

.dot.active {
    background: white;
    transform: scale(1.2);
}

/* Brand Showcase */
.brand-showcase {
    background: var(--bg-secondary);
}

.section-title {
    font-size: 2.5rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.section-subtitle {
    font-size: 1.1rem;
    color: var(--text-light);
    font-weight: 300;
}

.collection-card {
    border-radius: var(--radius-2xl);
    overflow: hidden;
    transition: var(--transition-normal);
    height: 100%;
}

.collection-image {
    height: 250px;
    overflow: hidden;
}

.collection-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition-slow);
}

.collection-card:hover .collection-image img {
    transform: scale(1.1);
}

.collection-content {
    padding: var(--space-xl);
    text-align: center;
}

.collection-content h4 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: var(--space-sm);
    color: var(--text-dark);
}

.collection-content p {
    color: var(--text-medium);
    margin-bottom: var(--space-lg);
}

/* Products Section */
.products-section {
    background: var(--bg-primary);
}

.filter-bar {
    margin-bottom: var(--space-xl);
}

.btn-filter {
    background: transparent;
    border: 2px solid var(--glass-border);
    color: var(--text-medium);
    padding: var(--space-sm) var(--space-lg);
    margin-right: var(--space-sm);
    border-radius: var(--radius-full);
    transition: var(--transition-normal);
    font-weight: 600;
}

.btn-filter.active,
.btn-filter:hover {
    background: var(--gradient-modern);
    border-color: var(--primary-color);
    color: white;
    transform: translateY(-2px);
}

/* Product Cards */
.product-card {
    border-radius: var(--radius-2xl);
    overflow: hidden;
    transition: var(--transition-normal);
    height: 100%;
    position: relative;
}

.product-image-container {
    position: relative;
    height: 280px;
    overflow: hidden;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition-slow);
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: var(--transition-normal);
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.product-card:hover .product-image {
    transform: scale(1.1);
}

.product-actions {
    display: flex;
    gap: var(--space-sm);
}

.btn-action {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: var(--glass-bg);
    backdrop-filter: var(--glass-backdrop);
    border: 1px solid var(--glass-border);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition-normal);
}

.btn-action:hover {
    background: var(--primary-color);
    transform: scale(1.1);
}

.product-badge {
    position: absolute;
    top: var(--space-md);
    right: var(--space-md);
    padding: var(--space-xs) var(--space-sm);
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-badge.sale {
    background: var(--accent-color);
    color: white;
}

.product-content {
    padding: var(--space-xl);
}

.product-category {
    font-size: 0.8rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: var(--space-xs);
}

.product-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: var(--space-sm);
    color: var(--text-dark);
    line-height: 1.3;
}

.product-description {
    font-size: 0.9rem;
    color: var(--text-medium);
    margin-bottom: var(--space-lg);
    line-height: 1.5;
}

.product-price {
    margin-bottom: var(--space-lg);
}

.price-old {
    text-decoration: line-through;
    color: var(--text-muted);
    font-size: 0.9rem;
    margin-right: var(--space-sm);
}

.price-new {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--accent-color);
}

.price-current {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--primary-color);
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .product-image-container {
        height: 220px;
    }
    
    .btn-filter {
        padding: var(--space-xs) var(--space-md);
        font-size: 0.85rem;
        margin-bottom: var(--space-sm);
    }
    
    .filter-buttons {
        text-align: center;
        margin-bottom: var(--space-md);
    }
}

@media (max-width: 576px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-buttons {
        text-align: center;
    }
    
    .hero-buttons .btn {
        display: block;
        width: 100%;
        margin-bottom: var(--space-sm);
    }
    
    .product-content {
        padding: var(--space-lg);
    }
}

/* Video fallback for older browsers */
@supports not (object-fit: cover) {
    .hero-video {
        width: 100%;
        height: 100%;
        min-width: 100%;
        min-height: 100%;
    }
}

/* Debug video loading */
.hero-video-background::after {
    content: "Video loading...";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    z-index: 10;
    display: none;
}

.hero-video-background:not(:has(video[src]))::after {
    display: block;
}
</style>

<script>
// Check if video loads successfully
document.addEventListener('DOMContentLoaded', function() {
    const video = document.querySelector('.hero-video');
    const videoBackground = document.querySelector('.hero-video-background');
    const imageBackground = document.querySelector('.hero-background');
    
    if (video) {
        video.addEventListener('loadeddata', function() {
            console.log('Video loaded successfully');
            videoBackground.style.display = 'block';
            imageBackground.style.display = 'none';
        });
        
        video.addEventListener('error', function() {
            console.log('Video failed to load, showing image fallback');
            videoBackground.style.display = 'none';
            imageBackground.style.display = 'block';
        });
        
        // Force load video
        video.load();
    }
});

// Hero Slider
let currentSlideIndex = 0;
const slides = document.querySelectorAll('.hero-slide');
const dots = document.querySelectorAll('.dot');

function showSlide(index) {
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    slides[index].classList.add('active');
    dots[index].classList.add('active');
}

function currentSlide(index) {
    currentSlideIndex = index - 1;
    showSlide(currentSlideIndex);
}

// Auto slide
setInterval(() => {
    currentSlideIndex = (currentSlideIndex + 1) % slides.length;
    showSlide(currentSlideIndex);
}, 5000);

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.btn-filter');
    const productItems = document.querySelectorAll('.product-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter products
            productItems.forEach(item => {
                const category = item.getAttribute('data-category');
                if (filter === 'all' || category.includes(filter)) {
                    item.style.display = 'block';
                    item.classList.add('fade-in');
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    
    // Sort functionality
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const sortValue = this.value;
            const productsGrid = document.getElementById('productsGrid');
            const products = Array.from(productsGrid.children);
            
            products.sort((a, b) => {
                const priceA = parseInt(a.querySelector('.price-current, .price-new')?.textContent.replace(/[^\d]/g, '') || 0);
                const priceB = parseInt(b.querySelector('.price-current, .price-new')?.textContent.replace(/[^\d]/g, '') || 0);
                
                switch(sortValue) {
                    case 'price-low':
                        return priceA - priceB;
                    case 'price-high':
                        return priceB - priceA;
                    default:
                        return 0;
                }
            });
            
            products.forEach(product => productsGrid.appendChild(product));
        });
    }
});
</script>
@endsection