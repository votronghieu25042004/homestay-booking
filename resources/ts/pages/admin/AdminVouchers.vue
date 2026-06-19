<template>
  <div class="px-6 py-8 w-full max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 font-['Playfair_Display']">Quản lý Mã Giảm Giá</h1>
        <p class="text-gray-500 mt-2">Theo dõi và cấu hình các voucher ưu đãi</p>
      </div>
      <button 
        @click="openCreateModal"
        class="bg-[#4A7055] hover:bg-[#3b5a44] text-white px-5 py-2.5 rounded-xl font-medium transition-all shadow-sm flex items-center gap-2"
      >
        <Plus class="w-5 h-5" />
        Tạo Voucher
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center items-center h-64 bg-white rounded-2xl border border-gray-100 shadow-sm">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#4A7055]"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!vouchers || vouchers.length === 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
      <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6">
        <Ticket class="w-10 h-10 text-[#4A7055]" />
      </div>
      <h3 class="text-xl font-bold text-gray-900 mb-2">Chưa có mã giảm giá nào</h3>
      <p class="text-gray-500 mb-6">Hãy tạo mã giảm giá đầu tiên để thu hút khách hàng.</p>
      <button @click="openCreateModal" class="bg-[#4A7055] hover:bg-[#3b5a44] text-white px-5 py-2.5 rounded-xl font-medium transition-colors">
        Tạo Voucher Mới
      </button>
    </div>

    <!-- Data Table -->
    <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-gray-50/50 border-b border-gray-100">
            <tr>
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mã Code</th>
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Loại & Giá trị</th>
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Lượt dùng</th>
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Hạn dùng</th>
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Trạng thái</th>
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="item in vouchers" :key="item.id" class="hover:bg-gray-50/50 transition-colors">
              <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-gray-100 text-gray-800 font-bold border border-gray-200 uppercase tracking-wider">
                  {{ item.code }}
                </span>
              </td>
              <td class="px-6 py-4">
                <p class="font-medium text-gray-900">
                  {{ formatDiscount(item) }}
                </p>
                <p class="text-xs text-gray-500 mt-1">{{ item.discount_type === 'percent' ? 'Giảm theo phần trăm' : 'Giảm cố định' }}</p>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <span class="text-gray-900 font-medium">{{ item.used_count }}</span>
                  <span class="text-gray-400">/</span>
                  <span class="text-gray-500">{{ item.max_uses ? item.max_uses : '∞' }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <span v-if="item.expires_at" :class="isExpired(item.expires_at) ? 'text-red-500' : 'text-gray-600'">
                  {{ formatDate(item.expires_at) }}
                </span>
                <span v-else class="text-gray-400">Không thời hạn</span>
              </td>
              <td class="px-6 py-4">
                <button 
                  @click="toggleStatus(item)"
                  class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                  :class="item.is_active ? 'bg-[#4A7055]' : 'bg-gray-200'"
                >
                  <span class="sr-only">Toggle active</span>
                  <span 
                    aria-hidden="true" 
                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                    :class="item.is_active ? 'translate-x-5' : 'translate-x-0'"
                  ></span>
                </button>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-3">
                  <button @click="openEditModal(item)" class="text-blue-600 hover:text-blue-800 p-1 rounded-md hover:bg-blue-50 transition-colors">
                    <Edit class="w-4 h-4" />
                  </button>
                  <button @click="deleteVoucher(item.id)" class="text-red-600 hover:text-red-800 p-1 rounded-md hover:bg-red-50 transition-colors">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block align-bottom bg-white text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-full max-w-lg rounded-2xl">
          <form @submit.prevent="saveVoucher">
            <div class="bg-white px-6 pt-6 pb-4">
              <h3 class="text-xl leading-6 font-bold text-gray-900 mb-6" id="modal-title">
                {{ isEditing ? 'Chỉnh sửa Voucher' : 'Thêm Voucher mới' }}
              </h3>
              
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Mã Code (Chữ in hoa)</label>
                  <input v-model="form.code" type="text" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-[#4A7055] uppercase outline-none" placeholder="VD: SUMMERCODE2026">
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại giảm giá</label>
                    <select v-model="form.discount_type" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-[#4A7055] outline-none bg-white">
                      <option value="percent">Giảm phần trăm (%)</option>
                      <option value="fixed">Giảm số tiền cố định</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá trị giảm</label>
                    <input v-model.number="form.discount_value" type="number" required min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-[#4A7055] outline-none">
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lượt dùng tối đa</label>
                    <input v-model.number="form.max_uses" type="number" min="1" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-[#4A7055] outline-none" placeholder="Vô hạn">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc</label>
                    <input v-model="form.expires_at" type="date" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-[#4A7055] outline-none">
                  </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                  <input v-model="form.is_active" type="checkbox" id="active-cb" class="w-4 h-4 text-[#4A7055] rounded focus:ring-[#4A7055] border-gray-300 cursor-pointer">
                  <label for="active-cb" class="text-sm font-medium text-gray-700 cursor-pointer">Kích hoạt voucher ngay lập tức</label>
                </div>
              </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-100">
              <button @click="closeModal" type="button" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 font-medium transition-colors">
                Hủy
              </button>
              <button type="submit" :disabled="isSaving" class="px-5 py-2 bg-[#4A7055] hover:bg-[#3b5a44] text-white rounded-xl font-medium flex items-center disabled:opacity-70">
                <div v-if="isSaving" class="animate-spin border-2 border-white border-t-transparent w-4 h-4 rounded-full mr-2"></div>
                Lưu Voucher
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Ticket, Plus, Trash2, Edit } from 'lucide-vue-next';

interface Voucher {
  id: number;
  code: string;
  discount_type: 'percent' | 'fixed';
  discount_value: number;
  expires_at: string | null;
  max_uses: number | null;
  used_count: number;
  is_active: boolean;
}

const vouchers = ref<Voucher[]>([]);
const isLoading = ref(true);

const isModalOpen = ref(false);
const isEditing = ref(false);
const isSaving = ref(false);

const defaultForm = {
  id: null,
  code: '',
  discount_type: 'percent' as const,
  discount_value: '',
  expires_at: null,
  max_uses: null,
  is_active: true
};

const form = ref<{
  id: number | null;
  code: string;
  discount_type: 'percent' | 'fixed';
  discount_value: number | string;
  expires_at: string | null;
  max_uses: number | string | null;
  is_active: boolean;
}>({ ...defaultForm });

const getAuthHeaders = () => {
  const token = localStorage.getItem('auth_token');
  return {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  };
};

const fetchVouchers = async () => {
  isLoading.value = true;
  try {
    const res = await fetch('/api/admin/vouchers', { headers: getAuthHeaders() });
    if (res.ok) {
      const data = await res.json();
      vouchers.value = data.data || []; // laravel pagination
    }
  } catch (error) {
    console.error('Lỗi lấy vouchers:', error);
  } finally {
    isLoading.value = false;
  }
};

const formatDiscount = (item: Voucher) => {
  if (item.discount_type === 'percent') {
    return `${parseFloat(item.discount_value.toString())}%`;
  }
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.discount_value);
};

const formatDate = (dateStr: string) => {
  const d = new Date(dateStr);
  return d.toLocaleDateString('vi-VN');
};

const isExpired = (dateStr: string) => {
  return new Date(dateStr) < new Date();
};

const openCreateModal = () => {
  isEditing.value = false;
  form.value = { ...defaultForm };
  isModalOpen.value = true;
};

const openEditModal = (item: Voucher) => {
  isEditing.value = true;
  form.value = {
    id: item.id,
    code: item.code,
    discount_type: item.discount_type,
    discount_value: parseFloat(item.discount_value.toString()),
    expires_at: item.expires_at ? item.expires_at.split(' ')[0] : null,
    max_uses: item.max_uses,
    is_active: item.is_active
  };
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

const saveVoucher = async () => {
  isSaving.value = true;
  const payload = {
    ...form.value,
    code: form.value.code.toUpperCase().trim(),
    expires_at: form.value.expires_at || null,
    max_uses: form.value.max_uses ? parseInt(form.value.max_uses.toString()) : null
  };

  try {
    const url = isEditing.value ? `/api/admin/vouchers/${form.value.id}` : '/api/admin/vouchers';
    const method = isEditing.value ? 'PUT' : 'POST';

    const res = await fetch(url, {
      method,
      headers: getAuthHeaders(),
      body: JSON.stringify(payload)
    });

    if (res.ok) {
      closeModal();
      fetchVouchers();
    } else {
      const err = await res.json();
      alert('Có lỗi xảy ra: ' + (err.message || 'Kiểm tra phần nhập'));
    }
  } catch (error) {
    console.error('Lưu voucher lỗi:', error);
  } finally {
    isSaving.value = false;
  }
};

const deleteVoucher = async (id: number) => {
  if (!confirm('Bạn có chắc chắn muốn xóa mã giảm giá này? Hành động này không thể hoàn tác.')) return;
  try {
    const res = await fetch(`/api/admin/vouchers/${id}`, {
      method: 'DELETE',
      headers: getAuthHeaders()
    });
    if (res.ok) fetchVouchers();
  } catch (error) {
    console.error('Xóa voucher lỗi:', error);
  }
};

const toggleStatus = async (item: Voucher) => {
  const prevStatus = item.is_active;
  item.is_active = !prevStatus;
  try {
    const res = await fetch(`/api/admin/vouchers/${item.id}/toggle-status`, {
      method: 'PATCH',
      headers: getAuthHeaders()
    });
    if (!res.ok) item.is_active = prevStatus;
  } catch (error) {
    item.is_active = prevStatus;
    console.error('Toggle status lỗi:', error);
  }
};

onMounted(() => fetchVouchers());
</script>
