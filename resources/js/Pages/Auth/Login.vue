<template>
  <Head title="Log in" />

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
        
        <form class="mt-8 space-y-6" @submit.prevent="submit">
          
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
              <InputError class="mt-2 text-red-500 text-xs" :message="form.errors.email" />
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
              <InputError class="mt-2 text-red-500 text-xs" :message="form.errors.password" />
            </div>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <Checkbox name="remember" v-model:checked="form.remember" class="text-[#4A7055] focus:ring-[#4A7055]" />
              <span class="ms-2 text-sm text-gray-600 font-medium">Ghi nhớ đăng nhập</span>
            </label>

            <div class="text-sm">
              <Link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="font-medium text-[#4A7055] hover:text-[#3b5a44] transition-colors"
              >
                Quên mật khẩu?
              </Link>
            </div>
          </div>

          <div>
            <button
              type="submit"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              class="w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-[#4A7055] hover:bg-[#3b5a44] transition-colors shadow-md"
            >
              Đăng nhập
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
            <Link :href="route('register')" class="font-bold text-[#4A7055] hover:text-[#3b5a44] ml-1">
              Đăng Ký
            </Link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Giữ nguyên 100% logic Backend của team
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>