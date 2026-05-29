<template>
  <div class="flex flex-col min-h-screen bg-[#FAF9F5]">
    
    <section class="relative pt-16 pb-24 bg-[#FFF9E5]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-12">
          <div class="flex-1 md:pr-10">
            <h1 class="text-4xl md:text-5xl font-bold text-[#4A7055] mb-4 font-['Playfair_Display'] leading-tight">
              Kỳ nghỉ trong mơ – Giá bất ngờ
            </h1>
            <p class="text-lg text-gray-700 mb-8 font-['Inter']">
              Săn ngay ưu đãi giảm đến 30% cho các homestay view biển đẹp nhất tháng này.
            </p>
            <button class="bg-[#4A7055] hover:bg-[#3b5a44] text-white px-8 py-3.5 rounded-lg font-medium transition-colors shadow-md">
              Đặt phòng ngay
            </button>
          </div>

          <div class="flex-1 w-full relative group overflow-hidden rounded-tl-[100px] rounded-br-[100px] shadow-xl h-[400px]">
            <transition name="fade" mode="out-in">
              <img 
                :key="currentSlide"
                :src="bannerImages[currentSlide]" 
                alt="Homestay"
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

      <!-- Unified Search & Filter Bar -->
      <div class="relative mt-8 w-full max-w-5xl mx-auto px-4 sm:px-0">
        <div class="bg-white rounded-[32px] shadow-2xl p-6 md:p-8 border border-[#4A7055]/10 backdrop-blur-sm bg-white/95">
          <!-- Hàng 1: Tìm kiếm chính -->
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

          <!-- Hàng 2: Lọc nâng cao -->
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
                    v-for="t in [ {label: 'Tất cả', value: ''}, {label: 'Riêng', value: 'Phòng riêng'}, {label: 'Căn', value: 'Nguyên căn'} ]"
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
                @click="executeSearch" 
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

    <div id="room-list-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-grow scroll-mt-32">
      
      <div v-if="filterByParentId" class="mb-10 bg-[#4A7055]/10 border border-[#4A7055]/20 p-6 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4 text-[#4A7055]">
          <div class="bg-[#4A7055] text-white p-3 rounded-2xl shadow-md">
            <HomeIcon class="w-6 h-6" />
          </div>
          <div>
            <h3 class="text-xl font-bold">Bạn đang xem phòng của Homestay</h3>
            <p class="text-sm font-medium opacity-80">{{ selectedParentTitle }}</p>
          </div>
        </div>
        <button @click="clearParentFilter" class="bg-[#4A7055] text-white px-6 py-2.5 rounded-full font-bold hover:bg-[#3b5a44] transition-all shadow-sm">
          Xem tất cả homestay
        </button>
      </div>
      
      <div v-if="isSearching">
        <div class="flex justify-between items-end mb-8">
          <div>
            <h2 class="text-3xl font-bold text-gray-900 font-['Playfair_Display']">
              Kết quả tìm kiếm ({{ filteredRooms.length }})
            </h2>
            <p class="text-sm text-[#4A7055] mt-1 font-medium">
              {{ location || 'Mọi nơi' }} <span v-if="type">• {{ type }}</span> <span v-if="adults">• {{ adults }} người lớn</span> <span v-if="children">• {{ children }} trẻ em</span>
            </p>
          </div>
          <button @click="resetSearch" class="text-sm text-gray-500 hover:text-[#4A7055] font-medium underline">
            Xóa bộ lọc
          </button>
        </div>

        <div v-if="filteredRooms.length === 0" class="text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm">
          <MapPin class="w-12 h-12 text-gray-300 mx-auto mb-4" />
          <h3 class="text-lg font-bold text-gray-900">Không tìm thấy homestay phù hợp</h3>
          <p class="text-gray-500">Thử thay đổi địa điểm hoặc loại hình thuê nhé.</p>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          <RoomCard 
            v-for="room in filteredRooms" 
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
            @filterByParent="setParentFilter"
          />
        </div>
      </div>

      <div v-else>
        <div class="mb-20" v-if="houseRooms.length > 0">
          <div class="flex justify-between items-end mb-8">
            <div>
              <h2 class="text-3xl font-bold text-gray-900 font-['Playfair_Display'] mb-2">Homestay Nguyên căn</h2>
              <p class="text-gray-600 font-medium">Không gian riêng tư, thoải mái trọn vẹn cho cả gia đình</p>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <RoomCard 
              v-for="room in houseRooms" 
              :key="'house-'+room.id" 
              :id="room.id" 
              :title="room.title" 
              :location="room.location" 
              :type="room.type" 
              :price="room.price" 
              :imageUrl="room.imageUrl" 
              :status="room.status"
              :parentTitle="room.parentTitle"
              :parentId="room.parentId"
              :average_rating="room.average_rating"
              @filterByParent="setParentFilter"
            />
          </div>
        </div>

        <div class="mb-16" v-if="privateRooms.length > 0">
          <div class="flex justify-between items-end mb-8">
            <div>
              <h2 class="text-3xl font-bold text-gray-900 font-['Playfair_Display'] mb-2">Homestay Phòng riêng</h2>
              <p class="text-gray-600 font-medium">Tiết kiệm chi phí, lý tưởng cho cặp đôi hoặc du lịch một mình</p>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <RoomCard 
              v-for="room in privateRooms" 
              :key="'room-'+room.id" 
              :id="room.id" 
              :title="room.title" 
              :location="room.location" 
              :type="room.type" 
              :price="room.price" 
              :imageUrl="room.imageUrl" 
              :status="room.status"
              :parentTitle="room.parentTitle"
              :parentId="room.parentId"
              :average_rating="room.average_rating"
              @filterByParent="setParentFilter"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { Search, MapPin, Calendar, Users, Home as HomeIcon, ChevronDown, ChevronLeft, ChevronRight, X } from 'lucide-vue-next';
