import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import './index.css';

// ===== DỌN DẸP localStorage BỊ HỎNG KHI KHỞI ĐỘNG =====
['user_info', 'auth_token', 'isLoggedIn'].forEach(key => {
  if (localStorage.getItem(key) === 'undefined') {
    localStorage.removeItem(key);
  }
});

// --- CÁC TRANG CỦA KHÁCH ---
import Home from './pages/Home.vue';
import Login from './pages/Login.vue';
import Register from './pages/Register.vue';
import ForgotPassword from './pages/ForgotPassword.vue';
import Listing from './pages/Listing.vue';
import RoomDetail from './pages/RoomDetail.vue';
import Payment from './pages/Payment.vue';
import PaymentSuccess from './pages/PaymentSuccess.vue';
import Profile from './pages/Profile.vue';
import AboutUs from './pages/AboutUs.vue';
import Rewards from './pages/Rewards.vue';

// --- CÁC TRANG CỦA ADMIN ---
import AdminLayout from './pages/admin/AdminLayout.vue';
import AdminUsers from './pages/admin/AdminUsers.vue';
import AdminRooms from './pages/admin/AdminRooms.vue';
import AdminRoomForm from './pages/admin/AdminRoomForm.vue';
import AdminBookings from './pages/admin/AdminBookings.vue';
import AdminInvoices from './pages/admin/AdminInvoices.vue';
import AdminDashboard from './pages/admin/AdminDashboard.vue';
import AdminVouchers from './pages/admin/AdminVouchers.vue';
import AdminReviews from './pages/admin/AdminReviews.vue';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', component: Home },
    { path: '/login', component: Login },
    { path: '/register', component: Register },
    { path: '/forgot-password', component: ForgotPassword },
    { path: '/listing', component: Listing },
    { path: '/room/:id', component: RoomDetail },
    { path: '/payment', component: Payment },
    { path: '/payment-success', component: PaymentSuccess },
    { path: '/profile', component: Profile },
    { path: '/about', component: AboutUs },
    { path: '/rewards', component: Rewards },

    // --- Route Admin ---
    {
      path: '/admin',
      component: AdminLayout,
      children: [
        {
          path: '/admin/dashboard',
          name: 'AdminDashboard',
          component: AdminDashboard
        },
        { path: 'rooms', component: AdminRooms },
        { path: 'users', component: AdminUsers },
        { path: 'rooms/create', component: AdminRoomForm },
        { path: 'rooms/edit/:id', component: AdminRoomForm },
        { path: 'booking-management', component: AdminBookings },
        // Fallback cho các thông báo cũ
        { path: 'booking-details/:id', redirect: '/admin/booking-management' },
        { path: 'bookings', redirect: '/admin/booking-management' },
        { path: 'invoices', component: AdminInvoices },
        { path: 'vouchers', component: AdminVouchers },
        { path: 'reviews', component: AdminReviews },
      ]
    }
  ]
});

// ==========================
// NAVIGATION GUARD BẢO VỆ ADMIN
// ==========================

router.beforeEach(async (to, from, next) => {
  const userInfo = localStorage.getItem('user_info');
  let user: any = null;
  try {
    user = (userInfo && userInfo !== 'undefined') ? JSON.parse(userInfo) : null;
  } catch {
    localStorage.removeItem('user_info'); // Xóa data hỏng
    user = null;
  }

  // 1. Kiểm tra quyền truy cập Admin trước
  if (to.path.startsWith('/admin')) {
    if (!user) {
      alert('Vui lòng đăng nhập tài khoản Quản trị viên!');
      return next('/login');
    }
    if (user.role !== 'admin') {
      alert('Cảnh báo: Bạn không có quyền truy cập khu vực này!');
      return next('/');
    }
  }

  // 2. Lính tuần tra ngầm (Chỉ chạy khi đã đăng nhập và không phải đang ở trang login)
  if (user && to.path !== '/login') {
    try {
      const res = await fetch('/api/check-status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: user.email })
      });

      if (res.status === 401) {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_info');
        alert('Cảnh báo: Tài khoản của bạn đã bị khóa vĩnh viễn!');
        return window.location.href = '/login';
      } else if (res.ok) {
        const data = await res.json();
        if (data.user && data.user.role !== user.role) {
          localStorage.setItem('user_info', JSON.stringify(data.user));
          alert('Quyền truy cập của bạn vừa bị thay đổi. Hệ thống sẽ tải lại!');
          return window.location.reload();
        }
      }
    } catch (e) {
      console.error("Lỗi tuần tra:", e);
    }
  }

  next();
});

// --- BẮT ĐẦU: LÍNH GÁC NGẦM BẮT LỖI 401 ---
const originalFetch = window.fetch;
window.fetch = async (...args) => {
  const requestInfo = args[0] as any;
  const url = typeof requestInfo === 'string' ? requestInfo : (requestInfo?.url || requestInfo?.href || '');

  const response = await originalFetch(...args);

  if (response.status === 401 && !url.includes('login')) {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_info');
    alert('Tài khoản của bạn đã bị Admin khóa hoặc phiên đăng nhập hết hạn!');
    window.location.href = '/login';
  }

  return response;
};

// --- KHỞI TẠO APP ---
const app = createApp(App);
app.use(router);
app.mount('#app');