<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingPaymentController extends Controller
{
    public function index($id)
    {
        return response()->json(Booking::findOrFail($id)->payments()->with('recordedBy')->get());
    }

    public function store(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $v = $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,transfer,card',
            'payment_type'   => 'required|in:deposit,balance,refund',
            'reference_code' => 'nullable|string',
            'note'           => 'nullable|string',
        ]);
        $payment = $booking->payments()->create([...$v,'recorded_by'=>auth('sanctum')->id()]);
        $v['payment_type']==='refund' ? $booking->decrement('paid_amount',(int)$v['amount']) : $booking->increment('paid_amount',(int)$v['amount']);
        $m = ['cash'=>'Tiền mặt','transfer'=>'Chuyển khoản','card'=>'Thẻ'];
        $t = ['deposit'=>'Cọc','balance'=>'Thanh toán','refund'=>'Hoàn tiền'];
        $booking->logActivity('payment_added',"{$t[$v['payment_type']]} ".number_format($v['amount'])."đ qua {$m[$v['payment_method']]}");
        return response()->json($payment->load('recordedBy'), 201);
    }
}
