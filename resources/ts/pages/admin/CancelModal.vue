<template>
  <div class="fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4" @click.self="$emit('close')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
      <h3 class="text-lg font-bold text-gray-800 mb-4">❌ Hủy Booking</h3>
      <div class="space-y-4">
        <div>
          <label class="label-sm">Lý do hủy <span class="text-red-500">*</span></label>
          <select v-model="cancelReason" class="input-sm w-full">
            <option value="">-- Chọn lý do --</option>
            <option value="Khách yêu cầu hủy">Khách yêu cầu hủy</option>
            <option value="Không thanh toán">Không thanh toán</option>
            <option value="Overbooking">Overbooking</option>
            <option value="Phòng không sẵn sàng">Phòng không sẵn sàng</option>
            <option value="Khác">Khác</option>
          </select>
        </div>
        <div v-if="booking.paid_amount > 0">
          <label class="label-sm">Hoàn tiền</label>
          <div class="flex gap-2">
            <input v-model.number="refundAmount" type="number" class="input-sm flex-1" :placeholder="`Tối đa: ${booking.paid_amount}`" :max="booking.paid_amount" min="0" />
            <select v-model="refundMethod" class="input-sm w-32">
              <option value="transfer">Chuyển khoản</option>
              <option value="cash">Tiền mặt</option>
            </select>
          </div>
          <p class="text-xs text-gray-400 mt-1">Đã cọc: {{ fmtMoney(booking.paid_amount) }}</p>
        </div>
        <div class="bg-red-50 rounded-lg p-3 text-sm text-red-700">
          ⚠ Thao tác này không thể hoàn tác. Booking sẽ chuyển sang trạng thái <strong>Đã hủy</strong>.
        </div>
      </div>
      <div class="flex gap-3 mt-6">
        <button @click="$emit('close')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 text-sm transition-colors">Không hủy</button>
        <button @click="submit" :disabled="loading || !cancelReason" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium transition-colors disabled:opacity-50">
          {{ loading ? 'Đang xử lý...' : 'Xác nhận hủy' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
const props = defineProps<{ booking: any }>();
const emit  = defineEmits(['close','done']);
const token = () => localStorage.getItem('auth_token') || '';
const loading      = ref(false);
const cancelReason = ref('');
const refundAmount = ref(props.booking.paid_amount ?? 0);
const refundMethod = ref('transfer');
async function submit() {
  if (!cancelReason.value) return;
  loading.value = true;
  const res = await fetch(`/api/admin/bookings/${props.booking.id}/cancel`, {
    method:'POST', headers:{'Content-Type':'application/json',Authorization:`Bearer ${token()}`},
    body: JSON.stringify({ cancel_reason:cancelReason.value, refund_amount:refundAmount.value, refund_method:refundMethod.value }),
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
