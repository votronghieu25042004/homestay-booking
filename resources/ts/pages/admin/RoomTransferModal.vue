<template>
  <div class="fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4" @click.self="$emit('close')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
      <h3 class="text-lg font-bold text-gray-800 mb-1">🔄 Đổi Phòng Cho Khách</h3>
      <p class="text-sm text-gray-500 mb-5">
        Chọn phòng trống để chuyển booking <span class="font-mono font-semibold text-emerald-700">{{ booking.booking_code }}</span> sang.
        Phòng cũ sẽ được chuyển sang trạng thái <strong>dọn dẹp</strong>.
      </p>

      <!-- Thông tin phòng hiện tại -->
      <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 mb-5 flex items-center gap-3">
        <span class="text-2xl">🛏️</span>
        <div>
          <p class="text-xs text-gray-500 mb-0.5">Phòng hiện tại</p>
          <p class="font-semibold text-gray-800">{{ booking.room?.title || booking.room_name || 'Không rõ' }}</p>
        </div>
        <span class="ml-auto text-2xl">→</span>
      </div>

      <!-- Danh sách phòng trống -->
      <div v-if="loadingRooms" class="text-center py-8 text-gray-400">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-emerald-600 mx-auto mb-2"></div>
        Đang tải danh sách phòng...
      </div>

      <div v-else-if="availableRooms.length === 0" class="text-center py-8 text-gray-400">
        <p class="text-3xl mb-2">😔</p>
        <p>Không có phòng trống nào khác để đổi.</p>
      </div>

      <div v-else class="space-y-2 max-h-60 overflow-y-auto pr-1 mb-5">
        <label
          v-for="room in availableRooms"
          :key="room.id"
          class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-colors"
          :class="selectedRoomId === room.id ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-emerald-300 hover:bg-emerald-50/50'"
        >
          <input type="radio" :value="room.id" v-model="selectedRoomId" class="text-emerald-600 focus:ring-emerald-400" />
          <div class="flex-1">
            <p class="font-semibold text-gray-800 text-sm">{{ room.title }}</p>
            <p class="text-xs text-gray-500">{{ room.type === 'house' ? 'Nguyên căn' : 'Phòng riêng' }} · Tối đa {{ room.max_guests }} khách</p>
          </div>
          <span class="font-bold text-emerald-700 text-sm whitespace-nowrap">{{ fmtMoney(room.price) }}/đêm</span>
        </label>
      </div>

      <!-- Lý do đổi phòng -->
      <div class="mb-5">
        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Lý do đổi phòng</label>
        <input
          v-model="reason"
          placeholder="VD: Máy lạnh hư, khách yêu cầu..."
          class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400"
        />
      </div>

      <div class="flex gap-3">
        <button @click="$emit('close')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 text-sm transition-colors">Hủy</button>
        <button
          @click="submit"
          :disabled="!selectedRoomId || loading"
          class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium transition-colors disabled:opacity-40"
        >
          {{ loading ? 'Đang xử lý...' : '🔄 Xác nhận đổi phòng' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';

const props = defineProps<{ booking: any }>();
const emit  = defineEmits(['close', 'done']);

const token = () => localStorage.getItem('auth_token') || '';

const loadingRooms = ref(true);
const loading      = ref(false);
const availableRooms = ref<any[]>([]);
const selectedRoomId = ref<number | null>(null);
const reason = ref('');

onMounted(async () => {
  try {
    const res = await fetch('/api/rooms?all=true', {
      headers: { Authorization: `Bearer ${token()}` }
    });
    if (res.ok) {
      const allRooms = await res.json();
      // Chỉ hiển thị phòng riêng (type=room), đang sẵn sàng (available), và khác phòng hiện tại
      availableRooms.value = allRooms.filter(
        (r: any) => r.status === 'available' && r.type === 'room' && r.id !== props.booking.room_id
      );
    }
  } finally {
    loadingRooms.value = false;
  }
});

async function submit() {
  if (!selectedRoomId.value) return;
  loading.value = true;

  const res = await fetch(`/api/admin/bookings/${props.booking.id}/transfer-room`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token()}` },
    body: JSON.stringify({ new_room_id: selectedRoomId.value, reason: reason.value })
  });

  loading.value = false;
  if (res.ok) {
    const data = await res.json();
    alert(data.message || 'Đổi phòng thành công!');
    emit('done');
    emit('close');
  } else {
    const d = await res.json();
    alert(d.message || 'Lỗi khi đổi phòng');
  }
}

function fmtMoney(n: number) {
  return new Intl.NumberFormat('vi-VN').format(n ?? 0) + 'đ';
}
</script>
