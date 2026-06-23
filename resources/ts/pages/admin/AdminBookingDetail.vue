<template>
  <!-- Overlay -->
  <div class="fixed inset-0 bg-black/50 z-50 flex items-start justify-end p-4" @click.self="$emit('close')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-hidden flex flex-col">
      <!-- Header -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0">
        <div class="flex items-center gap-3">
          <span class="font-mono font-bold text-emerald-700 text-lg">{{ booking?.booking_code }}</span>
          <StatusBadge v-if="booking" :status="booking.status" />
        </div>
        <div class="flex items-center gap-2">
          <StatusChangeDropdown v-if="booking" :booking="booking" @changed="refetch" />
          <button @click="$emit('close')" class="p-2 hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-600 transition-colors">
            <X class="w-5 h-5" />
          </button>
        </div>
      </div>

      <!-- Body -->
      <div v-if="loading" class="flex-1 flex items-center justify-center text-gray-400">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600"></div>
      </div>
      <div v-else-if="booking" class="flex-1 overflow-y-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-0 h-full">
          <!-- LEFT: Main Info -->
          <div class="lg:col-span-2 p-6 space-y-6">
            <!-- Guest Info -->
            <section>
              <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">👤 Khách hàng</h3>
              <div class="grid grid-cols-2 gap-3 text-sm">
                <div><span class="text-gray-400">Họ tên:</span> <span class="font-medium ml-1">{{ booking.customer?.name || booking.customer_name || 'Khách vãng lai' }}</span></div>
                <div><span class="text-gray-400">SĐT:</span> <a :href="`tel:${booking.customer?.phone || booking.customer_phone}`" class="text-emerald-600 hover:underline ml-1">{{ booking.customer?.phone || booking.customer_phone || 'Không có SĐT' }}</a></div>
                <div><span class="text-gray-400">Email:</span> <span class="ml-1">{{ booking.customer?.email || booking.customer_email || '—' }}</span></div>
                <div><span class="text-gray-400">Nguồn:</span> <span class="ml-1">{{ sourceLabel(booking.source) }}</span></div>
              </div>
            </section>

            <!-- Room & Dates -->
            <section>
              <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">🏠 Phòng & Thời gian</h3>
              <div class="grid grid-cols-2 gap-3 text-sm">
                <div><span class="text-gray-400">Phòng:</span> <span class="font-semibold text-emerald-700 ml-1">{{ booking.room?.room_number }} – {{ booking.room?.title }}</span></div>
                <div><span class="text-gray-400">Loại:</span> <span class="ml-1">{{ booking.room?.type }}</span></div>
                <div><span class="text-gray-400">Check-in:</span> <span class="font-medium ml-1">{{ fmtCombined(booking.check_in_date, booking.check_in_time) }}</span></div>
                <div><span class="text-gray-400">Check-out:</span> <span class="font-medium ml-1">{{ fmtCombined(booking.check_out_date, booking.check_out_time) }}</span></div>
                <div><span class="text-gray-400">Số đêm:</span> <span class="ml-1">{{ booking.nights_count ?? nightsCount(booking.check_in_date, booking.check_out_date) }} đêm</span></div>
                <div><span class="text-gray-400">Số người:</span> <span class="ml-1">{{ booking.adults }} lớn, {{ booking.children }} trẻ em</span></div>
              </div>
            </section>

            <!-- Invoice -->
            <section>
              <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">💰 Hóa đơn</h3>
              <table class="w-full text-sm">
                <tbody>
                  <tr class="border-b border-gray-50">
                    <td class="py-2 text-gray-500">Phòng ({{ nightsCount(booking.check_in_date, booking.check_out_date) }} đêm × {{ fmtMoney(booking.room?.price) }})</td>
                    <td class="py-2 text-right font-medium">{{ fmtMoney(booking.room?.price * nightsCount(booking.check_in_date, booking.check_out_date)) }}</td>
                  </tr>
                  <tr v-for="svc in booking.services" :key="svc.id" class="border-b border-gray-50">
                    <td class="py-2 text-gray-500 pl-4">+ {{ svc.service_name }} × {{ svc.quantity }}
                      <span v-if="svc.is_paid" class="ml-2 text-xs text-white bg-green-500 px-1.5 py-0.5 rounded-full inline-block">Đã thu tiền</span>
                    </td>
                    <td class="py-2 text-right">
                      <span v-if="svc.is_paid" class="line-through text-gray-400 mr-2">{{ fmtMoney(svc.total_price) }}</span>
                      <span v-else>{{ fmtMoney(svc.total_price) }}</span>
                      <button @click="removeService(svc.id)" class="text-red-400 hover:text-red-600 ml-2 text-xs">✕</button>
                    </td>
                  </tr>
                  <tr v-if="booking.additional_fee > 0" class="border-b border-gray-50">
                    <td class="py-2 text-gray-500 pl-4">+ Phụ thu khi Check-out: {{ booking.additional_note || 'Không ghi chú' }}</td>
                    <td class="py-2 text-right">{{ fmtMoney(booking.additional_fee) }}</td>
                  </tr>
                  <tr v-if="booking.discount_amount > 0" class="text-green-600 border-b border-gray-50">
                    <td class="py-2">Giảm giá</td>
                    <td class="py-2 text-right">-{{ fmtMoney(booking.discount_amount) }}</td>
                  </tr>
                  <tr class="font-bold">
                    <td class="py-2">Tổng cộng</td>
                    <td class="py-2 text-right text-emerald-700">{{ fmtMoney(Number(booking.total_amount) || 0) }}</td>
                  </tr>
                  <tr class="text-gray-500">
                    <td class="py-2">Đã thanh toán (Cọc + Thu trực tiếp)</td>
                    <td class="py-2 text-right text-green-600">{{ fmtMoney(Number(booking.paid_amount) || 0) }}</td>
                  </tr>
                  <tr v-if="(booking.remaining_amount ?? 0) > 0" class="text-red-600 font-semibold">
                    <td class="py-2">Còn lại</td>
                    <td class="py-2 text-right">{{ fmtMoney(booking.remaining_amount) }}</td>
                  </tr>
                </tbody>
              </table>
              <div class="mt-3 flex gap-2">
                <button @click="showAddService = !showAddService" class="text-sm text-emerald-600 hover:underline">+ Thêm dịch vụ/phụ thu</button>
              </div>
              <div v-if="showAddService" class="mt-2 p-3 bg-gray-50 rounded-lg space-y-3">
                <div class="flex flex-wrap gap-2">
                  <!-- Preset Surcharges Dropdown -->
                  <select v-model="selectedPreset" @change="onPresetChange" class="input-sm w-40">
                    <option value="">-- Chọn dịch vụ --</option>
                    <option v-for="preset in presetServices" :key="preset.name" :value="preset.name">
                      {{ preset.name }} ({{ fmtMoney(preset.price) }})
                    </option>
                    <option value="other">Phụ thu khác...</option>
                  </select>
                  
                  <!-- Custom Service Name (show when 'other' is selected OR no preset chosen) -->
                  <input v-if="selectedPreset === 'other' || !selectedPreset" v-model="newSvc.service_name" placeholder="Tên phụ thu/dịch vụ" class="input-sm flex-1" />
                  <!-- Show selected preset name as readonly when a preset is chosen -->
                  <span v-else class="input-sm flex-1 bg-gray-50 text-gray-700 truncate">{{ newSvc.service_name }}</span>
                  
                  <input v-model.number="newSvc.unit_price" type="number" placeholder="Đơn giá" class="input-sm w-28" />
                  <input v-model.number="newSvc.quantity" type="number" placeholder="SL" class="input-sm w-16" min="1" />
                  <button @click="addService" class="px-3 py-1.5 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition-colors">Thêm</button>
                </div>
                
                <label class="flex items-center gap-2 cursor-pointer mt-2 select-none">
                  <input type="checkbox" v-model="newSvc.is_paid" class="w-4 h-4 accent-emerald-600" />
                  <span class="text-sm font-medium text-gray-700">Khách đã trả tiền mặt món này (Đưa vào két ngay)</span>
                </label>
              </div>
            </section>

            <!-- Notes -->
            <section class="grid grid-cols-2 gap-4">
              <div>
                <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide block mb-2">📝 Ghi chú nội bộ</label>
                <textarea v-model="internalNote" rows="3" class="input-sm w-full resize-none" placeholder="Chỉ admin thấy..."></textarea>
                <button @click="saveNotes" class="mt-1 text-xs text-emerald-600 hover:underline">Lưu ghi chú</button>
              </div>
              <div>
                <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide block mb-2">💬 Ghi chú cho khách</label>
                <textarea v-model="guestNote" rows="3" class="input-sm w-full resize-none" placeholder="Khách sẽ thấy trong email..."></textarea>
              </div>
            </section>

            <!-- ID Card Section -->
            <section v-if="booking.id_card_image">
              <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">🪪 Hình ảnh CCCD</h3>
              <div v-if="idCardUrl" class="relative group w-64 h-40 bg-gray-100 rounded-xl overflow-hidden border border-gray-200">
                <img :src="idCardUrl" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <button @click="viewFullIdCard" class="text-white text-xs font-medium bg-white/20 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/30 hover:bg-white/40">Xem ảnh lớn</button>
                </div>
              </div>
              <div v-else class="text-xs text-gray-400 italic">Đang tải ảnh...</div>
            </section>


            <!-- Banner cảnh báo khi đơn đang chờ tiền CK -->
            <div v-if="booking.status === 'pending'" class="mt-2 p-3 bg-orange-50 border border-orange-200 rounded-xl flex items-start gap-2 text-sm">
              <span class="text-orange-500 text-base mt-0.5">⏳</span>
              <div>
                <p class="font-bold text-orange-800">Đang chờ tiền chuyển khoản</p>
                <p class="text-orange-700 text-xs mt-0.5">Phòng đã bị khóa lịch. Nếu không nhận được tiền cọc, bấm <strong>"Từ chối đặt cọc"</strong> để hủy đơn và giải phóng phòng.</p>
              </div>
            </div>

            <!-- Action buttons -->
            <section class="flex flex-wrap gap-2 pt-2">
              <!-- Nút Xác nhận đã nhận tiền CK (chỉ hiện khi pending) -->
              <button v-if="booking.status === 'pending'"
                @click="confirmPaymentReceived"
                :disabled="isConfirmingPayment"
                class="btn-primary bg-blue-600 hover:bg-blue-700 flex items-center gap-2">
                <span v-if="isConfirmingPayment" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white inline-block"></span>
                💰 {{ isConfirmingPayment ? 'Đang xác nhận...' : 'Xác nhận đã nhận tiền CK' }}
              </button>
              <!-- Nút Từ chối đặt cọc (chỉ hiện khi pending) -->
              <button v-if="booking.status === 'pending'"
                @click="rejectDeposit"
                :disabled="isRejectingDeposit"
                class="btn-danger flex items-center gap-2">
                <span v-if="isRejectingDeposit" class="animate-spin rounded-full h-4 w-4 border-b-2 border-red-600 inline-block"></span>
                🚫 {{ isRejectingDeposit ? 'Đang từ chối...' : 'Từ chối đặt cọc' }}
              </button>
              <button v-if="booking.status === 'confirmed'"
                @click="showCheckin = true"
                class="btn-primary">✅ Check-in</button>
              <button v-if="booking.status === 'checked_in'"
                @click="showCheckout = true"
                class="btn-primary bg-purple-600 hover:bg-purple-700">🚪 Check-out</button>
              <button v-if="!['checked_out','cancelled'].includes(booking.status)"
                @click="showCancel = true"
                class="btn-danger">❌ Hủy booking</button>
              <button @click="showPayment = true" class="btn-secondary">💳 Ghi thanh toán</button>
            </section>
          </div>

          <!-- RIGHT: Tabs -->
          <div class="lg:col-span-1 border-l border-gray-100 flex flex-col">
            <div class="flex border-b border-gray-100">
              <button v-for="t in ['log','payments']" :key="t"
                @click="rightTab = t"
                class="flex-1 py-3 text-sm font-medium transition-colors"
                :class="rightTab===t?'text-emerald-700 border-b-2 border-emerald-600':'text-gray-400 hover:text-gray-600'">
                {{ t === 'log' ? '📜 Lịch sử' : '💳 Thanh toán' }}
              </button>
            </div>

            <!-- Activity Log -->
            <div v-if="rightTab==='log'" class="flex-1 overflow-y-auto p-4 space-y-3">
              <div v-if="booking.activities?.length === 0" class="text-sm text-gray-400 text-center py-4">Chưa có hoạt động</div>
              <div v-for="act in booking.activities" :key="act.id" class="flex gap-3">
                <div class="w-1.5 rounded-full shrink-0 mt-1" :class="actDot(act.action_type)" style="height:auto;min-height:12px"></div>
                <div>
                  <div class="text-sm text-gray-700">{{ act.description }}</div>
                  <div class="text-xs text-gray-400 mt-0.5">{{ act.actor_name }} · {{ fmtDateTime(act.created_at) }}</div>
                </div>
              </div>
            </div>

            <!-- Payments -->
            <div v-if="rightTab==='payments'" class="flex-1 overflow-y-auto p-4 space-y-2">
              <div v-if="!booking.payments?.length" class="text-sm text-gray-400 text-center py-4">Chưa có giao dịch</div>
              <div v-for="p in booking.payments" :key="p.id"
                class="flex justify-between items-center p-2 rounded-lg border border-gray-100 text-sm">
                <div>
                  <div class="font-medium" :class="p.payment_type==='refund'?'text-red-600':p.payment_type==='deposit'?'text-blue-600':'text-emerald-600'">
                    {{ paymentTypeLabel(p.payment_type) }} {{ fmtMoney(p.amount) }}
                  </div>
                  <div class="text-xs text-gray-400">{{ p.recorded_by?.name }} · {{ methodLabel(p.payment_method) }} · {{ fmtDateTime(p.created_at) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Check-in Modal -->
    <CheckinModal v-if="showCheckin && booking" :booking="booking" @close="showCheckin=false" @done="refetch" />
    <!-- Check-out Modal -->
    <CheckoutModal v-if="showCheckout && booking" :booking="booking" @close="showCheckout=false" @done="refetch" />
    <!-- Cancel Modal -->
    <CancelModal v-if="showCancel && booking" :booking="booking" @close="showCancel=false" @done="refetch" />
    <!-- Payment Modal -->
    <PaymentModal v-if="showPayment && booking" :booking="booking" @close="showPayment=false" @done="refetch" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { X } from 'lucide-vue-next';
import StatusBadge from './StatusBadge.vue';
import StatusChangeDropdown from './StatusChangeDropdown.vue';
import CheckinModal from './CheckinModal.vue';
import CheckoutModal from './CheckoutModal.vue';
import CancelModal from './CancelModal.vue';
import PaymentModal from './PaymentModal.vue';

const props  = defineProps<{ bookingId: number }>();
const emit   = defineEmits(['close','updated']);

const API    = '/api/admin';
const token  = () => localStorage.getItem('auth_token') || '';

const loading     = ref(true);
const booking     = ref<any>(null);
const rightTab    = ref('log');
const showCheckin  = ref(false);
const showCheckout = ref(false);
const showCancel   = ref(false);
const showPayment  = ref(false);
const showAddService = ref(false);
const internalNote = ref('');
const guestNote    = ref('');
const newSvc       = ref({ service_name:'', unit_price:0, quantity:1, is_paid: false });
const selectedPreset = ref('');
const idCardUrl    = ref('');
const isConfirmingPayment = ref(false);
const isRejectingDeposit = ref(false);


const presetServices = [
  { name: 'Giặt ủi', price: 60000 },
  { name: 'Nước suối', price: 10000 },
  { name: 'Mì tôm', price: 15000 },
  { name: 'Bò húc', price: 20000 },
  { name: 'Nước ngọt', price: 15000 }
];

function onPresetChange() {
  if (selectedPreset.value && selectedPreset.value !== 'other') {
    const preset = presetServices.find(p => p.name === selectedPreset.value);
    if (preset) {
      newSvc.value.service_name = preset.name;
      newSvc.value.unit_price = preset.price;
      newSvc.value.quantity = 1; // reset quantity
    }
  } else {
    newSvc.value.service_name = '';
    newSvc.value.unit_price = 0;
  }
}

async function refetch() {
  loading.value = true;
  const res = await fetch(`${API}/bookings/${props.bookingId}`, { headers: { Authorization:`Bearer ${token()}` } });
  if (res.ok) {
    const data = await res.json();
    const b = data.data;
    if (b.room && b.room.data) b.room = b.room.data;
    if (b.customer && b.customer.data) b.customer = b.customer.data;
    booking.value = b;
    internalNote.value = booking.value.internal_note || '';
    guestNote.value    = booking.value.guest_note || '';
    if (b.id_card_image) fetchIdCard();
  }
  loading.value = false;
  emit('updated');
}

async function fetchIdCard() {
  try {
    const res = await fetch(`${API}/bookings/${props.bookingId}/id-card`, {
      headers: { Authorization:`Bearer ${token()}` }
    });
    if (res.ok) {
      const blob = await res.blob();
      if (idCardUrl.value) URL.revokeObjectURL(idCardUrl.value);
      idCardUrl.value = URL.createObjectURL(blob);
    }
  } catch (e) { console.error("Error fetching ID card", e); }
}

function viewFullIdCard() {
  if (idCardUrl.value) window.open(idCardUrl.value, '_blank');
}


async function rejectDeposit() {
  const reason = prompt('Nhập lý do từ chối (VD: Không nhận được tiền cọc sau 24h):');
  if (!reason) return;
  isRejectingDeposit.value = true;
  try {
    const res = await fetch(`${API}/bookings/${props.bookingId}/cancel`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token()}` },
      body: JSON.stringify({
        cancel_reason: reason || 'Không nhận được tiền cọc chuyển khoản',
        refund_amount: 0,
        refund_method: 'transfer'
      })
    });
    const data = await res.json();
    if (res.ok) {
      alert('✅ Đã từ chối đặt cọc! Phòng đã được giải phóng.');
      refetch();
    } else {
      alert('Lỗi: ' + (data.message || 'Không thể từ chối'));
    }
  } finally {
    isRejectingDeposit.value = false;
  }
}

async function confirmPaymentReceived() {
  if (!confirm('Xác nhận đã nhận tiền chuyển khoản và duyệt đơn này?')) return;
  isConfirmingPayment.value = true;
  try {
    const res = await fetch(`${API}/bookings/${props.bookingId}/confirm-payment`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token()}` },
      body: JSON.stringify({ note: 'Admin xác nhận đã nhận tiền chuyển khoản' })
    });
    const data = await res.json();
    if (res.ok) {
      alert('✅ Đã xác nhận nhận tiền! Đơn chuyển sang trạng thái Đã xác nhận.');
      refetch();
    } else {
      alert('Lỗi: ' + (data.message || 'Không thể xác nhận'));
    }
  } finally {
    isConfirmingPayment.value = false;
  }
}

