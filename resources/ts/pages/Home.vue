<template>
  <div class="flex flex-col min-h-screen bg-[#FAF9F5]">
    
    <section class="relative pt-16 pb-24 bg-[#FFF8EA]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-12">
          
          <div class="flex-1 md:pr-10">
            <h1 class="text-4xl md:text-5xl font-bold text-[#4A7055] mb-4 font-['Playfair_Display'] leading-tight">
              Kỳ nghỉ trong mơ – Giá bất ngờ
            </h1>
            <p class="text-lg text-gray-700 mb-8 font-['Inter']">
              Săn ngay ưu đãi giảm đến 30% cho các homestay view biển đẹp nhất tháng này.
            </p>
            <router-link 
              to="/listing#room-list-section"
              class="inline-block bg-[#4A7055] hover:bg-[#3b5a44] text-white px-8 py-3.5 rounded-lg font-semibold transition-colors shadow-md"
            >
              Đặt phòng ngay
            </router-link>
          </div>

          <div class="flex-1 w-full relative group overflow-hidden rounded-tl-[100px] rounded-br-[100px] shadow-xl h-[400px]">
            <transition name="fade" mode="out-in">
              <img
                :key="currentSlide"
                :src="bannerImages[currentSlide]"
                alt="Homestay Banner"
                class="w-full h-full object-cover"
              />
            </transition>
            
            <button 
              @click="prevSlide" 
              class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/40 backdrop-blur-md text-white p-2 rounded-full transition-all"
            >
              <ChevronLeft class="w-6 h-6" />
            </button>

            <button 
              @click="nextSlide" 
              class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/40 backdrop-blur-md text-white p-2 rounded-full transition-all"
            >
              <ChevronRight class="w-6 h-6" />
            </button>

            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
              <button 
                v-for="(img, index) in bannerImages" 
                :key="index"
                @click="goToSlide(index)"
                :class="['w-2.5 h-2.5 rounded-full transition-all', currentSlide === index ? 'bg-white w-6' : 'bg-white/50']"
              ></button>
            </div>
          </div>

        </div>
      </div>

      <div class="relative mt-8 w-full max-w-5xl mx-auto px-4 sm:px-0">
        <div class="bg-white rounded-[32px] shadow-2xl p-6 md:p-8 border border-[#4A7055]/10 backdrop-blur-sm bg-white/95">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-x-4 gap-y-4 mb-4">
            <div class="lg:col-span-3 relative group">
              <label class="block text-[10px] font-bold text-[#4A7055] uppercase tracking-wider mb-1 ml-2">Tìm kiếm</label>
              <div class="relative">
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4 group-focus-within:text-[#4A7055] transition-colors" />
                <input 
                  v-model="nameSearch" 
                  type="text" 
                  placeholder="Tên homestay..." 
                  class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:border-[#4A7055] outline-none transition-all text-xs"
                />
              </div>
            </div>

            <div class="lg:col-span-3 relative group">
              <label class="block text-[10px] font-bold text-[#4A7055] uppercase tracking-wider mb-1 ml-2">Địa điểm</label>
              <button @click="toggleDropdown('location')" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:border-[#4A7055]/30 transition-all text-left">
                <div class="flex items-center gap-2 truncate">
                  <MapPin class="text-[#4A7055] w-4 h-4 shrink-0" />
                  <span class="text-xs font-semibold truncate" :class="location ? 'text-gray-900' : 'text-gray-400'">
                    {{ location || 'Bạn muốn đi đâu?' }}
                  </span>
                </div>
                <ChevronDown class="text-gray-400 w-4 h-4" />
              </button>
              
              <div v-if="activeDropdown === 'location'" class="absolute top-full left-0 mt-2 w-full min-w-[280px] bg-white rounded-2xl shadow-2xl border border-gray-100 z-[60] overflow-hidden">
                <div class="p-2 max-h-60 overflow-y-auto custom-scrollbar">
                  <div v-if="availableLocations.length === 0" class="p-4 text-center text-gray-500 text-sm italic">Đang tải...</div>
                  <button v-for="loc in availableLocations" :key="loc.id" @click="selectLocation(loc.location)" class="w-full text-left px-3 py-2.5 hover:bg-emerald-50 rounded-xl transition-colors group">
                    <div class="flex items-start gap-3">
                      <div class="mt-1 p-1 bg-emerald-100 text-emerald-600 rounded group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <MapPin class="w-3 h-3" />
                      </div>
                      <div>
                        <div class="text-xs font-bold text-gray-900 group-hover:text-emerald-700">{{ loc.title }}</div>
                        <div class="text-[10px] text-gray-500 line-clamp-1 italic">{{ loc.location }}</div>
                      </div>
                    </div>
                  </button>
                </div>
              </div>
            </div>

            <div class="lg:col-span-4 relative">
              <label class="block text-[10px] font-bold text-[#4A7055] uppercase tracking-wider mb-1 ml-2">Thời gian</label>
              <div class="flex items-center gap-1 w-full bg-gray-50/50 rounded-xl border border-gray-100 px-4 py-2.5 hover:bg-white hover:border-[#4A7055]/30 transition-all relative">
                <Calendar class="text-[#4A7055] w-4 h-4 shrink-0" />
                <div class="flex items-center gap-2 flex-1 min-w-0">
                  <div class="relative flex-1 flex items-center" @click="openDatePicker">
                    <div class="text-xs font-semibold truncate pointer-events-none" :class="checkIn ? 'text-gray-900' : 'text-gray-400'">
                      {{ checkIn ? formatDate(checkIn) : 'Từ ngày' }}
                    </div>
                    <input type="date" v-model="checkIn" :min="todayStr" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                  </div>
                  <span class="text-gray-300">-</span>
                  <div class="relative flex-1 flex items-center" @click="openDatePicker">
                    <div class="text-xs font-semibold truncate pointer-events-none" :class="checkOut ? 'text-gray-900' : 'text-gray-400'">
                      {{ checkOut ? formatDate(checkOut) : 'Đến ngày' }}
                    </div>
                    <input type="date" v-model="checkOut" :min="checkIn || todayStr" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                  </div>
                </div>
              </div>
            </div>

            <div class="lg:col-span-2 relative">
              <label class="block text-[10px] font-bold text-[#4A7055] uppercase tracking-wider mb-1 ml-2">Khách</label>
              <div class="flex items-center gap-2 bg-gray-50/50 rounded-xl border border-gray-100 px-3 py-2.5 hover:bg-white hover:border-[#4A7055]/30 transition-all">
                <Users class="text-[#4A7055] w-3 h-3 shrink-0" />
                <div class="flex items-center gap-2 text-[10px] font-bold">
                  <div class="flex flex-col">
                    <input type="number" v-model="adults" min="1" class="bg-transparent outline-none w-6 text-center" />
                  </div>
                  <div class="w-px h-6 bg-gray-200"></div>
                  <div class="flex flex-col">
                    <input type="number" v-model="children" min="0" class="bg-transparent outline-none w-6 text-center" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="flex flex-col lg:flex-row items-center justify-between gap-6 pt-4 border-t border-gray-100">
            <div class="flex-1 w-full flex flex-wrap items-center gap-6">
              
              <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Khoảng giá</p>
                <div class="flex items-center gap-2">
                  <input 
                    v-model.number="minPrice" 
                    type="number" 
                    placeholder="Từ" 
                    class="w-20 px-3 py-2 text-[11px] rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:border-[#4A7055] outline-none"
                  />
                  <span class="text-gray-400">-</span>
                  <input 
                    v-model.number="maxPrice" 
                    type="number" 
                    placeholder="Đến" 
                    class="w-20 px-3 py-2 text-[11px] rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:border-[#4A7055] outline-none"
                  />
                </div>
              </div>

              <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Loại phòng</p>
                <div class="flex flex-wrap gap-1.5">
                  <button 
                    v-for="sub in ['Tất cả', 'Đơn', 'Đôi', 'VIP']" 
                    :key="sub"
                    @click="roomSubtype = sub"
                    :class="['px-3 py-2 rounded-xl text-[10px] font-bold border transition-all whitespace-nowrap', 
                             roomSubtype === sub ? 'bg-[#4A7055] text-white border-[#4A7055]' : 'bg-white text-gray-600 border-gray-100 opacity-80']"
                  >
                    {{ sub }}
                  </button>
                </div>
              </div>

              <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Hình thức</p>
                <div class="flex flex-wrap gap-1.5">
                  <button 
                    v-for="t in [ {label: 'Tất cả', value: ''}, {label: 'Riêng', value: 'Phòng riêng'}, {label: 'Nguyên căn', value: 'Nguyên căn'} ]"
                    :key="t.label"
                    @click="selectType(t.value)" 
                    :class="['px-3 py-2 rounded-xl text-[10px] font-bold border transition-all whitespace-nowrap', 
                             type === t.value ? 'bg-[#4A7055] text-white border-[#4A7055]' : 'bg-white text-gray-600 border-gray-100 opacity-80']"
                  >{{ t.label }}</button>
                </div>
              </div>

            </div>

            <div class="shrink-0">
              <button 
                @click="handleSearch" 
                class="bg-[#4A7055] hover:bg-[#3b5a44] text-white px-8 py-3 rounded-xl font-bold flex items-center justify-center gap-2 transition-all shadow-lg hover:shadow-[#4A7055]/20"
              >
                <Search class="w-4 h-4" />
                <span>Tìm Homestay</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Banner Voucher -->
    <section class="py-12 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-[#4A7055] to-[#5B8C6A] rounded-[40px] p-8 md:p-12 flex flex-col md:flex-row items-center justify-between shadow-xl relative overflow-hidden">
          <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
          <div class="relative z-10 text-center md:text-left mb-8 md:mb-0">
            <span class="inline-block bg-white/20 text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-4">Ưu đãi giới hạn</span>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4 font-['Playfair_Display']">Săn Deal Hè - Giảm Ngay 15%</h2>
            <p class="text-white/80 text-lg mb-0 font-['Inter']">Sử dụng mã <span class="font-bold text-white underline">HE2024</span> cho tất cả các phòng tại Đà Nẵng.</p>
          </div>
          <div class="relative z-10">
            <button 
              @click="claimVoucher('HE2024')" 
              class="bg-white text-[#4A7055] px-10 py-4 rounded-2xl font-bold text-lg hover:shadow-2xl transition-all active:scale-95 flex items-center gap-2"
            >
              <Gift class="w-5 h-5" />
              Lưu mã ngay
            </button>
          </div>
        </div>
      </div>
    </section>

    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-10">
          <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2 font-['Playfair_Display']">Gợi ý phòng tốt nhất</h2>
            <p class="text-gray-600">Những chỗ nghỉ có điểm đánh giá cao nhất từ khách hàng</p>
          </div>
          <router-link to="/listing" class="text-[#4A7055] font-medium hover:text-[#3b5a44] hidden sm:block">
            Xem tất cả &rarr;
          </router-link>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          <RoomCard
            v-for="room in popularRooms"
            :key="room.id"
            :id="room.id"
            :title="room.title"
            :location="room.location"
            :type="room.type"
            :price="room.price"
            :imageUrl="room.imageUrl"
            :status="room.status"
            :parentTitle="room.parentTitle"
            :parentId="room.parentId"
            :rent_type="room.rent_type"
            :max_guests="room.max_guests"
            :max_children="room.max_children"
            :average_rating="room.average_rating"
          />
        </div>
        <div class="mt-8 text-center sm:hidden">
          <router-link to="/listing" class="text-[#4A7055] font-medium hover:text-[#3b5a44]">
            Xem tất cả &rarr;
          </router-link>
        </div>
      </div>
    </section>

    <section class="py-20 bg-[#FAF9F5]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-10 text-center font-['Playfair_Display']">Loại chỗ nghỉ của bạn</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

          <router-link to="/listing?type=house" class="group relative rounded-2xl overflow-hidden h-80 shadow-md">
            <img
              src="https://picsum.photos/seed/fullhouse/800/600"
              alt="Full House"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              referrerpolicy="no-referrer"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-8">
              <h3 class="text-3xl font-bold text-white mb-2">Full House</h3>
              <p class="text-white/90">Trải nghiệm không gian riêng tư trọn vẹn</p>
            </div>
          </router-link>

          <router-link to="/listing?type=room" class="group relative rounded-2xl overflow-hidden h-80 shadow-md">
            <img
              src="https://picsum.photos/seed/roomtype/800/600"
              alt="Room"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              referrerpolicy="no-referrer"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-8">
              <h3 class="text-3xl font-bold text-white mb-2">Phòng riêng</h3>
              <p class="text-white/90">Tiết kiệm chi phí, tiện nghi đầy đủ</p>
            </div>
          </router-link>

        </div>
      </div>
    </section>

    <section class="py-24 bg-[#F2F6F3]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <h2 class="text-4xl font-bold mb-6 text-[#4A7055] font-['Playfair_Display']">Về chúng tôi</h2>
            <p class="text-gray-700 text-lg mb-8 leading-relaxed">
              Luôn đảm bảo lịch trình của bạn sẽ được trọn vẹn. Sự riêng tư và thoải mái trong mỗi không gian chúng tôi mang lại cho bạn. Phương thức thanh toán nhanh chóng, an toàn, uy tín. Luôn nhận được sự yêu mến từ khách hàng.
            </p>
            <ul class="space-y-4 mb-8">
              <li class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-[#4A7055] flex items-center justify-center shrink-0 shadow-sm">
                  <span class="text-white font-bold">✓</span>
                </div>
                <span class="text-gray-800 font-medium">Đảm bảo lịch trình trọn vẹn</span>
              </li>
              <li class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-[#4A7055] flex items-center justify-center shrink-0 shadow-sm">
                  <span class="text-white font-bold">✓</span>
                </div>
                <span class="text-gray-800 font-medium">Không gian riêng tư & thoải mái</span>
              </li>
              <li class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-[#4A7055] flex items-center justify-center shrink-0 shadow-sm">
                  <span class="text-white font-bold">✓</span>
                </div>
                <span class="text-gray-800 font-medium">Thanh toán an toàn, uy tín</span>
              </li>
            </ul>
            <router-link 
              to="/about" 
              class="inline-block bg-[#4A7055] text-white px-8 py-3 rounded-full font-bold hover:bg-[#3b5a44] transition-colors shadow-md"
            >
              Tìm hiểu thêm
            </router-link>
          </div>
          <div class="relative">
            <div class="aspect-square rounded-3xl overflow-hidden shadow-xl border-4 border-white">
              <img
                src="https://picsum.photos/seed/aboutus/800/800"
                alt="About Us"
                class="w-full h-full object-cover"
                referrerpolicy="no-referrer"
              />
            </div>
            <div class="absolute -bottom-6 -left-6 bg-[#4A7055] p-6 rounded-2xl shadow-xl border border-[#4A7055]/50">
              <div class="text-4xl font-bold text-white mb-1">10k+</div>
              <div class="text-white/80 text-sm font-medium">Khách hàng hài lòng</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-12 text-center font-['Playfair_Display']">Khách hàng nói gì về chúng tôi</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="i in 4" :key="i" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow duration-300">
            <div class="flex gap-1 mb-4">
              <Star v-for="star in 5" :key="star" class="w-4 h-4 fill-amber-400 text-amber-400" />
            </div>
            <p class="text-gray-600 mb-6 text-sm italic">
              "Mình ở một tuần, trải nghiệm cực kì tốt, 100% sẽ giới thiệu cho bạn mình. Phòng đẹp hơn cả mình kỳ vọng mà giá cả phải chăng."
            </p>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-[#4A7055]/10 flex items-center justify-center text-[#4A7055] font-bold">
                U
              </div>
              <div>
                <div class="font-semibold text-gray-900 text-sm">User {{ i }}</div>
                <div class="text-xs text-gray-500">Khách hàng</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { Search, MapPin, Calendar, Users, Home as HomeIcon, Star, ChevronDown, ChevronLeft, ChevronRight, Gift } from 'lucide-vue-next';
import RoomCard from '../components/RoomCard.vue'; 

const router = useRouter();

interface Room {
  id: string;
  title: string;
  location: string;
  type: string;
  price: string;
  imageUrl: string;
  status: string;
  parentTitle?: string;
  parentId?: number | string;
  rent_type?: string;
  max_guests?: number | string;
  max_children?: number | string;
  average_rating?: number | string;
}

const bannerImages = ref([
  '/images/banner 1.jpg',
  '/images/banner 2.jpg',
  '/images/banner 3.jpg'
]);
const currentSlide = ref(0);
let slideInterval: ReturnType<typeof setInterval> | null = null;

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % bannerImages.value.length;
  resetInterval();
};

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + bannerImages.value.length) % bannerImages.value.length;
  resetInterval();
};

const goToSlide = (index: number) => {
  currentSlide.value = index;
  resetInterval();
};

const startInterval = () => {
  slideInterval = setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % bannerImages.value.length;
  }, 4000);
};

const resetInterval = () => {
  if (slideInterval) clearInterval(slideInterval);
  startInterval();
};

onUnmounted(() => {
  if (slideInterval) clearInterval(slideInterval);
});

const location = ref('');
const checkIn = ref('');
const checkOut = ref('');
const adults = ref<number | string>(2);
const children = ref<number | string>(0);
const type = ref('');
const activeDropdown = ref<string | null>(null);

// Mới thêm: Đồng bộ với Listing.vue
const nameSearch = ref('');
const minPrice = ref<number | null>(null);
const maxPrice = ref<number | null>(null);
const roomSubtype = ref('Tất cả');

const todayStr = new Date().toISOString().split('T')[0];

const formatDate = (dateStr: string) => {
  if (!dateStr) return '';
  try {
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return '';
    return d.toLocaleDateString('vi-VN');
  } catch (e) {
    return '';
  }
};

const openDatePicker = (e: Event) => {
  // Tìm input type="date" gần nhất với phần tử được click
  const input = (e.currentTarget as HTMLElement).querySelector('input[type="date"]') as HTMLInputElement;
  if (input) {
    if (typeof (input as any).showPicker === 'function') {
      (input as any).showPicker();
    } else {
      input.focus();
      input.click();
    }
  }
};

const availableLocations = ref<{id: number, title: string, location: string}[]>([]);

const fetchAvailableLocations = async () => {
  try {
    const response = await fetch('/api/locations/available');
    if (response.ok) {
      availableLocations.value = await response.json();
    }
  } catch (error) {
    console.error('Lỗi khi tải danh sách địa điểm:', error);
  }
};

const toggleDropdown = (menuName: string) => {
  activeDropdown.value = activeDropdown.value === menuName ? null : menuName;
};

const selectLocation = (loc: string) => {
  location.value = loc;
  activeDropdown.value = null;
};

const selectType = (selectedType: string) => {
  type.value = selectedType;
  activeDropdown.value = null;
  // Khi đổi sang loại hình khác thì reset quy mô phòng
  roomSubtype.value = 'Tất cả';
};

const handleSearch = () => {
  const minV = (minPrice.value !== null && minPrice.value !== undefined) ? Number(minPrice.value) : null;
  const maxV = (maxPrice.value !== null && maxPrice.value !== undefined) ? Number(maxPrice.value) : null;

  router.push({
    path: '/listing',
    query: {
      name: nameSearch.value || undefined,
      location: location.value || undefined,
      type: type.value || undefined,
      adults: adults.value || undefined,
      children: children.value || undefined,
      checkIn: checkIn.value || undefined,
      checkOut: checkOut.value || undefined,
      minPrice: minV !== null ? String(minV) : undefined,
      maxPrice: maxV !== null ? String(maxV) : undefined,
      subtype: roomSubtype.value !== 'Tất cả' ? roomSubtype.value : undefined
    }
  });
};

const claimVoucher = async (code: string) => {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    alert('Vui lòng đăng nhập để lưu mã ưu đãi!');
    router.push('/login');
    return;
  }

  try {
    const response = await fetch('/api/vouchers/claim', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ code })
    });
    const data = await response.json();
    
    if (response.ok) {
      alert('Tuyệt vời! Mã ưu đãi đã được lưu vào ví của bạn.');
    } else {
      alert(data.message || 'Có lỗi xảy ra khi lưu mã.');
    }
  } catch (error) {
    console.error('Lỗi khi lưu voucher:', error);
    alert('Không thể kết nối đến máy chủ.');
  }
};

