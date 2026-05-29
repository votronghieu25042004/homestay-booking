<template>
  <footer class="bg-[#4A7055] text-white py-16 mt-auto font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="grid grid-cols-1 md:grid-cols-5 gap-12 lg:gap-8">
        
        <div class="md:col-span-2 space-y-4">
          <router-link to="/" class="text-3xl text-white block font-['Inter'] tracking-wider hover:opacity-90 transition-opacity">
            homebooking
          </router-link>
          <p class="text-emerald-100/70 text-sm leading-relaxed max-w-sm">
            Kỳ nghỉ trong mơ, giá bất ngờ. Trải nghiệm không gian sống tuyệt vời dành riêng cho bạn.
          </p>
        </div>
        
        <div>
          <h3 class="text-lg font-semibold mb-6 text-white tracking-wide">Liên hệ</h3>
          <ul class="space-y-4 text-emerald-100/80 text-sm">
            <li>
              <router-link to="/about" class="hover:text-white hover:translate-x-1 inline-block transition-all duration-200">
                Về Chúng tôi
              </router-link>
            </li>
            <li>
              <a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-all duration-200">
                Hỗ trợ 24/7
              </a>
            </li>
          </ul>
        </div>
        
        <div>
          <h3 class="text-lg font-semibold mb-6 text-white tracking-wide">Điều khoản</h3>
          <ul class="space-y-4 text-emerald-100/80 text-sm">
            <li>
              <a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-all duration-200">
                Quyền riêng tư
              </a>
            </li>
            <li>
              <a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-all duration-200">
                Chương trình đại lý
              </a>
            </li>
          </ul>
        </div>
        
        <div>
          <h3 class="text-lg font-semibold mb-6 text-white tracking-wide">Kết nối</h3>
          <ul class="space-y-4 text-emerald-100/80 text-sm">
            <li>
              <a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-all duration-200 flex items-center gap-2">
                 Facebook
              </a>
            </li>
            <li>
              <a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-all duration-200 flex items-center gap-2">
                 Instagram
              </a>
            </li>
          </ul>
        </div>
        
      </div>
      
      <div class="mt-16 pt-8 border-t border-emerald-700/50 flex flex-col md:flex-row justify-between items-center text-emerald-100/60 text-sm gap-4">
        <p>© 2026 Homebooking. All rights reserved.</p>
        <p class="flex items-center gap-1">Designed with <span class="text-red-400">♥</span> in Da Nang</p>
      </div>
    </div>
  </footer>
</template>