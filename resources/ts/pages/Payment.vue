<template>
  <div class="bg-[#FAF9F5] flex flex-col min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-grow w-full font-['Inter']">
      <h1 class="text-3xl font-bold text-gray-900 mb-8 font-['Playfair_Display']">Xác nhận thông tin</h1>
      
      <div v-if="loading" class="text-center py-20 text-[#4A7055] font-medium animate-pulse">
        Đang tải thông tin thanh toán...
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2">
          <form @submit.prevent="handlePayment" class="space-y-8 bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <div class="space-y-6">
              <h2 class="text-xl font-bold text-gray-900">Thông tin liên hệ</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Tên *</label>
                  <input type="text" id="name" v-model="customerInfo.name" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-[#4A7055] focus:border-[#4A7055] outline-none transition-colors" />
                </div>
                <div>
                  <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Email *</label>
                  <input type="email" id="email" v-model="customerInfo.email" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-[#4A7055] focus:border-[#4A7055] outline-none transition-colors" />
                </div>
                <div class="md:col-span-2">
                  <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">Số điện thoại *</label>
                  <input type="tel" id="phone" v-model="customerInfo.phone" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-[#4A7055] focus:border-[#4A7055] outline-none transition-colors" />
                </div>
                <div class="md:col-span-2">
                  <label for="note" class="block text-sm font-bold text-gray-700 mb-2">Ghi chú</label>
                  <textarea id="note" v-model="customerInfo.note" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-[#4A7055] focus:border-[#4A7055] outline-none transition-colors"></textarea>
                </div>
              </div>
            </div>

            <div class="pt-8 border-t border-gray-100">
              <h2 class="text-xl font-bold text-gray-900 mb-6">Phương thức thanh toán</h2>
              <div class="space-y-4">
                <label :class="`flex items-center p-4 border-2 rounded-xl cursor-pointer transition-colors ${paymentMethod === 'bank' ? 'border-[#4A7055] bg-[#4A7055]/5' : 'border-gray-100 hover:bg-gray-50'}`">
                  <input 
                    type="radio" 
                    name="payment" 
                    value="bank" 
                    v-model="paymentMethod"
                    class="w-5 h-5 text-[#4A7055] focus:ring-[#4A7055]"
                  />
                  <span class="ml-3 font-medium text-gray-900">Chuyển khoản ngân hàng</span>
                </label>
                <label :class="`flex items-center p-4 border-2 rounded-xl cursor-pointer transition-colors ${paymentMethod === 'ewallet' ? 'border-[#4A7055] bg-[#4A7055]/5' : 'border-gray-100 hover:bg-gray-50'}`">
                  <input 
                    type="radio" 
                    name="payment" 
                    value="ewallet" 
                    v-model="paymentMethod"
                    class="w-5 h-5 text-[#4A7055] focus:ring-[#4A7055]"
                  />
                  <span class="ml-3 font-medium text-gray-900">Ví điện tử (Momo, ZaloPay, VNPay)</span>
                </label>
              </div>
            </div>

            <div class="pt-8 border-t border-gray-100">
              <button type="submit" :disabled="isSubmitting" class="w-full bg-[#4A7055] hover:bg-[#3b5a44] text-white py-4 rounded-xl font-bold text-lg transition-colors shadow-md disabled:bg-gray-400">
                {{ isSubmitting ? 'Đang xử lý...' : 'Xác nhận & Thanh toán cọc' }}
              </button>
            </div>
          </form>
        </div>

        <div class="lg:col-span-1">
          <div v-if="room" class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm sticky top-24">
            <div class="flex gap-4 mb-6 pb-6 border-b border-gray-100">
              <img :src="primaryImage" alt="Room" class="w-24 h-24 object-cover rounded-xl shadow-sm" referrerpolicy="no-referrer" />
              <div>
                <h3 class="font-bold text-gray-900 line-clamp-2 mb-1">{{ room.title }}</h3>
                <div class="text-sm text-[#4A7055] font-medium">{{ room.type === 'house' ? 'Nguyên căn' : 'Phòng riêng' }}</div>
              </div>
            </div>

            <h3 class="font-bold text-gray-900 mb-4 text-lg">Chi tiết đặt phòng</h3>
            
            <div class="space-y-4 mb-6 pb-6 border-b border-gray-100 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-500">Ngày nhận - trả</span>
                <span class="font-bold text-gray-900 text-right">{{ formatDate(checkIn) }} - {{ formatDate(checkOut) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Số lượng</span>
                <span class="font-bold text-gray-900 text-right">
                  {{ adults }} người lớn<span v-if="children > 0">, {{ children }} trẻ em</span>
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Thời gian lưu trú</span>
                <span class="font-bold text-[#4A7055] text-right">{{ numberOfNights }} đêm</span>
              </div>
            </div>

            <div class="space-y-4 mb-6 pb-6 border-b border-gray-100">
              <div class="flex justify-between">
                <span class="text-gray-500 underline">{{ formatPrice(room.price) }} x {{ numberOfNights }} đêm</span>
                <span class="font-medium text-gray-900">{{ formatPrice(originalPrice) }}</span>
              </div>
              <div class="flex justify-between" v-if="appliedVoucher">
                <span class="text-gray-500 underline">Voucher ({{ appliedVoucher.code }})</span>
                <span class="font-medium text-red-500">-{{ formatPrice(voucherDiscountAmount) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500 underline">Phí dịch vụ</span>
                <span class="font-medium text-gray-900">0đ</span>
              </div>
            </div>

            <!-- VOUCHER SECTION -->
            <div class="mb-6 pb-6 border-b border-gray-100">
              <h4 class="font-bold text-gray-900 mb-3 text-sm flex justify-between items-center">
                Mã giảm giá
                <button v-if="appliedVoucher" @click="removeVoucher" class="text-xs text-red-500 hover:underline font-normal">Hủy bỏ</button>
              </h4>
              <div class="flex gap-2">
                <input 
                  type="text" 
                  v-model="voucherCode" 
                  placeholder="Nhập mã voucher" 
                  class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-[#4A7055] focus:border-[#4A7055] outline-none text-sm uppercase placeholder-gray-400"
                />
                <button v-if="!voucherCode" @click.prevent="openVoucherModal" type="button" class="bg-[#4A7055]/10 hover:bg-[#4A7055]/20 text-[#4A7055] px-3 py-2 rounded-lg text-sm font-bold transition-colors whitespace-nowrap">
                  Mã lưu
                </button>
                <button v-else @click.prevent="applyVoucher" type="button" class="bg-[#4A7055] hover:bg-[#3b5a44] text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                  Áp dụng
                </button>
              </div>
              <p v-if="appliedVoucher" class="text-xs text-[#4A7055] mt-2 font-medium flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                Đã áp dụng mã thành công!
              </p>
              <p v-if="voucherError" class="text-xs text-red-500 mt-2 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ voucherError }}
              </p>
            </div>

            <div class="flex justify-between items-center">
              <span class="font-bold text-gray-900 text-xl">Tổng tiền</span>
              <div class="text-right">
                <div v-if="appliedVoucher" class="text-sm text-gray-400 line-through mb-1">{{ formatPrice(originalPrice) }}</div>
                <span class="font-extrabold text-[#4A7055] text-3xl">{{ formatPrice(totalPrice) }}</span>
              </div>
            </div>
            
            <div class="mt-4 bg-[#4A7055]/10 rounded-xl p-4 flex justify-between items-center">
              <div>
                <p class="text-sm text-gray-700 font-medium">Cọc trước (30%)</p>
                <p class="text-xs text-gray-500 mt-1">Thanh toán phần còn lại khi nhận phòng</p>
              </div>
              <p class="font-extrabold text-xl text-gray-900">{{ formatPrice(totalPrice * 0.3) }}</p>
            </div>

          </div>
          
          <div v-else class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm text-center text-gray-500">
            Đang tải thông tin phòng...
          </div>
        </div>
      </div>
    </div>
    
    <!-- Voucher Modal -->
    <div v-if="showVoucherModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 transition-opacity">
      <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden flex flex-col max-h-[85vh] shadow-2xl animate-in fade-in zoom-in-95 duration-200">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-white sticky top-0 z-10 w-full">
          <div>
            <h3 class="font-bold text-xl text-gray-900">Voucher của bạn</h3>
            <p class="text-sm text-gray-500 mt-1">Chọn mã để được giảm giá</p>
          </div>
          <button @click="showVoucherModal = false" class="text-gray-400 hover:text-gray-900 transition-colors bg-gray-50 p-2 rounded-full hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
        
        <div class="p-5 overflow-y-auto flex-1 space-y-4 bg-gray-50/50">
          <div v-if="savedVouchers.length === 0" class="text-center py-8 text-gray-500">
            Bạn chưa có voucher nào.
          </div>
          <div v-else v-for="v in savedVouchers" :key="v.code" 
               @click="selectVoucher(v)"
               class="bg-white border-2 border-dashed border-[#4A7055]/40 rounded-2xl p-4 cursor-pointer hover:border-[#4A7055] hover:bg-[#4A7055]/5 transition-all relative group shadow-sm">
            <div class="flex justify-between items-start mb-3">
              <div class="flex-1 pr-4">
                <span class="inline-block bg-[#4A7055] text-white text-xs font-bold px-2.5 py-1 rounded-md mb-2 uppercase tracking-wide">{{ v.code }}</span>
                <h4 class="font-bold text-gray-900 leading-tight">{{ v.title }}</h4>
              </div>
              <span class="text-[#4A7055] font-extrabold text-lg whitespace-nowrap bg-[#4A7055]/10 px-3 py-1 rounded-lg">
                {{ v.discount_type === 'percent' ? v.discount_value + '%' : formatPrice(Number(v.discount_value)) }}
              </span>
            </div>
            <p class="text-xs text-gray-500 flex items-center gap-1.5">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              HSD: {{ v.expires_at }}
            </p>
            
            <!-- Hover Overlay -->
            <div class="absolute inset-0 hidden group-hover:flex items-center justify-center bg-white/70 rounded-2xl backdrop-blur-[2px]">
              <span class="bg-[#4A7055] text-white px-6 py-2 rounded-xl text-sm font-bold shadow-lg transform scale-95 group-hover:scale-100 transition-transform">Dùng ngay</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';

// Định nghĩa Interface
interface RoomData {
  id: number | string;
  title: string;
  type: string;
  price: number;
  images?: any[];
}

const route = useRoute();
const router = useRouter();

// State
const loading = ref(true);
const isSubmitting = ref(false);
const room = ref<RoomData | null>(null);
const primaryImage = ref('https://picsum.photos/seed/room/800/600');

// Lấy thông tin từ URL Query
const roomId = route.query.roomId as string;
const checkIn = ref(route.query.checkIn as string);
const checkOut = ref(route.query.checkOut as string);
const adults = ref(Number(route.query.adults) || 1);
const children = ref(Number(route.query.children) || 0);

const paymentMethod = ref('bank');
const customerInfo = ref({
  name: '',
  email: '',
  phone: '',
  note: ''
});

// Tính số đêm
const numberOfNights = computed(() => {
  if (!checkIn.value || !checkOut.value) return 1;
  const start = new Date(checkIn.value);
  const end = new Date(checkOut.value);
  const diffTime = Math.abs(end.getTime() - start.getTime());
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
  return diffDays > 0 ? diffDays : 1;
});

// Tính nguyên giá (chưa giảm)
const originalPrice = computed(() => {
  if (!room.value) return 0;
  return room.value.price * numberOfNights.value;
});

// Tính số tiền giảm của Voucher
const voucherDiscountAmount = computed(() => {
  if (!appliedVoucher.value) return 0;
  
  const discountType = appliedVoucher.value.discount_type;
  const discountValue = Number(appliedVoucher.value.discount_value);
  
  if (discountType === 'percent') {
    return (originalPrice.value * discountValue) / 100;
  }
  return discountValue;
});

// Tính tổng tiền sau discount
const totalPrice = computed(() => {
  let basePrice = originalPrice.value;
  basePrice -= voucherDiscountAmount.value;
  if (basePrice < 0) basePrice = 0;
  return basePrice;
});

// State cho mã giảm giá
const voucherCode = ref('');
const appliedVoucher = ref<any>(null);
const voucherError = ref('');
const showVoucherModal = ref(false);

const savedVouchers = ref<any[]>([]);

const fetchVouchers = async () => {
  const token = localStorage.getItem('auth_token');
  if (!token) return;
  
  try {
    const response = await fetch('/api/vouchers/my-vouchers', {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    if (response.ok) {
      savedVouchers.value = await response.json();
    }
  } catch (error) {
    console.error('Lỗi tải voucher:', error);
  }
};

const openVoucherModal = () => {
  showVoucherModal.value = true;
};

const selectVoucher = (v: any) => {
  voucherCode.value = v.code;
  showVoucherModal.value = false;
  applyVoucher();
};

const applyVoucher = () => {
  voucherError.value = '';
  if (!voucherCode.value) return;
  
  const found = savedVouchers.value.find(v => v.code === voucherCode.value.toUpperCase());
  if (found) {
    appliedVoucher.value = found;
  } else {
    appliedVoucher.value = null;
    voucherError.value = 'Mã giảm giá không hợp lệ hoặc đã hết hạn';
  }
};

const removeVoucher = () => {
  appliedVoucher.value = null;
  voucherCode.value = '';
  voucherError.value = '';
};

// Load Data
onMounted(async () => {
  // Nếu không có roomId, văng về trang chủ hoặc listing
  if (!roomId) {
    router.push('/listing');
    return;
  }

  try {
    const response = await fetch(`/api/rooms/${roomId}`);
    if (!response.ok) throw new Error('Không tìm thấy phòng');
    
    const data = await response.json();
    room.value = data;

    if (data.images && data.images.length > 0) {
      const pImg = data.images.find((img: any) => img.is_primary);
      let thumbUrl = pImg ? pImg.image_url : data.images[0].image_url;
      
      if (thumbUrl && !thumbUrl.startsWith('http') && !thumbUrl.startsWith('/storage/')) {
        thumbUrl = thumbUrl.startsWith('/') ? `/storage${thumbUrl}` : `/storage/${thumbUrl}`;
      }
      
      primaryImage.value = thumbUrl;
    }
  } catch (error) {
    console.error('Lỗi lấy thông tin phòng thanh toán:', error);
    alert('Không thể tải thông tin phòng, vui lòng thử lại.');
    router.push('/listing');
  } finally {
    loading.value = false;
  }

  // Tự động điền thông tin nếu khách đã đăng nhập
  const userInfo = localStorage.getItem('user_info');
  if (userInfo) {
    const user = JSON.parse(userInfo);
    customerInfo.value.name = user.name || '';
    customerInfo.value.email = user.email || '';
    customerInfo.value.phone = user.phone || '';
  }
  
  fetchVouchers();
});

// Helper Functions
const formatDate = (dateStr: string) => {
  if (!dateStr) return '';
  const [year, month, day] = dateStr.split('-');
  return `${day}/${month}/${year}`;
};

const formatPrice = (price: number) => {
  return price.toLocaleString('vi-VN') + 'đ';
};

// Gửi thanh toán
// Gửi thanh toán
const handlePayment = async () => {
  if(isSubmitting.value) return;
  isSubmitting.value = true;
  
  try {
    const depositAmount = totalPrice.value * 0.3; // Tiền cọc 30%
    
    // BƯỚC 1: TẠO BOOKING VÀO DATABASE TRƯỚC
    const response = await fetch('/api/bookings', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      },
      body: JSON.stringify({
        room_id: room.value?.id,
        room_name: room.value?.title,      
        customer_name: customerInfo.value.name, 
        customer_email: customerInfo.value.email, 
        customer_phone: customerInfo.value.phone, 
        total_price: totalPrice.value,     
        deposit_amount: depositAmount,     
        check_in_date: checkIn.value,
        check_out_date: checkOut.value,
        adults: adults.value,
        children: children.value,
        voucher_id: appliedVoucher.value ? appliedVoucher.value.id : null,
        subtotal: originalPrice.value
      })
    });

    if (response.ok) {
      const resData = await response.json();
      
      // Tìm id của booking vừa tạo để gửi cho VNPay
      const newBookingId = resData.booking ? resData.booking.id : (resData.data ? resData.data.id : resData.id);
      
      // BƯỚC 2: KIỂM TRA PHƯƠNG THỨC THANH TOÁN
      if (paymentMethod.value === 'ewallet') {
        // NẾU CHỌN VNPAY (Ví điện tử): Gọi API tạo link thanh toán
        try {
            const vnpayResponse = await fetch('/api/payment/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    amount: depositAmount, // Truyền tiền cọc 30% vào VNPay
                    booking_id: newBookingId // Mã đơn hàng vừa tạo
                })
            });

            const vnpayData = await vnpayResponse.json();

            if (vnpayData.status === 'success') {
                // Phóng thẳng sang trang giao diện của VNPay
                window.location.href = vnpayData.payment_url; 
            } else {
                alert('Có lỗi khi kết nối với cổng thanh toán VNPay!');
                isSubmitting.value = false;
            }
        } catch (vnpayError) {
            console.error(vnpayError);
            alert('Lỗi khi khởi tạo thanh toán VNPay.');
            isSubmitting.value = false;
        }

      } else {
         // NẾU CHỌN CHUYỂN KHOẢN NGÂN HÀNG: Chuyển thẳng sang trang QR, không alert
         router.push(`/payment-success?id=${newBookingId}`); 
      }

    } else {
      const data = await response.json();
      alert('Lỗi: ' + (data.message || 'Không thể đặt phòng lúc này.'));
      isSubmitting.value = false;
    }
  } catch (error) {
    alert('Lỗi kết nối máy chủ!');
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
</style>