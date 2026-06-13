<template>
  <div class="flex h-screen bg-[#FCFAF6] font-sans text-gray-800">
    
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col z-20 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
      
      <div class="h-20 flex items-center justify-center border-b border-gray-100 bg-[#FCFAF6]">
        <router-link to="/">
        <img src="/images/logohomebooking.svg" alt="Homestay Logo" class="h-55 w-auto object-contain" />
        </router-link>
        </div>
      <nav class="flex-1 py-6 flex flex-col gap-1 overflow-y-auto px-3 bg-[#FCFAF6]">
        <router-link to="/admin/dashboard" 
          class="flex items-center px-4 py-3 rounded-xl text-gray-500 hover:bg-[#EDF2EF] hover:text-[#4A7055] transition-all duration-200 group" 
          active-class="bg-[#4A7055] !text-white shadow-md">
          <LayoutDashboard class="w-5 h-5 mr-3 transition-transform group-hover:scale-110" /> 
          <span class="font-medium">Quản lý thống kê</span>
        </router-link>

        <router-link to="/admin/rooms" 
          class="flex items-center px-4 py-3 rounded-xl text-gray-500 hover:bg-[#EDF2EF] hover:text-[#4A7055] transition-all duration-200 group" 
          active-class="bg-[#4A7055] !text-white shadow-md">
          <Home class="w-5 h-5 mr-3 transition-transform group-hover:scale-110" /> 
          <span class="font-medium">Quản lý phòng</span>
        </router-link>

        <router-link to="/admin/booking-management" 
          class="flex items-center px-4 py-3 rounded-xl text-gray-500 hover:bg-[#EDF2EF] hover:text-[#4A7055] transition-all duration-200 group" 
          active-class="bg-[#4A7055] !text-white shadow-md">
          <ClipboardList class="w-5 h-5 mr-3 transition-transform group-hover:scale-110" /> 
          <span class="font-medium">Quản lý Booking</span>
        </router-link>

        <router-link to="/admin/invoices" 
          class="flex items-center px-4 py-3 rounded-xl text-gray-500 hover:bg-[#EDF2EF] hover:text-[#4A7055] transition-all duration-200 group" 
          active-class="bg-[#4A7055] !text-white shadow-md">
          <Receipt class="w-5 h-5 mr-3 transition-transform group-hover:scale-110" /> 
          <span class="font-medium">Hóa đơn & Thanh toán</span>
        </router-link>

        <router-link to="/admin/users" 
          class="flex items-center px-4 py-3 rounded-xl text-gray-500 hover:bg-[#EDF2EF] hover:text-[#4A7055] transition-all duration-200 group" 
          active-class="bg-[#4A7055] !text-white shadow-md">
          <Users class="w-5 h-5 mr-3 transition-transform group-hover:scale-110" /> 
          <span class="font-medium">Quản lý người dùng</span>
        </router-link>

        <router-link to="/admin/vouchers" 
          class="flex items-center px-4 py-3 rounded-xl text-gray-500 hover:bg-[#EDF2EF] hover:text-[#4A7055] transition-all duration-200 group" 
          active-class="bg-[#4A7055] !text-white shadow-md">
          <Ticket class="w-5 h-5 mr-3 transition-transform group-hover:scale-110" /> 
           <span class="font-medium">Quản lý mã giảm giá</span>
         </router-link>

        <router-link to="/admin/reviews" 
          class="flex items-center px-4 py-3 rounded-xl text-gray-500 hover:bg-[#EDF2EF] hover:text-[#4A7055] transition-all duration-200 group" 
          active-class="bg-[#4A7055] !text-white shadow-md">
          <Star class="w-5 h-5 mr-3 transition-transform group-hover:scale-110" /> 
          <span class="font-medium">Quản lý đánh giá</span>
        </router-link>
      </nav>

      <div class="p-4 border-t border-gray-100 mb-2 bg-[#FCFAF6]">
        <button @click="handleLogout" class="flex items-center justify-center w-full px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors">
          <LogOut class="w-5 h-5 mr-2" /> Đăng xuất
        </button>
      </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden ">
      <header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-200 flex items-center justify-between px-10 shrink-0 z-10">
        <h2 class="text-xl font-bold text-gray-800 tracking-tight ">Hệ thống quản lý Homestay</h2>
        <div class="flex items-center gap-4">
          <div class="w-10 h-10 rounded-full bg-[#4A7055] text-white flex items-center justify-center font-bold shadow-md cursor-pointer hover:bg-[#3b5a44] transition-colors">
            A
          </div>
        </div>
      </header>
      
      <div class="flex-1 overflow-y-auto p-10">
        <router-view></router-view>
      </div>
    </main>
    
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { LayoutDashboard, Home, ClipboardList, Users, LogOut, Receipt, Ticket, Star } from 'lucide-vue-next';

const user = ref<any>(null);

const handleLogout = () => {
  if(confirm('Bạn có chắc muốn đăng xuất khỏi quản trị?')) {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_info');
    window.location.href = '/login';
  }
};

onMounted(() => {
  const userInfo = localStorage.getItem('user_info');
  if (userInfo) {
    user.value = JSON.parse(userInfo);
  }
});
</script>