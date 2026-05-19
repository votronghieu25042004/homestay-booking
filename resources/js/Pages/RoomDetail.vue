<template>
  <div class="flex flex-col min-h-screen bg-white">
    <div class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
      
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2 font-['Playfair_Display']">Phòng Mơ Màng - Số 10 Núi Thành - Quận Cẩm Lệ - TP. Đà Nẵng</h1>
        <div class="flex items-center gap-4 text-sm text-gray-600 font-['Inter']">
          <div class="flex items-center gap-1">
            <Star class="w-4 h-4 fill-amber-400 text-amber-400" />
            <span class="font-bold text-gray-900">4.8</span>
            <span class="underline cursor-pointer hover:text-[#4A7055] transition-colors">(120 đánh giá)</span>
          </div>
          <div class="flex items-center gap-1">
            <MapPin class="w-4 h-4" />
            <span class="underline cursor-pointer hover:text-[#4A7055] transition-colors">Quận Cẩm Lệ, TP. Đà Nẵng</span>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-12 h-[400px] md:h-[500px] rounded-2xl overflow-hidden shadow-sm">
        <div class="md:col-span-2 h-full">
          <img src="https://picsum.photos/seed/roommain/1200/800" alt="Main" class="w-full h-full object-cover hover:opacity-95 transition-opacity cursor-pointer" referrerpolicy="no-referrer" />
        </div>
        <div class="hidden md:grid grid-rows-2 gap-4 h-full">
          <img src="https://picsum.photos/seed/room2/600/400" alt="Room 2" class="w-full h-full object-cover hover:opacity-95 transition-opacity cursor-pointer" referrerpolicy="no-referrer" />
          <img src="https://picsum.photos/seed/room3/600/400" alt="Room 3" class="w-full h-full object-cover hover:opacity-95 transition-opacity cursor-pointer" referrerpolicy="no-referrer" />
        </div>
        <div class="hidden md:grid grid-rows-2 gap-4 h-full">
          <img src="https://picsum.photos/seed/room4/600/400" alt="Room 4" class="w-full h-full object-cover hover:opacity-95 transition-opacity cursor-pointer" referrerpolicy="no-referrer" />
          <div class="relative h-full">
            <img src="https://picsum.photos/seed/room5/600/400" alt="Room 5" class="w-full h-full object-cover hover:opacity-95 transition-opacity cursor-pointer" referrerpolicy="no-referrer" />
            <div class="absolute inset-0 bg-black/40 flex items-center justify-center cursor-pointer hover:bg-black/50 transition-colors">
              <span class="text-white font-bold text-lg font-['Inter'] tracking-wide">Xem tất cả ảnh</span>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 pb-12">
        <div class="lg:col-span-2 space-y-12">
          
          <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4 font-['Playfair_Display']">Mô tả chi tiết phòng</h2>
            <div class="prose text-gray-600 font-['Inter'] leading-relaxed">
              <p>
                Phòng Mơ Màng mang đến không gian nghỉ ngơi lý tưởng với thiết kế hiện đại, ấm cúng. Nằm ngay trung tâm quận Cẩm Lệ, thuận tiện di chuyển đến các điểm tham quan nổi tiếng của Đà Nẵng.
              </p>
              <p class="mt-4">
                Phòng được trang bị đầy đủ tiện nghi, view nhìn ra thành phố tuyệt đẹp, không gian yên tĩnh phù hợp cho cả kỳ nghỉ dưỡng lẫn chuyến công tác.
              </p>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4 font-['Playfair_Display']">Tiện nghi</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
              <div v-for="(item, i) in ['Máy chiếu', 'Wifi miễn phí', 'Điều hòa', 'Bếp mini', 'Tủ lạnh', 'Máy sấy tóc']" :key="i" class="flex items-center gap-3 text-gray-700 font-['Inter']">
                <div class="w-8 h-8 rounded-full bg-[#4A7055]/10 flex items-center justify-center text-[#4A7055]">
                  <Star class="w-4 h-4" />
                </div>
                <span class="font-medium">{{ item }}</span>
              </div>
            </div>
            <button class="mt-6 text-[#4A7055] font-bold font-['Inter'] hover:text-[#3b5a44] underline transition-colors">
              Xem thêm tiện nghi
            </button>
          </section>

          <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4 font-['Playfair_Display']">Nội quy & Chính sách</h2>
            <div class="bg-[#FAF9F5] border border-gray-100 p-8 rounded-3xl space-y-8 font-['Inter'] shadow-sm">
              <div>
                <h3 class="font-bold text-gray-900 mb-3 text-lg">Giờ check in - check out</h3>
                <ul class="list-disc pl-5 text-gray-600 space-y-1.5 marker:text-[#4A7055]">
                  <li>Giờ check in: 14:00 (Đúng giờ có thể delay 10-15 phút)</li>
                  <li>Giờ check out: 12:00 (Đúng giờ có thể delay 10-15 phút)</li>
                </ul>
              </div>
              <div>
                <h3 class="font-bold text-gray-900 mb-3 text-lg">Nội quy căn hộ</h3>
                <ul class="list-disc pl-5 text-gray-600 space-y-1.5 marker:text-[#4A7055]">
                  <li>Nghiêm cấm sử dụng chất kích thích, các chất gây nghiện, các chất cấm.</li>
                  <li>Không tự ý cho người lạ vào khi chưa đăng kí trước.</li>
                  <li>Không tự ý di chuyển hoặc làm bẩn, hỏng nội thất, thiết bị.</li>
                  <li>Vui lòng làm sạch bếp sau khi sử dụng.</li>
                </ul>
              </div>
              <div>
                <h3 class="font-bold text-gray-900 mb-3 text-lg">Lưu ý đặc biệt</h3>
                <ul class="list-disc pl-5 text-gray-600 space-y-1.5 marker:text-[#4A7055]">
                  <li>Vui lòng mang theo nước lọc và bàn chải đánh răng.</li>
                  <li>Không hút thuốc. Không làm ồn sau 11 giờ tối.</li>
                </ul>
              </div>
              <div>
                <h3 class="font-bold text-gray-900 mb-3 text-lg">Chính sách hủy phòng</h3>
                <ul class="list-disc pl-5 text-gray-600 space-y-1.5 marker:text-[#4A7055]">
                  <li>Hủy phòng một tuần trước ngày nhận phòng: Hoàn 100%.</li>
                  <li>Các trường hợp còn lại không hỗ trợ hoàn/hủy phòng. Chỉ hỗ trợ dời lịch đặt hoặc bảo lưu theo yêu cầu của khách hàng.</li>
                </ul>
              </div>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-6 font-['Playfair_Display']">Đánh giá</h2>
            <div class="space-y-6">
              <div v-for="i in 4" :key="i" class="pb-6 border-b border-gray-100 last:border-0">
                <div class="flex items-center gap-4 mb-4">
                  <div class="w-12 h-12 rounded-full bg-[#4A7055]/10 flex items-center justify-center text-[#4A7055] font-bold text-lg">
                    U
                  </div>
                  <div>
                    <div class="font-bold text-gray-900 font-['Inter']">User {{ i }}</div>
                    <div class="text-sm text-gray-500 font-['Inter']">Tháng 10 năm 2023</div>
                  </div>
                </div>
                <p class="text-gray-700 italic font-['Inter'] leading-relaxed">
                  "Mình ở một tuần, trải nghiệm cực kì tốt, 100% sẽ giới thiệu cho bạn mình. Phòng đẹp hơn cả mình kỳ vọng mà giá cả phải chăng."
                </p>
              </div>
            </div>
            <button class="mt-6 border-2 border-gray-900 text-gray-900 px-8 py-3 rounded-full font-bold font-['Inter'] hover:bg-gray-900 hover:text-white transition-colors">
              Hiển thị tất cả 120 đánh giá
            </button>
          </section>

        </div>

        <div class="lg:col-span-1">
          <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-2xl sticky top-28 font-['Inter']">
            <div class="flex items-baseline gap-1.5 mb-6">
              <span class="text-3xl font-extrabold text-[#4A7055]">120.000đ</span>
              <span class="text-gray-500 font-medium">/ đêm</span>
            </div>

            <div class="space-y-4 mb-6">
              <div class="border border-gray-200 rounded-xl overflow-hidden focus-within:border-[#4A7055] transition-colors">
                <div class="flex border-b border-gray-200">
                  <div class="flex-1 p-3 border-r border-gray-200">
                    <label class="block text-[10px] font-extrabold text-gray-900 uppercase mb-1">Nhận phòng</label>
                    <input type="date" class="w-full outline-none text-sm text-gray-600 bg-transparent cursor-pointer" />
                  </div>
                  <div class="flex-1 p-3">
                    <label class="block text-[10px] font-extrabold text-gray-900 uppercase mb-1">Trả phòng</label>
                    <input type="date" class="w-full outline-none text-sm text-gray-600 bg-transparent cursor-pointer" />
                  </div>
                </div>
                <div class="p-3 border-b border-gray-200">
                  <label class="block text-[10px] font-extrabold text-gray-900 uppercase mb-1">Số lượng người</label>
                  <select class="w-full outline-none text-sm text-gray-600 bg-transparent cursor-pointer">
                    <option>1 khách</option>
                    <option>2 khách</option>
                    <option>3 khách</option>
                  </select>
                </div>
                <div class="p-3">
                  <label class="block text-[10px] font-extrabold text-gray-900 uppercase mb-1">Loại hình thuê</label>
                  <select class="w-full outline-none text-sm text-gray-600 bg-transparent cursor-pointer">
                    <option>Phòng</option>
                    <option>Nguyên căn</option>
                  </select>
                </div>
              </div>
            </div>

            <button 
              @click="handleBook"
              class="w-full bg-[#4A7055] hover:bg-[#3b5a44] text-white py-4 rounded-xl font-bold text-lg transition-colors shadow-md"
            >
              Đặt phòng ngay
            </button>

            <div class="mt-4 text-center text-sm font-medium text-gray-500">
              Bạn vẫn chưa bị trừ tiền
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3'; 
import { MapPin, Star } from 'lucide-vue-next';

// VÀ VẪN GIỮ IMPORT TẠI ĐÂY
import Footer from '../Components/Footer.vue'; 

const dates = ref('');
const guests = ref('');
const type = ref('');

const handleBook = () => {
  router.get('/payment');
};
</script>

<style scoped>
/* CSS cho lịch gọn gàng hơn */
input[type="date"]::-webkit-calendar-picker-indicator {
  cursor: pointer;
  opacity: 0.6;
  margin-left: 0;
}
</style>