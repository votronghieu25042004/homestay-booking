<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Room;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\Admin\BookingController;
use App\Http\Controllers\Api\Admin\BookingPaymentController;
use App\Http\Controllers\Api\Admin\BookingServiceController;
use App\Http\Controllers\Api\Admin\RoomAvailabilityController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\Api\ReviewController;

Route::post('/payment/create', [PaymentController::class, 'createPayment']);
Route::get('/payment/vnpay-return', [PaymentController::class, 'vnpayReturn']);

// --- GHI ĐÈ API LẤY DANH SÁCH PHÒNG (TRẢ VỀ KÈM ẢNH) ---
Route::get('/rooms', function (Request $request) {
    $query = Room::with(['images', 'parentHomestay']);

    // Nếu không có param ?all=true (tức là người dùng thường) thì mới ẩn đi
    if (!$request->query('all')) {
        $query->where('is_visible', 1)
            ->where('status', '!=', 'hidden')
            ->where('rent_type', '!=', 'room_based');
    }

    // GỢI Ý THEO ĐÁNH GIÁ (Nếu có param ?sort=rating)
    if ($request->query('sort') === 'rating') {
        $query->withAvg(['reviews' => function($q) {
            $q->where('is_hidden', false);
        }], 'rating')
        ->orderBy('reviews_avg_rating', 'desc');
    } else {
        $query->orderBy('id', 'desc');
    }

    $rooms = $query->get()->map(function ($room) {
        // Ưu tiên hình ảnh của chính phòng đó
        $primaryImage = $room->images->where('is_primary', true)->first()
            ?? $room->images->first();

        // Nếu không có ảnh, lấy ảnh của Homestay cha (nếu là phòng riêng)
        if (!$primaryImage && $room->parentHomestay) {
            $primaryImage = $room->parentHomestay->images->where('is_primary', true)->first()
                ?? $room->parentHomestay->images->first();
        }

        return [
            'id' => $room->id,
            'title' => $room->title,
            'type' => $room->type,
            'rent_type' => $room->rent_type,
            'parent_id' => $room->parent_id,
            'parent_title' => $room->parentHomestay ? $room->parentHomestay->title : null,
            'price' => $room->price,
            'status' => $room->status,
            'is_visible' => $room->is_visible,
            'max_guests' => $room->max_guests,
            'max_children' => $room->max_children,
            'image' => $primaryImage ? $primaryImage->image_url : null,
            'average_rating' => $room->average_rating
        ];
    });

    return response()->json($rooms);
});

// Lấy danh sách địa điểm có phòng đang trống
Route::get('/locations/available', [RoomController::class, 'getAvailableLocations']);

// --- GHI ĐÈ API LẤY CHI TIẾT 1 PHÒNG (ĐỂ XEM CHI TIẾT KHÔNG BỊ LỖI) ---
Route::get('/rooms/{id}', function ($id) {
    $room = Room::find($id);

    if (!$room) {
        return response()->json(['message' => 'Không tìm thấy phòng'], 404);
    }

    // Lấy ảnh
    $room->images = DB::table('room_images')->where('room_id', $id)->get();

    // Lấy tiện nghi
    $room->amenity_list = DB::table('room_amenities')
        ->join('amenities', 'room_amenities.amenity_id', '=', 'amenities.id')
        ->where('room_amenities.room_id', $id)
        ->select('amenities.*')
        ->get();

    return response()->json($room);
});

// --- ĐÁNH GIÁ (REVIEWS) ---
Route::get('/rooms/{id}/reviews', [ReviewController::class, 'index']);
Route::middleware('auth:sanctum')->post('/reviews', [ReviewController::class, 'store'])->middleware('throttle:5,1');

// --- QUẢN LÝ PHÒNG ---
Route::post('/admin/rooms', [RoomController::class, 'store']);
Route::put('/admin/rooms/{id}', [RoomController::class, 'update']);
Route::delete('/admin/rooms/{id}', [RoomController::class, 'destroy']);

// --- AUTH & PROFILE ---
Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register'])->middleware('throttle:register');
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/profile/update', [App\Http\Controllers\Api\AuthController::class, 'updateProfile']);
Route::post('/check-status', [App\Http\Controllers\Api\AuthController::class, 'checkStatus']);
Route::middleware('auth:sanctum')->post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);

// --- QUẢN LÝ TIỆN NGHI ---
Route::get('/amenities', [App\Http\Controllers\Api\RoomController::class, 'getAmenities']);
Route::post('/amenities', [App\Http\Controllers\Api\RoomController::class, 'storeAmenity']);
Route::delete('/amenities/{id}', [App\Http\Controllers\Api\RoomController::class, 'deleteAmenity']);

// --- ĐẶT PHÒNG & HÓA ĐƠN ---
Route::get('/admin/invoices', [App\Http\Controllers\Api\AdminBookingController::class, 'index']);
Route::delete('/admin/invoices/{id}', [App\Http\Controllers\Api\AdminBookingController::class, 'destroy']);

