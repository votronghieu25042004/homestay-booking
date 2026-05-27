<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#FAF9F5]">
    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row border border-gray-100">
      
      <div class="md:w-1/2 relative group hidden md:block">
        <img src="https://picsum.photos/seed/register/800/1000" alt="Register Background" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-[#4A7055]/10 group-hover:bg-[#4A7055]/0 transition-colors"></div>
      </div>

      <div class="md:w-1/2 p-10 md:p-14 flex flex-col justify-center w-full">
        <div>
          <h2 class="text-3xl font-bold text-gray-900 mb-2 font-['Playfair_Display']">
            Tạo Tài Khoản
          </h2>
        </div>
        
        <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
          <div class="space-y-6">
            
            <div class="relative">
              <input
                id="name"
                type="text"
                v-model="form.name"
                required
                autofocus
                autocomplete="name"
                class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] focus:ring-0 text-gray-900 placeholder-transparent bg-transparent"
                placeholder="Họ và Tên"
              />
              <label for="name" class="absolute left-0 -top-3.5 text-sm text-gray-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">
                Họ và Tên
              </label>
              <p v-if="validationErrors.name" class="mt-2 text-red-500 text-xs">{{ validationErrors.name[0] }}</p>
            </div>
            
            <div class="relative">
              <input
                id="email"
                type="email"
                v-model="form.email"
                required
                autocomplete="email"
                class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] focus:ring-0 text-gray-900 placeholder-transparent bg-transparent"
                placeholder="Email"
              />
              <label for="email" class="absolute left-0 -top-3.5 text-sm text-gray-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">
                Email
              </label>
              <p v-if="validationErrors.email" class="mt-2 text-red-500 text-xs">{{ validationErrors.email[0] }}</p>
            </div>

            <div class="relative">
              <input
                id="phone"
                type="tel"
                v-model="form.phone"
                required
                class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] focus:ring-0 text-gray-900 placeholder-transparent bg-transparent"
                placeholder="Số điện thoại"
              />
              <label for="phone" class="absolute left-0 -top-3.5 text-sm text-gray-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">
                Số điện thoại
              </label>
              <p v-if="validationErrors.phone" class="mt-2 text-red-500 text-xs">{{ validationErrors.phone[0] }}</p>
            </div>
            
            <div class="relative">
              <input
                id="password"
                type="password"
                v-model="form.password"
                required
                minlength="6"
                autocomplete="new-password"
                class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] focus:ring-0 text-gray-900 placeholder-transparent bg-transparent"
                placeholder="Mật Khẩu"
              />
              <label for="password" class="absolute left-0 -top-3.5 text-sm text-gray-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">
                Mật Khẩu (Tối thiểu 6 ký tự)
              </label>
              <p v-if="validationErrors.password" class="mt-2 text-red-500 text-xs">{{ validationErrors.password[0] }}</p>
            </div>

            <div class="relative mt-4">
              <input
                id="password_confirmation"
                type="password"
                v-model="form.password_confirmation"
                required
                minlength="6"
                autocomplete="new-password"
                class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] focus:ring-0 text-gray-900 placeholder-transparent bg-transparent"
                placeholder="Xác nhận mật khẩu"
              />
              <label for="password_confirmation" class="absolute left-0 -top-3.5 text-sm text-gray-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">
                Xác nhận mật khẩu
              </label>
            </div>

            <div class="relative">
              <input
                id="ref_code"
                type="text"
                v-model="form.ref_code"
                class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] focus:ring-0 text-gray-900 placeholder-transparent bg-transparent"
                placeholder="Mã giới thiệu (Nếu có)"
              />
              <label for="ref_code" class="absolute left-0 -top-3.5 text-sm text-gray-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">
                Mã giới thiệu (Nếu có)
              </label>
            </div>
          </div>

          <div class="flex items-center mt-4">
            <input id="terms" type="checkbox" v-model="form.terms" required class="h-4 w-4 text-[#4A7055] focus:ring-[#4A7055] border-gray-300 rounded cursor-pointer" />
            <label for="terms" class="ml-2 block text-sm text-gray-700 font-medium cursor-pointer">
              Tôi đồng ý với tất cả điều khoản trên
            </label>
          </div>

          <div>
            <button
              type="submit"
              :class="{ 'opacity-50 cursor-not-allowed': isProcessing }"
              :disabled="isProcessing || !form.terms"
              class="w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-[#4A7055] hover:bg-[#3b5a44] transition-colors shadow-md mt-6"
            >
              {{ isProcessing ? 'Đang xử lý...' : 'Đăng ký' }}
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
            Đã có tài khoản?
            <router-link to="/login" class="font-bold text-[#4A7055] hover:text-[#3b5a44] ml-1">
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
