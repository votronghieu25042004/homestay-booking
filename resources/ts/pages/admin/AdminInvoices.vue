<template>
  <div class="p-6 relative">
    
    <div class="mb-6 space-y-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Quản lý Hóa đơn</h1>
        <p class="text-gray-500 text-sm mt-1">Lịch sử hóa đơn và các giao dịch thanh toán</p>
      </div>

      <div class="flex items-center gap-3 w-full max-w-md">
        <div class="relative flex-1">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Tìm mã HĐ, khách hàng..." 
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all shadow-sm"
          >
          <div class="absolute left-3 top-2.5 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>
        <button 
          @click="fetchBookings" 
          class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-100 transition-colors shadow-sm border border-emerald-100"
          title="Tải lại"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
      <table class="w-full text-left border-collapse min-w-[1000px]">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
            <th class="p-4 font-semibold whitespace-nowrap">Mã HĐ</th>
            <th class="p-4 font-semibold min-w-[150px]">Khách hàng</th>
            <th class="p-4 font-semibold min-w-[150px]">Phòng</th>
            <th class="p-4 font-semibold whitespace-nowrap">Tổng tiền</th>
            <th class="p-4 font-semibold whitespace-nowrap">Tiền cọc (30%)</th>
            <th class="p-4 font-semibold whitespace-nowrap text-center">Trạng thái</th>
            <th class="p-4 font-semibold whitespace-nowrap text-center">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="booking in bookings" :key="booking.id" class="border-b border-gray-100 hover:bg-gray-50 text-sm transition-colors">
            
            <td class="p-4 font-bold text-emerald-700 whitespace-nowrap">{{ booking.booking_code }}</td>
            
            <td class="p-4">
              <div class="font-medium text-gray-900 truncate max-w-[150px]" :title="booking.customer_name">{{ booking.customer_name }}</div>
              <div class="text-xs text-gray-500 truncate max-w-[150px]" :title="booking.customer_email">{{ booking.customer_email }}</div>
            </td>
            
            <td class="p-4 font-medium text-gray-700">
              <div class="truncate max-w-[150px]" :title="booking.room_name">{{ booking.room_name }}</div>
            </td>
            
            <td class="p-4 font-semibold text-gray-900 whitespace-nowrap">{{ Number(booking.total_price).toLocaleString('vi-VN') }}đ</td>
            <td class="p-4 font-semibold text-orange-600 whitespace-nowrap">{{ Number(booking.deposit_amount).toLocaleString('vi-VN') }}đ</td>
            
            <td class="p-4 text-center whitespace-nowrap">
              <span v-if="booking.payment_status === 'deposited'" class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-bold border border-amber-200 inline-block">
                Đã đặt cọc
              </span>
              <span v-else-if="booking.payment_status === 'completed'" class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold border border-emerald-200 inline-block">
                Đã thanh toán đủ
              </span>
            </td>
            
            <td class="p-4 text-center whitespace-nowrap">
              <div class="flex items-center justify-center gap-2">
                <button 
                  @click="openInvoiceModal(booking)" 
                  class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-100 transition-colors whitespace-nowrap"
                >
                  {{ booking.payment_status === 'deposited' ? 'Xem Phiếu Cọc' : 'Xem Hóa Đơn' }}
                </button>

                <button 
                  @click="deleteBooking(booking.id)" 
                  class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-100 transition-colors whitespace-nowrap"
                >
                  Xóa
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="bookings.length === 0">
            <td colspan="7" class="p-8 text-center text-gray-500">
              {{ searchQuery ? 'Không tìm thấy hóa đơn nào phù hợp.' : 'Chưa có hóa đơn nào trong hệ thống.' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="isModalOpen && selectedBooking" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-100">
        
        <div :class="selectedBooking.payment_status === 'deposited' ? 'bg-amber-600' : 'bg-emerald-800'" class="p-6 text-white flex justify-between items-start">
          <div>
            <h2 class="text-xl font-bold mb-1">
              {{ selectedBooking.payment_status === 'deposited' ? 'BIÊN LAI THU TIỀN CỌC' : 'HÓA ĐƠN THANH TOÁN CHI TIẾT' }}
            </h2>
            <p class="text-white/80 text-sm font-mono">Mã số: {{ selectedBooking.booking_code }}</p>
          </div>
          <button @click="closeInvoiceModal" class="text-white/80 hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="p-6 space-y-6">
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <p class="text-gray-500 mb-1">Khách hàng</p>
              <p class="font-bold text-gray-900">{{ selectedBooking.customer_name }}</p>
            </div>
            <div>
              <p class="text-gray-500 mb-1">Số điện thoại</p>
              <p class="font-bold text-gray-900">{{ selectedBooking.customer_phone }}</p>
            </div>
            <div>
              <p class="text-gray-500 mb-1">Thời gian tạo</p>
              <p class="font-bold text-gray-900">{{ selectedBooking.time_vn }}</p>
            </div>
            
            <div>
              <p class="text-gray-500 mb-1">Thanh toán qua</p>
              <p class="font-bold text-blue-700 bg-blue-50 inline-block px-2 py-0.5 rounded border border-blue-100">
                {{ selectedBooking.payment_method === 'ewallet' ? '📱 Ví điện tử' : '🏦 Chuyển khoản' }}
              </p>
            </div>
          </div>

          <div class="border-t border-dashed border-gray-300 pt-6">
            <div class="flex justify-between items-center mb-4">
              <p class="text-gray-500 text-sm">Phòng đã đặt</p>
              <p class="font-bold text-gray-900">{{ selectedBooking.room_name }}</p>
            </div>
          </div>

          <!-- CẤU TRÚC HÓA ĐƠN CHI TIẾT TƯƠNG TỰ CHI TIẾT BOOKING -->
          <div class="bg-gray-50 rounded-xl p-4 space-y-3 border border-gray-100">
            <template v-if="selectedBooking.payment_status === 'deposited'">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Tổng giá trị đơn phòng:</span>
                <span class="font-medium text-gray-900">{{ Number(selectedBooking.total_price).toLocaleString('vi-VN') }}đ</span>
              </div>
              <div class="flex justify-between text-sm pt-3 border-t border-gray-200">
                <span class="font-bold text-gray-900">SỐ TIỀN ĐÃ THU (CỌC 30%):</span>
                <span class="font-extrabold text-amber-600 text-lg">
                  {{ Number(selectedBooking.deposit_amount).toLocaleString('vi-VN') }}đ
                </span>
              </div>
              <p class="text-xs text-center text-amber-600 mt-2 italic">Khách hàng chưa Check-out, Hóa đơn có thể phát sinh thêm.</p>
            </template>

            <template v-else>
              <table class="w-full text-sm">
                <tbody>
                  <tr class="border-b border-gray-200">
                    <td class="py-2 text-gray-600">Phòng ({{ nightsCount(selectedBooking.check_in_date, selectedBooking.check_out_date) }} đêm × {{ Number(selectedBooking.room?.price || 0).toLocaleString('vi-VN') }}đ)</td>
                    <td class="py-2 text-right font-semibold text-gray-900">{{ Number((selectedBooking.room?.price || 0) * nightsCount(selectedBooking.check_in_date, selectedBooking.check_out_date)).toLocaleString('vi-VN') }}đ</td>
                  </tr>
                  
                  <tr v-for="svc in selectedBooking.services" :key="svc.id" class="border-b border-gray-200">
                    <td class="py-2 text-gray-600 pl-4">+ {{ svc.service_name }} × {{ svc.quantity }}
                      <span v-if="svc.is_paid" class="ml-2 text-[10px] text-white bg-green-500 px-1.5 py-0.5 rounded-full inline-block">Đã thu tiền</span>
                    </td>
                    <td class="py-2 text-right">
                      <span v-if="svc.is_paid" class="line-through text-gray-400 font-medium">{{ Number(svc.total_price).toLocaleString('vi-VN') }}đ</span>
                      <span v-else class="font-semibold text-gray-900">{{ Number(svc.total_price).toLocaleString('vi-VN') }}đ</span>
                    </td>
                  </tr>

                  <tr v-if="selectedBooking.discount_amount > 0" class="border-b border-gray-200 text-green-600">
                    <td class="py-2 pl-4">Khuyến mãi / Giảm giá</td>
                    <td class="py-2 text-right font-semibold">-{{ Number(selectedBooking.discount_amount).toLocaleString('vi-VN') }}đ</td>
                  </tr>

                  <tr class="font-bold">
                    <td class="py-3 text-gray-900">TỔNG CỘNG</td>
                    <td class="py-3 text-right text-emerald-700 text-lg">{{ Number(selectedBooking.total_price).toLocaleString('vi-VN') }}đ</td>
                  </tr>

                  <tr class="text-gray-600">
                    <td class="py-2">Đã thanh toán (Cọc + Thu trực tiếp)</td>
                    <td class="py-2 text-right font-semibold">{{ Number(selectedBooking.deposit_amount).toLocaleString('vi-VN') }}đ</td>
                  </tr>
                </tbody>
              </table>

              <div v-if="(selectedBooking.total_price - selectedBooking.deposit_amount) > 0" class="flex justify-between text-sm pt-3 border-t border-gray-200">
                <span class="font-bold text-red-600">SỐ TIỀN CÒN NỢ:</span>
                <span class="font-extrabold text-red-600 text-lg">
                  {{ Number(selectedBooking.total_price - selectedBooking.deposit_amount).toLocaleString('vi-VN') }}đ
                </span>
              </div>
              <p v-else class="text-xs text-center text-emerald-600 mt-4 font-bold bg-emerald-50 py-2 rounded-lg">✓ KHÁCH ĐÃ THANH TOÁN ĐỦ 100%</p>
            </template>
          </div>
        </div>

        <div class="p-6 bg-gray-50 border-t border-gray-100 flex gap-3">
          <button @click="closeInvoiceModal" class="flex-1 py-2.5 px-4 bg-white border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors">
            Đóng
          </button>
          <button 
            @click="printInvoice"
            class="flex-1 py-2.5 px-4 bg-gray-800 text-white rounded-xl font-medium hover:bg-gray-900 transition-colors flex items-center justify-center gap-2"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            In {{ selectedBooking.payment_status === 'deposited' ? 'Phiếu Cọc' : 'Hóa Đơn' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';

const bookings = ref<any[]>([]);
const isModalOpen = ref(false);
const selectedBooking = ref<any>(null);
const searchQuery = ref('');
let searchTimer: any = null;

const openInvoiceModal = (booking: any) => {
  selectedBooking.value = booking;
  isModalOpen.value = true;
};

const closeInvoiceModal = () => {
  isModalOpen.value = false;
  selectedBooking.value = null;
};

const fetchBookings = async () => {
  try {
    const url = searchQuery.value 
      ? `/api/admin/invoices?search=${encodeURIComponent(searchQuery.value)}`
      : '/api/admin/invoices';
    const response = await fetch(url);
    if (response.ok) {
      bookings.value = await response.json();
    }
  } catch (error) {
    console.error('Lỗi tải hóa đơn:', error);
  }
};

watch(searchQuery, () => {
  if (searchTimer) clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    fetchBookings();
  }, 500);
});

const printInvoice = () => {
  window.print();
};

const deleteBooking = async (id: number) => {
  if (confirm('Xóa hóa đơn này vĩnh viễn?')) {
    try {
      const response = await fetch(`/api/admin/invoices/${id}`, { method: 'DELETE' });
      if (response.ok) {
        fetchBookings();
      }
    } catch (error) {
      alert('Lỗi thao tác!');
    }
  }
};

onMounted(() => {
  fetchBookings();
});

function nightsCount(ci:string,co:string){
  if(!ci||!co) return 0;
  const [cy,cm,cd]=ci.slice(0,10).split('-').map(Number);
  const [oy,om,od]=co.slice(0,10).split('-').map(Number);
  const d1=new Date(cy,cm-1,cd), d2=new Date(oy,om-1,od);
  return Math.max(1, Math.round((d2.getTime()-d1.getTime())/86400000));
}
</script>