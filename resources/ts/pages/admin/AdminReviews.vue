<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Quản lý Đánh giá</h1>
      <div class="text-sm text-gray-500">Tổng số: {{ pagination.total }} đánh giá</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-gray-600 text-sm uppercase font-semibold">
          <tr>
            <th class="px-6 py-4">Phòng / Khách hàng</th>
            <th class="px-6 py-4">Đánh giá</th>
            <th class="px-6 py-4">Nội dung</th>
            <th class="px-6 py-4">Trạng thái</th>
            <th class="px-6 py-4 text-right">Thao tác</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 italic font-['Inter']">
          <tr v-for="review in reviews" :key="review.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4">
              <div class="font-bold text-gray-900">{{ review.room?.title }}</div>
              <div class="text-xs text-gray-500">{{ review.user?.name }} ({{ review.user?.email }})</div>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-0.5">
                <Star v-for="i in 5" :key="i" class="w-4 h-4" :class="i <= review.rating ? 'fill-amber-400 text-amber-400' : 'text-gray-200'" />
              </div>
              <div class="text-[10px] text-gray-400 mt-1">{{ formatDate(review.created_at) }}</div>
            </td>
            <td class="px-6 py-4 max-w-xs">
              <p class="text-sm text-gray-700 truncate" :title="review.comment">
                {{ review.comment || 'Không có bình luận' }}
              </p>
            </td>
            <td class="px-6 py-4">
              <span 
                :class="review.is_hidden ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600'"
                class="px-2 py-1 rounded-full text-[10px] font-bold uppercase"
              >
                {{ review.is_hidden ? 'Đang ẩn' : 'Hiển thị' }}
              </span>
            </td>
            <td class="px-6 py-4 text-right space-x-2">
              <button 
                @click="toggleVisibility(review)"
                class="p-2 rounded-lg hover:bg-gray-200 text-gray-600 transition-colors"
                :title="review.is_hidden ? 'Hiện đánh giá' : 'Ẩn đánh giá'"
              >
                <Eye v-if="review.is_hidden" class="w-4 h-4" />
                <EyeOff v-else class="w-4 h-4" />
              </button>
              <button 
                @click="deleteReview(review.id)"
                class="p-2 rounded-lg hover:bg-red-100 text-red-500 transition-colors"
                title="Xóa vĩnh viễn"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </td>
          </tr>
          <tr v-if="reviews.length === 0">
            <td colspan="5" class="px-6 py-12 text-center text-gray-500">Chưa có đánh giá nào.</td>
          </tr>
        </tbody>
      </table>

      <!-- Phân trang -->
      <div v-if="pagination.last_page > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-center gap-2">
        <button 
          v-for="p in pagination.last_page" :key="p"
          @click="fetchReviews(p)"
          class="w-8 h-8 rounded flex items-center justify-center text-sm font-bold transition-colors"
          :class="p === pagination.current_page ? 'bg-[#4A7055] text-white' : 'bg-white text-gray-600 hover:bg-gray-100'"
        >
          {{ p }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Star, Trash2, Eye, EyeOff } from 'lucide-vue-next';

const reviews = ref<any[]>([]);
const pagination = ref({
  total: 0,
  current_page: 1,
  last_page: 1
});

const fetchReviews = async (page = 1) => {
  try {
    const token = localStorage.getItem('auth_token');
    const res = await fetch(`/api/admin/reviews?page=${page}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    const data = await res.json();
    reviews.value = data.data;
    pagination.value = {
      total: data.meta.total,
      current_page: data.meta.current_page,
      last_page: data.meta.last_page
    };
  } catch (e) {
    console.error('Lỗi tải đánh giá:', e);
  }
};

const toggleVisibility = async (review: any) => {
  try {
    const token = localStorage.getItem('auth_token');
    const res = await fetch(`/api/admin/reviews/${review.id}/toggle-visibility`, {
      method: 'PATCH',
      headers: { 'Authorization': `Bearer ${token}` }
    });
    if (res.ok) {
      review.is_hidden = !review.is_hidden;
    }
  } catch (e) {
    alert('Lỗi khi cập nhật trạng thái');
  }
};

const deleteReview = async (id: number) => {
  if (!confirm('Bạn có chắc chắn muốn xóa vĩnh viễn đánh giá này?')) return;
  try {
    const token = localStorage.getItem('auth_token');
    const res = await fetch(`/api/admin/reviews/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}` }
    });
    if (res.ok) {
      fetchReviews(pagination.value.current_page);
    }
  } catch (e) {
    alert('Lỗi khi xóa');
  }
};

const formatDate = (dateStr: string) => {
  return new Date(dateStr).toLocaleString('vi-VN');
};

onMounted(() => fetchReviews());
</script>
