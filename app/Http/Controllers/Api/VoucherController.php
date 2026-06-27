<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\User;

class VoucherController extends Controller
{
    // Lấy danh sách Voucher của User (dành cho Payment.vue và Rewards.vue)
    public function myVouchers(Request $request)
    {
        $user = $request->user();
        
        // Lấy tất cả voucher chưa sử dụng và còn hạn hoặc không có hạn
        $vouchers = $user->vouchers()
            ->wherePivot('is_used', false)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->where('is_active', true)
            ->get()
            ->map(function($voucher) {
                return [
                    'id' => $voucher->id,
                    'code' => $voucher->code,
                    'title' => $voucher->title,
                    'description' => $voucher->description,
                    'discount_type' => $voucher->discount_type,
                    'discount_value' => $voucher->discount_value,
                    'expires_at' => $voucher->expires_at ? $voucher->expires_at->format('d/m/Y') : 'Vĩnh viễn',
                ];
            });

        return response()->json($vouchers);
    }

    // Lấy danh sách tất cả Voucher có thể đổi bằng Điểm
    public function redeemable(Request $request)
    {
        $vouchers = Voucher::whereNotNull('points_required')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->get();
            
        return response()->json([
            'points' => $request->user()->points,
            'vouchers' => $vouchers
        ]);
    }

    // Đổi điểm lấy Voucher
    public function redeem(Request $request, $id)
    {
        $user = $request->user();
        $voucher = Voucher::findOrFail($id);

        if (!$voucher->points_required) {
            return response()->json(['message' => 'Voucher này không thể đổi bằng điểm.'], 400);
        }

        if ($user->points < $voucher->points_required) {
            return response()->json(['message' => 'Bạn không đủ điểm để đổi mã này.'], 400);
        }

        // Kiểm tra xem đã sở hữu chưa
        if ($user->vouchers()->where('voucher_id', $voucher->id)->wherePivot('is_used', false)->exists()) {
            return response()->json(['message' => 'Bạn đã đổi mã này rồi và chưa sử dụng.'], 400);
        }

        // Trừ điểm và thêm voucher
        $user->decrement('points', $voucher->points_required);
        $user->vouchers()->attach($voucher->id);

        return response()->json(['message' => 'Đổi điểm thành công! Mã đã được lưu vào ví.']);
    }

    // Lưu mã từ Banner (Claim)
    public function claim(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $user = $request->user();
        $voucher = Voucher::where('code', $request->code)
            ->where('is_active', true)
            ->first();

        if (!$voucher) {
            return response()->json(['message' => 'Mã không tồn tại hoặc đã hết hạn.'], 404);
        }

        // Kiểm tra xem đã sở hữu chưa
        if ($user->vouchers()->where('voucher_id', $voucher->id)->exists()) {
            return response()->json(['message' => 'Bạn đã lưu mã này rồi!'], 400);
        }

        $user->vouchers()->attach($voucher->id);

        return response()->json(['message' => 'Lưu mã thành công!']);
    }

    // API Vòng quay may mắn
    public function spin(Request $request)
    {
        $user = $request->user();
        
        // Mỗi ngày được quay 1 lần (Có thể lưu lại timestamp, tạm thời bỏ qua check ngày)
        // Lấy ngẫu nhiên 1 voucher có points_required là null, hoặc tạo một mã ngẫu nhiên

        $vouchers = Voucher::where('is_active', true)
            ->inRandomOrder()
            ->take(3)
            ->get();

        if ($vouchers->isEmpty()) {
            return response()->json(['message' => 'Chưa có giải thưởng. Cứ thử lại sau nhé!'], 400);
        }

        $wonVoucher = $vouchers->first();

        // Kiểm tra xem đã có chưa
        if (!$user->vouchers()->where('voucher_id', $wonVoucher->id)->wherePivot('is_used', false)->exists()) {
            $user->vouchers()->attach($wonVoucher->id);
            return response()->json([
                'message' => 'Chúc mừng! Bạn quay trúng mã: ' . $wonVoucher->code,
                'voucher' => $wonVoucher
            ]);
        }

        return response()->json([
            'message' => 'Bạn trúng ' . $wonVoucher->code . ' nhưng đã có mã này rồi. Lần sau may mắn nhé!'
        ]);
    }
}