const popularRooms = ref<Room[]>([]);

onMounted(async () => {
  startInterval(); 
  fetchAvailableLocations();

  try {
    const response = await fetch('/api/rooms?sort=rating');
    const data = await response.json();
    
    // Lọc: Chỉ lấy những phòng đang TRỐNG và ĐƯỢC HIỆN THỊ
    const visibleRooms = data.filter((room: any) => 
        room.status === 'available' && 
        (room.is_visible == 1 || room.is_visible === true)
    ); 

    popularRooms.value = visibleRooms.map((room: any) => {
      let thumb = room.image || 'https://picsum.photos/seed/room/800/600';
      
      if (thumb && !thumb.startsWith('http') && !thumb.startsWith('/storage/') && !thumb.startsWith('data:')) {
          thumb = thumb.startsWith('/') ? `/storage${thumb}` : `/storage/${thumb}`;
      }

      return {
        id: String(room.id),
        title: room.title,
        location: room.location,
        type: room.rent_type === 'whole_house' || room.rent_type === 'home' ? 'Nguyên căn' : 'Phòng riêng',
        price: Number(room.price).toLocaleString('vi-VN') + ' VNĐ/đêm', 
        imageUrl: thumb, 
        status: room.status,
        parentTitle: room.parent_title,
        parentId: room.parent_id,
        rent_type: room.rent_type,
        max_guests: room.max_guests,
        max_children: room.max_children,
        average_rating: room.average_rating
      };
    }).slice(0, 6); 
    
  } catch (error) {
    console.error('Lỗi khi tải dữ liệu trang chủ:', error);
  }
});
</script>

<style scoped>
/* CSS cho hiệu ứng mờ dần (Fade) khi đổi ảnh banner */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0.5;
}

/* CSS cho thanh tìm kiếm */
.hide-arrows::-webkit-outer-spin-button,
.hide-arrows::-webkit-inner-spin-button {
  -webkit-appearance: none;
  appearance: none;
  margin: 0;
}
.hide-arrows {
  -moz-appearance: textfield;
  appearance: textfield;
}

.date-overlay::-webkit-calendar-picker-indicator {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
  cursor: pointer;
  opacity: 0; 
}
</style>