async function saveNotes() {
  await fetch(`${API}/bookings/${props.bookingId}`, {
    method:'PUT', headers:{'Content-Type':'application/json',Authorization:`Bearer ${token()}`},
    body: JSON.stringify({ internal_note: internalNote.value, guest_note: guestNote.value }),
  });
  refetch();
}

async function addService() {
  if (!newSvc.value.service_name) {
    alert('Vui lòng chọn dịch vụ từ danh sách hoặc nhập tên phụ thu!');
    return;
  }
  if (!newSvc.value.unit_price || newSvc.value.unit_price <= 0) {
    alert('Vui lòng nhập đơn giá!');
    return;
  }
  const res = await fetch(`${API}/bookings/${props.bookingId}/services`, {
    method:'POST', headers:{'Content-Type':'application/json',Authorization:`Bearer ${token()}`},
    body: JSON.stringify(newSvc.value),
  });
  if (res.ok) { 
    newSvc.value = { service_name:'', unit_price:0, quantity:1, is_paid: false }; 
    selectedPreset.value = '';
    showAddService.value=false; 
    refetch(); 
  } else {
    const d = await res.json();
    alert(d.message || "Lỗi thêm dịch vụ");
  }
}

async function removeService(sid: number) {
  if (!confirm('Xóa dịch vụ này?')) return;
  const res = await fetch(`${API}/bookings/${props.bookingId}/services/${sid}`, { method:'DELETE', headers:{ Authorization:`Bearer ${token()}` } });
  if (res.ok) {
    refetch();
  } else {
    const d = await res.json();
    alert(d.message || "Lỗi khi xóa dịch vụ");
  }
}

