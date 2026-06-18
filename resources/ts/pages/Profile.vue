<template>
  <div class="min-h-[calc(100vh-4rem-20rem)] bg-[#FAF9F5] pb-20">
    
    <div v-if="user">
      <div class="bg-white border-b border-gray-100 shadow-sm pt-8 pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div class="flex items-center gap-6">
              <div class="w-20 h-20 rounded-full bg-[#4A7055]/10 flex items-center justify-center text-[#4A7055] font-bold text-3xl shadow-sm border border-[#4A7055]/20">
                {{ user.name ? user.name.charAt(0).toUpperCase() : 'U' }}
              </div>
              <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1 font-['Playfair_Display']">{{ user.name }}</h1>
                <p class="text-gray-500 flex items-center gap-2 text-sm font-['Inter']">
                  <span class="w-2 h-2 rounded-full bg-[#4A7055]"></span>
                  Thành viên hệ thống
                </p>
              </div>
            </div>

            <button @click="handleLogout" class="text-red-500 font-medium px-6 py-2.5 border border-red-100 rounded-lg hover:bg-red-50 hover:border-red-200 transition-colors w-max font-['Inter'] shadow-sm">
              Đăng xuất tài khoản
            </button>
          </div>

          <div class="flex space-x-12 text-lg font-bold font-['Playfair_Display']">
            <button 
              @click="activeTab = 'saved'"
              :class="['pb-4 border-b-2 transition-colors', activeTab === 'saved' ? 'border-[#4A7055] text-[#4A7055]' : 'border-transparent text-gray-400 hover:text-[#4A7055]']"
            >
              Đã thích
            </button>
            <button 
              @click="activeTab = 'history'"
              :class="['pb-4 border-b-2 transition-colors', activeTab === 'history' ? 'border-[#4A7055] text-[#4A7055]' : 'border-transparent text-gray-400 hover:text-[#4A7055]']"
            >
              Lịch sử
            </button>
            <button 
              @click="activeTab = 'account'"
              :class="['pb-4 border-b-2 transition-colors', activeTab === 'account' ? 'border-[#4A7055] text-[#4A7055]' : 'border-transparent text-gray-400 hover:text-[#4A7055]']"
            >
              Thông tin tài khoản
            </button>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        
        <div v-if="activeTab === 'account'" class="max-w-3xl">
          <form class="space-y-8 bg-white p-10 rounded-3xl shadow-sm border border-gray-100 font-['Inter']" @submit.prevent="handleUpdateAccount">
            <div class="space-y-6">
              <div class="relative">
                <input type="text" id="fullname" v-model="accountInfo.name" class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] text-gray-900 placeholder-transparent" placeholder="Họ và Tên" />
                <label for="fullname" class="absolute left-0 -top-3.5 text-sm text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">Họ và Tên</label>
              </div>
              
              <div class="relative">
                <input type="email" id="email" v-model="accountInfo.email" readonly title="Email không thể thay đổi" class="peer w-full py-3 border-b border-gray-200 focus:outline-none text-gray-500 bg-gray-50 cursor-not-allowed placeholder-transparent" placeholder="Email" />
                <label for="email" class="absolute left-0 -top-3.5 text-sm text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">Email (Không thể thay đổi)</label>
              </div>

              <div class="relative">
                <input type="tel" id="phone" v-model="accountInfo.phone" readonly title="Số điện thoại không thể thay đổi" class="peer w-full py-3 border-b border-gray-200 focus:outline-none text-gray-500 bg-gray-50 cursor-not-allowed placeholder-transparent" placeholder="Số Điện Thoại" />
                <label for="phone" class="absolute left-0 -top-3.5 text-sm text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">Số Điện Thoại (Không thể thay đổi)</label>
              </div>

              <div class="relative">
                <input type="password" id="password" v-model="accountInfo.password" class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] text-gray-900 placeholder-transparent" placeholder="Mật Khẩu" />
                <label for="password" class="absolute left-0 -top-3.5 text-sm text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">Mật Khẩu (Để trống nếu không đổi)</label>
              </div>
            </div>

            <div class="pt-4">
              <button type="submit" class="bg-[#4A7055] hover:bg-[#3b5a44] text-white px-10 py-3 rounded-xl font-bold transition-colors shadow-md">
                Cập nhật thông tin
              </button>
            </div>
          </form>
        </div>

        <div v-if="activeTab === 'saved'">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <RoomCard
              v-for="(room, index) in savedRooms"
              :key="'saved-'+index"
              :id="room.id"
              :title="room.title"
              :location="room.location"
              :type="room.type"
              :price="room.price"
              :imageUrl="room.imageUrl"
            />
          </div>
        </div>

        <div v-if="activeTab === 'history'">
          <div v-if="loadingHistory" class="text-center py-20 text-[#4A7055]">
            <p>Đang tải lịch sử...</p>
          </div>
          <div v-else-if="bookings.length === 0" class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200">
            <p class="text-gray-400">Bạn chưa có lịch sử đặt phòng nào.</p>
          </div>
          <div v-else class="space-y-4 max-w-3xl font-['Inter']">
            <div v-for="booking in bookings" :key="booking.id"
              class="bg-white rounded-2xl shadow-sm border overflow-hidden hover:shadow-md transition-shadow"
              :class="{
                'border-orange-200': booking.status === 'pending',
                'border-red-200': booking.status === 'cancelled',
                'border-gray-100': !['pending','cancelled'].includes(booking.status)
              }">

              <!-- Banner trạng thái nổi bật -->
              <!-- Pending: chờ thanh toán -->
              <div v-if="booking.status === 'pending'" class="bg-orange-50 border-b border-orange-200 px-5 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="animate-spin rounded-full h-4 w-4 border-b-2 border-orange-500 inline-block"></span>
                  <span class="font-bold text-orange-700 text-sm">⏳ Chờ xác nhận thanh toán</span>
                </div>
                <router-link :to="`/payment-success?id=${booking.id}`" class="text-xs text-orange-600 underline font-semibold">Xem mã QR →</router-link>
              </div>
              <!-- Confirmed: đã duyệt -->
              <div v-else-if="booking.status === 'confirmed'" class="bg-blue-50 border-b border-blue-100 px-5 py-3">
                <span class="font-bold text-blue-700 text-sm">✅ Đã xác nhận — Chờ nhận phòng</span>
              </div>
              <!-- Checked in -->
              <div v-else-if="booking.status === 'checked_in'" class="bg-green-50 border-b border-green-100 px-5 py-3">
                <span class="font-bold text-green-700 text-sm">🏠 Đang ở tại Homestay</span>
              </div>
              <!-- Checked out -->
              <div v-else-if="booking.status === 'checked_out'" class="bg-gray-50 border-b border-gray-100 px-5 py-3">
                <span class="font-bold text-gray-500 text-sm">🏁 Đã hoàn thành chuyến ở</span>
              </div>
              <!-- Cancelled: bị hủy / từ chối -->
              <div v-else-if="booking.status === 'cancelled'" class="bg-red-50 border-b border-red-200 px-5 py-3">
                <p class="font-bold text-red-700 text-sm">❌ Đơn đặt phòng đã bị hủy</p>
                <p v-if="booking.cancel_reason" class="text-red-600 text-xs mt-1">
                  Lý do: <strong>{{ booking.cancel_reason }}</strong>
                </p>
                <p v-else class="text-red-500 text-xs mt-1">Liên hệ Homestay nếu cần hỗ trợ.</p>
              </div>

              <!-- Body card -->
              <div class="p-5 flex gap-4">
                <img
                  :src="booking.room?.images?.[0]?.image_url || 'https://picsum.photos/seed/room/800/600'"
                  class="w-24 h-20 object-cover rounded-xl shrink-0 border border-gray-100"
                  referrerpolicy="no-referrer"
                />
                <div class="flex-1 min-w-0">
                  <h4 class="font-bold text-gray-900 mb-1 truncate">{{ booking.room_name }}</h4>
                  <p class="text-gray-500 text-sm">{{ formatDate(booking.check_in_date) }} → {{ formatDate(booking.check_out_date) }}</p>
                  <p class="text-gray-500 text-sm">Tổng: <span class="font-semibold text-gray-700">{{ formatMoney(booking.total_amount) }}</span> · Cọc: <span class="font-semibold text-[#4A7055]">{{ formatMoney(booking.deposit_amount) }}</span></p>
                  <p class="text-gray-400 text-xs mt-1">Đặt lúc: {{ formatDate(booking.created_at) }}</p>
                </div>
              </div>

              <!-- Footer card: actions -->
              <div v-if="booking.status !== 'cancelled'" class="px-5 pb-4 flex items-center gap-4 flex-wrap">
                <router-link
                  :to="`/payment-success?id=${booking.id}`"
                  class="text-[#4A7055] font-bold text-sm hover:underline flex items-center gap-1"
                >
                  {{ booking.status === 'pending' ? 'Xem thông tin thanh toán' : 'Xem hóa đơn chi tiết' }}
                  <ChevronRight class="w-4 h-4" />
                </router-link>

                <!-- Nút Viết đánh giá (chỉ hiện sau khi trả phòng) -->
                <button
                  v-if="booking.status === 'checked_out'"
                  @click="openReview(booking)"
                  class="flex items-center gap-1.5 bg-amber-50 border border-amber-300 text-amber-700 font-bold text-sm px-4 py-1.5 rounded-xl hover:bg-amber-100 transition-colors"
                >
                  ⭐ Viết đánh giá
                </button>

                <button
                  v-if="['pending', 'confirmed', 'deposited'].includes(booking.status)"
                  @click="handleCancel(booking)"
                  class="text-red-400 hover:text-red-600 text-sm font-semibold transition-colors ml-auto"
                >
                  Hủy đặt phòng
                </button>
              </div>
            </div>
          </div>

          <!-- Modal Viết Đánh Giá -->
          <div v-if="showReviewModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showReviewModal = false">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8">
              <h2 class="text-xl font-bold text-gray-900 mb-1 font-['Playfair_Display']">⭐ Viết đánh giá</h2>
              <p class="text-gray-500 text-sm mb-5">Phòng: <strong>{{ reviewTarget?.room_name }}</strong></p>

              <!-- Chọn số sao -->
              <div class="flex justify-center gap-2 mb-5">
                <button
                  v-for="star in 5" :key="star"
                  @click="reviewForm.rating = star"
                  class="text-4xl transition-transform hover:scale-110"
                  :class="star <= reviewForm.rating ? 'text-amber-400' : 'text-gray-200'"
                >★</button>
              </div>

              <!-- Nội dung nhận xét -->
              <textarea
                v-model="reviewForm.comment"
                rows="4"
                placeholder="Chia sẻ trải nghiệm của bạn... (dịch vụ, vệ sinh, vị trí,...)"
                class="w-full border border-gray-200 rounded-2xl p-4 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#4A7055] mb-5"
              ></textarea>

              <div class="flex gap-3">
                <button @click="showReviewModal = false" class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl font-semibold hover:bg-gray-50 transition-colors">Hủy</button>
                <button @click="submitReview" :disabled="reviewForm.rating === 0 || isSubmittingReview"
                  class="flex-1 bg-[#4A7055] text-white py-3 rounded-xl font-bold hover:bg-[#3b5a44] disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  {{ isSubmittingReview ? 'Đang gửi...' : 'Gửi đánh giá' }}
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <div v-else class="min-h-screen flex items-center justify-center">
      <div class="animate-pulse flex flex-col items-center text-[#4A7055]">
        <div class="w-12 h-12 border-4 border-[#4A7055] border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="font-medium font-['Inter']">Đang tải thông tin...</p>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import RoomCard from '../components/RoomCard.vue';