// --- QUẢN LÝ NGƯỜI DÙNG (ADMIN) ---
Route::get('/admin/users', [AdminUserController::class, 'index']);
Route::post('/admin/users', [AdminUserController::class, 'store']);
Route::put('/admin/users/{id}', [AdminUserController::class, 'update']);
Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy']);
Route::put('/admin/users/{id}/status', [AdminUserController::class, 'toggleStatus']);

// --- QUẢN LÝ THỐNG KÊ ---
Route::get('/admin/rooms/stats', [App\Http\Controllers\Api\RoomController::class, 'stats']);

// --- USER HIỆN TẠI ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/my-bookings', [App\Http\Controllers\Api\BookingController::class, 'myHistory']);
    Route::post('/bookings', [App\Http\Controllers\Api\BookingController::class, 'store']);
    Route::post('/bookings/{id}/cancel', [App\Http\Controllers\Api\BookingController::class, 'cancel']);
    Route::get('/notifications', function (Request $request) {
        return $request->user()->notifications()->latest()->limit(20)->get();
    });
    Route::post('/notifications/mark-as-read', function (Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'Đã đánh dấu tất cả là đã đọc']);
    });
    Route::post('/notifications/{id}/mark-as-read', function (Request $request, $id) {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['message' => 'Đã đánh dấu thông báo là đã đọc']);
    });
});

// --- QUẢN LÝ VOUCHER & ĐIỂM THƯỞNG ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/vouchers/my-vouchers', [VoucherController::class, 'myVouchers']);
    Route::get('/vouchers/redeemable', [VoucherController::class, 'redeemable']);
    Route::post('/vouchers/redeem/{id}', [VoucherController::class, 'redeem']);
    Route::post('/vouchers/claim', [VoucherController::class, 'claim']);
    Route::post('/vouchers/spin', [VoucherController::class, 'spin']);
});

// ══════════════════════════════════════════
// QUẢN LÝ BOOKING – ADMIN
// ══════════════════════════════════════════
// ĐÃ SỬA: Thêm middleware auth:sanctum để bảo vệ toàn bộ các route admin trong này
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {

    // MỚI: API Thống kê đã được chuyển vào đây và được bảo vệ an toàn
    Route::get('/dashboard/statistics', [DashboardController::class, 'getStatistics']);

    // Stats & calendar
    Route::get('/bookings/stats', [BookingController::class, 'stats']);
    Route::get('/bookings/calendar', [BookingController::class, 'calendar']);

    // CRUD
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::patch('/bookings/{id}/status', [BookingController::class, 'changeStatus']);
    Route::post('/bookings/{id}/checkin', [BookingController::class, 'checkin']);
    Route::get('/bookings/{id}/id-card', [BookingController::class, 'viewIdCard']);
    Route::post('/bookings/{id}/checkout', [BookingController::class, 'checkout']);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
    Route::post('/bookings/{id}/confirm-payment', [BookingController::class, 'confirmPayment']);
    Route::post('/bookings/{id}/transfer-room', [BookingController::class, 'transferRoom']);

    // Services
    Route::post('/bookings/{id}/services', [BookingServiceController::class, 'store']);
    Route::delete('/bookings/{id}/services/{sid}', [BookingServiceController::class, 'destroy']);

    // Payments
    Route::get('/bookings/{id}/payments', [BookingPaymentController::class, 'index']);
    Route::post('/bookings/{id}/payments', [BookingPaymentController::class, 'store']);

    // Room availability & status
    Route::get('/rooms/available', [RoomAvailabilityController::class, 'available']);
    Route::get('/rooms/all-status', [RoomAvailabilityController::class, 'allRooms']);
    Route::patch('/rooms/{id}/status', [RoomAvailabilityController::class, 'updateStatus']);
    Route::patch('/rooms/{id}/toggle-maintenance', [RoomAvailabilityController::class, 'toggleMaintenance']);

    // Lấy danh sách homestay và chuyển đổi mô hình
    Route::get('/rooms/homestays', [RoomController::class, 'getHomestays']);
    Route::post('/rooms/{id}/convert-to-room-based', [RoomController::class, 'convertToRoomBased']);

    // Danh sách hiển thị riêng cho Admin (có phân cấp)
    Route::get('/rooms', [RoomController::class, 'adminIndex']);

    // MỚI: QUẢN LÝ VOUCHER CHO ADMIN
    Route::get('/vouchers', [AdminVoucherController::class, 'index']);
    Route::post('/vouchers', [AdminVoucherController::class, 'store']);
    Route::put('/vouchers/{id}', [AdminVoucherController::class, 'update']);
    Route::delete('/vouchers/{id}', [AdminVoucherController::class, 'destroy']);
    Route::patch('/vouchers/{id}/toggle-status', [AdminVoucherController::class, 'toggleStatus']);

    // Quản lý đánh giá (Reviews)
    Route::get('/reviews', [ReviewController::class, 'adminIndex']);
    Route::patch('/reviews/{id}/toggle-visibility', [ReviewController::class, 'toggleVisibility']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
});
