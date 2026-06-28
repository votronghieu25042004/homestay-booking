<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $vouchers = \App\Models\Voucher::orderBy('id', 'desc')->paginate(10);
        return response()->json($vouchers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:vouchers,code|max:50',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'expires_at' => 'nullable|date',
            'max_uses' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $voucher = \App\Models\Voucher::create($validated);
        return response()->json(['message' => 'Tạo voucher thành công', 'data' => $voucher], 201);
    }

    public function update(Request $request, $id)
    {
        $voucher = \App\Models\Voucher::findOrFail($id);

        $validated = $request->validate([
            'code' => 'sometimes|required|string|max:50|unique:vouchers,code,' . $id,
            'discount_type' => 'sometimes|required|in:percent,fixed',
            'discount_value' => 'sometimes|required|numeric|min:0',
            'expires_at' => 'nullable|date',
            'max_uses' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $voucher->update($validated);
        return response()->json(['message' => 'Cập nhật voucher thành công', 'data' => $voucher]);
    }

    public function destroy($id)
    {
        $voucher = \App\Models\Voucher::findOrFail($id);
        $voucher->delete();
        return response()->json(['message' => 'Xóa voucher thành công']);
    }

    public function toggleStatus($id)
    {
        $voucher = \App\Models\Voucher::findOrFail($id);
        $voucher->update(['is_active' => !$voucher->is_active]);
        return response()->json([
            'message' => 'Cập nhật trạng thái thành công', 
            'is_active' => $voucher->is_active
        ]);
    }
}
