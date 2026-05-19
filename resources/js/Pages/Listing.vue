<template>
  <div class="flex flex-col min-h-screen bg-[#FAF9F5]">
    
    <section class="relative pt-16 pb-24 bg-[#FAF9F5]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-12">
          <div class="flex-1 md:pr-10">
            <h1 class="text-4xl md:text-5xl font-bold text-[#4A7055] mb-4 font-['Playfair_Display'] leading-tight">
              Kỳ nghỉ trong mơ – Giá bất ngờ
            </h1>
            <p class="text-lg text-gray-700 mb-8 font-['Inter']">
              Săn ngay ưu đãi giảm đến 30% cho các homestay view biển đẹp nhất tháng này.
            </p>
            <button class="bg-[#4A7055] hover:bg-[#3b5a44] text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-md">
              Đặt phòng ngay
            </button>
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

      <div class="relative mt-16 w-full">
        <div class="absolute top-1/2 -translate-y-1/2 left-0 right-0 h-[1px] bg-[#4A7055]/30 z-0"></div>
        <div class="relative z-10 bg-white rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.08)] p-2 flex flex-col md:flex-row items-center max-w-5xl mx-auto border border-[#4A7055]/10">
          
          <div class="flex-1 w-full relative">
            <button @click="toggleDropdown('location')" class="flex items-center justify-between px-6 py-3 w-full hover:bg-gray-50 rounded-full transition-colors border-b md:border-b-0 md:border-r border-gray-100">
              <div class="flex items-center gap-3">
                <MapPin class="text-[#4A7055] opacity-60 w-5 h-5 shrink-0" />
                <div class="text-left">
                  <div class="text-sm font-medium text-gray-700">Địa điểm</div>
                  <div class="text-xs" :class="location ? 'text-[#4A7055] font-bold' : 'text-gray-400'">
                    {{ location || 'Bạn muốn đi đâu?' }}
                  </div>
                </div>
              </div>
              <ChevronDown class="text-gray-400 w-4 h-4 hidden lg:block" />
            </button>
            <div v-if="activeDropdown === 'location'" class="absolute top-full left-0 mt-4 w-72 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">
              <div class="p-2 max-h-60 overflow-y-auto">
                <button v-for="loc in daNangDistricts" :key="loc" @click="selectLocation(loc)" class="w-full text-left px-4 py-3 hover:bg-gray-50 rounded-xl text-sm font-medium text-gray-700 transition-colors">
                  <MapPin class="inline-block w-4 h-4 mr-2 text-gray-400" />
                  {{ loc }}
                </button>
              </div>
            </div>
          </div>

          <div class="flex-1 w-full relative">
            <div class="flex items-center justify-between px-6 py-3 w-full rounded-full border-b md:border-b-0 md:border-r border-gray-100 hover:bg-gray-50 transition-colors">
              <div class="flex items-center gap-3 w-full">
                <Calendar class="text-[#4A7055] opacity-60 w-5 h-5 shrink-0" />
                <div class="flex flex-col w-full">
                  <div class="text-sm font-medium text-gray-700 mb-0.5">Nhận - Trả phòng</div>
                  <div class="flex items-center gap-1 w-full mt-0.25">
                    <div class="relative flex-1 cursor-pointer group">
                      <div class="text-xs group-hover:text-[#4A7055] transition-colors" :class="checkIn ? 'text-[#4A7055] font-bold' : 'text-gray-400'">
                        {{ checkIn ? formatDate(checkIn) : 'ngày nhận' }}
                      </div>
                      <input type="date" v-model="checkIn" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer date-overlay" />
                    </div>
                    <span class="text-xs text-gray-300">-</span>
                    <div class="relative flex-1 cursor-pointer group">
                      <div class="text-xs group-hover:text-[#4A7055] transition-colors" :class="checkOut ? 'text-[#4A7055] font-bold' : 'text-gray-400'">
                        {{ checkOut ? formatDate(checkOut) : 'ngày trả' }}
                      </div>
                      <input type="date" v-model="checkOut" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer date-overlay" />
                    </div>
                  </div>
                </div>
              </div>
              <ChevronDown class="text-gray-400 w-4 h-4 hidden lg:block ml-2 shrink-0 pointer-events-none" />
            </div>
          </div>

          <div class="flex-1 w-full relative">
            <button @click="toggleDropdown('type')" class="flex items-center justify-between px-6 py-3 w-full hover:bg-gray-50 rounded-full transition-colors border-b md:border-b-0 md:border-r border-gray-100">
              <div class="flex items-center gap-3">
                <HomeIcon class="text-[#4A7055] opacity-60 w-5 h-5 shrink-0" />
                <div class="text-left">
                  <div class="text-sm font-medium text-gray-700">Loại hình thuê</div>
                  <div class="text-xs" :class="type ? 'text-[#4A7055] font-bold' : 'text-gray-400'">
                    {{ type || 'Phòng / Nguyên căn' }}
                  </div>
                </div>
              </div>
              <ChevronDown class="text-gray-400 w-4 h-4 hidden lg:block" />
            </button>
            <div v-if="activeDropdown === 'type'" class="absolute top-full left-0 mt-4 w-48 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden">
              <div class="flex flex-col">
                <button @click="selectType('Phòng')" class="text-left px-5 py-3 hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors border-b border-gray-50">Phòng</button>
                <button @click="selectType('Nguyên căn')" class="text-left px-5 py-3 hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors">Nguyên căn</button>
              </div>
            </div>
          </div>

          <div class="flex-1 w-full relative">
            <div class="flex items-center justify-between px-6 py-3 w-full rounded-full transition-colors">
              <div class="flex items-center gap-3 w-full">
                <Users class="text-[#4A7055] opacity-60 w-5 h-5 shrink-0" />
                <div class="flex flex-col w-full">
                  <div class="text-sm font-medium text-gray-700">Số lượng người</div>
                  <input type="number" v-model="guests" min="1" placeholder="Thêm khách" class="hide-arrows text-xs text-gray-500 bg-transparent outline-none w-full mt-0.5" />
                </div>
              </div>
              <ChevronDown class="text-gray-400 w-4 h-4 hidden lg:block ml-2 shrink-0" />
            </div>
          </div>

          <button @click="executeSearch" class="bg-[#4A7055] hover:bg-[#3b5a44] text-white p-4 rounded-full transition-colors w-full md:w-14 md:h-14 flex justify-center items-center shrink-0 ml-2 shadow-md">
            <Search class="w-5 h-5" />
            <span class="md:hidden ml-2 font-medium">Tìm kiếm</span>
          </button>
        </div>
      </div>
    </section>

    <div id="room-list-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-grow scroll-mt-32">
      <div class="mb-16">
        <div class="flex justify-between items-end mb-6">
          <div>
            <h2 class="text-3xl font-bold text-gray-900 font-['Playfair_Display']">
              {{ isSearching ? `Kết quả tìm kiếm (${filteredRooms.length})` : 'Được tìm kiếm nhiều nhất' }}
            </h2>
            <p v-if="isSearching" class="text-sm text-[#4A7055] mt-1 font-medium">
              {{ location || 'Đà Nẵng' }} <span v-if="type">• {{ type }}</span> <span v-if="guests">• {{ guests }} khách</span>
            </p>
          </div>
          <button v-if="isSearching" @click="resetSearch" class="text-sm text-gray-500 hover:text-[#4A7055] font-medium underline">
            Xóa bộ lọc
          </button>
        </div>

        <div v-if="filteredRooms.length === 0" class="text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm">
          <MapPin class="w-12 h-12 text-gray-300 mx-auto mb-4" />
          <h3 class="text-lg font-bold text-gray-900">Không tìm thấy homestay phù hợp</h3>
          <p class="text-gray-500">Thử thay đổi địa điểm hoặc loại hình thuê nhé.</p>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          <RoomCard v-for="(room, index) in filteredRooms" :key="index" :id="room.id" :title="room.title" :location="room.location" :type="room.type" :price="room.price" :imageUrl="room.imageUrl" />
        </div>
      </div>

      <div class="mb-16" v-if="!isSearching || type === 'Nguyên căn'">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 font-['Playfair_Display']">Homestay Nguyên căn</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          <RoomCard v-for="room in houseRooms" :key="room.id" :id="room.id" :title="room.title" :location="room.location" :type="room.type" :price="room.price" :imageUrl="room.imageUrl" />
        </div>
      </div>

      <div v-if="!isSearching || type === 'Phòng'">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 font-['Playfair_Display']">Homestay Phòng riêng</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          <RoomCard v-for="room in privateRooms" :key="room.id" :id="room.id" :title="room.title" :location="room.location" :type="room.type" :price="room.price" :imageUrl="room.imageUrl" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// ĐÃ SỬA: Import thêm onUnmounted để dọn dẹp slider khi rời trang
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3'; 
import { Search, MapPin, Calendar, Users, Home as HomeIcon, ChevronDown, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import RoomCard from '@/Components/RoomCard.vue';

// --- LOGIC SLIDER ẢNH (Bổ sung để làm cho banner chạy) ---
const bannerImages = [
  'https://images.unsplash.com/photo-1499793983690-e29da59ef1c2?q=80&w=2070&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=2070&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1502672260266-1c1de2d9d0cb?q=80&w=2080&auto=format&fit=crop'
];
const currentSlide = ref(0);
let slideInterval: any = null;

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % bannerImages.length;
  resetInterval();
};

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + bannerImages.length) % bannerImages.length;
  resetInterval();
};

