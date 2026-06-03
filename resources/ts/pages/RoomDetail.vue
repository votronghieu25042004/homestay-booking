<template>
  <div class="flex flex-col min-h-screen bg-[#FAF9F5]">
    <div v-if="loading" class="flex-grow max-w-7xl mx-auto px-4 py-20 text-center text-[#4A7055]">
      <div class="animate-pulse flex flex-col items-center">
        <HomeIcon class="w-12 h-12 mb-4 opacity-50" />
        <p class="font-medium font-['Inter']">Đang tải thông tin phòng, vui lòng đợi...</p>
      </div>
    </div>

    <div v-else-if="room" class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
      
      <div class="mb-6">
        <div class="flex flex-wrap items-center gap-4 mb-2">
          <h1 class="text-3xl font-bold text-gray-900 font-['Playfair_Display']">{{ room.title }}</h1>
          <div 
            v-if="room.status && room.status !== 'available'" 
            class="px-3 py-1 rounded-full text-xs font-bold text-white shadow-sm font-['Inter'] tracking-wide"
            :class="statusClass"
          >
            {{ statusText }}
          </div>
          <div v-if="room.rent_type === 'home' || room.rent_type === 'whole_house'" class="bg-[#4A7055] text-white px-3 py-1 rounded-full text-xs font-bold font-['Inter'] shadow-sm">
            Sức chứa:
            {{ room.max_guests >= 99 ? 'Không giới hạn' : room.max_guests }} Người lớn
            và
            {{ (room.max_children || 0) >= 99 ? 'Không giới hạn' : (room.max_children || 0) }} Trẻ em (&lt; 10 tuổi)
          </div>
        </div>
          <div class="flex items-center gap-4 text-sm text-gray-600 font-['Inter']" v-if="room">
          <div class="flex items-center gap-1">
            <Star class="w-4 h-4" :class="room.average_rating > 0 ? 'fill-amber-400 text-amber-400' : 'text-gray-300'" />
            <span class="font-bold text-gray-900">{{ room.average_rating || 'Chưa có' }}</span>
            <span class="underline cursor-pointer hover:text-[#4A7055] transition-colors" @click="scrollToReviews">({{ totalReviews }} đánh giá)</span>
          </div>
          <div class="flex items-center gap-1">
            <MapPin class="w-4 h-4" />
            <span class="underline cursor-pointer hover:text-[#4A7055] transition-colors">{{ room.location }}</span>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-12 h-[400px] md:h-[500px] rounded-2xl overflow-hidden shadow-sm">
        
        <div class="md:col-span-2 h-full relative group">
          <img @click="openGallery(0)" :src="allImages[0] || 'https://picsum.photos/seed/roommain/1200/800'" alt="Main" class="w-full h-full object-cover hover:opacity-95 transition-opacity cursor-pointer" referrerpolicy="no-referrer" />
          <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors pointer-events-none"></div>
        </div>
        
        <div class="hidden md:grid grid-rows-2 gap-4 h-full">
          <div class="relative group h-full">
            <img @click="openGallery(1)" :src="allImages[1] || 'https://picsum.photos/seed/room2/600/400'" alt="Room" class="w-full h-full object-cover hover:opacity-95 transition-opacity cursor-pointer" referrerpolicy="no-referrer" />
          </div>
          <div class="relative group h-full">
            <img @click="openGallery(2)" :src="allImages[2] || 'https://picsum.photos/seed/room3/600/400'" alt="Room" class="w-full h-full object-cover hover:opacity-95 transition-opacity cursor-pointer" referrerpolicy="no-referrer" />
          </div>
        </div>
        
        <div class="hidden md:grid grid-rows-2 gap-4 h-full">
          <div class="relative group h-full">
            <img @click="openGallery(3)" :src="allImages[3] || 'https://picsum.photos/seed/room4/600/400'" alt="Room" class="w-full h-full object-cover hover:opacity-95 transition-opacity cursor-pointer" referrerpolicy="no-referrer" />
          </div>
          <div class="relative h-full group" @click="openGallery(0)">
            <img :src="allImages[4] || 'https://picsum.photos/seed/room5/600/400'" alt="Room" class="w-full h-full object-cover hover:opacity-95 transition-opacity cursor-pointer" referrerpolicy="no-referrer" />
            <div class="absolute inset-0 bg-black/40 flex items-center justify-center cursor-pointer hover:bg-black/50 transition-colors">
              <span class="text-white font-bold text-lg font-['Inter'] tracking-wide">Xem tất cả {{ allImages.length }} ảnh</span>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showGallery" class="fixed inset-0 z-[100] bg-black/95 backdrop-blur-sm flex flex-col" @keydown.esc="closeGallery" tabindex="0">
        <div class="flex justify-between items-center p-6 text-white shrink-0">
          <div class="font-medium text-sm">{{ currentImageIndex + 1 }} / {{ allImages.length }}</div>
          <button @click="closeGallery" class="p-2 bg-white/10 hover:bg-white/20 rounded-full transition-colors">
            <X class="w-6 h-6" />
          </button>
        </div>
        
        <div class="flex-1 flex items-center justify-center relative px-12 overflow-hidden">
          <button @click="prevImage" class="absolute left-6 p-4 bg-white/10 hover:bg-white/20 text-white rounded-full transition-colors">
            <ChevronLeft class="w-8 h-8" />
          </button>
          
          <img :src="allImages[currentImageIndex]" class="max-h-full max-w-full object-contain rounded-lg shadow-2xl transition-transform duration-300" />
          
          <button @click="nextImage" class="absolute right-6 p-4 bg-white/10 hover:bg-white/20 text-white rounded-full transition-colors">
            <ChevronRight class="w-8 h-8" />
          </button>
        </div>
        
        <div class="h-24 shrink-0 p-4 flex justify-center gap-2 overflow-x-auto">
          <img 
            v-for="(img, index) in allImages" 
            :key="index"
            :src="img" 
            @click="currentImageIndex = index"
            class="h-full w-24 object-cover rounded-md cursor-pointer transition-all duration-200"
            :class="index === currentImageIndex ? 'border-2 border-white opacity-100 scale-105' : 'opacity-40 hover:opacity-70'"
          />
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 pb-12">
        <div class="lg:col-span-2 space-y-12">
          
          <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4 font-['Playfair_Display']">Mô tả chi tiết phòng</h2>
            <div class="prose text-gray-600 font-['Inter'] leading-relaxed whitespace-pre-line">
              {{ room.description || 'Chủ nhà chưa cung cấp mô tả cho phòng này. Tuy nhiên, phòng được trang bị đầy đủ tiện nghi, view nhìn ra thành phố tuyệt đẹp, không gian yên tĩnh phù hợp cho cả kỳ nghỉ dưỡng lẫn chuyến công tác.' }}
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4 font-['Playfair_Display']">Tiện nghi</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
              <template v-if="room.amenity_list && room.amenity_list.length > 0">
                <div v-for="amenity in room.amenity_list" :key="amenity.id" class="flex items-center gap-3 text-gray-700 font-['Inter']">
                  <div class="w-8 h-8 rounded-full bg-[#4A7055]/10 flex items-center justify-center text-[#4A7055]">
                    <Star class="w-4 h-4" />
                  </div>
                  <span class="font-medium">{{ amenity.name }}</span>
                </div>
              </template>
              <template v-else>
                 <div class="col-span-full text-gray-500 italic">Đang cập nhật danh sách tiện nghi.</div>
              </template>
            </div>
            <button v-if="room.amenity_list && room.amenity_list.length > 6" class="mt-6 text-[#4A7055] font-bold font-['Inter'] hover:text-[#3b5a44] underline transition-colors">
              Xem thêm tiện nghi
            </button>
          </section>

          <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4 font-['Playfair_Display']">Nội quy & Chính sách</h2>
            <div class="bg-white border border-gray-100 p-8 rounded-3xl space-y-8 font-['Inter'] shadow-sm">
              <div>
                <h3 class="font-bold text-gray-900 mb-3 text-lg flex items-center gap-2">
                  <Clock class="w-5 h-5 text-[#4A7055]" /> Giờ check in - check out
                </h3>
                <ul class="list-disc pl-5 text-gray-600 space-y-1.5 marker:text-[#4A7055]">
                  <li>Giờ check in: Đúng giờ có thể delay 10-15 phút</li>
                  <li>Giờ check out: Đúng giờ có thể delay 10-15 phút</li>
                  <li class="text-red-500 font-medium mt-2 list-none -ml-5 bg-red-50 p-2 rounded-md"><AlertTriangle class="w-4 h-4 inline-block mr-1 mb-1" />Khách có thể đến trễ tối đa 1 tiếng so với giờ Check-in. Quá 1 tiếng, hệ thống tự động hủy phòng và không hoàn cọc.</li>
                </ul>
              </div>
              
              <div>
                <h3 class="font-bold text-gray-900 mb-3 text-lg flex items-center gap-2">
                  <Users class="w-5 h-5 text-[#4A7055]" /> Quy định số lượng khách
                </h3>
                <div class="space-y-4 text-gray-600">
                  <div v-if="room.rent_type === 'whole_house' || room.rent_type === 'home'" class="flex items-start gap-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 shrink-0"></div>
                    <div>
                      <p class="font-bold text-emerald-900">Quy định thuê nguyên căn / standalone:</p>
                      <p class="text-sm">Tối đa {{ room.max_guests >= 99 ? 'Không giới hạn' : room.max_guests }} người lớn và {{ (room.max_children || 0) >= 99 ? 'Không giới hạn' : (room.max_children || 0) }} trẻ em đi kèm.</p>
                    </div>
                  </div>
                  <div v-else class="flex items-start gap-3 p-3 bg-blue-50 rounded-xl border border-blue-100">
                    <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5 shrink-0"></div>
                    <div>
                      <p class="font-bold text-blue-900">Quy định phòng riêng này:</p>
                      <p class="text-sm">Tối đa {{ room.max_guests >= 99 ? 'Không giới hạn' : room.max_guests }} người lớn. Hỗ trợ ở ghép tối đa {{ (room.max_children || 0) >= 99 ? 'Không giới hạn' : (room.max_children || 0) }} trẻ em (dưới 10 tuổi).</p>
                    </div>
                  </div>
                  <p class="text-red-500 italic text-xs mt-2">* Khách đoàn từ 5 người lớn trở lên vui lòng cân nhắc thuê nhiều phòng hoặc chọn Nguyên căn.</p>
                </div>
              </div>

              <div>
                <h3 class="font-bold text-gray-900 mb-3 text-lg flex items-center gap-2">
                  <CreditCard class="w-5 h-5 text-[#4A7055]" /> Chính sách thanh toán & hủy phòng
                </h3>
                <p class="text-[#4A7055] font-medium bg-[#4A7055]/10 p-2 rounded-lg text-center mb-3">
                    Khách đặt phòng vui lòng thanh toán cọc trước <span class="text-lg font-bold">30%</span> tổng tiền.
                </p>
                <ul class="list-disc pl-5 text-gray-600 space-y-1.5 marker:text-[#4A7055]">
                  <li>Hủy trong 30 phút đầu hoặc trước 3 ngày: Hoàn 100% cọc.</li>
                  <li>Hủy trước 1 - 3 ngày: Hoàn 50% cọc.</li>
                  <li>Hủy trong vòng 24h: Không hỗ trợ hoàn cọc.</li>
                  <li class="text-blue-600 font-medium list-none -ml-5 bg-blue-50 p-2 rounded-md mt-2">💡 Hỗ trợ dời lịch: Nếu quá hạn hủy nhưng muốn đổi ngày, vui lòng liên hệ sớm qua Hotline. KHÔNG hỗ trợ nếu báo sát giờ Check-in.</li>
                </ul>
              </div>
            </div>
          </section>

          <section id="reviews-section" class="scroll-mt-24">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 font-['Playfair_Display']">Đánh giá từ khách hàng</h2>

            <div v-if="reviews.length > 0" class="space-y-6">
              <div v-for="review in reviews" :key="review.id" class="pb-6 border-b border-gray-100 last:border-0 hover:bg-white/50 transition-colors p-4 rounded-xl">
                <div class="flex justify-between items-start mb-4">
                  <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-[#4A7055]/10 flex items-center justify-center text-[#4A7055] font-bold text-lg uppercase">
                      {{ review.user?.name?.charAt(0) || 'U' }}
                    </div>
                    <div>
                      <div class="font-bold text-gray-900 font-['Inter']">{{ review.user?.name }}</div>
                      <div class="text-sm text-gray-500 font-['Inter']">{{ formatDate(review.created_at) }}</div>
                    </div>
                  </div>
                  <div class="flex items-center">
                    <Star v-for="s in 5" :key="s" class="w-4 h-4" :class="s <= review.rating ? 'fill-amber-400 text-amber-400' : 'text-gray-200'" />
                  </div>
                </div>
                <p class="text-gray-700 font-['Inter'] leading-relaxed">
                  {{ review.comment || 'Không có nhận xét.' }}
                </p>
              </div>
              
              <button 
                v-if="hasMoreReviews" 
                @click="loadMoreReviews"
                class="w-full mt-6 border-2 border-gray-200 text-gray-600 px-8 py-3 rounded-full font-bold font-['Inter'] hover:border-gray-900 hover:text-gray-900 transition-all"
              >
                Tải thêm đánh giá
              </button>
            </div>
            <div v-else class="text-center py-12 bg-white rounded-2xl border border-dashed border-gray-200">
              <Star class="w-12 h-12 text-gray-200 mx-auto mb-3" />
              <p class="text-gray-500 font-['Inter']">Chưa có đánh giá nào cho phòng này.</p>
            </div>
          </section>

        </div>

        <div class="lg:col-span-1">
          <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-2xl sticky top-28 font-['Inter']">
            <div class="flex items-baseline gap-1.5 mb-6">
              <span class="text-3xl font-extrabold text-[#4A7055]">{{ Number(room.price).toLocaleString('vi-VN') }}đ</span>
              <span class="text-gray-500 font-medium">/ đêm</span>
            </div>

            <div class="space-y-4 mb-6">
              <div class="border border-gray-200 rounded-xl overflow-hidden focus-within:border-[#4A7055] transition-colors">
                <div class="flex border-b border-gray-200">
                  <div class="flex-1 p-3 border-r border-gray-200">
                    <label class="block text-[10px] font-extrabold text-gray-900 uppercase mb-1">Nhận phòng</label>
                    <input type="date" v-model="checkIn" :min="minDate" class="w-full outline-none text-sm text-gray-600 bg-transparent cursor-pointer" />
                  </div>
                  <div class="flex-1 p-3">
                    <label class="block text-[10px] font-extrabold text-gray-900 uppercase mb-1">Trả phòng</label>
                    <input type="date" v-model="checkOut" :min="checkIn || minDate" class="w-full outline-none text-sm text-gray-600 bg-transparent cursor-pointer" />
                  </div>
                </div>
                
                <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                  <div>
                    <label class="block text-[10px] font-extrabold text-gray-900 uppercase mb-1">Người lớn</label>
                    <span class="text-[10px] text-gray-500">Từ 10 tuổi</span>
                  </div>
                  <div class="flex items-center gap-3">
                    <button @click="adults > 1 ? adults-- : null" class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:border-[#4A7055] transition-colors">-</button>
                    <span class="w-4 text-center text-sm font-medium">{{ adults }}</span>
                    <button @click="adults++" class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:border-[#4A7055] transition-colors">+</button>
                  </div>
                </div>

                <div class="p-3 flex justify-between items-center">
                  <div>
                    <label class="block text-[10px] font-extrabold text-gray-900 uppercase mb-1">Trẻ em</label>
                    <span class="text-[10px] text-gray-500">Dưới 10 tuổi</span>
                  </div>
                  <div class="flex items-center gap-3">
                    <button @click="children > 0 ? children-- : null" class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:border-[#4A7055] transition-colors">-</button>
                    <span class="w-4 text-center text-sm font-medium">{{ children }}</span>
                    <button @click="children++" class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:border-[#4A7055] transition-colors">+</button>
                  </div>
                </div>
              </div>
              
              <div v-if="errorMessage" class="text-red-500 text-xs font-medium bg-red-50 p-3 rounded-lg border border-red-100">
                {{ errorMessage }}
              </div>
            </div>

            <button 
              @click="handleBook"
              class="w-full bg-[#4A7055] hover:bg-[#3b5a44] text-white py-4 rounded-xl font-bold text-lg transition-colors shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
              :disabled="!checkIn || !checkOut || room?.status !== 'available'"
            >
              {{ room?.status === 'available' ? 'Đặt phòng ngay' : statusText }}
            </button>

            <div class="mt-4 text-center text-sm font-medium text-gray-500">
              Bạn vẫn chưa bị trừ tiền
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="flex-grow max-w-7xl mx-auto px-4 py-20 text-center flex flex-col items-center justify-center">
      <AlertTriangle class="w-16 h-16 text-gray-300 mb-4" />
      <h2 class="text-2xl font-bold text-gray-900 mb-4 font-['Playfair_Display']">Không tìm thấy phòng!</h2>
      <p class="text-gray-500 mb-6 font-['Inter']">Phòng này có thể đã bị xóa hoặc đường dẫn không chính xác.</p>
      <button @click="router.push('/listing')" class="bg-[#4A7055] text-white px-6 py-2.5 rounded-lg hover:bg-[#3b5a44] transition-colors font-medium">
        Quay lại danh sách phòng
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { MapPin, Star, Clock, CreditCard, Users, AlertTriangle, Home as HomeIcon, X, ChevronLeft, ChevronRight } from 'lucide-vue-next';

interface RoomData {
  id: number | string;
  title: string;
  location: string;
  type: string;
  price: string | number;
  description: string;
  max_guests: number;
  max_children: number;
  rent_type?: string; 
  status?: string;
  images?: any[];
  amenities?: any[]; 
  amenity_list?: any[]; 
  average_rating?: number;
}

interface ReviewData {
  id: number;
  rating: number;
  comment: string;
  created_at: string;
  user: {
    name: string;
  };
}

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const room = ref<RoomData | null>(null);

// Mảng chứa TẤT CẢ url ảnh đã được xử lý chuẩn (http / storage)
const allImages = ref<string[]>([]);

// State cho Modal Xem Ảnh
const showGallery = ref(false);
const currentImageIndex = ref(0);

const checkIn = ref('');
const checkOut = ref('');
const adults = ref(1);
const children = ref(0);
const errorMessage = ref('');

// --- REVIEWS STATE ---
const reviews = ref<ReviewData[]>([]);
const totalReviews = ref(0);
const curPage = ref(1);
const hasMoreReviews = ref(false);
const canReviewBooking = ref<any>(null); // Lưu thông tin booking có thể review
const isSubmittingReview = ref(false);
const newReview = ref({
  rating: 0,
  comment: ''
});

const todayObj = new Date();
const minDate = `${todayObj.getFullYear()}-${String(todayObj.getMonth() + 1).padStart(2, '0')}-${String(todayObj.getDate()).padStart(2, '0')}`;

// --- CÁC HÀM ĐIỀU KHIỂN GALLERY ---
const openGallery = (index: number) => {
  if(allImages.value.length === 0) return;
  currentImageIndex.value = index;
  showGallery.value = true;
  document.body.style.overflow = 'hidden'; // Chống cuộn trang phía sau
};

const closeGallery = () => {
  showGallery.value = false;
  document.body.style.overflow = ''; 
};

const nextImage = () => {
  currentImageIndex.value = (currentImageIndex.value + 1) % allImages.value.length;
};

const prevImage = () => {
  currentImageIndex.value = (currentImageIndex.value - 1 + allImages.value.length) % allImages.value.length;
};

// Đóng modal khi component bị destroy
onUnmounted(() => {
  document.body.style.overflow = '';
});

const scrollToReviews = () => {
  const el = document.getElementById('reviews-section');
  if (el) el.scrollIntoView({ behavior: 'smooth' });
};

const formatDate = (dateStr: string) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return `Tháng ${date.getMonth() + 1} năm ${date.getFullYear()}`;
};

const fetchReviews = async (page = 1) => {
  try {
    const res = await fetch(`/api/rooms/${route.params.id}/reviews?page=${page}`);
    const data = await res.json();
    if (page === 1) reviews.value = data.data;
    else reviews.value.push(...data.data);
    
    totalReviews.value = data.total;
    hasMoreReviews.value = data.current_page < data.last_page;
    curPage.value = data.current_page;
  } catch (e) {
    console.error('Lỗi tải đánh giá:', e);
  }
};

const loadMoreReviews = () => {
  fetchReviews(curPage.value + 1);
};

// Đã dời chức năng đánh giá qua Lịch sử đặt phòng (Profile.vue)


onMounted(async () => {
  try {
    const roomId = route.params.id;
    checkIn.value = (route.query.checkIn as string) || '';
    checkOut.value = (route.query.checkOut as string) || '';

    const response = await fetch(`/api/rooms/${roomId}`);
    if (!response.ok) throw new Error('Không tìm thấy phòng');
    
    const data = await response.json();
    
    data.amenity_list = data.amenities || [];
    room.value = data;

    // Hàm chuẩn hóa URL Ảnh
    const formatImageUrl = (url: string) => {
      if (!url) return '';
      if (!url.startsWith('http') && !url.startsWith('/storage/') && !url.startsWith('data:')) {
        return url.startsWith('/') ? `/storage${url}` : `/storage/${url}`;
      }
      return url;
    };

    // XỬ LÝ LẤY TOÀN BỘ ẢNH VÀO MẢNG CHUNG
    if (data.images && data.images.length > 0) {
      // Tìm ảnh primary (nếu có)
      const primaryImg = data.images.find((img: any) => img.is_primary);
      const primaryUrl = primaryImg ? primaryImg.image_url : data.images[0].image_url;
      
      // Đẩy ảnh chính vào vị trí số 0
      allImages.value.push(formatImageUrl(primaryUrl));
      
      // Đẩy các ảnh còn lại vào theo sau
      data.images.forEach((img: any) => {
        if(img.image_url !== primaryUrl) {
           allImages.value.push(formatImageUrl(img.image_url));
        }
      });
    } else {
      // Nếu phòng không có ảnh nào trong DB, tạo 5 ảnh mặc định để UI không bị vỡ
      allImages.value = [
        'https://picsum.photos/seed/fallback/1200/800',
        'https://picsum.photos/seed/fallback1/600/400',
        'https://picsum.photos/seed/fallback2/600/400',
        'https://picsum.photos/seed/fallback3/600/400',
        'https://picsum.photos/seed/fallback4/600/400'
      ];
    }
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }

  fetchReviews();

  // Tự động cuộn đến phần đánh giá nếu có param ?review=true
  if (route.query.review === 'true') {
    setTimeout(() => {
      scrollToReviews();
    }, 800);
  }
});

const statusText = computed(() => {
  if (!room.value) return '';
  switch (room.value.status) {
    case 'booked': return 'Đã đặt cọc';
    case 'in_use': return 'Đang có khách';
    case 'maintenance': return 'Bảo trì';
    case 'available': return 'Sẵn sàng';
    default: return '';
  }
});

const statusClass = computed(() => {
  if (!room.value) return '';
  switch (room.value.status) {
    case 'booked': return 'bg-amber-500';
    case 'in_use': return 'bg-red-500';
    case 'maintenance': return 'bg-gray-500';
    default: return 'bg-[#4A7055]';
  }
});

const validateCapacity = () => {
  errorMessage.value = ''; 
  if (!room.value) return false;

  const type = room.value.type;
  const max = room.value.max_guests || 2; 

  if(!checkIn.value || !checkOut.value) {
    errorMessage.value = 'Vui lòng chọn ngày nhận và trả phòng.';
    return false;
  }

  const inDate = new Date(checkIn.value);
  const outDate = new Date(checkOut.value);
  if(outDate <= inDate) {
     errorMessage.value = 'Ngày trả phòng phải sau ngày nhận phòng.';
     return false;
  }

  if (type === 'house') {
    if (adults.value > 20) {
      errorMessage.value = 'Nguyên căn chỉ chứa tối đa 20 người lớn. Vui lòng liên hệ hotline để được hỗ trợ.';
      return false;
    }
  } else {
    if (adults.value > max) {
        errorMessage.value = `Phòng này chỉ chứa tối đa ${max} người lớn. Vui lòng chọn phòng lớn hơn.`;
        return false;
    }
    
    const maxChildren = room.value.max_children || 0;
    if (children.value > maxChildren) {
        errorMessage.value = `Loại phòng này chỉ được kèm tối đa ${maxChildren} trẻ em.`;
        return false;
    }
  }
  
  return true;
};

const handleBook = () => {
  if (validateCapacity()) {
    router.push({
      path: '/payment',
      query: {
        roomId: room.value?.id,
        checkIn: checkIn.value,
        checkOut: checkOut.value,
        adults: adults.value,
        children: children.value
      }
    });
  }
};
</script>

<style scoped>
input[type="date"]::-webkit-calendar-picker-indicator {
  cursor: pointer;
  opacity: 0.6;
  margin-left: 0;
}
</style>