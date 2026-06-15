<template>
  <div class="fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4" @click.self="$emit('close')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 overflow-y-auto max-h-[90vh]">
      <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">✅ Xác nhận Check-in</h3>
      <div class="space-y-4">
        <div>
          <label class="label-sm">Gán phòng cụ thể</label>
          <select v-model="selectedRoomId" class="input-sm w-full">
            <option :value="booking.room_id">{{ booking.room?.room_number }} – {{ booking.room?.title }} (hiện tại)</option>
            <option v-for="r in cleanRooms" :key="r.id" :value="r.id">{{ r.room_number }} – {{ r.title }} ({{ r.type }})</option>
          </select>
        </div>

        <div>
          <label class="label-sm">Hình ảnh căn cước (Bắt buộc)</label>
          <div class="mt-1 flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-xl hover:border-emerald-500 transition-colors cursor-pointer bg-gray-50"  @click="fileInput?.click()">
            <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="handleFileChange">
            
            <div v-if="!previewUrl" class="text-center">
              <span class="text-3xl mb-2 block">📸</span>
              <p class="text-xs text-gray-500">Chụp hoặc chọn ảnh CCCD</p>
            </div>
            <img v-else :src="previewUrl" class="w-full h-44 object-contain rounded-lg shadow-sm" />
            
            <button v-if="previewUrl" type="button" @click.stop="clearFile" class="mt-2 text-xs text-red-600 font-semibold underline">Chọn lại</button>
          </div>
        </div>

        <div>
          <label class="label-sm">Ghi chú đặc biệt (nội bộ)</label>
          <textarea v-model="internalNote" rows="2" class="input-sm w-full resize-none" placeholder="VIP, dị ứng, yêu cầu đặc biệt..."></textarea>
        </div>
        <div class="bg-amber-50 rounded-lg p-3 text-sm text-amber-800" v-if="booking.remaining_amount > 0">
          ⚠ Khách còn thiếu <strong>{{ fmtMoney(booking.remaining_amount) }}</strong> chưa thanh toán
        </div>
      </div>
      <div class="flex gap-3 mt-6">
        <button @click="$emit('close')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 text-sm transition-colors">Hủy</button>
        <button @click="submit" :disabled="loading || !selectedFile" class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium transition-colors disabled:opacity-50">
          {{ loading ? 'Đang xử lý...' : '✅ Hoàn tất Check-in' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
const props = defineProps<{ booking: any }>();
const emit  = defineEmits(['close','done']);
const token = () => localStorage.getItem('auth_token') || '';

const loading = ref(false);
const cleanRooms = ref<any[]>([]);
const selectedRoomId = ref(props.booking.room_id);
const internalNote   = ref('');

const fileInput    = ref<any>(null);
const selectedFile = ref<File | null>(null);
const previewUrl   = ref('');

function handleFileChange(e: any) {
  const file = e.target.files[0];
  if (!file) return;
  selectedFile.value = file;
  previewUrl.value   = URL.createObjectURL(file);
}
function clearFile() {
  selectedFile.value = null;
  previewUrl.value   = '';
}

async function fetchCleanRooms() {
  const res = await fetch(`/api/admin/rooms/available?check_in=${props.booking.check_in_date}&check_out=${props.booking.check_out_date}`, { headers:{Authorization:`Bearer ${token()}`} });
  if (res.ok) cleanRooms.value = (await res.json()).filter((r:any) => r.id !== props.booking.room_id);
}
async function submit() {
  if (!selectedFile.value) {
    alert('Vui lòng chụp/tải lên ảnh CCCD');
    return;
  }
  loading.value = true;

  const fd = new FormData();
  fd.append('room_id', (selectedRoomId.value || '').toString());
  fd.append('internal_note', internalNote.value);
  fd.append('id_card_image', selectedFile.value);

  const res = await fetch(`/api/admin/bookings/${props.booking.id}/checkin`, {
    method:'POST', 
    headers:{ Authorization:`Bearer ${token()}` },
    body: fd,
  });
  
  loading.value = false;
  if (res.ok) { emit('done'); emit('close'); }
  else { const d = await res.json(); alert(d.message || 'Lỗi check-in'); }
}
function fmtMoney(n:number){ return new Intl.NumberFormat('vi-VN').format(n??0)+'đ'; }
onMounted(fetchCleanRooms);
</script>
<style scoped>
.input-sm { border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; width: 100%; }
.input-sm:focus { outline: none; box-shadow: 0 0 0 2px #10b981; }
.label-sm { display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.375rem; }
</style>
