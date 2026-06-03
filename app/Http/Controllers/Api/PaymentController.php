<?php

namespace App\Http\Controllers\Api; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking; // Import Model Booking để tương tác Database

class PaymentController extends Controller
{
    // ===================================================================
    // 1. HÀM TẠO LINK THANH TOÁN (Gửi cho VNPay)
    // ===================================================================
    public function createPayment(Request $request)
    {
        $vnp_TmnCode = env('VNPAY_TMN_CODE');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $vnp_Url = env('VNPAY_URL');
        $vnp_Returnurl = env('VNPAY_RETURN_URL');

        $vnp_TxnRef = $request->input('booking_id', time()); 
        $vnp_Amount = $request->input('amount') * 100; 
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip(); 

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => "Thanh toan don dat phong: " . $vnp_TxnRef,
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;

        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Đoạn return này giữ nguyên để trả về link cho Vue.js chuyển trang
        return response()->json([
            'status' => 'success',
            'payment_url' => $vnp_Url
        ]);
    }

    // ===================================================================
    // 2. HÀM HỨNG KẾT QUẢ TỪ VNPAY VÀ CẬP NHẬT DATABASE
    // ===================================================================
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $inputData = array();
        
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $bookingId = $request->input('vnp_TxnRef'); // Lấy mã booking do VNPay trả về
        
        // Link Frontend Vue.js của bạn để hiển thị thông báo
        $frontendUrl = 'http://127.0.0.1:8000/payment-success'; 

        // Kiểm tra chữ ký bảo mật
        if ($secureHash == $vnp_SecureHash) {
            // Tìm đơn đặt phòng trong Database
            $booking = Booking::find($bookingId);

            if ($booking) {
                // Nếu VNPay trả về mã 00 nghĩa là Khách đã quẹt thẻ thành công
                if ($request->input('vnp_ResponseCode') == '00') {
                    
                    // CẬP NHẬT TRẠNG THÁI THANH TOÁN
                    $booking->status = 1; 
                    $booking->save();

                    // CHUYỂN HƯỚNG VỀ VUE KÈM TRẠNG THÁI SUCCESS
                    return redirect($frontendUrl . '?status=success&booking_id=' . $bookingId);
                } else {
                    // CẬP NHẬT TRẠNG THÁI THẤT BẠI (Khách hủy hoặc thẻ hết tiền)
                    $booking->status = 'FAILED'; 
                    $booking->save();
                    
                    // CHUYỂN HƯỚNG VỀ VUE KÈM TRẠNG THÁI FAILED
                    return redirect($frontendUrl . '?status=failed&booking_id=' . $bookingId);
                }
            } else {
                 return redirect($frontendUrl . '?status=error&message=BookingNotFound');
            }
        } else {
            return redirect($frontendUrl . '?status=invalid_signature');
        }
    }
}