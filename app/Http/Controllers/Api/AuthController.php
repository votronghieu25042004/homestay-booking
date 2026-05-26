<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Voucher;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // HÀM ĐĂNG KÝ
    public function register(Request $request)
    {
        // Thêm "Bộ lọc thép" Regex và tùy chỉnh câu báo lỗi bằng tiếng Việt
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i'],
            'phone' => ['required', 'string', 'unique:users', 'regex:/^(0|\+84)[3|5|7|8|9][0-9]{8}$/'],
            'password' => 'required|string|min:6',
        ], [

            'email.regex' => 'Hệ thống hiện chỉ hỗ trợ đăng ký bằng đuôi @gmail.com!',
            'phone.regex' => 'Số điện thoại không hợp lệ (Phải là số Việt Nam, VD: 09..., 03... và đủ 10 số)!',
            'email.unique' => 'Email này đã có người sử dụng!',
            'phone.unique' => 'Số điện thoại này đã có người sử dụng!',
        ]);
        // kiểm tra xem có lỗi không
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $referredById = null;
        if ($request->has('ref_code')) {
            $referrer = User::where('referral_code', $request->ref_code)->first();
            if ($referrer) {
                $referredById = $referrer->id;
                // Thưởng điểm cho người giới thiệu (ví dụ 10 điểm = 100K VND mua voucher)
                $referrer->increment('points', 10);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'status' => 'active',
            'referral_code' => strtoupper(Str::random(8)),
            'referred_by_id' => $referredById
        ]);

        // Tặng Voucher Tân Thủ
        $welcomeVoucher = Voucher::where('code', 'WELCOME100')->first();
        if ($welcomeVoucher) {
            $user->vouchers()->attach($welcomeVoucher->id);
        }

        // Nếu có người giới thiệu, tặng mã "Bạn Bè" cho user mới (Tuỳ chọn thêm)
        if ($referredById) {
            $refVoucher = Voucher::where('code', 'REF50')->first();
            if ($refVoucher) {
                $user->vouchers()->attach($refVoucher->id);
            }
        }

        return response()->json(['message' => 'Đăng ký thành công!'], 201);
    }

    // HÀM ĐĂNG NHẬP
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i'],
            'password' => 'required',
        ], [
            'email.regex' => 'Chỉ hỗ trợ đăng nhập bằng tài khoản @gmail.com!'
        ]);

        $user = User::where('email', $request->email)->first();

        // Kiểm tra xem email có tồn tại và mật khẩu có khớp không
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email hoặc mật khẩu không chính xác!'], 401);
        }

        // KIỂM TRA XEM TÀI KHOẢN CÓ BỊ ADMIN KHÓA KHÔNG
        if ($user->status === 'blocked') {
            return response()->json(['message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin!'], 403);
        }

        // Tạo mã Token (vé thông hành) cho khách
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'access_token' => $token,
            'user' => new UserResource($user)
        ]);
    }
    // HÀM KIỂM TRA TRẠNG THÁI NGẦM
    public function checkStatus(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        // Nếu không tìm thấy user hoặc user đã bị khóa
        if (!$user || $user->status === 'blocked') {
            return response()->json(['message' => 'Bị khóa'], 401);
        }

        // TRẢ VỀ THÊM THÔNG TIN USER MỚI NHẤT ĐỂ VUE ĐỐI CHIẾU
        return response()->json([
            'message' => 'An toàn',
            'user' => new UserResource($user)
        ]);
    }
    // HÀM CẬP NHẬT PROFILE
    public function updateProfile(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng!'], 404);
        }

        // Kiểm tra nếu tài khoản bị khóa
        if ($user->status === 'blocked') {
            return response()->json([
                'message' => 'Tài khoản của bạn đã bị khóa!'
            ], 401);
        }

        // Cập nhật tên
        $user->name = $request->name;
        $user->save();

        return response()->json([
            'message' => 'Cập nhật tên thành công!',
            'user' => new UserResource($user)
        ]);
    }

    // HÀM ĐĂNG XUẤT
    public function logout(Request $request)
    {
        // Hủy bỏ token hiện tại của user
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công!'
        ]);
    }
}