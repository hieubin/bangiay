<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            $this->command->info('Không có danh mục nào. Vui lòng chạy CategorySeeder trước.');
            return;
        }

        $products = [
            [
                'name' => 'Nike Air Force 1',
                'description' => 'Giày sneaker cổ điển với thiết kế đơn giản nhưng thời trang. Chất liệu da cao cấp, đế cao su bền bỉ.',
                'price' => 2500000,
                'sale_price' => 2200000,
                'stock_quantity' => 50,
                'category_id' => $categories->where('name', 'Giày Sneaker')->first()->id,
                'is_active' => true,
                'is_featured' => true,
                'image' => 'images/product/Nike Air Force 1.jpg',
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'description' => 'Giày chạy bộ với công nghệ Boost tiên tiến, mang lại cảm giác êm ái và năng lượng phản hồi tốt.',
                'price' => 4500000,
                'sale_price' => 4000000,
                'stock_quantity' => 30,
                'category_id' => $categories->where('name', 'Giày Chạy Bộ')->first()->id,
                'is_active' => true,
                'is_featured' => true,
                'image' => 'images/product/Adidas Ultraboost 22.jpg',
            ],
            [
                'name' => 'Nike Mercurial Vapor',
                'description' => 'Giày bóng đá với công nghệ Flyknit, nhẹ và bám chân tốt. Phù hợp cho tiền đạo và tiền vệ.',
                'price' => 3800000,
                'sale_price' => null,
                'stock_quantity' => 25,
                'category_id' => $categories->where('name', 'Giày Bóng Đá')->first()->id,
                'is_active' => true,
                'is_featured' => false,
                'image' => 'images/product/Nike Mercurial Vapor.jpg',
            ],
            [
                'name' => 'Christian Louboutin Red Heels',
                'description' => 'Giày cao gót cao cấp với đế đỏ đặc trưng. Thiết kế sang trọng, phù hợp cho các dịp đặc biệt.',
                'price' => 15000000,
                'sale_price' => 12000000,
                'stock_quantity' => 10,
                'category_id' => $categories->where('name', 'Giày Cao Gót')->first()->id,
                'is_active' => true,
                'is_featured' => true,
                'image' => 'images/product/Christian Louboutin Red Heels.jpg',
            ],
            [
                'name' => 'Vans Old Skool',
                'description' => 'Giày lười cổ điển với thiết kế đơn giản. Chất liệu canvas bền bỉ, phù hợp cho giới trẻ.',
                'price' => 1800000,
                'sale_price' => null,
                'stock_quantity' => 40,
                'category_id' => $categories->where('name', 'Giày Lười')->first()->id,
                'is_active' => true,
                'is_featured' => false,
                'image' => 'images/product/Vans Old Skool.jpg',
            ],
            [
                'name' => 'Timberland Premium Boot',
                'description' => 'Giày boot cao cấp với chất liệu da thật. Chống thấm nước tốt, phù hợp cho mùa đông.',
                'price' => 3500000,
                'sale_price' => 3000000,
                'stock_quantity' => 20,
                'category_id' => $categories->where('name', 'Giày Boot')->first()->id,
                'is_active' => true,
                'is_featured' => false,
                'image' => 'images/product/Timberland Premium Bootb.jpg',
            ],
            [
                'name' => 'Birkenstock Arizona',
                'description' => 'Giày sandal với đế nút chai tự nhiên. Thiết kế thoáng mát, phù hợp cho mùa hè.',
                'price' => 2200000,
                'sale_price' => null,
                'stock_quantity' => 35,
                'category_id' => $categories->where('name', 'Giày Sandal')->first()->id,
                'is_active' => true,
                'is_featured' => false,
                'image' => 'images/product/Birkenstock Arizona.jpg',
            ],
            [
                'name' => 'Cole Haan Oxford',
                'description' => 'Giày Oxford cổ điển với chất liệu da cao cấp. Thiết kế tinh tế, phù hợp cho công sở.',
                'price' => 4200000,
                'sale_price' => 3800000,
                'stock_quantity' => 15,
                'category_id' => $categories->where('name', 'Giày Oxford')->first()->id,
                'is_active' => true,
                'is_featured' => false,
                'image' => 'images/product/Cole Haan Oxford.jpg',
            ],
            [
                'name' => 'Converse Chuck Taylor',
                'description' => 'Giày sneaker cổ điển với thiết kế đơn giản. Chất liệu canvas, phù hợp cho mọi lứa tuổi.',
                'price' => 1500000,
                'sale_price' => null,
                'stock_quantity' => 60,
                'category_id' => $categories->where('name', 'Giày Sneaker')->first()->id,
                'is_active' => true,
                'is_featured' => false,
                'image' => 'images/product/Converse Chuck Taylor.jpg',
            ],
            [
                'name' => 'New Balance 990v5',
                'description' => 'Giày chạy bộ với công nghệ FuelCell. Thiết kế hiện đại, mang lại cảm giác thoải mái.',
                'price' => 3200000,
                'sale_price' => 2800000,
                'stock_quantity' => 45,
                'category_id' => $categories->where('name', 'Giày Chạy Bộ')->first()->id,
                'is_active' => true,
                'is_featured' => false,
                'image' => 'images/product/New Balance 990v5.webp',
            ],
            [
                'name' => 'Adidas Predator Edge',
                'description' => 'Giày bóng đá với công nghệ Demonskin. Thiết kế aggresive, phù hợp cho tiền đạo.',
                'price' => 4100000,
                'sale_price' => null,
                'stock_quantity' => 20,
                'category_id' => $categories->where('name', 'Giày Bóng Đá')->first()->id,
                'is_active' => true,
                'is_featured' => false,
                'image' => 'images/product/Adidas Predator Edge.jpg',
            ],
            [
                'name' => 'Jimmy Choo Anouk',
                'description' => 'Giày cao gót cao cấp với thiết kế tinh tế. Chất liệu da cao cấp, phù hợp cho các dịp đặc biệt.',
                'price' => 12000000,
                'sale_price' => 10000000,
                'stock_quantity' => 8,
                'category_id' => $categories->where('name', 'Giày Cao Gót')->first()->id,
                'is_active' => true,
                'is_featured' => true,
                'image' => 'images/product/Jimmy Choo Anouk.avif',
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'description' => $productData['description'],
                'price' => $productData['price'],
                'sale_price' => $productData['sale_price'],
                'sku' => strtoupper(Str::random(8)),
                'stock_quantity' => $productData['stock_quantity'],
                'is_active' => $productData['is_active'],
                'is_featured' => $productData['is_featured'],
                'category_id' => $productData['category_id'],
                'image' => $productData['image'],
            ]);

            // Tạo ảnh chính từ field image
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $productData['image'],
                'is_primary' => true,
            ]);

            // Tạo thêm 1-2 ảnh phụ từ thư mục shoes
            $additionalImages = [
                'images/shoes/doi-giay-dat-nhat.jpg',
                'images/shoes/giay-dat-nhat-the-gioi.jpg',
            ];
            
            foreach ($additionalImages as $image) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $image,
                    'is_primary' => false,
                ]);
            }
        }

        $this->command->info('Đã tạo ' . count($products) . ' sản phẩm mẫu thành công!');
    }
}