import RoomCard from '../components/RoomCard.vue';

interface Room {
  id: string;
  title: string;
  location: string;
  rawType: string;
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

const route = useRoute();
const router = useRouter();

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

const location = ref('');
const checkIn = ref('');
const checkOut = ref('');
const adults = ref<number | string>(2);
const children = ref<number | string>(0);
const type = ref('');
const activeDropdown = ref<string | null>(null);
const filterByParentId = ref<number | string | null>(null);

const nameSearch = ref('');
const minPrice = ref<number | null>(null);
const maxPrice = ref<number | null>(null);
const roomSubtype = ref('Tất cả');

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

const selectedParentTitle = computed(() => {
  if (!filterByParentId.value) return '';
  const room = allRooms.value.find(r => r.parentId == filterByParentId.value || r.id == filterByParentId.value);
  return room ? (room.parentTitle || room.title) : 'Homestay';
});

const setParentFilter = (parentId: number | string) => {
  filterByParentId.value = parentId;
  executeSearch();
};

const clearParentFilter = () => {
  filterByParentId.value = null;
  executeSearch();
};

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
  // Reset quy mô khi đổi loại hình
  roomSubtype.value = 'Tất cả';
};

const allRooms = ref<Room[]>([]);

const filteredRooms = computed(() => {
  return allRooms.value.filter(room => {
    const matchName = !nameSearch.value || 
                     room.title.toLowerCase().includes(nameSearch.value.toLowerCase());
    const matchLocation = !location.value || 
                         room.location.includes(location.value);
    const matchType = !type.value || 
                      room.type === type.value;
    
    // Lọc theo người lớn và trẻ em
    const adVal = Number(adults.value) || 0;
    const chVal = Number(children.value) || 0;
    const matchAdults = adVal <= 0 || Number(room.max_guests) >= adVal;
    const matchChildren = chVal <= 0 || Number(room.max_children) >= chVal;

    let matchSubtype = true;
    if (roomSubtype.value !== 'Tất cả') {
        const guestsMax = Number(room.max_guests);
        const childrenMax = Number(room.max_children || 0);
        const titleL = room.title.toLowerCase();
        
        if (roomSubtype.value === 'Đơn' || roomSubtype.value === 'Phòng đơn') {
            matchSubtype = guestsMax >= 2 && childrenMax >= 1;
        } else if (roomSubtype.value === 'Đôi' || roomSubtype.value === 'Phòng đôi') {
            matchSubtype = guestsMax >= 4 && childrenMax >= 2;
        } else if (roomSubtype.value === 'VIP') {
            matchSubtype = titleL.includes('vip') || guestsMax >= 6;
        }
    }

    // Lọc theo giá
    const rawPriceString = String(room.price).replace(/[^0-9]/g, '');
    const priceNum = Number(rawPriceString) || 0;
    
    const minVal = (minPrice.value !== null && minPrice.value !== undefined) ? Number(minPrice.value) : null;
    const maxVal = (maxPrice.value !== null && maxPrice.value !== undefined) ? Number(maxPrice.value) : null;
    
    const matchMinPrice = minVal === null || priceNum >= minVal;
    const matchMaxPrice = maxVal === null || priceNum <= maxVal;

    const matchParent = !filterByParentId.value || 
                      room.parentId == filterByParentId.value || 
                      room.id == filterByParentId.value;

    return matchName && matchLocation && matchType && matchAdults && matchChildren && matchSubtype && matchMinPrice && matchMaxPrice && matchParent;
  });
});

