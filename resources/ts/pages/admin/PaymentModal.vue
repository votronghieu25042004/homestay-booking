<template>
  <div class="fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4" @click.self="$emit('close')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
      <h3 class="text-lg font-bold text-gray-800 mb-4">💳 Ghi nhận Thanh toán</h3>
      <div class="space-y-4">
        <div>
          <label class="label-sm">Loại giao dịch</label>
          <select v-model="form.payment_type" class="input-sm w-full">
            <option value="deposit">Cọc trước</option>
            <option value="balance">Thanh toán</option>
            <option value="refund">Hoàn tiền</option>
          </select>
        </div>
        <div>
          <label class="label-sm">Số tiền</label>
          <input v-model.number="form.amount" type="number" class="input-sm w-full" placeholder="0" min="1" />
        </div>
        <div>
          <label class="label-sm">Phương thức</label>
          <select v-model="form.payment_method" class="input-sm w-full">
            <option value="cash">Tiền mặt</option>
            <option value="transfer">Chuyển khoản</option>
            <option value="card">Thẻ</option>
          </select>
        </div>
        <div>
          <label class="label-sm">Mã tham chiếu (nếu có)</label>
          <input v-model="form.reference_code" class="input-sm w-full" placeholder="Mã giao dịch ngân hàng..." />
        </div>
        <div>
          <label class="label-sm">Ghi chú</label>
          <input v-model="form.note" class="input-sm w-full" placeholder="Ghi chú..." />
        </div>
        <div class="bg-gray-50 rounded-lg p-3 text-sm">
          <div class="flex justify-between text-gray-600">
            <span>Tổng hóa đơn</span><span class="font-medium">{{ fmtMoney(booking.total_amount) }}</span>
          </div>
          <div class="flex justify-between text-green-600 mt-1">
            <span>Đã thanh toán</span><span class="font-medium">{{ fmtMoney(booking.paid_amount) }}</span>
          </div>
          <div class="flex justify-between text-red-600 mt-1" v-if="booking.remaining_amount > 0">
            <span>Còn lại</span><span class="font-bold">{{ fmtMoney(booking.remaining_amount) }}</span>
          </div>
        </div>
      </div>
      <div class="flex gap-3 mt-6">
        <button @click="$emit('close')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 text-sm transition-colors">Hủy</button>
        <button @click="submit" :disabled="loading || !form.amount" class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium transition-colors disabled:opacity-50">
          {{ loading ? 'Đang lưu...' : '💾 Ghi nhận' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue';
const props = defineProps<{ booking: any }>();
const emit  = defineEmits(['close','done']);
const token = () => localStorage.getItem('auth_token') || '';
const loading = ref(false);
const form = reactive({ amount: props.booking.remaining_amount ?? 0, payment_method:'cash', payment_type:'balance', reference_code:'', note:'' });
async function submit() {
  if (!form.amount) return;
  loading.value = true;
  const res = await fetch(`/api/admin/bookings/${props.booking.id}/payments`, {
    method:'POST', headers:{'Content-Type':'application/json',Authorization:`Bearer ${token()}`},
    body: JSON.stringify(form),
  });
  loading.value = false;
  if (res.ok) { emit('done'); emit('close'); }
  else { const d = await res.json(); alert(d.message||'Lỗi'); }
}
function fmtMoney(n:number){ return new Intl.NumberFormat('vi-VN').format(n??0)+'đ'; }
</script>
<style scoped>
.input-sm { border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; }
.input-sm:focus { outline: none; box-shadow: 0 0 0 2px #10b981; }
.label-sm { display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.375rem; }
</style>