const goToSlide = (index: number) => {
  currentSlide.value = index;
  resetInterval();
};

const startInterval = () => {
  slideInterval = setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % bannerImages.length;
  }, 4000); // 4 giây tự chuyển ảnh
};

const resetInterval = () => {
  clearInterval(slideInterval);
  startInterval();
};


// --- QUẢN LÝ TÌM KIẾM ---
const location = ref('');
const checkIn = ref('');
const checkOut = ref('');
const guests = ref('');
const type = ref('');
const isSearching = ref(false);

const activeDropdown = ref<string | null>(null);

const formatDate = (dateStr: string) => {
  if (!dateStr) return '';
  const [year, month, day] = dateStr.split('-');
  return `${day}/${month}/${year}`;
};


// --- CHẠY KHI TRANG VỪA LOAD LÊN ---
onMounted(() => {
  // 1. Kích hoạt tự động chuyển ảnh Banner
  startInterval();

  // 2. Kích hoạt hiệu ứng trượt mượt nếu bấm từ Home sang
  if (window.location.hash === '#room-list-section') {
    setTimeout(() => {
      const section = document.getElementById('room-list-section');
      if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
      }
    }, 150);
  }
});

// Khi rời trang thì tắt vòng lặp ảnh để web không bị nặng
onUnmounted(() => {
  clearInterval(slideInterval);
});


