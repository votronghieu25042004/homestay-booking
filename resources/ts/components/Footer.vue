<template>
  <footer class="bg-[#4A7055] text-white pt-20 pb-10 mt-auto font-sans">
    <div class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-12">
      
      <!-- Top Section -->
      <div class="grid grid-cols-1 md:grid-cols-12 gap-12 lg:gap-16 border-b border-emerald-400/30 pb-16">
        
        <!-- Brand & Description -->
        <div class="md:col-span-5 space-y-6">
          <router-link to="/" class="block">
            <span class="text-4xl font-extrabold text-white tracking-tight font-['Playfair_Display']">
              Home<span class="text-emerald-200">booking</span>.
            </span>
          </router-link>
          <p class="text-emerald-50/90 text-base leading-relaxed max-w-md">
            Chúng tôi mang đến những trải nghiệm lưu trú tuyệt vời nhất. Hãy để Homebooking đồng hành cùng bạn trong những kỳ nghỉ đáng nhớ.
          </p>
          <div class="flex items-center gap-4 pt-2">
            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white hover:text-[#4A7055] hover:scale-110 transition-all group">
              <Facebook class="w-5 h-5 text-emerald-100 group-hover:text-[#4A7055] transition-colors" />
            </a>
            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white hover:text-[#4A7055] hover:scale-110 transition-all group">
              <Instagram class="w-5 h-5 text-emerald-100 group-hover:text-[#4A7055] transition-colors" />
            </a>
            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white hover:text-[#4A7055] hover:scale-110 transition-all group">
              <Twitter class="w-5 h-5 text-emerald-100 group-hover:text-[#4A7055] transition-colors" />
            </a>
          </div>
        </div>
        
        <!-- Quick Links -->
        <div class="md:col-span-2">
          <h3 class="font-bold mb-6 text-white uppercase tracking-widest text-sm">Khám Phá</h3>
          <ul class="space-y-4 text-emerald-50/90 font-medium">
            <li><router-link to="/about" class="hover:text-white hover:translate-x-1 inline-block transition-all duration-300">Về chúng tôi</router-link></li>
            <li><router-link to="/rooms" class="hover:text-white hover:translate-x-1 inline-block transition-all duration-300">Danh sách phòng</router-link></li>
            <li><a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-all duration-300">Ưu đãi & Khuyến mãi</a></li>
            <li><a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-all duration-300">Cẩm nang du lịch</a></li>
          </ul>
        </div>
        
        <!-- Policies -->
        <div class="md:col-span-2">
          <h3 class="font-bold mb-6 text-white uppercase tracking-widest text-sm">Hỗ Trợ</h3>
          <ul class="space-y-4 text-emerald-50/90 font-medium">
            <li><a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-all duration-300">Trung tâm trợ giúp</a></li>
            <li><a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-all duration-300">Chính sách bảo mật</a></li>
            <li><a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-all duration-300">Điều khoản dịch vụ</a></li>
            <li><a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-all duration-300">Quy định hoàn hủy</a></li>
          </ul>
        </div>
        
        <!-- Contact -->
        <div class="md:col-span-3">
          <h3 class="font-bold mb-6 text-white uppercase tracking-widest text-sm">Liên Hệ</h3>
          <ul class="space-y-5 text-emerald-50/90 font-medium">
            <li class="flex items-start gap-3 group">
              <MapPin class="w-5 h-5 text-emerald-200 shrink-0 mt-0.5 group-hover:scale-110 transition-transform" />
              <span class="group-hover:text-white transition-colors">123 Đường Nguyễn Văn Linh, Quận Hải Châu, TP. Đà Nẵng</span>
            </li>
            <li class="flex items-center gap-3 group">
              <Phone class="w-5 h-5 text-emerald-200 shrink-0 group-hover:scale-110 transition-transform" />
              <span class="group-hover:text-white transition-colors">+84 236 123 4567</span>
            </li>
            <li class="flex items-center gap-3 group">
              <Mail class="w-5 h-5 text-emerald-200 shrink-0 group-hover:scale-110 transition-transform" />
              <span class="group-hover:text-white transition-colors">support@homebooking.vn</span>
            </li>
          </ul>
        </div>
        
      </div>
      
      <!-- Bottom Section -->
      <div class="mt-8 flex flex-col md:flex-row justify-between items-center text-emerald-100/70 text-sm gap-4">
        <p>© 2026 Homebooking. Mọi quyền được bảo lưu.</p>
        <div class="flex items-center gap-2 font-medium">
          Được thiết kế với <Heart class="w-4 h-4 text-red-300 fill-red-300 animate-pulse" /> tại Đà Nẵng
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { Facebook, Instagram, Twitter, MapPin, Phone, Mail, Heart } from 'lucide-vue-next';
</script>