import { ChevronRight } from 'lucide-vue-next';

// Khai báo Interface cho User
interface User {
  id: number;
  name: string;
  email: string;
  phone?: string | null;
  role?: string;
  status?: string;
}

const router = useRouter();
const route = useRoute();

// State
const activeTab = ref('account');
const user = ref<User | null>(null);
const bookings = ref<any[]>([]);
const loadingHistory = ref(false);
const savedRooms = ref<any[]>([]);

// Review Modal
const showReviewModal = ref(false);
const reviewTarget = ref<any>(null);
const isSubmittingReview = ref(false);
const reviewForm = ref({ rating: 0, comment: '' });

const openReview = (booking: any) => {
  reviewTarget.value = booking;
  reviewForm.value = { rating: 0, comment: '' };
  showReviewModal.value = true;
};

const submitReview = async () => {
  if (!reviewForm.value.rating || !reviewTarget.value) return;
  isSubmittingReview.value = true;
  try {
    const res = await fetch('/api/reviews', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        room_id: reviewTarget.value.room_id,
        booking_id: reviewTarget.value.id,
        rating: reviewForm.value.rating,
        comment: reviewForm.value.comment
      })
    });
    const data = await res.json();
    if (res.ok) {
      alert('✅ Cảm ơn bạn đã đánh giá! Nhận xét của bạn đã được ghi nhận.');
      showReviewModal.value = false;
    } else {
      alert('Lỗi: ' + (data.message || 'Không thể gửi đánh giá'));
    }
  } catch {
    alert('Lỗi kết nối, vui lòng thử lại!');
  } finally {
    isSubmittingReview.value = false;
  }
};

