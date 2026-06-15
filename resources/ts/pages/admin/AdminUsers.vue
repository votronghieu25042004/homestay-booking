<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Quản lý người dùng</h1>
      <button @click="openModal()" class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 font-medium transition-colors">
        + Thêm người dùng
      </button>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex gap-4">
      <input 
        v-model="searchQuery" 
        @keyup.enter="fetchUsers"
        type="text" 
        placeholder="Tìm theo Tên, Email hoặc Số điện thoại..." 
        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
      />
      <button @click="fetchUsers" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 transition-colors">
        Tìm kiếm
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 text-sm uppercase">
            <th class="p-4 font-semibold">ID</th>
            <th class="p-4 font-semibold">Họ Tên</th>
            <th class="p-4 font-semibold">Liên hệ</th>
            <th class="p-4 font-semibold">Chức vụ</th>
            <th class="p-4 font-semibold">Trạng thái</th>
            <th class="p-4 font-semibold text-center">Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id" class="border-b border-gray-50 hover:bg-gray-50">
            <td class="p-4 text-gray-500">#{{ user.id }}</td>
            <td class="p-4 font-medium text-gray-900">{{ user.name }}</td>
            <td class="p-4">
              <div class="text-sm text-gray-900">{{ user.email }}</div>
              <div class="text-sm text-gray-500">{{ user.phone || 'Chưa cập nhật' }}</div>
            </td>
            <td class="p-4">
              <span :class="user.role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'" class="px-3 py-1 text-xs rounded-full font-medium uppercase">
                {{ user.role }}
              </span>
            </td>
            <td class="p-4">
              <span :class="user.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-3 py-1 text-xs rounded-full font-medium">
                {{ user.status === 'active' ? 'Hoạt động' : 'Bị Khóa' }}
              </span>
            </td>
            <td class="p-4 flex justify-center gap-2">
              <button v-if="user.role !== 'admin'" @click="toggleStatus(user.id)" :class="user.status === 'active' ? 'bg-orange-100 text-orange-600 hover:bg-orange-200' : 'bg-green-100 text-green-600 hover:bg-green-200'" class="px-3 py-1 rounded-lg text-sm font-medium transition-colors">
                {{ user.status === 'active' ? 'Khóa' : 'Mở Khóa' }}
              </button>
              
              <button @click="openModal(user)" class="bg-gray-100 text-gray-600 px-3 py-1 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">Sửa</button>
              
              <button v-if="user.role !== 'admin'" @click="deleteUser(user.id)" class="bg-red-50 text-red-600 px-3 py-1 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">Xóa</button>
            </td>
          </tr>
          <tr v-if="users.length === 0">
            <td colspan="6" class="p-8 text-center text-gray-500">Không tìm thấy người dùng nào.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl w-full max-w-md p-6">
        <h2 class="text-xl font-bold mb-4">{{ isEditing ? 'Cập nhật tài khoản' : 'Thêm tài khoản mới' }}</h2>
        <form @submit.prevent="saveUser" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Họ Tên</label>
            <input v-model="form.name" type="text" required class="w-full px-3 py-2 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input v-model="form.email" type="email" required class="w-full px-3 py-2 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
            <input v-model="form.phone" type="tel" required class="w-full px-3 py-2 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Chức vụ</label>
            <select v-model="form.role" class="w-full px-3 py-2 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
              <option value="customer">Khách hàng (Customer)</option>
              <option value="admin">Quản trị viên (Admin)</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Mật khẩu <span v-if="isEditing" class="text-gray-400 font-normal">(Bỏ trống nếu không đổi)</span>
            </label>
            <input v-model="form.password" type="password" :required="!isEditing" minlength="6" class="w-full px-3 py-2 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500" />
          </div>
          <div class="flex justify-end gap-2 pt-4">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Hủy</button>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Lưu lại</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';

const users = ref<any[]>([]);
const searchQuery = ref('');
const showModal = ref(false);
const isEditing = ref(false);
const editId = ref<number | null>(null);

const form = ref({
  name: '',
  email: '',
  phone: '',
  role: 'customer',
  password: ''
});

// 1. Tải danh sách
const fetchUsers = async () => {
  try {
    const res = await fetch(`/api/admin/users?search=${searchQuery.value}`);
    users.value = await res.json();
  } catch (error) {
    console.error('Lỗi tải dữ liệu', error);
  }
};

onMounted(() => fetchUsers());

// 2. Mở Modal
const openModal = (user = null) => {
  if (user) {
    isEditing.value = true;
    editId.value = user.id;
    form.value = { name: user.name, email: user.email, phone: user.phone, role: user.role, password: '' };
  } else {
    isEditing.value = false;
    form.value = { name: '', email: '', phone: '', role: 'customer', password: '' };
  }
  showModal.value = true;
};

// 3. Thêm / cập nhập 
const saveUser = async () => {
  const url = isEditing.value ? `/api/admin/users/${editId.value}` : '/api/admin/users';
  const method = isEditing.value ? 'PUT' : 'POST';

  try {
    const res = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(form.value)
    });
    const data = await res.json();
    
    if (res.ok) {
      alert(data.message);
      showModal.value = false;
      fetchUsers();
    } else {
      alert('Lỗi: Kiểm tra lại thông tin (Email/SĐT có thể bị trùng)');
    }
  } catch (error) {
    alert('Lỗi kết nối máy chủ!');
  }
};

// 4. Khóa / Mở khóa
const toggleStatus = async (id: number) => {
  if (!confirm('Bạn có chắc muốn thay đổi trạng thái tài khoản này?')) return;
  try {
    const res = await fetch(`/api/admin/users/${id}/status`, { method: 'PUT' });
    if (res.ok) fetchUsers();
  } catch (error) {
    alert('Lỗi thao tác!');
  }
};

// 5. Xóa
const deleteUser = async (id: number) => {
  if (!confirm('Xóa vĩnh viễn tài khoản này? Hành động không thể hoàn tác!')) return;
  try {
    const res = await fetch(`/api/admin/users/${id}`, { method: 'DELETE' });
    if (res.ok) fetchUsers();
  } catch (error) {
    alert('Lỗi thao tác!');
  }
};
</script>