<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#FAF9F5]">
    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row border border-gray-100">
      
      <div class="md:w-1/2 relative group hidden md:block">
        <img src="https://picsum.photos/seed/login/800/1000" alt="Login Background" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-[#4A7055]/10 group-hover:bg-[#4A7055]/0 transition-colors"></div>
      </div>


      <div class="md:w-1/2 p-10 md:p-14 flex flex-col justify-center w-full">
        <div>
          <h2 class="text-3xl font-bold text-gray-900 mb-2 font-['Playfair_Display']">
            Đăng Nhập
          </h2>
        </div>
        
        <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
          
          <div class="space-y-6">
            <div class="relative">
              <input
                id="email"
                type="email"
                v-model="form.email"
                required
                autofocus
                autocomplete="username"
                class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] focus:ring-0 text-gray-900 placeholder-transparent bg-transparent"
                placeholder="Email"
              />
              <label for="email" class="absolute left-0 -top-3.5 text-sm text-gray-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">
                Email đăng nhập
              </label>
            </div>
            
            <div class="relative">
              <input
                id="password"
                type="password"
                v-model="form.password"
                required
                autocomplete="current-password"
                class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] focus:ring-0 text-gray-900 placeholder-transparent bg-transparent"
                placeholder="Mật khẩu"
              />
              <label for="password" class="absolute left-0 -top-3.5 text-sm text-gray-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">
                Mật khẩu
              </label>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center cursor-pointer">
              <input type="checkbox" v-model="form.remember" class="w-4 h-4 text-[#4A7055] border-gray-300 rounded focus:ring-[#4A7055] cursor-pointer" />
              <span class="ms-2 text-sm text-gray-600 font-medium">Ghi nhớ đăng nhập</span>
            </label>

            <div class="text-sm">
              <router-link
                to="/forgot-password"
                class="font-medium text-[#4A7055] hover:text-[#3b5a44] transition-colors"
              >
                Quên mật khẩu?
              </router-link>
            </div>
          </div>

          <div>
            <button
              type="submit"
              :class="{ 'opacity-50 cursor-not-allowed': isProcessing }"
              :disabled="isProcessing"
              class="w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-[#4A7055] hover:bg-[#3b5a44] transition-colors shadow-md"
            >
              {{ isProcessing ? 'Đang xử lý...' : 'Đăng nhập' }}
            </button>
          </div>
          
          <div class="relative mt-10">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-200" />
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-3 bg-white text-gray-500 font-medium">Hoặc đăng nhập bằng cách khác</span>
            </div>
          </div>

          <div class="mt-6 flex justify-center gap-6">
            <button type="button" class="w-12 h-12 flex items-center justify-center rounded-full border border-gray-200 shadow-sm hover:bg-gray-50 transition-colors">
              <img src="https://authjs.dev/img/providers/google.svg" alt="Google" class="w-6 h-6" />
            </button>
            <button type="button" class="w-12 h-12 flex items-center justify-center rounded-full border border-gray-200 shadow-sm hover:bg-gray-50 transition-colors">
              <img src="https://authjs.dev/img/providers/facebook.svg" alt="Facebook" class="w-6 h-6" />
            </button>
            <button type="button" class="w-12 h-12 flex items-center justify-center rounded-full border border-gray-200 shadow-sm hover:bg-gray-50 transition-colors">
              <img src="https://raw.githubusercontent.com/vinh-nguyen/zalo-logo/master/zalo_sharelogo.png" alt="Zalo" class="w-7 h-7" />
            </button>
          </div>
        </form>
        
        <div class="text-center mt-12 pt-8 border-t border-gray-100">
          <p class="text-sm text-gray-600 font-medium">
            Tôi chưa có tài khoản?
            <router-link to="/register" class="font-bold text-[#4A7055] hover:text-[#3b5a44] ml-1">
              Đăng Ký
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

// Khai báo form state
const form = ref({
  email: '',
  password: '',
  remember: false
});

// Trạng thái nút submit
const isProcessing = ref(false);

const handleLogin = async () => {
  if (!form.value.email || !form.value.password) {
    alert('Vui lòng nhập đầy đủ email và mật khẩu');
    return;
  }

  isProcessing.value = true;

  try {
    const response = await fetch('/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: form.value.email,
        password: form.value.password
      })
    });

    const data = await response.json();

    if (!response.ok) {
      alert(data.message || 'Sai email hoặc mật khẩu!');
      return;
    }

    // Lưu Token và thông tin user vào LocalStorage
    localStorage.setItem('auth_token', data.access_token);
    localStorage.setItem('user_info', JSON.stringify(data.user));

    alert('Đăng nhập thành công!');

    if (data.user.role === 'admin') {
      window.location.href = '/admin/rooms'; // Dùng window.location.href để reload app và vào thẳng admin
    } else {
      window.location.href = '/';
    }

  } catch (error) {
    console.error('Lỗi kết nối:', error);
    alert('Không thể kết nối đến máy chủ!');
  } finally {
    isProcessing.value = false;
  }
};
</script>