// Form thông tin
const accountInfo = ref({
  name: '',
  email: '',
  phone: '',
  password: ''
});

// Hàm lấy lịch sử đặt phòng
const fetchHistory = async () => {
  if (bookings.value.length === 0) {
    loadingHistory.value = true;
    try {
      const response = await axios.get('/api/my-bookings', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
      });
      bookings.value = response.data.data;
    } catch (err) {
      console.error('Failed to fetch bookings:', err);
    } finally {
      loadingHistory.value = false;
    }
  }
};

// Hàm lấy phòng đã lưu
const loadSavedRooms = () => {
  const data = localStorage.getItem('saved_rooms');
  if (data) {
    savedRooms.value = JSON.parse(data);
  } else {
    savedRooms.value = [];
  }
};

const getStatusLabel = (status: string) => {
  switch(status) {
    case 'pending': return 'Chờ xác nhận';
    case 'confirmed': return 'Đã xác nhận';
    case 'checked_in': return 'Đang ở';
    case 'checked_out': return 'Đã trả phòng';
    case 'cancelled': return 'Đã hủy';
    default: return status;
  }
};

const formatMoney = (amount: number | string) => {
  if (!amount) return '0đ';
  return Number(amount).toLocaleString('vi-VN') + 'đ';
};

const formatDate = (dateString: string) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('vi-VN');
};

