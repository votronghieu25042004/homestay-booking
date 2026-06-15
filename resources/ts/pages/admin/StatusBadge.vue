<template>
  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
    :class="badgeClass">
    {{ label }}
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{ status: string }>();

const config: Record<string, { label: string; cls: string }> = {
  pending:     { label: 'Chờ xác nhận', cls: 'bg-yellow-100 text-yellow-800' },
  confirmed:   { label: 'Đã xác nhận',  cls: 'bg-blue-100 text-blue-800' },
  checked_in:  { label: 'Đang ở',        cls: 'bg-emerald-100 text-emerald-800' },
  checked_out: { label: 'Check-out',     cls: 'bg-purple-100 text-purple-800' },
  cancelled:   { label: 'Đã hủy',        cls: 'bg-red-100 text-red-800' },
  no_show:     { label: 'No-show',       cls: 'bg-gray-100 text-gray-600' },
};

const badgeClass = computed(() => config[props.status]?.cls ?? 'bg-gray-100 text-gray-600');
const label      = computed(() => config[props.status]?.label ?? props.status);
</script>
