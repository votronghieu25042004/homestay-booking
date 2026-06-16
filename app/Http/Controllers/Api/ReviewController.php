<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Booking;
use App\Http\Resources\ReviewResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Lấy danh sách đánh giá của một phòng.
     */
    public function index($roomId)
    {
        $reviews = Review::where('room_id', $roomId)
            ->where('is_hidden', false)
            ->with(['user:id,name', 'room'])
            ->latest()
            ->paginate(10);

        return ReviewResource::collection($reviews);
    }

    /**
     * Gửi đánh giá mới.
     * Rate limiting được cấu hình ở Route (throttle:5,1).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking = Booking::findOrFail($request->booking_id);

        // Kiểm tra quyền sở hữu (customer_id là cột lưu ID người dùng trong bảng bookings)
        if ((int)$booking->customer_id !== (int)auth()->id()) {
            return response()->json(['message' => 'Bạn không có quyền đánh giá đơn đặt phòng này.'], 403);
        }

        // Chỉ cho phép đánh giá khi đã hoàn tất (status = checked_out)
        if ($booking->status !== 'checked_out') {
            return response()->json(['message' => 'Bạn chỉ có thể đánh giá sau khi đã hoàn tất kỳ nghỉ (Check-out).'], 403);
        }

        // Kiểm tra xem đã đánh giá chưa (mỗi booking chỉ 1 lần)
        if (Review::where('booking_id', $booking->id)->exists()) {
            return response()->json(['message' => 'Bạn đã gửi đánh giá cho lần đặt phòng này rồi.'], 400);
        }

        // Xử lý dữ liệu đầu vào (Scrub HTML tags)
        $comment = $request->comment ? strip_tags($request->comment) : null;

        $review = Review::create([
            'user_id' => Auth::id(),
            'room_id' => $booking->room_id,
            'booking_id' => $booking->id,
            'rating' => $request->rating,
            'comment' => $comment,
            'is_hidden' => false,
        ]);

        // Thông báo cho tất cả Admin
        try {
            $adminUsers = \App\Models\User::where('role', 'admin')->get();
            $review->load(['user', 'room']);
            \Illuminate\Support\Facades\Notification::send($adminUsers, new \App\Notifications\NewReviewAdmin($review));
        } catch (\Exception $e) {
            \Log::error("Thông báo admin về review mới thất bại: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'Cảm ơn bạn đã gửi đánh giá!',
            'review' => new ReviewResource($review)
        ], 201);
    }

    /**
     * Danh sách đánh giá cho Admin quản lý.
     */
    public function adminIndex()
    {
        $reviews = Review::with(['user:id,name,email', 'room:id,title'])
            ->latest()
            ->paginate(20);

        return ReviewResource::collection($reviews);
    }

    /**
     * Admin ẩn/hiện đánh giá.
     */
    public function toggleVisibility($id)
    {
        $review = Review::findOrFail($id);
        $review->is_hidden = !$review->is_hidden;
        $review->save();

        return response()->json([
            'message' => $review->is_hidden ? 'Đã ẩn đánh giá.' : 'Đã hiển thị đánh giá.',
            'review' => new ReviewResource($review)
        ]);
    }

    /**
     * Admin xóa đánh giá.
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Đã xóa đánh giá thành công.']);
    }
}
