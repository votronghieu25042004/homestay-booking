<template>
  <div class="min-h-[calc(100vh-4rem-20rem)] bg-[#FAF9F5] pb-20">
    
    <div class="bg-white border-b border-gray-200 shadow-sm pt-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex space-x-12 text-lg font-bold font-['Playfair_Display']">
          <button 
            @click="activeTab = 'saved'"
            :class="['pb-4 border-b-2 transition-colors', activeTab === 'saved' ? 'border-[#4A7055] text-[#4A7055]' : 'border-transparent text-gray-400 hover:text-[#4A7055]']"
          >
            Đã lưu
          </button>
          <button 
            @click="activeTab = 'history'"
            :class="['pb-4 border-b-2 transition-colors', activeTab === 'history' ? 'border-[#4A7055] text-[#4A7055]' : 'border-transparent text-gray-400 hover:text-[#4A7055]']"
          >
            Lịch sử
          </button>
          <button 
            @click="activeTab = 'account'"
            :class="['pb-4 border-b-2 transition-colors', activeTab === 'account' ? 'border-[#4A7055] text-[#4A7055]' : 'border-transparent text-gray-400 hover:text-[#4A7055]']"
          >
            Thông tin tài khoản
          </button>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
      
      <div v-if="activeTab === 'account'" class="max-w-3xl">
        <form class="space-y-8 bg-white p-10 rounded-3xl shadow-sm border border-gray-100" @submit.prevent="handleUpdateAccount">
          <div class="space-y-6">
            <div class="relative">
              <input type="text" id="fullname" v-model="accountInfo.fullname" class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] text-gray-900 placeholder-transparent" placeholder="Họ và Tên" />
              <label for="fullname" class="absolute left-0 -top-3.5 text-sm text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">Họ và Tên</label>
            </div>
            
            <div class="relative">
              <input type="email" id="email" v-model="accountInfo.email" class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] text-gray-900 placeholder-transparent" placeholder="Email" />
              <label for="email" class="absolute left-0 -top-3.5 text-sm text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">Email</label>
            </div>

            <div class="relative">
              <input type="tel" id="phone" v-model="accountInfo.phone" class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] text-gray-900 placeholder-transparent" placeholder="Số Điện Thoại" />
              <label for="phone" class="absolute left-0 -top-3.5 text-sm text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">Số Điện Thoại</label>
            </div>

            <div class="relative">
              <input type="password" id="password" v-model="accountInfo.password" class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] text-gray-900 placeholder-transparent" placeholder="Mật Khẩu" />
              <label for="password" class="absolute left-0 -top-3.5 text-sm text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">Mật Khẩu</label>
            </div>
          </div>

          <div class="pt-4">
            <button type="submit" class="bg-[#4A7055] hover:bg-[#3b5a44] text-white px-10 py-3 rounded-lg font-semibold transition-colors shadow-md">
              Chỉnh sửa
            </button>
          </div>
        </form>
      </div>

      <div v-if="activeTab === 'saved' || activeTab === 'history'">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          
          <div v-for="room in mockRooms" :key="room.id" class="group relative rounded-2xl overflow-hidden h-72 shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer">
            <img :src="room.imageUrl" :alt="room.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent flex flex-col justify-end p-5">
              <h3 class="text-white font-bold text-lg leading-tight mb-1 line-clamp-1">{{ room.title }}</h3>
              <p class="text-white/90 text-sm font-medium mb-2">{{ room.location }}</p>
              <div class="flex items-end justify-between">
                <p class="text-white/80 text-xs">Chỗ nghỉ: <span class="font-semibold">{{ room.type }}</span></p>
                <p class="text-[#F2C94C] font-bold text-sm">{{ room.price }} <span class="text-white/70 text-xs font-normal">/ đêm</span></p>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

// State quản lý tab đang mở (Mặc định mở tab Thông tin tài khoản)
const activeTab = ref('account');

// Mock data cho Form Thông tin
const accountInfo = ref({
  fullname: 'Death Pool',
  email: 'deathpool@example.com',
  phone: '0901234567',
  password: 'mysecretpassword'
});

const handleUpdateAccount = () => {
  alert("Cập nhật thông tin thành công!");
};

// Mock data cho các phòng Đã lưu / Lịch sử
const mockRooms = [
  {
    id: '1',
    title: 'Phòng Mơ Màng, Số 10 Núi Thành',
    location: 'Cẩm Lệ, TP. Đà Nẵng',
    type: 'Phòng',
    price: '120.000đ',
    imageUrl: 'https://picsum.photos/seed/room10/800/600',
  },
  {
    id: '2',
    title: 'Sunshine Studio View Biển',
    location: 'Sơn Trà, TP. Đà Nẵng',
    type: 'Nguyên căn',
    price: '450.000đ',
    imageUrl: 'https://picsum.photos/seed/room11/800/600',
  },
  {
    id: '3',
    title: 'Chill House - Căn hộ ấm cúng',
    location: 'Hải Châu, TP. Đà Nẵng',
    type: 'Phòng',
    price: '200.000đ',
    imageUrl: 'https://picsum.photos/seed/room12/800/600',
  },
  {
    id: '4',
    title: 'Phòng Mơ Màng, Số 10 Núi Thành',
    location: 'Cẩm Lệ, TP. Đà Nẵng',
    type: 'Phòng',
    price: '120.000đ',
    imageUrl: 'https://picsum.photos/seed/room13/800/600',
  },
  {
    id: '5',
    title: 'Sunshine Studio View Biển',
    location: 'Sơn Trà, TP. Đà Nẵng',
    type: 'Nguyên căn',
    price: '450.000đ',
    imageUrl: 'https://picsum.photos/seed/room14/800/600',
  },
  {
    id: '6',
    title: 'Chill House - Căn hộ ấm cúng',
    location: 'Hải Châu, TP. Đà Nẵng',
    type: 'Phòng',
    price: '200.000đ',
    imageUrl: 'https://picsum.photos/seed/room15/800/600',
  }
];
</script>