<template>
  <Link :href="`/room/${id}`" class="group block h-full">
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 h-full flex flex-col">
      
      <div class="relative aspect-[16/10] overflow-hidden">
        <img
          :src="imageUrl"
          :alt="title"
          class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
          referrerpolicy="no-referrer"
        />
        
        <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-full flex items-center gap-1.5 text-xs font-bold text-gray-800 shadow-sm border border-gray-100">
          <Star class="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
          <span>4.8</span>
        </div>
      </div>

      <div class="flex-grow flex flex-col p-6">
        
        <h3 class="text-xl font-extrabold text-gray-950 mb-1.5 font-['Playfair_Display'] line-clamp-1 group-hover:text-[#4A7055] transition-colors leading-tight">
          {{ title }}
        </h3>
        
        <div class="flex items-center gap-1.5 text-gray-600 text-sm mb-5 font-['Inter']">
          <MapPin class="w-4 h-4 shrink-0 text-[#4A7055] opacity-80" />
          <span class="line-clamp-1">{{ location }}</span>
        </div>
        
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
            <div class="text-2xl font-extrabold text-[#4A7055] font-['Playfair_Display']">{{ price }}</div>
            <div class="text-xs text-gray-500 font-['Inter']">/ đêm</div>
          </div>

        </div>
      </div>
    </div>
  </Link>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { MapPin, Star, Home as HomeIcon } from 'lucide-vue-next';

defineProps<{
  id: string;
  title: string;
  location: string;
  type: string;
  price: string;
  imageUrl: string;
}>();
</script>