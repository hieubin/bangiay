<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Giày Sneaker',
                'description' => 'Giày thể thao thời trang, phù hợp cho mọi hoạt động hàng ngày',
                'is_active' => true,
            ],
            [
                'name' => 'Giày Chạy Bộ',
                'description' => 'Giày chuyên dụng cho chạy bộ và tập luyện thể thao',
                'is_active' => true,
            ],
            [
                'name' => 'Giày Bóng Đá',
                'description' => 'Giày chuyên dụng cho bóng đá, có đế đinh và chống trượt',
                'is_active' => true,
            ],
            [
                'name' => 'Giày Cao Gót',
                'description' => 'Giày cao gót thời trang cho phụ nữ',
                'is_active' => true,
            ],
            [
                'name' => 'Giày Lười',
                'description' => 'Giày lười thoải mái, dễ mang và tháo',
                'is_active' => true,
            ],
            [
                'name' => 'Giày Boot',
                'description' => 'Giày boot cao cổ, phù hợp cho mùa đông',
                'is_active' => true,
            ],
            [
                'name' => 'Giày Sandal',
                'description' => 'Giày sandal mở, thoáng mát cho mùa hè',
                'is_active' => true,
            ],
            [
                'name' => 'Giày Oxford',
                'description' => 'Giày Oxford cổ điển, phù hợp cho công sở',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            $category['slug'] = Str::slug($category['name']);
            Category::create($category);
        }
    }
}