// Hàm Đăng xuất
const handleLogout = () => {
  if(confirm('Bạn có chắc chắn muốn đăng xuất khỏi tài khoản này?')) {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_info');
    window.location.href = '/'; 
  }
};

// Hàm Hủy đặt phòng
const handleCancel = async (booking: any) => {
  const policyMessage = `
CHÍNH SÁCH HỦY PHÒNG:
- Hủy trước 3 ngày: Hoàn 100% tiền cọc.
- Hủy từ 1-3 ngày: Hoàn 50% tiền cọc.
- Hủy dưới 24h: Không hoàn cọc.

Bạn có chắc chắn muốn hủy đơn đặt phòng #${booking.booking_code}?
  `;

  if (!confirm(policyMessage)) return;

  const reason = prompt('Vui lòng nhập lý do hủy (không bắt buộc):') || 'Khách hàng tự hủy';

  try {
    const response = await fetch(`/api/bookings/${booking.id}/cancel`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      },
      body: JSON.stringify({ reason })
    });

    const data = await response.json();

    if (response.ok) {
      alert(`Hủy phòng thành công! Số tiền hoàn lại dự kiến: ${formatMoney(data.refund_amount)}`);
      // Cập nhật lại danh sách local thay vì fetch hết
      booking.status = 'cancelled';
      booking.status_label = 'Đã hủy';
    } else {
      alert('Lỗi: ' + data.message);
    }
  } catch (error) {
    console.error('Lỗi khi hủy phòng:', error);
    alert('Không thể kết nối đến máy chủ!');
  }
};

// Cập nhật thông tin
const handleUpdateAccount = async () => {
  if (!accountInfo.value.name.trim()) {
    alert('Tên không được để trống!');
    return;
  }

  try {
    const response = await fetch('/api/profile/update', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: accountInfo.value.email,
        name: accountInfo.value.name
      })
    });

    const data = await response.json();

    if (response.ok) {
      alert('Cập nhật thông tin thành công!');
      localStorage.setItem('user_info', JSON.stringify(data.user));
      user.value = data.user as User;
      window.location.reload();
    } else {
      alert('Lỗi: ' + data.message);
    }
  } catch (error) {
    console.error('Lỗi kết nối:', error);
    alert('Không thể kết nối đến máy chủ!');
  }
};

// Life cycle & Watchers
onMounted(() => {
  const userInfo = localStorage.getItem('user_info');
  
  if (userInfo) {
    user.value = JSON.parse(userInfo) as User;
    accountInfo.value.name = user.value.name;
    accountInfo.value.email = user.value.email;
    accountInfo.value.phone = user.value.phone || '';
    
    // Kiểm tra query parameter để chuyển tab (Hỗ trợ deep-linking)
    const tabParam = route.query.tab as string;
    if (tabParam && ['saved', 'history', 'account'].includes(tabParam)) {
      activeTab.value = tabParam;
    }
  } else {
    router.push('/login');
    return;
  }
  
  // Tải dữ liệu ban đầu dựa trên tab mặc định
  if (activeTab.value === 'history') fetchHistory();
  if (activeTab.value === 'saved') loadSavedRooms();
});

watch(activeTab, (newTab) => {
  if (newTab === 'saved') {
    loadSavedRooms();
  } else if (newTab === 'history') {
    fetchHistory();
  }
});
</script>