<template>
  <div class="relative" ref="el">
    <button @click="open = !open"
      class="p-1.5 rounded-md text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 transition-colors"
      title="Đổi trạng thái">
      <RefreshCw class="w-3.5 h-3.5" />
    </button>
    <div v-if="open"
      class="absolute right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 min-w-[160px] py-1">
      <button v-for="s in availableStatuses" :key="s.value"
        @click="change(s.value)"
        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition-colors flex items-center gap-2">
        <span class="w-2 h-2 rounded-full" :class="s.dot"></span>
        {{ s.label }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { RefreshCw } from 'lucide-vue-next';

const props  = defineProps<{ booking: any }>();
const emit   = defineEmits(['changed']);
const open   = ref(false);
const el     = ref<HTMLElement | null>(null);

const allStatuses = [
  { value:'pending',     label:'Chờ xác nhận', dot:'bg-yellow-400' },
  { value:'confirmed',   label:'Đã xác nhận',  dot:'bg-blue-400' },
  { value:'checked_in',  label:'Check-in',      dot:'bg-emerald-400' },
  { value:'checked_out', label:'Check-out',     dot:'bg-purple-400' },
  { value:'cancelled',   label:'Hủy',           dot:'bg-red-400' },
  { value:'no_show',     label:'No-show',       dot:'bg-gray-400' },
];

const availableStatuses = computed(() => allStatuses.filter(s => s.value !== props.booking.status));

async function change(status: string) {
  open.value = false;
  const res = await fetch(`/api/admin/bookings/${props.booking.id}/status`, {
    method: 'PATCH',
    headers: { 'Content-Type':'application/json', Authorization:`Bearer ${localStorage.getItem('auth_token')||''}` },
    body: JSON.stringify({ status }),
  });
  if (res.ok) emit('changed');
  else alert('Lỗi đổi trạng thái');
}

const clickOutside = (e: MouseEvent) => { if (el.value && !el.value.contains(e.target as Node)) open.value = false; };
onMounted(()  => document.addEventListener('click', clickOutside));
onUnmounted(() => document.removeEventListener('click', clickOutside));
</script>