const isSearching = computed(() => {
    return !!(location.value || type.value || roomSubtype.value !== 'Tất cả' || minPrice.value || maxPrice.value || Number(adults.value) > 1 || Number(children.value) > 0);
});

const houseRooms = computed(() => {
  return filteredRooms.value.filter(room => room.rent_type === 'whole_house' || room.rent_type === 'home');
});

const privateRooms = computed(() => {
  return filteredRooms.value.filter(room => room.rent_type === 'private_room');
});

onMounted(async () => {
  startInterval();
  fetchAvailableLocations();

  try {
    const response = await fetch('/api/rooms');
    const data = await response.json();
    
    const visibleRooms = data.filter((room: any) => 
        room.status !== 'hidden' && 
        (room.is_visible == 1 || room.is_visible === true)
    );
    
    allRooms.value = visibleRooms.map((room: any) => {
      let thumb = room.image || 'https://picsum.photos/seed/room/800/600';
      if (thumb && !thumb.startsWith('http') && !thumb.startsWith('/storage/') && !thumb.startsWith('data:')) {
          thumb = thumb.startsWith('/') ? `/storage${thumb}` : `/storage/${thumb}`;
      }
      let rawType = room.rent_type;
      return {
        id: String(room.id),
        title: room.title,
        location: room.location,
        rawType: rawType, 
        type: (rawType === 'whole_house' || rawType === 'home') ? 'Nguyên căn' : 'Phòng riêng',
        price: Number(room.price).toLocaleString('vi-VN') + ' VNĐ/đêm',
        imageUrl: thumb,
        status: room.status,
        parentTitle: room.parent_title,
        parentId: room.parent_id,
        rent_type: rawType,
        max_guests: room.max_guests,
        max_children: room.max_children,
        average_rating: room.average_rating
      };
    });

    if (Object.keys(route.query).length > 0) {
      location.value = (route.query.location as string) || '';
      if (route.query.type === 'house' || route.query.type === 'Nguyên căn') type.value = 'Nguyên căn';
      else if (route.query.type === 'room' || route.query.type === 'Phòng riêng') type.value = 'Phòng riêng';
      else type.value = (route.query.type as string) || '';
      adults.value = route.query.adults ? Number(route.query.adults) : 2;
      children.value = route.query.children ? Number(route.query.children) : 0;
      minPrice.value = route.query.minPrice ? Number(route.query.minPrice) : null;
      maxPrice.value = route.query.maxPrice ? Number(route.query.maxPrice) : null;
      roomSubtype.value = (route.query.subtype as string) || 'Tất cả';
      checkIn.value = (route.query.checkIn as string) || '';
      checkOut.value = (route.query.checkOut as string) || '';
    }
  } catch (error) {
    console.error('Lỗi khi tải danh sách phòng:', error);
  }

  if (route.hash === '#room-list-section') {
    setTimeout(() => {
      const section = document.getElementById('room-list-section');
      if (section) section.scrollIntoView({ behavior: 'smooth' });
    }, 300); 
  }
});

onUnmounted(() => {
  if (slideInterval) clearInterval(slideInterval);
});

const executeSearch = () => {
    activeDropdown.value = null; 
    // Đồng bộ URL khi nhấn "Cập nhật" để bộ lọc không bị mất khi F5
    router.replace({
        query: {
            location: location.value || undefined,
            type: type.value || undefined,
            adults: adults.value || undefined,
            children: children.value || undefined,
            minPrice: minPrice.value || undefined,
            maxPrice: maxPrice.value || undefined,
            subtype: roomSubtype.value !== 'Tất cả' ? roomSubtype.value : undefined,
            checkIn: checkIn.value || undefined,
            checkOut: checkOut.value || undefined,
        }
    });

    const section = document.getElementById('room-list-section');
    if (section) section.scrollIntoView({ behavior: 'smooth' });
};

const resetSearch = () => {
  nameSearch.value = '';
  location.value = '';
  checkIn.value = '';
  checkOut.value = '';
  adults.value = 1;
  children.value = 0;
  type.value = '';
  minPrice.value = null;
  maxPrice.value = null;
  roomSubtype.value = 'Tất cả';
  filterByParentId.value = null;
  router.replace({ query: {} }); 
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

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