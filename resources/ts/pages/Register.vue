<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-[#FAF9F5] to-gray-100">
    <div class="max-w-5xl w-full bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col md:flex-row border border-gray-100/50">
      
      <div class="md:w-1/2 relative group hidden md:block">
        <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&q=80&w=800" alt="Register Background" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        <div class="absolute bottom-10 left-10 right-10 text-white">
          <h3 class="text-3xl font-bold font-['Playfair_Display'] mb-2 shadow-sm">Khám phá thế giới</h3>
          <p class="text-white/80 font-medium">Tạo tài khoản để nhận những ưu đãi tốt nhất từ hàng ngàn homestay.</p>
        </div>
      </div>

      <div class="md:w-1/2 p-10 md:p-14 flex flex-col justify-center w-full bg-white relative z-10">
        <div>
          <h2 class="text-3xl font-bold text-gray-900 mb-2 font-['Playfair_Display'] tracking-tight">
            Tạo Tài Khoản
          </h2>
          <p class="text-gray-500 mb-6 font-medium">Chỉ vài bước đơn giản để bắt đầu hành trình của bạn.</p>
        </div>
        
        <form class="space-y-5" @submit.prevent="handleRegister">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="relative group col-span-1">
              <input
                id="name"
                type="text"
                v-model="form.name"
                required
                autofocus
                autocomplete="name"
                class="peer w-full px-5 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4A7055]/20 focus:border-[#4A7055] text-gray-900 bg-gray-50/50 hover:bg-gray-50 transition-colors placeholder-transparent"
                placeholder="Họ và Tên"
              />
              <label for="name" class="absolute left-4 -top-2.5 bg-white px-1.5 text-sm text-gray-500 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3.5 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-[#4A7055] font-medium rounded-md pointer-events-none">Họ và Tên</label>
              <p v-if="validationErrors.name" class="mt-1 text-red-500 text-[11px]">{{ validationErrors.name[0] }}</p>
            </div>
            
            <div class="relative group col-span-1">
              <input
                id="phone"
                type="tel"
                v-model="form.phone"
                required
                class="peer w-full px-5 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4A7055]/20 focus:border-[#4A7055] text-gray-900 bg-gray-50/50 hover:bg-gray-50 transition-colors placeholder-transparent"
                placeholder="Số điện thoại"
              />
              <label for="phone" class="absolute left-4 -top-2.5 bg-white px-1.5 text-sm text-gray-500 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3.5 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-[#4A7055] font-medium rounded-md pointer-events-none">Số điện thoại</label>
              <p v-if="validationErrors.phone" class="mt-1 text-red-500 text-[11px]">{{ validationErrors.phone[0] }}</p>
            </div>
          </div>

          <div class="relative group">
            <input
              id="email"
              type="email"
              v-model="form.email"
              required
              autocomplete="email"
              class="peer w-full px-5 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4A7055]/20 focus:border-[#4A7055] text-gray-900 bg-gray-50/50 hover:bg-gray-50 transition-colors placeholder-transparent"
              placeholder="Email"
            />
            <label for="email" class="absolute left-4 -top-2.5 bg-white px-1.5 text-sm text-gray-500 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3.5 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-[#4A7055] font-medium rounded-md pointer-events-none">Email</label>
            <p v-if="validationErrors.email" class="mt-1 text-red-500 text-[11px]">{{ validationErrors.email[0] }}</p>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="relative group col-span-1">
              <input
                id="password"
                type="password"
                v-model="form.password"
                required
                minlength="6"
                autocomplete="new-password"
                class="peer w-full px-5 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4A7055]/20 focus:border-[#4A7055] text-gray-900 bg-gray-50/50 hover:bg-gray-50 transition-colors placeholder-transparent"
                placeholder="Mật Khẩu"
              />
              <label for="password" class="absolute left-4 -top-2.5 bg-white px-1.5 text-sm text-gray-500 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3.5 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-[#4A7055] font-medium rounded-md pointer-events-none">Mật khẩu</label>
              <p v-if="validationErrors.password" class="mt-1 text-red-500 text-[11px]">{{ validationErrors.password[0] }}</p>
            </div>

            <div class="relative group col-span-1">
              <input
                id="password_confirmation"
                type="password"
                v-model="form.password_confirmation"
                required
                minlength="6"
                autocomplete="new-password"
                class="peer w-full px-5 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4A7055]/20 focus:border-[#4A7055] text-gray-900 bg-gray-50/50 hover:bg-gray-50 transition-colors placeholder-transparent"
                placeholder="Xác nhận mật khẩu"
              />
              <label for="password_confirmation" class="absolute left-4 -top-2.5 bg-white px-1.5 text-sm text-gray-500 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3.5 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-[#4A7055] font-medium rounded-md pointer-events-none">Xác nhận mật khẩu</label>
            </div>
          </div>

          <div class="relative group">
            <input
              id="ref_code"
              type="text"
              v-model="form.ref_code"
              class="peer w-full px-5 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4A7055]/20 focus:border-[#4A7055] text-gray-900 bg-gray-50/50 hover:bg-gray-50 transition-colors placeholder-transparent"
              placeholder="Mã giới thiệu (Nếu có)"
            />
            <label for="ref_code" class="absolute left-4 -top-2.5 bg-white px-1.5 text-sm text-gray-500 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3.5 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-[#4A7055] font-medium rounded-md pointer-events-none">Mã giới thiệu (Tùy chọn)</label>
          </div>

          <div class="flex items-center group pt-1">
            <input id="terms" type="checkbox" v-model="form.terms" required class="h-4 w-4 text-[#4A7055] focus:ring-[#4A7055] border-gray-300 rounded cursor-pointer transition-colors" />
            <label for="terms" class="ml-2 block text-sm text-gray-600 font-medium cursor-pointer group-hover:text-gray-900 transition-colors">
              Tôi đồng ý với các <span class="text-[#4A7055] underline underline-offset-2">điều khoản sử dụng</span>
            </label>
          </div>

          <div>
            <button
              type="submit"
              :class="{ 'opacity-70 cursor-not-allowed scale-[0.98]': isProcessing || !form.terms }"
              :disabled="isProcessing || !form.terms"
              class="w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-[#4A7055] hover:bg-[#3b5a44] active:scale-[0.98] transition-all shadow-md hover:shadow-lg mt-4"
            >
              {{ isProcessing ? 'Đang xử lý...' : 'Tạo tài khoản ngay' }}
            </button>
          </div>
          
          <div class="relative mt-8">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-200" />
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-4 bg-white text-gray-400 font-semibold tracking-wide uppercase text-xs">Hoặc đăng ký bằng</span>
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
        
        <div class="text-center mt-8 pt-6 border-t border-gray-100">
          <p class="text-sm text-gray-600 font-medium">
            Đã có tài khoản?
            <router-link to="/login" class="font-bold text-[#4A7055] hover:text-[#3b5a44] ml-1 transition-colors underline underline-offset-4 decoration-2 decoration-[#4A7055]/30 hover:decoration-[#4A7055]">
              Đăng nhập
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

// State lưu trữ dữ liệu form
const form = ref({
  name: '',
  email: '',
  phone: '', 
  password: '',
  password_confirmation: '', 
  ref_code: '',
  terms: false
});

// State lưu trữ lỗi trả về từ API (thay thế cho InputError của Inertia)
const validationErrors = ref<Record<string, string[]>>({});
const isProcessing = ref(false);

const handleRegister = async () => {
  // mật khẩu gõ 2 lần có giống nhau không
  if (form.value.password !== form.value.password_confirmation) {
    alert('Mật khẩu xác nhận không khớp!');
    return;
  }

  isProcessing.value = true;
  validationErrors.value = {}; 

  try {
    const response = await fetch('/api/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        name: form.value.name,
        email: form.value.email,
        phone: form.value.phone,
        password: form.value.password,
        password_confirmation: form.value.password_confirmation,
        ref_code: form.value.ref_code
      })
    });

    const data = await response.json();

    if (!response.ok) {
      // Bắt lỗi Validation từ Laravel và gán vào biến validationErrors
      if (data.errors) {
        validationErrors.value = data.errors;
      } else {
        alert(data.message || 'Lỗi: Không thể đăng ký tài khoản!');
      }
      return;
    }

    alert('🎉 Đăng ký thành công! Mời bạn đăng nhập.');
    router.push('/login');

  } catch (error) {
    console.error('Lỗi kết nối:', error);
    alert('Không thể kết nối đến máy chủ!');
  } finally {
    isProcessing.value = false;
  }
};
</script>
