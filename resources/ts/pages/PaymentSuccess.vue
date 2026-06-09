<template>
  <div class="bg-[#FAF9F5] min-h-screen flex flex-col font-['Inter']">
    <div class="flex-grow flex items-center justify-center py-10">
      <div class="max-w-lg w-full mx-auto px-4">

        <!-- Loading -->
        <div v-if="isLoading" class="bg-white rounded-3xl shadow-sm p-12 text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#4A7055] mx-auto mb-4"></div>
          <p class="text-gray-500">Đang tải thông tin...</p>
        </div>

        <!-- Error -->
        <div v-else-if="errorMessage" class="bg-white rounded-3xl shadow-sm p-12 text-center">
          <p class="text-5xl mb-4">❌</p>
          <h1 class="text-2xl font-bold text-gray-900 mb-2">Giao dịch thất bại</h1>
          <p class="text-red-500 mb-6">{{ errorMessage }}</p>
          <button @click="$router.push('/')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-bold">Về trang chủ</button>
        </div>

        <div v-else-if="booking">

          <!-- ============================================= -->
          <!-- BƯỚC 1: MÀN HÌNH QR (khi pending + chưa xác nhận) -->
          <!-- ============================================= -->
          <div v-if="booking.status === 'pending' && !hasConfirmedTransfer">

            <!-- Header -->
            <div class="bg-gradient-to-br from-[#4A7055] to-[#2d4a37] rounded-t-3xl p-7 text-center text-white">
              <div class="text-5xl mb-3">🏦</div>
              <h1 class="text-2xl font-bold font-['Playfair_Display'] mb-1">Chuyển khoản đặt cọc</h1>
              <p class="text-green-100 text-sm mb-4">Phòng đã được giữ chỗ! Hoàn tất cọc 30% để xác nhận đặt phòng.</p>
              <div class="inline-flex items-center gap-2 bg-white/15 rounded-2xl px-5 py-2 border border-white/20">
                <span class="text-green-200 text-xs">Mã đặt phòng</span>
                <span class="text-white font-mono font-bold tracking-widest text-sm">{{ booking.booking_code }}</span>
              </div>
            </div>

            <!-- Body QR -->
            <div class="bg-white rounded-b-3xl shadow-sm border border-t-0 border-gray-100 p-6 flex flex-col items-center">

              <!-- QR Image -->
              <div class="bg-white p-3 rounded-2xl shadow-lg border-2 border-[#4A7055]/20 inline-block mb-5 hover:scale-105 transition-transform">
                <img
                  :src="`https://img.vietqr.io/image/MB-0934998341-compact2.png?amount=${booking.deposit_amount}&addInfo=${encodeURIComponent(booking.booking_code)}&accountName=VO%20VAN%20THANH`"
                  alt="VietQR thanh toán cọc"
                  class="w-52 h-52 object-contain"
                />
              </div>

              <!-- Thông tin chuyển khoản -->
              <div class="w-full rounded-2xl border border-gray-100 overflow-hidden text-sm mb-4">
                <div class="flex justify-between items-center px-4 py-3 bg-gray-50 border-b border-gray-100">
                  <span class="text-gray-500">Ngân hàng</span>
                  <span class="font-bold text-gray-900">🏦 MB Bank</span>
                </div>
                <div class="flex justify-between items-center px-4 py-3 border-b border-gray-100">
                  <span class="text-gray-500">Số tài khoản</span>
                  <span class="font-bold text-gray-900 tracking-wider">0934998341</span>
                </div>
                <div class="flex justify-between items-center px-4 py-3 bg-gray-50 border-b border-gray-100">
                  <span class="text-gray-500">Chủ tài khoản</span>
                  <span class="font-bold text-gray-900">VO VAN THANH</span>
                </div>
                <div class="flex justify-between items-center px-4 py-3 border-b border-gray-100">
                  <span class="text-gray-500">Số tiền cọc (30%)</span>
                  <span class="font-black text-[#4A7055] text-lg">{{ formatMoney(booking.deposit_amount) }}</span>
                </div>
                <div class="flex justify-between items-center px-4 py-3 bg-yellow-50">
                  <span class="text-gray-500">Nội dung CK</span>
                  <code class="font-bold bg-yellow-200 border border-yellow-400 px-3 py-1 rounded-lg tracking-wider text-xs text-gray-900">{{ booking.booking_code }}</code>
                </div>
              </div>

              <!-- Cảnh báo nội dung CK -->
              <div class="w-full bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-600 text-center mb-6">
                ⚠️ Nhập <strong>đúng nội dung CK</strong> để hệ thống nhận diện tự động. Không thay đổi!
              </div>

              <!-- TRẠNG THÁI TỰ ĐỘNG CHỜ XÁC NHẬN -->
              <div class="w-full bg-[#4A7055]/5 border border-[#4A7055]/20 rounded-2xl p-5 text-center">
                <div class="flex items-center justify-center gap-3 mb-2">
                  <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-[#4A7055]"></div>
                  <span class="font-semibold text-[#4A7055]">Đang chờ xác nhận thanh toán...</span>
                </div>
                <p class="text-gray-500 text-xs">Sau khi bạn chuyển khoản xong, Admin sẽ xác nhận và trang sẽ <strong>tự động</strong> chuyển sang hóa đơn của bạn.</p>
                <p class="text-gray-400 text-xs mt-2">Tự động kiểm tra sau mỗi 5 giây · Đã kiểm tra {{ pollCount }} lần</p>
              </div>
            </div>
          </div>

          <!-- ============================================= -->
          <!-- BƯỚC 2: HÓA ĐƠN (sau khi xác nhận hoặc đã confirmed) -->
          <!-- ============================================= -->
          <div v-else class="space-y-4">

            <!-- Header hóa đơn -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
              <div :class="booking.status === 'pending' ? 'bg-orange-50 border-orange-100' : 'bg-[#4A7055]/5 border-[#4A7055]/10'" class="p-6 text-center border-b">
                <div class="text-5xl mb-2">{{ booking.status === 'pending' ? '⏳' : '✅' }}</div>
                <h1 class="text-xl font-bold text-gray-900 font-['Playfair_Display'] mb-1">
                  {{ booking.status === 'pending' ? 'Chờ Admin xác nhận' : 'Thanh toán thành công!' }}
                </h1>
                <p class="text-sm text-gray-500">
                  {{ booking.status === 'pending'
                    ? 'Chúng tôi sẽ xác nhận đơn của bạn ngay khi nhận được tiền chuyển khoản.'
                    : 'Đơn đặt phòng của bạn đã được xác nhận.' }}
                </p>
                <div class="mt-3 inline-block bg-gray-100 rounded-xl px-4 py-1.5">
                  <span class="font-mono font-bold text-gray-700 tracking-wider text-sm">{{ booking.booking_code }}</span>
                </div>
              </div>

              <!-- Nav: Chỉ có nút xem lịch sử + in hóa đơn -->
              <div class="px-5 py-3 border-b border-gray-100 flex justify-between items-center">
                <button @click="$router.push('/profile?tab=history')" class="flex items-center gap-2 text-[#4A7055] hover:text-[#3b5a44] font-semibold text-sm transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  Xem lịch sử đặt phòng
                </button>
                <button @click="handlePrint" class="bg-[#4A7055] text-white px-4 py-1.5 rounded-lg font-bold flex items-center gap-1.5 hover:bg-[#3b5a44] transition-all text-sm print:hidden">
                  <Printer class="w-3.5 h-3.5" /> In hóa đơn
                </button>
              </div>

              <!-- Thông tin đặt phòng -->
              <div class="p-5">
                <div class="flex gap-4 mb-5 pb-5 border-b border-gray-100">
                  <img :src="roomImage" alt="Room" class="w-24 h-20 object-cover rounded-xl shrink-0 border border-gray-100" referrerpolicy="no-referrer" />
                  <div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ booking.room_name }}</h3>
                    <p class="text-sm text-gray-500">{{ formatDate(booking.check_in_date) }} → {{ formatDate(booking.check_out_date) }}</p>
                    <p class="text-sm text-gray-500">{{ getNights(booking.check_in_date, booking.check_out_date) }} đêm · {{ booking.adults }} người lớn<span v-if="booking.children > 0">, {{ booking.children }} trẻ em</span></p>
                    <p class="text-sm font-semibold text-[#4A7055] mt-1">{{ booking.customer_name }}</p>
                  </div>
                </div>

                <!-- Bảng tiền -->
                <div class="space-y-2.5 text-sm">
                  <div class="flex justify-between text-gray-500">
                    <span>Tổng tiền phòng</span>
                    <span>{{ formatMoney(booking.total_amount) }}</span>
                  </div>
                  <div class="flex justify-between text-[#4A7055] font-semibold">
                    <span>Tiền cọc đã chuyển khoản (30%)</span>
                    <span>{{ formatMoney(booking.deposit_amount) }}</span>
                  </div>
                  <div class="flex justify-between pt-3 border-t-2 border-gray-200 font-bold text-gray-900 text-base">
                    <span>Còn lại (trả khi nhận phòng)</span>
                    <span class="text-[#4A7055]">{{ formatMoney(booking.total_amount - booking.deposit_amount) }}</span>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div class="bg-gray-50 px-5 py-4 text-center text-xs text-gray-400 border-t border-gray-100">
                Cảm ơn bạn đã tin tưởng <strong>Duy Homestay</strong> 🏡 · Hóa đơn tự động · Có giá trị tại quầy khi nhận phòng
              </div>
            </div>

            <!-- Hướng dẫn kiểm tra trạng thái -->
            <div v-if="booking.status === 'pending'" class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-sm text-blue-700 flex items-start gap-3 print:hidden">
              <span class="text-blue-500 text-lg mt-0.5">💡</span>
              <div>
                <p class="font-bold mb-1">Kiểm tra trạng thái xác nhận</p>
                <p>Vào <strong>Lịch sử đặt phòng</strong> để xem Admin đã duyệt đơn của bạn chưa. Khi được duyệt, trạng thái sẽ đổi thành <strong>"Đã xác nhận"</strong>.</p>
                <button @click="$router.push('/profile?tab=history')" class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-xl font-bold text-xs hover:bg-blue-700 transition-all">
                  📋 Xem lịch sử đặt phòng →
                </button>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { Printer, CheckCircle } from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();