// Helpers
function fmtDate(d: string)     { if(!d) return '—'; const [y,m,day]=d.slice(0, 10).split('-'); return `${day}/${m}/${y}`; }
function fmtCombined(date: string, time: string) {
  if (!date) return '—';
  const [y, m, d] = date.slice(0, 10).split('-');
  const t = time ? time.slice(0, 5) : '00:00';
  return `${t} ${d}/${m}/${y}`;
}
function fmtDateTime(d: string) { if(!d) return '—'; return new Date(d).toLocaleString('vi-VN'); }
function fmtMoney(n: number)    { return new Intl.NumberFormat('vi-VN').format(n??0)+'đ'; }
function nightsCount(ci:string,co:string){
  if(!ci||!co) return 0;
  const [cy,cm,cd]=ci.slice(0,10).split('-').map(Number);
  const [oy,om,od]=co.slice(0,10).split('-').map(Number);
  const d1=new Date(cy,cm-1,cd), d2=new Date(oy,om-1,od);
  return Math.round((d2.getTime()-d1.getTime())/86400000);
}
function sourceLabel(src:string){ return {website:'Website',booking_com:'Booking.com',agoda:'Agoda',walkin:'Walk-in',phone:'Điện thoại',other:'Khác'}[src]??src; }
function paymentTypeLabel(t:string){ return {deposit:'Cọc',balance:'Thanh toán',refund:'Hoàn tiền'}[t]??t; }
function methodLabel(m:string){ return {cash:'Tiền mặt',transfer:'Chuyển khoản',card:'Thẻ'}[m]??m; }
function actDot(type:string){ return {created:'bg-blue-400',status_changed:'bg-yellow-400',checkin:'bg-green-500',checkout:'bg-purple-400',cancelled:'bg-red-400',payment_added:'bg-emerald-400'}[type]??'bg-gray-300'; }

onMounted(refetch);
</script>

<style scoped>
.input-sm {
  border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
}
.input-sm:focus { outline: none; box-shadow: 0 0 0 2px #10b981; }
.btn-primary { padding: 0.5rem 1rem; background: #059669; color: white; font-size: 0.875rem; border-radius: 0.5rem; font-weight: 500; transition: background 0.15s; }
.btn-primary:hover { background: #047857; }
.btn-danger { padding: 0.5rem 1rem; background: #fee2e2; color: #b91c1c; font-size: 0.875rem; border-radius: 0.5rem; font-weight: 500; transition: background 0.15s; }
.btn-danger:hover { background: #fecaca; }
.btn-secondary { padding: 0.5rem 1rem; background: #f3f4f6; color: #374151; font-size: 0.875rem; border-radius: 0.5rem; font-weight: 500; transition: background 0.15s; }
.btn-secondary:hover { background: #e5e7eb; }
</style>
