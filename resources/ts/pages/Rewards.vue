<template>
  <div class="min-h-screen bg-[#FAF9F5] pb-20">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-100 shadow-sm pt-12 pb-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center" >
        <h1 class="text-4xl font-bold text-[#4A7055] mb-4 font-['Playfair_Display']">Ưu đãi & Đổi thưởng</h1>
        <p class="text-gray-600 max-w-2xl mx-auto font-['Inter']">
          Tích lũy điểm từ mỗi chuyến đi và đổi lấy những ưu đãi đặc biệt dành riêng cho bạn.
        </p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Points & Referral -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Points Card -->
          <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-[#4A7055]/5 rounded-full -mr-16 -mt-16"></div>
            <div class="relative z-10">
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#4A7055]/10 text-[#4A7055] mb-4">
                <Coins class="w-8 h-8" />
              </div>
              <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Điểm tích lũy hiện có</h2>
              <div class="text-5xl font-bold text-gray-900 mb-2 font-['Playfair_Display']">{{ userPoints }}</div>
              <p class="text-xs text-gray-400">100.000đ = 1 điểm</p>
            </div>
          </div>

          <!-- Referral Card -->
          <div class="bg-[#4A7055] p-8 rounded-3xl shadow-lg text-white">
            <h3 class="text-xl font-bold mb-4 font-['Playfair_Display']">Giới thiệu bạn bè</h3>
            <p class="text-white/80 text-sm mb-6 leading-relaxed">
              Chia sẻ mã giới thiệu của bạn. Khi bạn bè đăng ký và đặt phòng, cả hai sẽ nhận được những phần quà hấp dẫn!
            </p>
            <div class="bg-white/10 border border-white/20 p-4 rounded-2xl flex items-center justify-between">
              <div>
                <div class="text-[10px] uppercase text-white/60 mb-1">Mã của bạn</div>
                <div class="text-lg font-mono font-bold tracking-widest">{{ referralCode }}</div>
              </div>
              <button @click="copyReferral" class="bg-white text-[#4A7055] p-2.5 rounded-xl hover:bg-gray-100 transition-colors shadow-md">
                <Copy v-if="!copied" class="w-5 h-5" />
                <Check v-else class="w-5 h-5" />
              </button>
            </div>
            <p v-if="copied" class="text-center text-xs mt-3 text-white/90">Đã sao chép mã!</p>
          </div>
        </div>

        <!-- Right Column: Spin & Redeem -->
        <div class="lg:col-span-2 space-y-8">
          
          <!-- Lucky Spin Section -->
          <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between mb-8">
              <div>
                <h2 class="text-2xl font-bold text-gray-900 font-['Playfair_Display']">Vòng quay may mắn</h2>
                <p class="text-gray-500 text-sm">Quay mỗi ngày để nhận Voucher bất ngờ!</p>
              </div>
              <Gift class="w-10 h-10 text-[#4A7055] opacity-20" />
            </div>

            <div class="flex flex-col md:flex-row items-center gap-12">
              <div class="relative w-64 h-64 md:w-72 md:h-72 flex-shrink-0">
                <!-- Pointer -->
                <div class="absolute -top-1 left-1/2 -translate-x-1/2 z-20">
                  <div class="w-6 h-8 bg-red-500 clip-path-triangle shadow-md"></div>
                </div>
                
                <!-- The Wheel -->
                <div 
                  ref="wheelRef"
                  class="w-full h-full rounded-full border-8 border-gray-100 shadow-inner relative transition-transform duration-[4s] cubic-bezier(0.15, 0, 0.15, 1)"
                  :style="{ transform: `rotate(${rotation}deg)` }"
                >
                  <div v-for="(segment, index) in segments" :key="index" 
                       class="absolute top-0 left-0 w-full h-full"
                       :style="{ transform: `rotate(${index * (360 / segments.length)}deg)` }">
                    <div 
                      class="h-1/2 w-1/2 absolute top-0 left-1/2 -translate-x-1/2 origin-bottom flex items-start justify-center pt-4"
                      :style="{ 
                        backgroundColor: colors[index % colors.length],
                        clipPath: segmentClipPath
                      }"
                    >
                      <span class="text-[10px] font-bold text-white uppercase rotate-180 writing-mode-vertical py-2">{{ segment.label }}</span>
                    </div>
                  </div>
                  <!-- Center button -->
                  <div class="absolute inset-0 m-auto w-12 h-12 bg-white rounded-full shadow-md z-30 flex items-center justify-center border-4 border-gray-100">
                    <div class="w-4 h-4 bg-[#4A7055] rounded-full"></div>
                  </div>
                </div>
              </div>

              <div class="flex-1 space-y-4">
                <div class="bg-gray-50 p-6 rounded-2xl border border-dashed border-gray-200">
                  <h4 class="font-bold text-gray-900 mb-2">Cơ cấu giải thưởng:</h4>
                  <ul class="text-sm text-gray-600 space-y-2">
                    <li class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-[#4A7055]"></div> Voucher giảm 10%</li>
                    <li class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-[#E5B25D]"></div> Voucher 50k - 100k</li>
                    <li class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-gray-400"></div> Thêm lượt quay</li>
                  </ul>
                </div>
                <button 
                  @click="spinWheel" 
                  :disabled="isSpinning"
                  class="w-full bg-[#4A7055] hover:bg-[#3b5a44] text-white py-4 rounded-2xl font-bold transition-all shadow-lg active:scale-95 disabled:bg-gray-300 disabled:cursor-not-allowed"
                >
                  {{ isSpinning ? 'Đang quay...' : 'BẮT ĐẦU QUAY' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Redeem Vouchers Section -->
          <div class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 font-['Playfair_Display']">Đổi điểm lấy Voucher</h2>
            
            <div v-if="redeemableVouchers.length" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="voucher in redeemableVouchers" :key="voucher.id" class="group bg-white p-5 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all flex justify-between items-center relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-2 bg-[#4A7055]"></div>
                <div>
                  <h4 class="font-bold text-gray-900 mb-1">{{ voucher.title }}</h4>
                  <p class="text-xs text-gray-500 mb-3">{{ voucher.description }}</p>
                  <div class="inline-flex items-center px-3 py-1 bg-[#4A7055]/10 text-[#4A7055] rounded-lg text-xs font-bold">
                    {{ voucher.points_required }} điểm
                  </div>
                </div>
                <button 
                  @click="redeemVoucher(voucher)"
                  :disabled="userPoints < voucher.points_required"
                  class="ml-4 px-4 py-2 rounded-xl text-sm font-bold transition-all"
                  :class="userPoints >= voucher.points_required ? 'bg-[#4A7055] text-white hover:bg-[#3b5a44]' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                >
                  Đổi mã
                </button>
              </div>
            </div>
            
            <div v-else class="bg-gray-50 border border-gray-100 p-10 rounded-3xl text-center">
              <p class="text-gray-400 italic">Hiện không có voucher nào khả dụng để đổi.</p>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Modal kết quả vòng quay -->
    <div v-if="spinResult" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
      <div class="bg-white w-full max-w-sm rounded-[40px] overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-300">
        <div class="bg-[#4A7055] p-8 text-center text-white relative">
          <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg">
            <Trophy class="w-12 h-12 text-[#E5B25D]" />
          </div>
          <h3 class="text-2xl font-bold mt-10 mb-2 font-['Playfair_Display']">Chúc mừng bạn!</h3>
          <p class="text-white/80">Bạn đã nhận được phần thưởng</p>
        </div>
        <div class="p-8 text-center">
          <div class="bg-gray-50 border-2 border-dashed border-[#4A7055]/30 p-4 rounded-2xl mb-6">
            <span class="text-3xl font-mono font-bold text-[#4A7055] tracking-widest uppercase">{{ spinResult.code }}</span>
          </div>
          <p class="text-sm text-gray-600 mb-8">{{ spinResult.message }}</p>
          <button @click="spinResult = null" class="w-full bg-[#4A7055] text-white py-4 rounded-2xl font-bold hover:bg-[#3b5a44] transition-all">
            Tuyệt vời!
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Coins, Copy, Check, Gift, Trophy } from 'lucide-vue-next';

interface Voucher {
  id: number;
  code: string;
  title: string;
  description: string;
  points_required: number;
  discount_type: string;
  discount_value: number;
}

const userPoints = ref(0);
const referralCode = ref('');
const copied = ref(false);
const redeemableVouchers = ref<Voucher[]>([]);
const isSpinning = ref(false);
const rotation = ref(0);
const spinResult = ref<any>(null);

const segments = [
  { label: 'Giảm 10%', value: 'sale10' },
  { label: '50.000đ', value: '50k' },
  { label: 'Giảm 5%', value: 'sale5' },
  { label: 'May mắn', value: 'luck' },
  { label: '100.000đ', value: '100k' },
  { label: 'Giảm 20%', value: 'sale20' },
];

const colors = ['#4A7055', '#5B8C6A', '#6B9E7C', '#7CAC8D', '#8DBB9F', '#9FC9B1'];
const segmentClipPath = 'polygon(50% 100%, 0 0, 100% 0)';

const fetchData = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    if (!token) return;

    const response = await fetch('/api/vouchers/redeemable', {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    const data = await response.json();
    
    userPoints.value = data.points;
    redeemableVouchers.value = data.vouchers;
    
    const userInfo = JSON.parse(localStorage.getItem('user_info') || '{}');
    referralCode.value = userInfo.referral_code || 'HOME-XXXX';
  } catch (error) {
    console.error('Lỗi khi tải dữ liệu:', error);
  }
};

onMounted(fetchData);

const copyReferral = () => {
  navigator.clipboard.writeText(referralCode.value);
  copied.value = true;
  setTimeout(() => copied.value = false, 2000);
};

const redeemVoucher = async (voucher: Voucher) => {
  if (!confirm(`Bạn có chắc muốn dùng ${voucher.points_required} điểm để đổi mã ${voucher.title}?`)) return;

  try {
    const token = localStorage.getItem('auth_token');
    const response = await fetch(`/api/vouchers/redeem/${voucher.id}`, {
      method: 'POST',
      headers: { 
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });
    const data = await response.json();
    
    if (response.ok) {
      alert(data.message);
      fetchData(); // Cập nhật lại điểm
    } else {
      alert(data.message);
    }
  } catch (error) {
    alert('Có lỗi xảy ra khi đổi điểm.');
  }
};

const spinWheel = async () => {
  if (isSpinning.value) return;
  
  try {
    const token = localStorage.getItem('auth_token');
    const response = await fetch('/api/vouchers/spin', {
      method: 'POST',
      headers: { 
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });
    const data = await response.json();
    
    if (response.ok) {
      isSpinning.value = true;
      // Quay ít nhất 5 vòng (1800 độ) + góc ngẫu nhiên
      const extraRotation = Math.floor(Math.random() * 360) + 1800;
      rotation.value += extraRotation;
      
      setTimeout(() => {
        isSpinning.value = false;
        spinResult.value = {
          code: data.voucher ? data.voucher.code : 'UNKNOWN',
          message: data.message
        };
      }, 4000);
    } else {
      alert(data.message);
    }
  } catch (error) {
    alert('Không thể thực hiện vòng quay lúc này.');
  }
};
</script>

<style scoped>
.clip-path-triangle {
  clip-path: polygon(50% 100%, 0 0, 100% 0);
}

.writing-mode-vertical {
  writing-mode: vertical-rl;
  text-orientation: mixed;
}
</style>
