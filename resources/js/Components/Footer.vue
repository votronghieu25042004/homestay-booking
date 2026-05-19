<template>
  <footer class="bg-[#4A7055] text-white py-12 mt-auto font-['Inter']">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="col-span-1 md:col-span-1">
          <Link href="/" class="text-3xl font-extrabold text-white mb-4 block font-['Playfair_Display'] tracking-widest">
            LOGO
          </Link>
          <p class="text-white/80 text-sm">
            for Figma
          </p>
        </div>
        <div>
          <h3 class="text-lg font-semibold mb-4 text-white">Liên hệ</h3>
          <ul class="space-y-2 text-white/80 text-sm">
            <li>
              <Link href="/about" class="hover:text-white transition-colors">
                 Về Chúng tôi
             </Link>
            </li>
            <li><a href="#" class="hover:text-white transition-colors">Hỗ trợ</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-lg font-semibold mb-4 text-white">Điều khoản</h3>
          <ul class="space-y-2 text-white/80 text-sm">
            <li><a href="#" class="hover:text-white transition-colors">Quyền riêng tư</a></li>
            <li><a href="#" class="hover:text-white transition-colors">Chương trình</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-lg font-semibold mb-4 text-white">Kết nối</h3>
          <ul class="space-y-2 text-white/80 text-sm">
            <li><a href="#" class="hover:text-white transition-colors">Facebook</a></li>
            <li><a href="#" class="hover:text-white transition-colors">Instagram</a></li>
          </ul>
        </div>
      </div>
      <div class="mt-12 pt-8 border-t border-white/20 text-center text-white/70 text-sm">
        <p>Homestay Booking © 2026. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>