const daNangDistricts = [
  'Quận Hải Châu, Đà Nẵng',
  'Quận Sơn Trà, Đà Nẵng',
  'Quận Ngũ Hành Sơn, Đà Nẵng',
  'Quận Cẩm Lệ, Đà Nẵng',
  'Quận Thanh Khê, Đà Nẵng',
  'Quận Liên Chiểu, Đà Nẵng',
  'Huyện Hòa Vang, Đà Nẵng'
];

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
};

const mockDatabase = [
  { id: '1', title: 'Căn hộ Cẩm Lệ View Phố', location: 'Quận Cẩm Lệ, Đà Nẵng', type: 'Phòng', price: '120.000đ', imageUrl: 'https://picsum.photos/seed/dn1/800/600' },
  { id: '2', title: 'Villa Biển Mỹ Khê', location: 'Quận Sơn Trà, Đà Nẵng', type: 'Nguyên căn', price: '1.200.000đ', imageUrl: 'https://picsum.photos/seed/dn2/800/600' },
  { id: '3', title: 'Studio Trung Tâm Hải Châu', location: 'Quận Hải Châu, Đà Nẵng', type: 'Phòng', price: '350.000đ', imageUrl: 'https://picsum.photos/seed/dn3/800/600' },
  { id: '4', title: 'Homestay Gần Cầu Rồng', location: 'Quận Sơn Trà, Đà Nẵng', type: 'Phòng', price: '250.000đ', imageUrl: 'https://picsum.photos/seed/dn4/800/600' },
  { id: '5', title: 'Biệt thự Ngũ Hành Sơn', location: 'Quận Ngũ Hành Sơn, Đà Nẵng', type: 'Nguyên căn', price: '2.500.000đ', imageUrl: 'https://picsum.photos/seed/dn5/800/600' },
  { id: '6', title: 'Nhà Vườn Hòa Vang', location: 'Huyện Hòa Vang, Đà Nẵng', type: 'Nguyên căn', price: '800.000đ', imageUrl: 'https://picsum.photos/seed/dn6/800/600' },
];

const filteredRooms = ref([...mockDatabase]); 

const houseRooms = computed(() => mockDatabase.filter(r => r.type === 'Nguyên căn'));
const privateRooms = computed(() => mockDatabase.filter(r => r.type === 'Phòng'));

const executeSearch = () => {
  isSearching.value = true;
  activeDropdown.value = null; 

  filteredRooms.value = mockDatabase.filter(room => {
    const matchLocation = location.value === '' || room.location === location.value;
    const matchType = type.value === '' || room.type === type.value;
    return matchLocation && matchType;
  });
};

const resetSearch = () => {
  location.value = '';
  checkIn.value = '';
  checkOut.value = '';
  guests.value = '';
  type.value = '';
  isSearching.value = false;
  filteredRooms.value = [...mockDatabase];
};
</script>

<style scoped>
/* CSS CHO BANNER TRƯỢT ẢNH MƯỢT MÀ */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Ẩn icon tăng giảm số lượng */
.hide-arrows::-webkit-outer-spin-button,
.hide-arrows::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.hide-arrows {
  -moz-appearance: textfield;
}

/* ĐÂY LÀ ĐOẠN CSS MA THUẬT: Kéo giãn icon lịch bao phủ toàn bộ vùng chọn */
.date-overlay::-webkit-calendar-picker-indicator {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
  cursor: pointer;
  opacity: 0; /* Giữ nó trong suốt nhưng vẫn bấm được */
}
</style>