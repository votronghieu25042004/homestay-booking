<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Voucher::updateOrCreate(
            ['code' => 'WELCOME100'],
            [
                'title' => 'Voucher Tân Thủ',
                'description' => 'Chào mừng bạn đến với hệ thống! Giảm ngay 100K cho đơn hàng đầu tiên.',
                'discount_type' => 'fixed',
                'discount_value' => 100000,
                'is_active' => true,
            ]
        );

        \App\Models\Voucher::updateOrCreate(
            ['code' => 'REF50'],
            [
                'title' => 'Voucher Bạn Bè',
                'description' => 'Ưu đãi dành cho khách hàng được giới thiệu. Giảm 50K.',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'is_active' => true,
            ]
        );

        \App\Models\Voucher::updateOrCreate(
            ['code' => 'HE2024'],
            [
                'title' => 'Chào Hè 2024',
                'description' => 'Ưu đãi mùa hè rực rỡ. Giảm 15% tổng hóa đơn.',
                'discount_type' => 'percent',
                'discount_value' => 15,
                'is_active' => true,
            ]
        );

        // Voucher đổi bằng điểm
        \App\Models\Voucher::updateOrCreate(
            ['code' => 'REDEEM50'],
            [
                'title' => 'Voucher Đổi Thưởng 50K',
                'description' => 'Sử dụng 10 điểm để đổi voucher này.',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'points_required' => 10,
                'is_active' => true,
            ]
        );

        \App\Models\Voucher::updateOrCreate(
            ['code' => 'REDEEM100'],
            [
                'title' => 'Voucher Đổi Thưởng 100K',
                'description' => 'Sử dụng 18 điểm để đổi voucher này.',
                'discount_type' => 'fixed',
                'discount_value' => 100000,
                'points_required' => 18,
                'is_active' => true,
            ]
        );
    }
}
