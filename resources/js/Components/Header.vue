<template>
  <header class="bg-[#FAF9F5] shadow-sm sticky top-0 z-50 border-b border-[#4A7055]/30">
    <div class="max-w-[1440px] mx-auto px-10 h-20 flex items-center justify-between">
      
      <div class="flex items-center">
        <Link href="/" class="text-4xl font-extrabold text-black font-['Playfair_Display'] tracking-widest">
          LOGO
        </Link>
      </div>

      <div v-if="!isLoggedIn" class="hidden md:flex items-center space-x-4">
        <Link 
          href="/register" 
          class="bg-[#4A7055] text-white px-6 py-2.5 rounded-md font-medium font-['Inter'] text-sm hover:bg-[#3b5a44] transition-colors shadow-sm"
        >
          Đăng kí
        </Link>
        
        <Link
          href="/login"
          class="bg-[#4A7055] text-white px-6 py-2.5 rounded-md font-medium font-['Inter'] text-sm hover:bg-[#3b5a44] transition-colors shadow-sm"
        >
          Đăng nhập
        </Link>
      </div>

      <div v-else class="hidden md:flex items-center space-x-6 relative">
        
        <div @click="router.get('/profile')" class="flex items-center gap-3 cursor-pointer hover:opacity-80 transition-opacity">
          <div class="text-right">
            <div class="font-bold text-gray-900 font-['Inter'] text-sm">Death Pool</div>
            <div class="text-xs text-gray-500 font-['Inter']">Đà Nẵng, Việt Nam</div>
          </div>
          <img src="https://i.pravatar.cc/150?img=11" alt="Avatar" class="w-10 h-10 rounded-full border border-[#4A7055] object-cover" />
        </div>
        
        <div class="relative">
          <button 
            @click="showNotifications = !showNotifications" 
            class="relative text-[#4A7055] hover:text-[#3b5a44] transition-colors flex items-center justify-center mt-1"
          >
            <Bell class="w-6 h-6 fill-current" />
            <span class="absolute -top-0.5 -right-0.5 flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
            </span>
          </button>

          <div 
            v-if="showNotifications" 
            class="absolute right-0 mt-6 w-[450px] bg-white rounded-3xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] border border-gray-100 overflow-hidden z-50 transform origin-top-right transition-all"
          >
            <div class="px-6 py-5 border-b border-gray-100 bg-white">
              <h3 class="text-lg font-bold text-center text-gray-900 font-['Inter']">
                Thông báo của bạn
              </h3>
            </div>

            <div class="max-h-[420px] overflow-y-auto divide-y divide-gray-50 bg-white">
              <div v-for="i in 3" :key="i" class="p-5 flex gap-4 hover:bg-gray-50 transition-colors cursor-pointer">
                <div class="flex-shrink-0">
                  <img src="https://i.pravatar.cc/150?img=11" alt="Avatar" class="w-12 h-12 rounded-full object-cover border border-gray-100 shadow-sm" />
                </div>
                
                <div class="flex-1">
                  <h4 class="text-sm font-bold text-gray-900 mb-1 font-['Inter']">
                    Bạn đã đặt phòng thành công
                  </h4>
                  <p class="text-xs text-gray-600 mb-1.5 leading-relaxed font-['Inter']">
                    Bạn đã thanh toán thành công và phòng bạn đặt đã được chấp thuận. 
                    Hóa đơn chi tiết đã gửi về mail của bạn. <span class="text-blue-600 hover:underline">Xem chi tiết tại đây</span>
                  </p>
                  <p class="text-[10px] text-gray-400 font-medium">1 ngày trước</p>
                </div>
              </div>
            </div>
            <div class="h-6 bg-white"></div>
          </div>
        </div>

        <button @click="handleLogout" class="text-gray-400 hover:text-red-500 transition-colors ml-2" title="Đăng xuất">
          <LogOut class="w-5 h-5" />
        </button>
      </div>

      <div class="md:hidden flex items-center">
        <button class="text-gray-600 hover:text-[#4A7055]">
          <Menu class="w-7 h-7" />
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { Menu, Bell, LogOut } from 'lucide-vue-next';

// Trạng thái đăng nhập
const isLoggedIn = ref(false);

// Trạng thái bật/tắt dialog thông báo
const showNotifications = ref(false);

onMounted(() => {
  // Ghi chú nhỏ: Sau này team Backend sẽ bỏ localStorage và dùng session của Laravel, 
  // nhưng hiện tại dùng cái này để bạn test giao diện vẫn rất OK!
  isLoggedIn.value = localStorage.getItem('isLoggedIn') === 'true';
});

const handleLogout = () => {
  localStorage.removeItem('isLoggedIn');
  isLoggedIn.value = false;
  showNotifications.value = false; 
  router.get('/login'); // Inertia thay vì window.location
};
</script>