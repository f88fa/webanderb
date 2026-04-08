<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;

class MenuItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التحقق من وجود عناصر القائمة الأساسية
        $existingItems = MenuItem::whereIn('url', ['#home', '#about', '#news', '#contact', '/board-members', '/policies'])->count();
        
        if ($existingItems > 0) {
            $this->command->info('عناصر القائمة موجودة بالفعل. تم تخطي الإضافة.');
            return;
        }

        // إضافة عناصر القائمة الأساسية
        $menuItems = [
            [
                'title' => 'الرئيسية',
                'type' => 'link',
                'url' => '#home',
                'parent_id' => null,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'من نحن',
                'type' => 'link',
                'url' => '#about',
                'parent_id' => null,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'مجلس الإدارة',
                'type' => 'link',
                'url' => '/board-members',
                'parent_id' => null,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'اللوائح والسياسات',
                'type' => 'link',
                'url' => '/policies',
                'parent_id' => null,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'الأخبار',
                'type' => 'link',
                'url' => '#news',
                'parent_id' => null,
                'order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'اتصل بنا',
                'type' => 'link',
                'url' => '#contact',
                'parent_id' => null,
                'order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($menuItems as $item) {
            MenuItem::create($item);
        }

        $this->command->info('تم إضافة عناصر القائمة بنجاح!');
    }
}
