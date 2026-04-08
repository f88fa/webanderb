<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $admin = User::where('email', 'admin@example.com')->first();
        
        if (!$admin) {
            User::create([
                'name' => 'المدير',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('تم إنشاء المستخدم الافتراضي بنجاح!');
            $this->command->info('البريد الإلكتروني: admin@example.com');
            $this->command->info('كلمة المرور: admin123');
        } else {
            $this->command->info('المستخدم الافتراضي موجود بالفعل!');
        }
    }
}
