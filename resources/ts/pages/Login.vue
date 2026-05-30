<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-[#FAF9F5] to-gray-100">
    <div class="max-w-5xl w-full bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col md:flex-row border border-gray-100/50">
      
      <div class="md:w-1/2 relative group hidden md:block">
        <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&q=80&w=800" alt="Login Background" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        <div class="absolute bottom-10 left-10 right-10 text-white">
          <h3 class="text-3xl font-bold font-['Playfair_Display'] mb-2 shadow-sm">Chào mừng trở lại!</h3>
          <p class="text-white/80 font-medium">Đăng nhập để tiếp tục trải nghiệm tuyệt vời cùng chúng tôi.</p>
        </div>
      </div>

      <div class="md:w-1/2 p-10 md:p-16 flex flex-col justify-center w-full bg-white relative z-10">
        <div>
          <h2 class="text-3xl font-bold text-gray-900 mb-2 font-['Playfair_Display'] tracking-tight">
            Đăng Nhập
          </h2>
          <p class="text-gray-500 mb-8 font-medium">Vui lòng điền thông tin bên dưới để tiếp tục.</p>
        </div>
        
        <form class="space-y-6" @submit.prevent="handleLogin">
          
          <div class="space-y-5">
            <div class="relative group">
              <input
                id="email"
                type="email"
                v-model="form.email"
                required
                autofocus
                autocomplete="username"
                class="peer w-full px-5 py-4 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4A7055]/20 focus:border-[#4A7055] text-gray-900 bg-gray-50/50 hover:bg-gray-50 transition-colors placeholder-transparent"
                placeholder="Email đăng nhập"
              />
              <label for="email" class="absolute left-4 -top-2.5 bg-white px-1.5 text-sm text-gray-500 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-[#4A7055] font-medium rounded-md pointer-events-none">
                Email đăng nhập
              </label>
            </div>
            
            <div class="relative group">
              <input
                id="password"
                type="password"
                v-model="form.password"
                required
                autocomplete="current-password"
                class="peer w-full px-5 py-4 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4A7055]/20 focus:border-[#4A7055] text-gray-900 bg-gray-50/50 hover:bg-gray-50 transition-colors placeholder-transparent"
                placeholder="Mật khẩu"
              />
              <label for="password" class="absolute left-4 -top-2.5 bg-white px-1.5 text-sm text-gray-500 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-[#4A7055] font-medium rounded-md pointer-events-none">
                Mật khẩu
              </label>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center cursor-pointer group">
              <input type="checkbox" v-model="form.remember" class="w-4 h-4 text-[#4A7055] border-gray-300 rounded focus:ring-[#4A7055] transition-colors" />
              <span class="ms-2 text-sm text-gray-600 font-medium group-hover:text-gray-900 transition-colors">Ghi nhớ đăng nhập</span>
            </label>

            <div class="text-sm">
              <router-link
                to="/forgot-password"
                class="font-semibold text-[#4A7055] hover:text-[#3b5a44] transition-colors"
              >
                Quên mật khẩu?
              </router-link>
            </div>
          </div>

          <div>
            <button
              type="submit"
              :class="{ 'opacity-70 cursor-not-allowed scale-[0.98]': isProcessing }"
              :disabled="isProcessing"
              class="w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-[#4A7055] hover:bg-[#3b5a44] active:scale-[0.98] transition-all shadow-md hover:shadow-lg"
            >
              {{ isProcessing ? 'Đang xử lý...' : 'Đăng nhập ngay' }}
            </button>
          </div>
          
          <div class="relative mt-10">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-200" />
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-4 bg-white text-gray-400 font-semibold tracking-wide uppercase text-xs">Hoặc đăng nhập bằng</span>
            </div>
          </div>

          <div class="mt-6 flex justify-center gap-4">
            <button type="button" class="flex-1 h-12 flex items-center justify-center rounded-xl border border-gray-200 shadow-sm hover:bg-gray-50 transition-colors gap-2 bg-white">
              <img src="https://authjs.dev/img/providers/google.svg" alt="Google" class="w-5 h-5" />
              <span class="text-sm font-bold text-gray-700">Google</span>
            </button>
            <button type="button" class="flex-1 h-12 flex items-center justify-center rounded-xl border border-gray-200 shadow-sm hover:bg-gray-50 transition-colors gap-2 bg-white">
              <img src="https://authjs.dev/img/providers/facebook.svg" alt="Facebook" class="w-5 h-5" />
              <span class="text-sm font-bold text-gray-700">Facebook</span>
            </button>
          </div>
        </form>
        
        <div class="text-center mt-10 pt-8 border-t border-gray-100">
          <p class="text-sm text-gray-600 font-medium">
            Bạn chưa có tài khoản?
            <router-link to="/register" class="font-bold text-[#4A7055] hover:text-[#3b5a44] ml-1 transition-colors underline underline-offset-4 decoration-2 decoration-[#4A7055]/30 hover:decoration-[#4A7055]">
              Đăng Ký Ngay
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
