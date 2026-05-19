<template>
  <Head title="Confirm Password" />

  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#FAF9F5]">
    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row border border-gray-100">
      
      <div class="md:w-1/2 relative group hidden md:block">
        <img
          src="https://picsum.photos/seed/secure/800/1000"
          alt="Secure Background"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-[#4A7055]/10 group-hover:bg-[#4A7055]/0 transition-colors"></div>
      </div>

      <div class="md:w-1/2 p-10 md:p-14 flex flex-col justify-center w-full">
        <div class="text-center mb-6">
          <h2 class="text-3xl font-bold text-[#4A7055] font-['Playfair_Display']">
            Xác nhận bảo mật
          </h2>
        </div>

        <div class="mb-6 text-sm text-gray-600 font-['Inter'] text-center leading-relaxed">
          Đây là khu vực bảo mật của hệ thống. Vui lòng xác nhận mật khẩu của bạn trước khi tiếp tục thực hiện thao tác.
        </div>
        
        <form class="space-y-8" @submit.prevent="submit">
          
          <div class="relative">
            <input
              id="password"
              type="password"
              v-model="form.password"
              required
              autofocus
              autocomplete="current-password"
              class="peer w-full py-3 border-b border-gray-200 focus:outline-none focus:border-[#4A7055] focus:ring-0 text-gray-900 placeholder-transparent bg-transparent"
              placeholder="Mật khẩu của bạn"
            />
            <label for="password" class="absolute left-0 -top-3.5 text-sm text-gray-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-[#4A7055]">
              Mật khẩu của bạn
            </label>
            <InputError class="mt-2 text-red-500 text-xs" :message="form.errors.password" />
          </div>

          <div class="mt-8">
            <button
              type="submit"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              class="w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-[#4A7055] hover:bg-[#3b5a44] transition-colors shadow-md"
            >
              Xác nhận mật khẩu
            </button>
          </div>
        </form>
        
        <div class="text-center mt-10 pt-8 border-t border-gray-100">
          <Link :href="route('dashboard')" class="text-sm font-medium text-gray-500 hover:text-[#4A7055] transition-colors">
            &larr; Hủy và quay lại
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();
        },
    });
};
</script>