const booking = ref<any>(null);
const isLoading = ref(true);
const errorMessage = ref('');
const hasConfirmedTransfer = ref(false);
const pollCount = ref(0);
let pollTimer: ReturnType<typeof setInterval> | null = null;

const roomImage = computed(() => {
  if (!booking.value?.room?.images?.length) return 'https://picsum.photos/seed/room/800/600';
  const pImg = booking.value.room.images.find((img: any) => img.is_primary) || booking.value.room.images[0];
  let url = pImg.image_url;
  if (url && !url.startsWith('http') && !url.startsWith('/storage/')) {
    url = url.startsWith('/') ? `/storage${url}` : `/storage/${url}`;
  }
  return url;
});

const formatMoney = (amount: number | string) => {
  if (!amount) return '0đ';
  return Number(amount).toLocaleString('vi-VN') + 'đ';
};
const formatDate = (d: string) => d ? new Date(d).toLocaleDateString('vi-VN') : '';
const getNights = (start: string, end: string) => {
  if (!start || !end) return 1;
  return Math.ceil(Math.abs(new Date(end).getTime() - new Date(start).getTime()) / 86400000);
};
const handlePrint = () => window.print();

// Lấy thông tin booking
async function fetchBooking(bookingId: string | string[]) {
  const res = await fetch(`/api/admin/bookings/${bookingId}`, {
    headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}`, 'Accept': 'application/json' }
  });
  if (!res.ok) throw new Error();
  const data = await res.json();
  return data.data;
}

// Polling: tự động kiểm tra mỗi 5 giây
function startPolling(bookingId: string | string[]) {
  pollTimer = setInterval(async () => {
    try {
      pollCount.value++;
      const data = await fetchBooking(bookingId);
      booking.value = data;

      // Nếu Admin đã xác nhận → tự chuyển sang màn hóa đơn
      if (data.status !== 'pending') {
        hasConfirmedTransfer.value = true;
        stopPolling();
      }
    } catch {
      // Bỏ qua lỗi mạng tạm thời, tiếp tục poll
    }
  }, 5000);
}

function stopPolling() {
  if (pollTimer) {
    clearInterval(pollTimer);
    pollTimer = null;
  }
}

onMounted(async () => {
  const status = route.query.status as string;
  const bookingId = route.query.booking_id || route.query.id;

  if (status === 'failed') { errorMessage.value = "Thanh toán thất bại hoặc bị hủy."; isLoading.value = false; return; }
  if (status === 'invalid_signature') { errorMessage.value = "Dữ liệu thanh toán không hợp lệ."; isLoading.value = false; return; }
  if (!bookingId) { errorMessage.value = "Không tìm thấy mã đơn hàng."; isLoading.value = false; return; }

  try {
    const data = await fetchBooking(bookingId as string);
    booking.value = data;

    if (data.status !== 'pending') {
      // Đơn đã confirmed (VNPay hoặc đã duyệt) → bỏ qua bước QR
      hasConfirmedTransfer.value = true;
    } else {
      // Đơn đang pending → bắt đầu polling để chờ Admin xác nhận
      startPolling(bookingId as string);
    }
  } catch {
    errorMessage.value = "Không thể tải thông tin đặt phòng. Kiểm tra lại trong Lịch sử đặt phòng.";
  } finally {
    isLoading.value = false;
  }
});

// Dừng polling khi thoát trang
onUnmounted(() => {
  stopPolling();
});
</script>

<style scoped>
@media print {
  @page { margin: 0; size: A4; }
  body { background: white !important; }
  .print\:hidden { display: none !important; }
}
</style>