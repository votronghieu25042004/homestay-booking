<template>
  <div class="bg-[#FAF9F5] flex flex-col min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-grow w-full font-['Inter']">
      <h1 class="text-3xl font-bold text-gray-900 mb-8 font-['Playfair_Display']">Xác nhận thông tin</h1>
      
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2">
          <form @submit.prevent="handlePayment" class="space-y-8 bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <div class="space-y-6">
              <h2 class="text-xl font-bold text-gray-900">Thông tin liên hệ</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Tên *</label>
                  <input type="text" id="name" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-[#4A7055] focus:border-[#4A7055] outline-none transition-colors" />
                </div>
                <div>
                  <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Email *</label>
                  <input type="email" id="email" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-[#4A7055] focus:border-[#4A7055] outline-none transition-colors" />
                </div>
                <div class="md:col-span-2">
                  <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">Số điện thoại *</label>
                  <input type="tel" id="phone" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-[#4A7055] focus:border-[#4A7055] outline-none transition-colors" />
                </div>
                <div class="md:col-span-2">
                  <label for="note" class="block text-sm font-bold text-gray-700 mb-2">Ghi chú</label>
                  <textarea id="note" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-[#4A7055] focus:border-[#4A7055] outline-none transition-colors"></textarea>
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
              <button type="submit" class="w-full bg-[#4A7055] hover:bg-[#3b5a44] text-white py-4 rounded-xl font-bold text-lg transition-colors shadow-md">
                Thanh toán
              </button>
            </div>
          </form>
        </div>

        <div class="lg:col-span-1">
          <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm sticky top-24">
            <div class="flex gap-4 mb-6 pb-6 border-b border-gray-100">
              <img src="https://picsum.photos/seed/roommain/200/200" alt="Room" class="w-24 h-24 object-cover rounded-xl shadow-sm" referrerpolicy="no-referrer" />
              <div>
                <h3 class="font-bold text-gray-900 line-clamp-2 mb-1">Phòng Mơ Màng - Số 10 Núi Thành - Quận Cẩm Lệ - TP. Đà Nẵng</h3>
                <div class="text-sm text-[#4A7055] font-medium">Phòng</div>
              </div>
            </div>

            <h3 class="font-bold text-gray-900 mb-4 text-lg">Chi tiết đặt phòng</h3>
            
            <div class="space-y-4 mb-6 pb-6 border-b border-gray-100">
              <div class="flex justify-between">
                <span class="text-gray-500">Ngày</span>
                <span class="font-bold text-gray-900 text-right">15/1 - 17/1/2026</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Số lượng</span>
                <span class="font-bold text-gray-900 text-right">2 người lớn</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Loại hình thuê</span>
                <span class="font-bold text-gray-900 text-right">Thuê dạng phòng</span>
              </div>
            </div>

            <div class="space-y-4 mb-6 pb-6 border-b border-gray-100">
              <div class="flex justify-between">
                <span class="text-gray-500 underline">120.000đ x 2 đêm</span>
                <span class="font-medium text-gray-900">240.000đ</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500 underline">Phí dịch vụ</span>
                <span class="font-medium text-gray-900">0đ</span>
              </div>
            </div>

            <div class="flex justify-between items-center">
              <span class="font-bold text-gray-900 text-xl">Tổng tiền</span>
              <span class="font-extrabold text-[#4A7055] text-2xl">240.000đ</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3'; // Đổi sang Inertia
import Footer from '../Components/Footer.vue';

const paymentMethod = ref('bank');

const handlePayment = () => {
  // Chuyển trang mượt mà bằng Inertia
  router.get('/payment-success');
};
</script>