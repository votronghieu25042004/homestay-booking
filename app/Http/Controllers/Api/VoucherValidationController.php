<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VoucherValidationController extends Controller
{
    public function validateVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'booking_amount' => 'required|numeric|min:0',
        ]);

        $voucher = \App\Models\Voucher::where('code', $request->code)->first();

        if (!$voucher) {
            return response()->json(['message' => 'Mã giảm giá không tồn tại.', 'is_valid' => false], 404);
        }

        if (!$voucher->isValid()) {
            return response()->json(['message' => 'Mã giảm giá đã hết hạn, hết lượt sử dụng hoặc bị khóa.', 'is_valid' => false], 400);
        }

        $discountValue = $voucher->calculateDiscount($request->booking_amount);

        return response()->json([
            'is_valid' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'discount_amount' => $discountValue,
            'voucher_id' => $voucher->id,
            'discount_type' => $voucher->discount_type,
            'discount_value' => $voucher->discount_value
        ]);
    }
}
