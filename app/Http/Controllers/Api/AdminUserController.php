<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    // 1. LẤY DANH SÁCH & TÌM KIẾM
    public function index(Request $request)
    {
        $query = User::query();
        
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
        }

        return response()->json($query->orderBy('id', 'desc')->get());
    }

    // 2. KHÓA / MỞ KHÓA TÀI KHOẢN
   public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Không thể khóa tài khoản Admin!'], 403);
        }

        $user->status = $user->status === 'active' ? 'blocked' : 'active';
        $user->save();

        // THÊM ĐOẠN NÀY LÀ CHÍ MẠNG NHẤT:
        // Nếu khóa tài khoản, lập tức tiêu hủy toàn bộ vé đăng nhập (Token) của tên này!
        if ($user->status === 'blocked') {
            $user->tokens()->delete();
        }

        return response()->json(['message' => 'Đã cập nhật trạng thái!', 'user' => $user]);
    }

    // 3. THÊM NGƯỜI DÙNG MỚI
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active'
        ]);
        return response()->json(['message' => 'Thêm thành công!', 'user' => $user]);
    }

    // 4. SỬA THÔNG TIN
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$id,
            'phone' => 'required|string|unique:users,phone,'.$id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role = $request->role;
        // Nếu có nhập pass mới thì đổi, không thì thôi
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json(['message' => 'Cập nhật thành công!']);
    }

    // 5. XÓA TÀI KHOẢN
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Không thể xóa Admin!'], 403);
        }
        $user->delete();
        return response()->json(['message' => 'Đã xóa người dùng!']);
    }
}