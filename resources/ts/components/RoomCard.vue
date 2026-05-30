<template>
  <div @click="goToDetail" class="group block h-full cursor-pointer">
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 h-full flex flex-col border border-gray-100/50">
      
      <div class="relative aspect-[16/10] overflow-hidden bg-gray-100">
        <img
          :src="imageUrl"
          :alt="title"
          class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
          referrerpolicy="no-referrer"
          @error="(e) => (e.target as HTMLImageElement).src = 'https://placehold.co/600x400/e2e8f0/64748b?text=No+Image'"
        />
        
        <div 
          v-if="status && status !== 'available'" 
          class="absolute top-4 left-4 px-3 py-1.5 rounded-full text-xs font-bold text-white shadow-md z-10 font-['Inter'] tracking-wide" 
          :class="statusClass"
        >
          {{ statusText }}
        </div>

        <button 
          @click.stop="toggleSaveRoom" 
          class="absolute top-4 right-4 p-2 rounded-full bg-white/50 backdrop-blur-md hover:bg-white hover:scale-110 transition-all duration-200 shadow-sm z-20 group/btn"
          title="Lưu phòng này"
        >
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            :class="['w-5 h-5 transition-colors', isSaved ? 'fill-red-500 text-red-500' : 'fill-transparent text-gray-800 group-hover/btn:text-red-500']" 
            viewBox="0 0 24 24" 
            stroke="currentColor" 
            stroke-width="2"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
          </svg>
        </button>
      </div>

      <div class="flex-grow flex flex-col p-5">
        
        <div class="flex justify-between items-start mb-2 gap-3">
          <h3 class="text-xl font-extrabold text-gray-950 font-['Playfair_Display'] line-clamp-1 group-hover:text-[#4A7055] transition-colors leading-tight">
            {{ title }}
          </h3>
          <div class="flex items-center gap-1 mt-1 text-sm font-bold text-gray-800 shrink-0">
            <Star class="w-3.5 h-3.5" :class="Number(average_rating) > 0 ? 'fill-amber-400 text-amber-400' : 'text-gray-300'" />
            <span>{{ Number(average_rating) > 0 ? average_rating : 'Mới' }}</span>
          </div>
        </div>
        
        <div class="flex items-center gap-1.5 text-gray-600 text-sm mb-1 font-['Inter']">
          <MapPin class="w-4 h-4 shrink-0 text-[#4A7055] opacity-80" />
          <span class="line-clamp-1">{{ location }}</span>
        </div>

        <div v-if="parentTitle" 
             @click.stop="$emit('filterByParent', parentId)"
             class="flex items-center gap-1.5 text-[#4A7055] hover:bg-emerald-100 cursor-pointer text-xs mb-4 font-bold font-['Inter'] bg-emerald-50 px-2 py-1 rounded-md w-fit transition-colors shadow-sm"
             title="Xem tất cả phòng thuộc homestay này"
        >
          <HomeIcon class="w-3 h-3 shrink-0" />
          <span class="line-clamp-1">Thuộc: {{ parentTitle }}</span>
        </div>
        <div v-else class="mb-5"></div>
        
        <div class="mt-auto pt-4 border-t border-gray-100/70 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="bg-gray-100 text-[#4A7055] p-2 rounded-full shadow-inner border border-gray-100">
              <HomeIcon class="w-4 h-4" />
            </div>
            <div class="text-sm text-gray-500 font-['Inter']">
              <span class="font-medium text-gray-900">{{ type }}</span>
            </div>
          </div>
          
          <div class="text-right">
            <div class="text-lg font-extrabold text-[#4A7055] font-['Inter']">{{ price }}</div>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'; 
import { useRouter } from 'vue-router';
import { MapPin, Star, Home as HomeIcon } from 'lucide-vue-next';

const router = useRouter();

// Khai báo sự kiện emit để TypeScript không cằn nhằn
defineEmits(['filterByParent']);

const props = defineProps<{
  id: string;
  title: string;
  location: string;
  type: string;
  price: string;
  imageUrl: string;
  status?: string; 
  // 2 biến này được khai báo để fix lỗi gạch đỏ:
  parentTitle?: string;
  parentId?: number | string;
  rent_type?: string;
  max_guests?: number | string;
  max_children?: number | string;
  average_rating?: number | string;
}>();

const isSaved = ref(false);

const goToDetail = () => {
  router.push(`/room/${props.id}`);
};

onMounted(() => {
  try {
    const savedRoomsList = JSON.parse(localStorage.getItem('saved_rooms') || '[]');
    isSaved.value = savedRoomsList.some((room: any) => room.id === props.id);
  } catch (e) {
    console.error("Lỗi đọc dữ liệu saved_rooms", e);
  }
});

const toggleSaveRoom = () => {
  let savedRoomsList = JSON.parse(localStorage.getItem('saved_rooms') || '[]');
  
  if (isSaved.value) {
    savedRoomsList = savedRoomsList.filter((room: any) => room.id !== props.id);
    isSaved.value = false;
  } else {
    savedRoomsList.push({
      id: props.id,
      title: props.title,
      location: props.location,
      type: props.type,
      price: props.price,
      imageUrl: props.imageUrl
    });
    isSaved.value = true;
  }
  
  localStorage.setItem('saved_rooms', JSON.stringify(savedRoomsList));
};

const statusText = computed(() => {
  switch (props.status) {
    case 'booked': return 'Đã đặt cọc';
    case 'in_use': return 'Đang có khách';
    case 'maintenance': return 'Bảo trì';
    default: return '';
  }
});

const statusClass = computed(() => {
  switch (props.status) {
    case 'booked': return 'bg-amber-500'; 
    case 'in_use': return 'bg-red-500'; 
    case 'maintenance': return 'bg-gray-500'; 
    default: return 'bg-[#4A7055]'; 
  }
});
</script>