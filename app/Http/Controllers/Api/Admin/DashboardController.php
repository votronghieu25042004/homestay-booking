<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getStatistics(Request $request)
    {
        // 1. Lấy khoảng thời gian (Mặc định là năm hiện tại nếu không truyền vào)
        $year = $request->input('year', date('Y'));
        
        // 2. Tính Tổng Booking (Chỉ lấy những đơn đã xác nhận hoặc hoàn thành)
        $totalBookings = Booking::whereYear('created_at', $year)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->count();

        // 3. Tính Tổng Doanh Thu
        $totalRevenue = Booking::whereYear('created_at', $year)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->sum('total_amount');

        // 4. Tính Tỷ Lệ Phòng Trống (Tính trung bình theo tháng hiện tại)
        // Cách tính đơn giản: (Tổng số phòng - Số phòng đang được đặt) / Tổng số phòng
        $totalRooms = Room::count();
        $currentlyBookedRooms = Booking::where('status', 'checked_in')
            ->distinct('room_id')
            ->count();
            
        $vacancyRate = $totalRooms > 0 
            ? round((($totalRooms - $currentlyBookedRooms) / $totalRooms) * 100, 2) 
            : 0;

        // 5. Thống Kê Doanh Thu Theo Từng Tháng (Dùng cho Biểu Đồ Bar Chart)
        $monthlyRevenue = Booking::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->whereYear('created_at', $year)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Định dạng lại mảng doanh thu để trả về cho Frontend dễ dùng (Ví dụ: [Tháng 1: 0, Tháng 2: 500k...])
        $revenueData = array_fill(1, 12, 0); // Tạo mảng 12 tháng với giá trị 0
        foreach ($monthlyRevenue as $data) {
            $revenueData[$data->month] = (float) $data->revenue;
        }

        // 6. Trả về kết quả JSON
        return response()->json([
            'status' => 'success',
            'data' => [
                'overview' => [
                    'total_bookings' => $totalBookings,
                    'total_revenue' => $totalRevenue,
                    'vacancy_rate' => $vacancyRate . '%',
                    'total_reviews' => Review::whereYear('created_at', $year)->count(),
                    'avg_rating' => round(Review::whereYear('created_at', $year)->avg('rating') ?: 0, 1),
                ],
                'chart' => [
                    'labels' => ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                    'data' => array_values($revenueData)
                ]
            ]
        ]);
    }
}