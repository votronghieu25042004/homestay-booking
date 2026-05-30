<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#FAF9F5]">
    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row border border-gray-100">
      
      <div class="md:w-1/2 relative group hidden md:block">
        <img
          src="https://picsum.photos/seed/confirm/800/1000"
          alt="Reset Password Background"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-[#4A7055]/10 group-hover:bg-[#4A7055]/0 transition-colors"></div>
      </div>

      <div class="md:w-1/2 p-10 md:p-14 flex flex-col justify-center w-full">
        <div class="text-center mb-8">
          <h2 class="text-3xl font-bold text-[#4A7055] font-['Playfair_Display']">
            Đặt lại mật khẩu
          </h2>
        </div>
        
        <form class="space-y-8" @submit.prevent="submit">
          
          <div class="space-y-6">
            <div class="relative">
              <input
                id="email"
                type="email"
                v-model="form.email"
                required
                readonly
                class="peer w-full py-3 border-b border-gray-200 focus:outline-none text-gray-500 bg-transparent cursor-not-allowed"
              />
              <label for="email" class="absolute left-0 -top-3.5 text-sm text-[#4A7055] font-medium transition-all">
                Email tài khoản
              </label>
              <p v-if="validationErrors.email" class="mt-2 text-red-500 text-xs">{{ validationErrors.email[0] }}</p>
            </div>

            <div class="relative flex items-center">
              <input
                id="password"
                :type="showNewPassword ? 'text' : 'password'"
                v-model="form.password"
                required
                autofocus
                autocomplete="new-password"
                class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] focus:ring-0 text-gray-900 placeholder-transparent pr-10 bg-transparent"
                placeholder="Mật khẩu mới"
              />
              <label for="password" class="absolute left-0 -top-3.5 text-sm text-gray-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">
                Mật khẩu mới
              </label>
              <button type="button" @click="showNewPassword = !showNewPassword" class="absolute right-0 text-gray-400 hover:text-[#4A7055] transition-colors">
                <EyeOff v-if="!showNewPassword" class="w-5 h-5" />
                <Eye v-else class="w-5 h-5" />
              </button>
              <p v-if="validationErrors.password" class="mt-1 text-red-500 text-xs absolute top-full left-0">{{ validationErrors.password[0] }}</p>
            </div>
            
            <div class="relative flex items-center mt-6">
              <input
                id="password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                v-model="form.password_confirmation"
                required
                autocomplete="new-password"
                class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] focus:ring-0 text-gray-900 placeholder-transparent pr-10 bg-transparent"
                placeholder="Nhập lại mật khẩu"
              />
              <label for="password_confirmation" class="absolute left-0 -top-3.5 text-sm text-gray-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">
                Nhập lại mật khẩu
              </label>
              <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-0 text-gray-400 hover:text-[#4A7055] transition-colors">
                <EyeOff v-if="!showConfirmPassword" class="w-5 h-5" />
                <Eye v-else class="w-5 h-5" />
              </button>
            </div>
          </div>

          <div class="pt-4">
            <button
              type="submit"
              :class="{ 'opacity-50 cursor-not-allowed': isProcessing }"
              :disabled="isProcessing"
              class="w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-[#4A7055] hover:bg-[#3b5a44] transition-colors shadow-md"
            >
              {{ isProcessing ? 'Đang xử lý...' : 'Xác nhận đổi mật khẩu' }}
            </button>
          </div>
          
          <div class="relative mt-8">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-200" />
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-3 bg-white text-gray-400 font-medium">Đăng nhập bằng cách khác</span>
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
          <p class="text-sm font-medium text-gray-600">
            Đã nhớ lại mật khẩu?
            <router-link to="/login" class="font-bold text-[#4A7055] hover:text-[#3b5a44] ml-1">
              Đăng Nhập
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { Eye, EyeOff } from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();

const showNewPassword = ref(false);
const showConfirmPassword = ref(false);
const isProcessing = ref(false);
const validationErrors = ref<Record<string, string[]>>({});

const form = ref({
  token: '',
  email: '',
  password: '',
  password_confirmation: '',
});

// Tự động điền email và token từ link (do email gửi đến chứa params)
onMounted(() => {
  form.value.token = (route.query.token as string) || '';
  form.value.email = (route.query.email as string) || '';
});

const submit = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    alert('Mật khẩu xác nhận không khớp!');
    return;
  }

  isProcessing.value = true;
  validationErrors.value = {};

  try {
    // API logic sẽ do backend xử lý
    await new Promise(resolve => setTimeout(resolve, 1000));
    alert('Đặt lại mật khẩu thành công! Vui lòng đăng nhập lại.');
    router.push('/login');
  } catch (error) {
    console.error(error);
  } finally {
    isProcessing.value = false;
  }
};
</script>