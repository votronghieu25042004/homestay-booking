<template>
  <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" @click.self="$emit('close')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 sticky top-0 bg-white z-10">
        <h3 class="text-lg font-bold text-gray-800">+ Tạo Booking Mới</h3>
        <div class="flex items-center gap-3">
          <div class="flex gap-1">
            <span v-for="(st, i) in steps" :key="i"
              class="w-8 h-1.5 rounded-full transition-colors"
              :class="step >= i+1 ? 'bg-emerald-600' : 'bg-gray-200'"></span>
          </div>
          <button @click="$emit('close')" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400"><X class="w-5 h-5"/></button>
        </div>
      </div>

      <div class="p-6 space-y-6">
        <!-- STEP 1: Tìm phòng -->
        <div v-if="step === 1" class="space-y-4">
          <h4 class="font-semibold text-gray-700">Bước 1: Chọn ngày & phòng</h4>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="label-sm">Check-in <span class="text-red-500">*</span></label>
              <input v-model="form.check_in_date" type="date" class="input-sm w-full" @change="searchRooms" />
            </div>
            <div>
              <label class="label-sm">Check-out <span class="text-red-500">*</span></label>
              <input v-model="form.check_out_date" type="date" class="input-sm w-full" @change="searchRooms" />
            </div>
            <div>
              <label class="label-sm">Người lớn</label>
              <input v-model.number="form.adults" type="number" min="1" class="input-sm w-full" />
            </div>
            <div>
              <label class="label-sm">Trẻ em</label>
              <input v-model.number="form.children" type="number" min="0" class="input-sm w-full" />
            </div>
          </div>

          <!-- Room list -->
          <div v-if="availableRooms.length > 0" class="space-y-2">
            <label class="label-sm">Chọn phòng</label>
            <div v-for="r in availableRooms" :key="r.id"
              @click="form.room_id = r.id"
              class="flex items-center justify-between p-3 rounded-xl border-2 cursor-pointer transition-colors"
              :class="form.room_id === r.id ? 'border-emerald-500 bg-emerald-50' : 'border-gray-100 hover:border-gray-300'">
              <div>
                <div class="font-semibold text-gray-800">{{ r.room_number ? `Phòng ${r.room_number} – ` : '' }}{{ r.title }}</div>
                <div class="text-xs text-gray-500">{{ r.type }} · Tối đa {{ r.max_guests }} khách</div>
              </div>
              <div class="text-emerald-700 font-bold">{{ fmtMoney(r.price) }}/đêm</div>
            </div>
          </div>
          <div v-else-if="form.check_in_date && form.check_out_date" class="text-center py-4 text-gray-400 text-sm">
            <span v-if="searchingRooms">🔍 Đang tìm phòng trống...</span>
            <span v-else>Không có phòng trống trong khoảng thời gian này</span>
          </div>
        </div>

        <!-- STEP 2: Thông tin khách -->
        <div v-if="step === 2" class="space-y-4">
          <h4 class="font-semibold text-gray-700">Bước 2: Thông tin khách hàng</h4>
          <div class="flex gap-2">
            <input v-model="customerSearch" @input="searchCustomer" placeholder="Tìm khách cũ (SĐT hoặc email)..." class="input-sm flex-1" />
          </div>
          <div v-if="foundCustomers.length > 0" class="space-y-1 border border-gray-100 rounded-xl overflow-hidden">
            <button v-for="c in foundCustomers" :key="c.id" @click="selectCustomer(c)"
              class="w-full text-left px-4 py-2.5 hover:bg-emerald-50 transition-colors flex justify-between items-center text-sm">
              <span class="font-medium">{{ c.name }}</span>
              <span class="text-gray-400">{{ c.phone }} · {{ c.email }}</span>
            </button>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
              <label class="label-sm">Họ tên <span class="text-red-500">*</span></label>
              <input v-model="form.customerName" class="input-sm w-full" placeholder="Nguyen Van A" />
            </div>
            <div>
              <label class="label-sm">Số điện thoại <span class="text-red-500">*</span></label>
              <input v-model="form.customerPhone" class="input-sm w-full" placeholder="0901234567" />
            </div>
            <div>
              <label class="label-sm">Email</label>
              <input v-model="form.customerEmail" type="email" class="input-sm w-full" placeholder="example@gmail.com" />
            </div>
            <div>
              <label class="label-sm">Nguồn đặt</label>
              <select v-model="form.source" class="input-sm w-full">
                <option value="walkin">Walk-in</option>
                <option value="phone">Điện thoại</option>
                <option value="website">Website</option>
                <option value="booking_com">Booking.com</option>
                <option value="agoda">Agoda</option>
                <option value="other">Khác</option>
              </select>
            </div>
          </div>
        </div>

        <!-- STEP 3: Dịch vụ & Thanh toán -->
        <div v-if="step === 3" class="space-y-4">
          <h4 class="font-semibold text-gray-700">Bước 3: Dịch vụ & Thanh toán</h4>

          <!-- Summary -->
          <div class="bg-emerald-50 rounded-xl p-4 text-sm">
            <div class="flex justify-between"><span class="text-gray-600">Phòng × {{ nights }} đêm</span><span class="font-semibold">{{ fmtMoney(roomSubtotal) }}</span></div>
          </div>

          <!-- Extra services -->
          <div>
            <label class="label-sm">Dịch vụ thêm</label>
            <div class="space-y-2">
              <div v-for="(svc, i) in form.services" :key="i" class="flex gap-2 items-center">
                <input v-model="svc.service_name" placeholder="Tên dịch vụ" class="input-sm flex-1" />
                <input v-model.number="svc.unit_price" type="number" placeholder="Giá" class="input-sm w-24" />
                <input v-model.number="svc.quantity" type="number" min="1" class="input-sm w-14" />
                <button @click="form.services.splice(i,1)" class="text-red-400 hover:text-red-600">✕</button>
              </div>
              <button @click="form.services.push({service_name:'',unit_price:0,quantity:1})" class="text-sm text-emerald-600 hover:underline">+ Thêm dịch vụ</button>
            </div>
          </div>

          <!-- Deposit -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="label-sm">Đặt cọc trước</label>
              <input v-model.number="form.deposit_amount" type="number" placeholder="0" class="input-sm w-full" />
            </div>
            <div>
              <label class="label-sm">Phương thức</label>
              <select v-model="form.deposit_method" class="input-sm w-full">
                <option value="cash">Tiền mặt</option>
                <option value="transfer">Chuyển khoản</option>
                <option value="card">Thẻ</option>
              </select>
            </div>
          </div>

          <div>
            <label class="label-sm">Ghi chú nội bộ</label>
            <textarea v-model="form.internal_note" rows="2" class="input-sm w-full resize-none" placeholder="Chỉ admin thấy..."></textarea>
          </div>

          <!-- Final total -->
          <div class="bg-gray-50 rounded-xl p-4 space-y-1 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>{{ fmtMoney(totalAmount) }}</span></div>
            <div class="flex justify-between font-bold text-base border-t border-gray-200 pt-2 mt-2">
              <span>Tổng cộng</span><span class="text-emerald-700">{{ fmtMoney(totalAmount) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer buttons -->
      <div class="flex gap-3 px-6 py-4 border-t border-gray-100 sticky bottom-0 bg-white">
        <button v-if="step > 1" @click="step--" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 text-sm transition-colors">‹ Quay lại</button>
        <div class="flex-1"></div>
        <button v-if="step < 3" @click="nextStep"
          :disabled="!canProceed"
          class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium transition-colors disabled:opacity-40">
          Tiếp theo ›
        </button>
        <button v-if="step === 3" @click="submitBooking"
          :disabled="loading"
          class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium transition-colors disabled:opacity-50">
          {{ loading ? 'Đang tạo...' : '✅ Xác nhận tạo booking' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import { X } from 'lucide-vue-next';

const emit  = defineEmits(['close','created']);
const token = () => localStorage.getItem('auth_token') || '';
const API   = '/api/admin';

const step    = ref(1);
const steps   = [1,2,3];
const loading = ref(false);
const searchingRooms = ref(false);
const availableRooms = ref<any[]>([]);
const customerSearch = ref('');
const foundCustomers = ref<any[]>([]);
const customerFound  = ref<any>(null);

const form = reactive({
  room_id: 0, check_in_date:'', check_out_date:'', adults:2, children:0,
  customerName:'', customerPhone:'', customerEmail:'', source:'walkin',
  services: [] as any[],
  deposit_amount:0, deposit_method:'cash', internal_note:'',
});

const selectedRoom = computed(() => availableRooms.value.find(r => r.id === form.room_id));
const nights       = computed(() => form.check_in_date && form.check_out_date
  ? Math.max(1, Math.round((new Date(form.check_out_date).getTime() - new Date(form.check_in_date).getTime()) / 86400000))
  : 0);
const roomSubtotal = computed(() => (selectedRoom.value?.price ?? 0) * nights.value);
const servicesTotal= computed(() => form.services.reduce((s,v) => s + (v.unit_price||0)*(v.quantity||1), 0));
const totalAmount  = computed(() => roomSubtotal.value + servicesTotal.value);
const canProceed   = computed(() => {
  if (step.value === 1) return form.room_id > 0;
  if (step.value === 2) return !!form.customerName && !!form.customerPhone;
  return true;
});

async function searchRooms() {
  if (!form.check_in_date || !form.check_out_date) return;
  searchingRooms.value = true;
  const res = await fetch(`${API}/rooms/available?check_in=${form.check_in_date}&check_out=${form.check_out_date}`, { headers:{Authorization:`Bearer ${token()}`} });
  searchingRooms.value = false;
  if (res.ok) availableRooms.value = await res.json();
}

let debounce: any;
async function searchCustomer() {
  clearTimeout(debounce);
  if (customerSearch.value.length < 3) { foundCustomers.value = []; return; }
  debounce = setTimeout(async () => {
    const params = new URLSearchParams({ customer_search: customerSearch.value, per_page:'5' });
    const res = await fetch(`/api/admin/users?${params}`, { headers:{Authorization:`Bearer ${token()}`} });
    if (res.ok) { const d = await res.json(); foundCustomers.value = d.data || d; }
  }, 300);
}

function selectCustomer(c: any) {
  customerFound.value = c;
  form.customerName  = c.name;
  form.customerPhone = c.phone;
  form.customerEmail = c.email;
  foundCustomers.value = [];
  customerSearch.value = '';
}

function nextStep() { if (canProceed.value) step.value++; }

async function submitBooking() {
  loading.value = true;
  // Xử lý: nếu có customerFound dùng luôn id, nếu không cần tạo khách mới trước
  let customerId = customerFound.value?.id;
  if (!customerId) {
    const regRes = await fetch('/api/register', {
      method:'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({ name:form.customerName, phone:form.customerPhone, email:form.customerEmail || `guest_${Date.now()}@temp.com`, password:'Guest@123', role:'customer' }),
    });
    if (regRes.ok) { const d = await regRes.json(); customerId = d.user?.id; }
  }

  const res = await fetch(`${API}/bookings`, {
    method:'POST', headers:{'Content-Type':'application/json',Authorization:`Bearer ${token()}`},
    body: JSON.stringify({
      customer_id:customerId, room_id:form.room_id,
      check_in_date:form.check_in_date, check_out_date:form.check_out_date,
      adults:form.adults, children:form.children, source:form.source,
      services:form.services.filter(s=>s.service_name),
      deposit_amount:form.deposit_amount, deposit_method:form.deposit_method,
      internal_note:form.internal_note,
    }),
  });
  loading.value = false;
  if (res.ok) emit('created');
  else { const d = await res.json(); alert(d.message || 'Lỗi tạo booking'); }
}

function fmtMoney(n:number){ return new Intl.NumberFormat('vi-VN').format(n??0)+'đ'; }
</script>
<style scoped>
.input-sm { border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; }
.input-sm:focus { outline: none; box-shadow: 0 0 0 2px #10b981; }
.label-sm { display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.375rem; }
</style>
