<template>
  <div class="fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4" @click.self="$emit('close')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[95vh] overflow-y-auto p-0 relative print:shadow-none print:w-full print:h-full print:bg-white flex flex-col">
      <div class="sticky top-0 bg-white/95 backdrop-blur-sm z-10 border-b border-gray-100 p-6 flex justify-between items-center shrink-0 print:hidden">
        <h3 class="text-xl font-bold text-gray-800">Phần mềm quản lý HD - Bản nháp & Check-out</h3>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">✕</button>
      </div>

      <div class="p-8 print:p-0 flex-1 printable-bill">
        <!-- Cảnh báo trả phòng sớm (Không in ra) -->
        <div v-if="isEarlyWarning" class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-xl print:hidden">
          <div class="flex items-start gap-2">
            <span class="text-amber-500 text-xl">⚠️</span>
            <div>
              <p class="font-semibold text-amber-800">Khách trả phòng sớm!</p>
              <p class="text-sm text-amber-700 mt-1">
                Ngày check-out theo hợp đồng là <strong>{{ fmtDateStr(booking.check_out_date) }}</strong>,
                nhưng hôm nay mới là <strong>{{ todayStr }}</strong>.
              </p>
              <label class="flex items-center gap-2 mt-3 cursor-pointer select-none">
                <input type="checkbox" v-model="earlyConfirmed" class="w-4 h-4 accent-amber-500" />
                <span class="text-sm font-medium text-amber-900">
                  Tôi xác nhận trả phòng sớm và đã thỏa thuận về chính sách với khách.
                </span>
              </label>
            </div>
          </div>
        </div>

        <!-- HÓA ĐƠN XUẤT RA -->
        <div class="space-y-6 text-gray-800" id="bill-content">
          <div class="text-center pb-4 border-b-2 border-dashed border-gray-300">
            <h2 class="text-2xl font-bold uppercase tracking-wider">Hóa Đơn Thanh Toán</h2>
            <p class="text-sm text-gray-500 mt-1">Mã booking: {{ booking.booking_code }}</p>
          </div>

          <!-- PHẦN 1: THÔNG TIN LƯU TRÚ -->
          <section>
            <h3 class="font-bold text-sm text-gray-500 uppercase tracking-widest mb-3 border-l-4 border-emerald-500 pl-2">Phần 1: Thông tin lưu trú</h3>
            <div class="grid grid-cols-2 gap-y-2 text-sm pl-3">
              <div class="col-span-2"><span class="text-gray-500 w-24 inline-block">Khách hàng:</span> <span class="font-semibold text-base">{{ customerName }} (Phòng {{ roomName }})</span></div>
              <div><span class="text-gray-500 w-24 inline-block">Giờ vào:</span> <span class="font-medium">{{ fmtCombined(booking.check_in_date, booking.check_in_time) }}</span></div>
              <div><span class="text-gray-500 w-24 inline-block">Giờ ra:</span> <span class="font-medium">{{ isEarlyWarning ? todayStr : fmtCombined(booking.check_out_date, booking.check_out_time) }}</span></div>
            </div>
          </section>

          <!-- PHẦN 2: CHI TIẾT TIỀN PHÒNG -->
          <section>
            <h3 class="font-bold text-sm text-gray-500 uppercase tracking-widest mb-3 border-l-4 border-blue-500 pl-2 mt-6">Phần 2: Chi tiết tiền phòng</h3>
            <div class="flex justify-between text-sm pl-3 font-medium">
              <span>Tiền phòng ({{ nights }} đêm x {{ fmtMoney(roomPrice) }})</span>
              <span>{{ fmtMoney(roomTotal) }}</span>
            </div>
          </section>

          <!-- PHẦN 3: DỊCH VỤ PHÁT SINH & PHỤ THU -->
          <section>
            <h3 class="font-bold text-sm text-gray-500 uppercase tracking-widest mb-3 border-l-4 border-amber-500 pl-2 mt-6">Phần 3: Dịch vụ phát sinh & Phụ thu</h3>
            <ul class="text-sm space-y-2 pl-3">
              <li v-for="svc in booking.services" :key="svc.id" class="flex justify-between">
                <span class="flex-1">
                  {{ fmtShortDate(svc.created_at) }}: {{ svc.service_name }} × {{ svc.quantity }}
                  <span v-if="svc.is_paid" class="text-xs text-green-600 ml-1 font-semibold italic">(Đã thu tiền)</span>
                </span>
                <span :class="{'line-through text-gray-400': svc.is_paid}">{{ fmtMoney(svc.total_price) }}</span>
              </li>
              <li v-if="additionalFee > 0" class="flex justify-between text-amber-700 font-medium">
                <span class="flex-1">
                  {{ todayStr }}: {{ additionalNote || 'Phụ thu khi trả phòng' }} × 1
                </span>
                <span>{{ fmtMoney(additionalFee) }}</span>
              </li>
              <li v-if="!booking.services?.length && additionalFee === 0" class="text-gray-400 italic">Không có phụ thu nào.</li>
            </ul>
            <div v-if="booking.services?.length || additionalFee > 0" class="flex justify-between text-sm font-semibold pl-3 mt-3 pt-3 border-t border-dashed border-gray-200">
              <span>Tổng cộng phụ thu (Bao gồm các món lưu trước trả sau):</span>
              <span>{{ fmtMoney(Number(surchargesTotal) + Number(additionalFee)) }}</span>
            </div>
          </section>

          <!-- MỚI: NHẬP THÊM PHỤ THU TẠI CHỖ (Không in ra, chỉ dùng tính toán) -->
          <div class="print:hidden mt-6 bg-gray-50 p-4 rounded-lg border border-gray-100 pb-4">
            <label class="label-sm">Phụ thu thêm ngay lúc này (Nếu có)</label>
            <div class="flex gap-2">
              <input v-model.number="additionalFee" type="number" placeholder="0" class="input-sm w-32" min="0" />
              <input v-model="additionalNote" placeholder="Ghi chú (Ví dụ: Đền ly vỡ)..." class="input-sm flex-1" />
            </div>
          </div>

          <!-- TỔNG KẾT -->
          <section class="mt-6 pt-4 border-t-2 border-gray-800">
            <h3 class="sr-only">Tổng kết</h3>
            <div class="space-y-2 text-base">
              <div class="flex justify-between">
                <span class="text-gray-600 font-medium">Tổng chi phí (Phòng + Dịch vụ):</span>
                <span class="font-bold">{{ fmtMoney(grandTotal) }}</span>
              </div>
              <div v-if="booking.discount_amount > 0" class="flex justify-between text-green-600">
                <span>Giảm giá:</span>
                <span class="font-medium">- {{ fmtMoney(booking.discount_amount) }}</span>
              </div>
              <div class="flex justify-between text-green-600">
                <span class="font-medium">Đã cọc / Trả trước (Bao gồm dịch vụ đã thu):</span>
                <span class="font-bold">- {{ fmtMoney(booking.paid_amount) }}</span>
              </div>
              
              <div class="flex justify-between items-center text-xl mt-4 pt-3 border-t border-dashed border-gray-300 text-purple-800">
                <span class="font-black uppercase">Khách Cần Thanh Toán:</span>
                <span class="font-black">{{ fmtMoney(finalAmount) }}</span>
              </div>
            </div>
          </section>

          <!-- Nút hành động -->
          <div class="print:hidden mt-8">
            <label class="label-sm mb-2 font-bold text-gray-800">Phương thức thanh toán khoản còn lại:</label>
            <div class="flex gap-4">
              <label class="flex items-center gap-2 cursor-pointer bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 flex-1 hover:bg-gray-100">
                <input type="radio" v-model="paymentMethod" value="cash" class="accent-purple-600 w-4 h-4"> Tiền mặt
              </label>
              <label class="flex items-center gap-2 cursor-pointer bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 flex-1 hover:bg-gray-100">
                <input type="radio" v-model="paymentMethod" value="transfer" class="accent-purple-600 w-4 h-4"> Chuyển khoản
              </label>
              <label class="flex items-center gap-2 cursor-pointer bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 flex-1 hover:bg-gray-100">
                <input type="radio" v-model="paymentMethod" value="card" class="accent-purple-600 w-4 h-4"> Thẻ
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Buttons (Fixed bottom) -->
      <div class="sticky bottom-0 bg-white/95 backdrop-blur-sm border-t border-gray-100 p-6 flex gap-3 print:hidden shrink-0 mt-auto">
        <button @click="printBill" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors flex items-center gap-2">
          🖨️ In Hóa Đơn
        </button>
        <button
          @click="submit"
          :disabled="loading || (isEarlyWarning && !earlyConfirmed)"
          class="flex-1 px-4 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-purple-200 flex items-center justify-center gap-2"
        >
          <span v-if="loading" class="animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full"></span>
          {{ loading ? 'Đang xử lý...' : (isEarlyWarning ? '🚪 Xác nhận Trả sớm & Check-out' : '✅ Hoàn tất Check-out') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
const props = defineProps<{ booking: any }>();
const emit  = defineEmits(['close','done']);
const token = () => localStorage.getItem('auth_token') || '';
const loading = ref(false);

const additionalFee  = ref(0);
const additionalNote = ref('');
const paymentMethod  = ref('cash');

const isEarlyWarning = ref(false);
const earlyConfirmed = ref(false);

// Formatting dates
const todayStr = new Date().toLocaleDateString('vi-VN');

function fmtDateStr(d: string) {
  if (!d) return '';
  const [y, m, day] = d.slice(0, 10).split('-');
  return `${day}/${m}/${y}`;
}
function fmtDateTime(d: string) { 
  if(!d) return '—'; 
  return new Date(d).toLocaleString('vi-VN', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit', year: 'numeric' }); 
}
function fmtShortDate(d: string) {
  if(!d) return '—';
  return new Date(d).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
}
function fmtCombined(date: string, time: string) {
  if (!date) return '—';
  const [y, m, d] = date.slice(0, 10).split('-');
  const t = time ? time.slice(0, 5) : '00:00';
  return `${t} ${d}/${m}/${y}`;
}
function fmtMoney(n:number){ return new Intl.NumberFormat('vi-VN').format(n??0)+'đ'; }

// Computations
const customerName = computed(() => props.booking.customer?.name || props.booking.customer_name || 'Khách vãng lai');
const roomName     = computed(() => props.booking.room?.room_number || props.booking.room?.title || 'Không rõ');
const roomPrice    = computed(() => props.booking.room?.price || 0);

const nights = computed(() => {
  const ci = props.booking.check_in_date;
  const co = props.booking.check_out_date;
  if(!ci || !co) return 1;
  const d1 = new Date(ci.slice(0,10));
  const d2 = new Date(co.slice(0,10));
  return Math.max(1, Math.round((d2.getTime()-d1.getTime())/86400000));
});

const roomTotal = computed(() => Number(roomPrice.value) * Number(nights.value));
const surchargesTotal = computed(() => {
  return (props.booking.services || []).reduce((acc: number, svc: any) => {
    return svc.is_paid ? acc : acc + Number(svc.total_price || 0);
  }, 0);
});

// Calculate actual grand total including the new additional string-based fee placed here
const grandTotal = computed(() => {
  return Number(roomTotal.value) + Number(surchargesTotal.value) + Number(additionalFee.value || 0);
});

// Final Amount calculation (Grand Total - Discount - Paid)
const remainingAmount = computed(() => {
  const tot = Number(grandTotal.value) - Number(props.booking.discount_amount || 0);
  return Math.max(0, tot - Number(props.booking.paid_amount || 0));
});

const finalAmount = computed(() => remainingAmount.value);

function printBill() {
  window.print();
}

async function submit() {
  loading.value = true;
  const res = await fetch(`/api/admin/bookings/${props.booking.id}/checkout`, {
    method:'POST',
    headers:{'Content-Type':'application/json',Authorization:`Bearer ${token()}`},
    body: JSON.stringify({
      additional_fee:  additionalFee.value,
      additional_note: additionalNote.value,
      payment_method:  paymentMethod.value,
      early_checkout:  isEarlyWarning.value && earlyConfirmed.value,
    }),
  });
  loading.value = false;

  if (res.ok) {
    const msg = isEarlyWarning.value
      ? 'Trả phòng sớm thành công. Phòng đang được dọn dẹp.'
      : 'Thanh toán & Check-out thành công. Phòng được chuyển sang dọn dẹp.';
    alert(msg);
    emit('done');
    emit('close');
  } else {
    const d = await res.json();
    if (d.is_early) {
      isEarlyWarning.value = true;
    } else {
      alert(d.message || 'Lỗi khi check-out');
    }
  }
}
</script>

<style scoped>
.input-sm { border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; }
.input-sm:focus { outline: none; box-shadow: 0 0 0 2px #9333ea; border-color: transparent; }
.label-sm { display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.375rem; }

/* Print Styles Override for the Modal */
@media print {
  body * {
    visibility: hidden;
  }
  .printable-bill, .printable-bill * {
    visibility: visible;
  }
  .printable-bill {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    margin: 0;
    padding: 20px;
    font-size: 12pt;
    color: #000;
  }
}
</style>

