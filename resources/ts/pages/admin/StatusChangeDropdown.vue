<template>
  <div class="relative" ref="el">
    <button @click="open = !open"
      class="flex items-center gap-2 px-3 py-1.5 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
      <StatusBadge :status="booking.status" />
      <ChevronDown class="w-3.5 h-3.5 text-gray-400" />
    </button>
    <div v-if="open"
      class="absolute left-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 min-w-[180px] py-1">
      <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">Đổi thành</div>
      <button v-for="s in availableStatuses" :key="s.value"
        @click="change(s.value)"
        class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors flex items-center gap-2">
        <span class="w-2 h-2 rounded-full" :class="s.dot"></span>{{ s.label }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { ChevronDown } from 'lucide-vue-next';
import StatusBadge from './StatusBadge.vue';

const props = defineProps<{ booking: any }>();
const emit  = defineEmits(['changed']);
const open  = ref(false);
const el    = ref<HTMLElement|null>(null);
const token = () => localStorage.getItem('auth_token') || '';

const allStatuses = [
  { value:'pending',     label:'Chờ xác nhận', dot:'bg-yellow-400' },
  { value:'confirmed',   label:'Đã xác nhận',  dot:'bg-blue-400' },
  { value:'cancelled',   label:'Hủy',           dot:'bg-red-400' },
  { value:'no_show',     label:'No-show',       dot:'bg-gray-400' },
];
const availableStatuses = computed(() => allStatuses.filter(s => s.value !== props.booking.status));

async function change(status:string) {
  open.value = false;
  const res = await fetch(`/api/admin/bookings/${props.booking.id}/status`, {
    method:'PATCH', headers:{'Content-Type':'application/json',Authorization:`Bearer ${token()}`},
    body: JSON.stringify({ status }),
  });
  if (res.ok) emit('changed');
}

const clickOutside = (e:MouseEvent) => { if (el.value && !el.value.contains(e.target as Node)) open.value = false; };
onMounted(()  => document.addEventListener('click', clickOutside));
onUnmounted(() => document.removeEventListener('click', clickOutside));
</script>
