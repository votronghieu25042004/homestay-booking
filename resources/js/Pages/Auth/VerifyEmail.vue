<template>
  <Head title="Email Verification" />

  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#FAF9F5]">
    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row border border-gray-100">
      
      <div class="md:w-1/2 relative group hidden md:block">
        <img
          src="https://picsum.photos/seed/otp/800/1000"
          alt="Verify Email Background"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-[#4A7055]/10 group-hover:bg-[#4A7055]/0 transition-colors"></div>
      </div>

      <div class="md:w-1/2 p-10 md:p-14 flex flex-col justify-center w-full">
        <div class="text-center mb-6">
          <h2 class="text-3xl font-bold text-[#4A7055] font-['Playfair_Display']">
            Xác Thực Email
          </h2>
          <p class="text-sm text-gray-600 mt-4 font-['Inter'] leading-relaxed">
            Cảm ơn bạn đã đăng ký! Trước khi bắt đầu trải nghiệm, vui lòng xác thực địa chỉ email bằng cách nhấp vào đường link chúng tôi vừa gửi cho bạn.
          </p>
        </div>

        <div class="mb-6 font-bold text-sm text-[#4A7055] bg-[#4A7055]/10 p-4 rounded-xl text-center border border-[#4A7055]/20" v-if="verificationLinkSent">
          Một đường link xác thực mới đã được gửi đến email bạn cung cấp khi đăng ký.
        </div>
        
        <form class="space-y-8" @submit.prevent="submit">
          
          <div class="mt-8">
            <button
              type="submit"
              :class="{ 'opacity-25': form.processing }" 
              :disabled="form.processing"
              class="w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-[#4A7055] hover:bg-[#3b5a44] transition-colors shadow-md"
            >
              Gửi lại email xác thực
            </button>
          </div>
        </form>

        <div class="text-center mt-10 pt-8 border-t border-gray-100 flex flex-col gap-3">
            <p class="text-sm text-gray-600 font-['Inter']">
              Bạn muốn dùng tài khoản khác?
            </p>
            <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="text-sm font-bold text-red-500 hover:text-red-700 transition-colors"
            >
                Đăng xuất
            </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Giữ nguyên 100% logic Backend của team
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>