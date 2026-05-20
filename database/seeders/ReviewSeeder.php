<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\User;
use App\Models\Booking;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        // 1. Tạo hoặc lấy một số user giả
        $users = User::limit(5)->get();
        if ($users->count() < 5) {
            for ($i = 0; $i < 5; $i++) {
                $users->push(User::create([
                    'name' => 'Khách hàng ' . ($i + 1),
                    'email' => 'khach' . ($i + 1) . time() . '@example.com',
                    'password' => Hash::make('password'),
                    'role' => 'user'
                ]));
            }
        }

        $comments = [
            'Phòng rất sạch sẽ và thoáng mát. Chắc chắn sẽ quay lại!',
            'View đẹp, nhân viên nhiệt tình, hỗ trợ rất tốt.',
            'Chỗ ở tiện nghi, gần trung tâm, dễ dàng di chuyển.',
            'Giá cả hợp lý so với chất lượng. Rất hài lòng.',
            'Không gian yên tĩnh, thích hợp để nghỉ ngơi thư giãn.',
            'Mọi thứ đều tuyệt vời, từ phòng ốc đến dịch vụ.',
            'Phòng đẹp giống như hình, trang trí rất có gu.',
            'Điểm trừ là cách âm chưa tốt lắm, nhưng tổng thể thì oke.',
            'Giường rất êm, mình đã có những giấc ngủ ngon.',
            'Gia đình mình rất thích kỳ nghỉ ở đây. Cảm ơn host!'
        ];

        $rooms = Room::all();

        foreach ($rooms as $room) {
            // Random 3 - 4 reviews per room
            $numReviews = rand(3, 4);

            for ($i = 0; $i < $numReviews; $i++) {
                $user = $users->random();
                $checkInDate = Carbon::now()->subDays(rand(10, 100));
                $checkOutDate = $checkInDate->copy()->addDays(rand(1, 3));

                // Tạo Booking giả (đã checked out)
                $booking = Booking::create([
                    'booking_code' => 'BK-' . date('Y') . strtoupper(Str::random(6)),
                    'customer_id' => $user->id,
                    'customer_name' => $user->name,
                    'customer_email' => $user->email,
                    'customer_phone' => '090' . rand(1000000, 9999999),
                    'room_id' => $room->id,
                    'room_name' => $room->title,
                    'check_in_date' => $checkInDate,
                    'check_out_date' => $checkOutDate,
                    'adults' => 2,
                    'children' => 0,
                    'status' => 'checked_out',
                    'source' => 'website',
                    'total_price' => $room->price * 2,
                    'total_amount' => $room->price * 2,
                    'subtotal' => $room->price * 2,
                    'deposit_amount' => $room->price,
                    'paid_amount' => $room->price * 2,
                    'payment_status' => 'paid',
                ]);

                // Tạo Review cho Booking này
                Review::create([
                    'user_id' => $user->id,
                    'room_id' => $room->id,
                    'booking_id' => $booking->id,
                    'rating' => rand(4, 5), // Rate 4 hoặc 5 sao cho đẹp
                    'comment' => $comments[array_rand($comments)],
                    'is_hidden' => false,
                    'created_at' => $checkOutDate->copy()->addHours(rand(1, 48)),
                    'updated_at' => $checkOutDate->copy()->addHours(rand(1, 48)),
                ]);
            }
        }
    }
}

