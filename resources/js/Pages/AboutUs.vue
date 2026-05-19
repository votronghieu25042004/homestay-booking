<template>
  <div class="bg-[#FAF9F5] min-h-screen pb-24 pt-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <section class="mb-24">
        <div class="text-center max-w-3xl mx-auto mb-12">
          <h1 class="text-4xl md:text-5xl font-bold text-[#4A7055] mb-6 font-['Playfair_Display']">
            Về chúng tôi
          </h1>
          <p class="text-lg text-gray-600 leading-relaxed font-['Inter']">
            Chào mừng bạn đến với hệ thống Homestay của chúng tôi. Với mong muốn mang lại một không gian lưu trú ấm cúng, tiện nghi và đậm chất bản địa, chúng tôi luôn nỗ lực không ngừng để mỗi kỳ nghỉ của bạn tại Đà Nẵng đều trở thành một kỷ niệm khó quên.
          </p>
        </div>
        
        <div class="w-full h-[400px] md:h-[500px] rounded-[3rem] overflow-hidden shadow-xl relative group">
          <img 
            src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=2070&auto=format&fit=crop" 
            alt="Về chúng tôi" 
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
          />
          <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
        </div>
      </section>

      <section class="mb-24">
        <h2 class="text-3xl font-bold text-gray-900 mb-10 text-center font-['Playfair_Display']">
          Chính sách của chúng tôi
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          
          <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 bg-[#4A7055]/10 rounded-2xl flex items-center justify-center mb-6 text-[#4A7055]">
              <Clock class="w-7 h-7" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3 font-['Playfair_Display']">Thời gian</h3>
            <ul class="space-y-2 text-gray-600 text-sm">
              <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#4A7055]"></span> Nhận phòng: Từ 14:00</li>
              <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#4A7055]"></span> Trả phòng: Trước 12:00 trưa</li>
              <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#4A7055]"></span> Hỗ trợ nhận phòng sớm (Tùy tình trạng trống)</li>
            </ul>
          </div>

          <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 bg-[#4A7055]/10 rounded-2xl flex items-center justify-center mb-6 text-[#4A7055]">
              <CreditCard class="w-7 h-7" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3 font-['Playfair_Display']">Thanh toán</h3>
            <ul class="space-y-2 text-gray-600 text-sm">
              <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#4A7055]"></span> Cọc trước 50% khi đặt phòng</li>
              <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#4A7055]"></span> Thanh toán 50% còn lại khi nhận phòng</li>
              <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#4A7055]"></span> Chấp nhận chuyển khoản & tiền mặt</li>
            </ul>
          </div>

          <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 bg-[#4A7055]/10 rounded-2xl flex items-center justify-center mb-6 text-[#4A7055]">
              <ShieldCheck class="w-7 h-7" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3 font-['Playfair_Display']">Hủy phòng</h3>
            <ul class="space-y-2 text-gray-600 text-sm">
              <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#4A7055]"></span> Hủy trước 3 ngày: Hoàn 100% cọc</li>
              <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#4A7055]"></span> Hủy trước 1 ngày: Hoàn 50% cọc</li>
              <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#4A7055]"></span> Hủy trong ngày: Không hoàn cọc</li>
            </ul>
          </div>

        </div>
      </section>

      <section class="mb-24 bg-white rounded-[3rem] shadow-sm border border-gray-100 p-10 md:p-16">
        <div class="flex flex-col md:flex-row gap-12 items-center">
          <div class="md:w-1/3">
            <h2 class="text-3xl font-bold text-gray-900 mb-4 font-['Playfair_Display']">
              Quy định của Homestay
            </h2>
            <p class="text-gray-600 font-['Inter']">
              Để đảm bảo trải nghiệm tốt nhất cho tất cả khách hàng lưu trú, mong quý khách lưu ý thực hiện các quy định sau.
            </p>
          </div>
          
          <div class="md:w-2/3 grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="flex items-start gap-4">
              <Ban class="w-6 h-6 text-red-500 shrink-0 mt-1" />
              <div>
                <h4 class="font-bold text-gray-900 mb-1">Không hút thuốc</h4>
                <p class="text-sm text-gray-500">Tuyệt đối không hút thuốc trong phòng và không gian kín.</p>
              </div>
            </div>
            
            <div class="flex items-start gap-4">
              <VolumeX class="w-6 h-6 text-[#4A7055] shrink-0 mt-1" />
              <div>
                <h4 class="font-bold text-gray-900 mb-1">Giữ yên tĩnh</h4>
                <p class="text-sm text-gray-500">Vui lòng hạn chế tiếng ồn lớn sau 22:00 để tránh ảnh hưởng xung quanh.</p>
              </div>
            </div>

            <div class="flex items-start gap-4">
              <Dog class="w-6 h-6 text-[#4A7055] shrink-0 mt-1" />
              <div>
                <h4 class="font-bold text-gray-900 mb-1">Thú cưng</h4>
                <p class="text-sm text-gray-500">Chúng tôi rất tiếc chưa thể hỗ trợ mang theo thú cưng vào khu vực phòng nghỉ.</p>
              </div>
            </div>

            <div class="flex items-start gap-4">
              <Users class="w-6 h-6 text-[#4A7055] shrink-0 mt-1" />
              <div>
                <h4 class="font-bold text-gray-900 mb-1">Đúng số lượng</h4>
                <p class="text-sm text-gray-500">Vui lòng lưu trú đúng số lượng người đã đăng ký lúc đặt phòng.</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section>
        <div class="bg-[#4A7055] rounded-[3rem] overflow-hidden shadow-lg relative">
          <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
          <div class="absolute bottom-0 left-0 -mb-16 -ml-16 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>

          <div class="relative z-10 flex flex-col md:flex-row items-center justify-between p-12 md:p-16 gap-8 text-white">
            <div class="md:w-1/2">
              <h2 class="text-3xl md:text-4xl font-bold mb-4 font-['Playfair_Display']">
                Bạn cần hỗ trợ?
              </h2>
              <p class="text-white/80 text-lg mb-8">
                Đừng ngần ngại liên hệ với chúng tôi bất cứ lúc nào. Đội ngũ hỗ trợ luôn sẵn sàng giải đáp mọi thắc mắc của bạn.
              </p>
              
              <div class="space-y-4">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <Phone class="w-5 h-5 text-white" />
                  </div>
                  <div>
                    <div class="text-sm text-white/60">Hotline 24/7</div>
                    <div class="font-bold text-lg">0901 234 567</div>
                  </div>
                </div>

                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <Mail class="w-5 h-5 text-white" />
                  </div>
                  <div>
                    <div class="text-sm text-white/60">Email</div>
                    <div class="font-bold text-lg">contact@homestay.com</div>
                  </div>
                </div>

                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <MapPin class="w-5 h-5 text-white" />
                  </div>
                  <div>
                    <div class="text-sm text-white/60">Địa chỉ</div>
                    <div class="font-bold text-lg">Hải Châu, TP. Đà Nẵng</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="md:w-1/2 flex justify-center md:justify-end w-full">
              <button class="bg-white text-[#4A7055] px-10 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition-colors shadow-xl flex items-center gap-2">
                <Mail class="w-5 h-5" />
                Gửi tin nhắn ngay
              </button>
            </div>
          </div>
        </div>
      </section>

    </div>
  </div>
</template>

<script setup lang="ts">
// Import các icon cần thiết từ lucide
import { Clock, CreditCard, ShieldCheck, Ban, VolumeX, Dog, Users, Phone, Mail, MapPin } from 'lucide-vue-next';
</script>

<style scoped>
/* Không cần thêm CSS phức tạp vì Tailwind đã xử lý hết giao diện */
</style>