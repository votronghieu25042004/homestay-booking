<template>
  <div class="bg-[#FAF9F5] min-h-screen flex flex-col font-['Inter']">
    <div class="flex-grow flex items-center justify-center py-12">
      <div class="max-w-3xl w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="bg-[#4A7055]/5 p-8 text-center border-b border-[#4A7055]/10">
            <div class="w-20 h-20 bg-[#4A7055]/10 rounded-full flex items-center justify-center mx-auto mb-6">
              <CheckCircle class="w-10 h-10 text-[#4A7055]" />
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2 font-['Playfair_Display']">Thanh toán thành công</h1>
            <p class="text-[#3b5a44] font-medium">Hóa đơn đã được gửi đến email của bạn</p>
          </div>
          
          <div class="p-8 md:p-12">
            <div class="flex flex-col md:flex-row gap-8 mb-12 pb-12 border-b border-gray-100">
              <img src="https://picsum.photos/seed/roommain/400/300" alt="Room" class="w-full md:w-1/3 h-48 object-cover rounded-2xl shadow-sm" referrerpolicy="no-referrer" />
              <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-900 mb-2 leading-tight">Phòng Mơ Màng - Số 10 Núi Thành - Quận Cẩm Lệ - TP. Đà Nẵng</h3>
                <div class="text-[#4A7055] font-bold mb-6">120.000đ <span class="text-gray-500 font-medium text-sm">/ đêm</span></div>
                
                <div class="grid grid-cols-2 gap-y-5 gap-x-8 text-sm">
                  <div>
                    <div class="text-gray-500 mb-1">Ngày</div>
                    <div class="font-bold text-gray-900">15/1 - 17/1/2026</div>
                  </div>
                  <div>
                    <div class="text-gray-500 mb-1">Số lượng</div>
                    <div class="font-bold text-gray-900">2 người lớn</div>
                  </div>
                  <div>
                    <div class="text-gray-500 mb-1">Loại hình thuê</div>
                    <div class="font-bold text-gray-900">Thuê dạng phòng</div>
                  </div>
                  <div>
                    <div class="text-gray-500 mb-1">Tổng tiền</div>
                    <div class="font-extrabold text-[#4A7055] text-lg">240.000đ</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
              <Link href="/profile" class="bg-[#4A7055] hover:bg-[#3b5a44] text-white px-8 py-3.5 rounded-xl font-bold text-center transition-colors shadow-sm">
                Xem hóa đơn
              </Link>
              <Link href="/" class="bg-white border-2 border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-700 px-8 py-3.5 rounded-xl font-bold text-center transition-colors">
                Trang chủ
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { CheckCircle } from 'lucide-vue-next';
import Footer from '../Components/Footer.vue'; 
</script>