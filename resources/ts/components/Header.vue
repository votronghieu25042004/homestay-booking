<template>
  <header class="bg-[#FAF9F5] shadow-sm sticky top-0 z-50 border-b border-[#4A7055]/30">
    <div class="max-w-[1440px] mx-auto px-10 h-20 flex items-center justify-between">
      
      <div class="flex items-center">
        <router-link to="/">
          <img src="/images/logohomebooking2.png" alt="Homestay Logo" class="h-55 w-auto object-contain" />
        </router-link>
      </div>

      <div v-if="!user" class="hidden md:flex items-center space-x-4">
        <router-link to="/register" class="bg-white text-[#4A7055] border border-[#4A7055] px-6 py-2.5 rounded-md font-medium text-sm hover:bg-gray-50 transition-colors shadow-sm">
          Đăng kí
        </router-link>
        
        <router-link to="/login" class="bg-[#4A7055] text-white px-6 py-2.5 rounded-md font-medium text-sm hover:bg-[#3b5a44] transition-colors shadow-sm">
          Đăng nhập
        </router-link>
      </div>

      <div v-else class="hidden md:flex items-center space-x-6 relative">
        <router-link to="/rewards" class="text-gray-700 hover:text-[#4A7055] font-medium transition-colors">
          Ưu đãi
        </router-link>
        
        <div @click="handleAvatarClick" class="flex items-center gap-3 cursor-pointer hover:opacity-80 transition-opacity">
          <div class="text-right">
            <div class="font-bold text-gray-900 text-sm">{{ user.name }}</div>
            <div class="text-xs text-gray-500">
              <span v-if="isAdmin" class="text-[#4A7055] font-semibold">Quản trị viên</span>
              <span v-else>Đà Nẵng, Việt Nam</span>
            </div>
          </div>
          <img src="https://i.pravatar.cc/150?img=11" alt="Avatar" class="w-10 h-10 rounded-full border border-[#4A7055] object-cover" />
        </div>
        
        <div class="flex items-center gap-4">
          <div class="relative">
            <button @click="showNotifications = !showNotifications" class="relative text-[#4A7055] hover:text-[#3b5a44] transition-colors flex items-center justify-center mt-1">
              <Bell class="w-6 h-6 fill-current" />
              <span v-if="hasUnreadNotifications" class="absolute -top-0.5 -right-0.5 flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
              </span>
            </button>

            <div v-if="showNotifications" class="absolute right-0 mt-6 w-[450px] bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden z-50 animate-in fade-in slide-in-from-top-2 duration-200">
              <div class="px-6 py-5 border-b border-gray-100 bg-white flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Thông báo</h3>
                <button v-if="displayNotifications.some(n => !n.read_at)" @click="markAsRead" class="text-xs text-[#4A7055] hover:underline font-medium">Đánh dấu tất cả là đã đọc</button>
              </div>
              <div class="max-h-[400px] overflow-y-auto">
                <div v-if="displayNotifications.length === 0" class="p-10 text-center">
                  <p class="text-gray-400 text-sm">Bạn không có thông báo mới.</p>
                </div>
                <div v-else v-for="notif in displayNotifications" :key="notif.id" @click="handleNotificationClick(notif)" :class="['p-5 flex gap-4 transition-colors cursor-pointer border-b border-gray-50 last:border-0', !notif.read_at ? (isAdmin && notif.data.action_url?.startsWith('/admin') ? 'bg-emerald-500/5 hover:bg-emerald-500/10' : 'bg-[#4A7055]/5 hover:bg-[#4A7055]/10') : 'hover:bg-gray-50']">
                  <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0" :class="isAdmin && notif.data.action_url?.startsWith('/admin') ? 'bg-emerald-50' : 'bg-gray-50'">
                    <component :is="getIcon(notif)" :class="['w-5 h-5', getIconColor(notif)]" />
                  </div>
                  <div class="flex-1">
                    <p class="text-sm font-bold text-gray-900 mb-0.5 flex items-center gap-2">
                       {{ notif.data.title || 'Thông báo mới' }}
                    </p>
                    <p class="text-xs text-gray-600 leading-relaxed">{{ notif.data.message }}</p>
                    <p class="text-[10px] text-gray-400 mt-2 font-medium">{{ new Date(notif.created_at).toLocaleString('vi-VN') }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <button @click="handleLogout" class="text-gray-400 hover:text-red-500 transition-colors ml-2" title="Đăng xuất">
          <LogOut class="w-5 h-5" />
        </button>
      </div>

      <div class="md:hidden flex items-center">
        <button class="text-gray-600 hover:text-[#4A7055]">
          <Menu class="w-7 h-7" />
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { Bell, LogOut, Menu, Info, CheckCircle, XCircle, Clock, Star } from 'lucide-vue-next';

const router = useRouter();

const user = ref<any>(null);
const isAdmin = ref(false);
const notifications = ref<any[]>([]);

const showNotifications = ref(false);

const displayNotifications = computed(() => {
  if (isAdmin.value) {
    // Admin thấy tất cả, sắp xếp thông báo admin lên đầu
    return [...notifications.value].sort((a, b) => {
      const aIsAdmin = a.data.action_url?.startsWith('/admin') ? 1 : 0;
      const bIsAdmin = b.data.action_url?.startsWith('/admin') ? 1 : 0;
      if (aIsAdmin !== bIsAdmin) return bIsAdmin - aIsAdmin;
      return new Date(b.created_at).getTime() - new Date(a.created_at).getTime();
    });
  }
  // User chỉ thấy thông báo không thuộc admin
  return notifications.value.filter(n => !n.data.action_url || !n.data.action_url.startsWith('/admin'));
});

const hasUnreadNotifications = computed(() => {
  return displayNotifications.value.some(n => !n.read_at);
});

const fetchNotifications = async () => {
  if (!user.value) return;
  try {
    const response = await axios.get('/api/notifications', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      }
    });
    notifications.value = response.data;
  } catch (err) {
    console.error('Failed to fetch notifications:', err);
  }
};

const markAsRead = async () => {
  try {
    await axios.post('/api/notifications/mark-as-read', {}, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      }
    });
    await fetchNotifications();
  } catch (err) {
    console.error('Failed to mark as read:', err);
  }
};

const getIcon = (notif: any) => {
  const { status, type } = notif.data;
  const isAdminNotif = notif.data.action_url?.startsWith('/admin');
  
  if (isAdminNotif) return Info;
  if (type === 'review_request') return Star;
  
  switch (status) {
    case 'confirmed': return CheckCircle;
    case 'cancelled': return XCircle;
    case 'checked_in': return Clock;
    case 'checked_out': return CheckCircle;
    default: return Info;
  }
};

const getIconColor = (notif: any) => {
  const { status, type } = notif.data;
  const isAdminNotif = notif.data.action_url?.startsWith('/admin');
  
  if (isAdminNotif) return 'text-emerald-600';
  if (type === 'review_request') return 'text-amber-500';
  
  switch (status) {
    case 'confirmed': return 'text-green-500';
    case 'cancelled': return 'text-red-500';
    case 'checked_in': return 'text-blue-500';
    case 'checked_out': return 'text-gray-500';
    default: return 'text-[#4A7055]';
  }
};

const handleNotificationClick = async (notif: any) => {
  if (!notif.read_at) {
    try {
      await axios.post(`/api/notifications/${notif.id}/mark-as-read`, {}, {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
      });
      await fetchNotifications();
    } catch (err) {
      console.error('Lỗi khi đánh dấu đã đọc:', err);
    }
  }

  if (notif.data.action_url) {
    router.push(notif.data.action_url);
  } else {
    router.push('/profile');
  }
  showNotifications.value = false;
};

onMounted(() => {
  try {
    const userInfoString = localStorage.getItem('user_info');
    if (userInfoString) {
      user.value = JSON.parse(userInfoString);
      if (user.value && user.value.role === 'admin') {
        isAdmin.value = true;
      }
      fetchNotifications();
      setInterval(fetchNotifications, 60000);
    }
  } catch (error) {
    console.error("Lỗi đọc dữ liệu user:", error);
  }
});

watch(showNotifications, (val) => {
  if (val) fetchNotifications();
});

const handleAvatarClick = () => {
  if (isAdmin.value) {
    router.push('/admin/rooms'); 
  } else {
    router.push('/profile');
  }
};

const handleLogout = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    if (token) {
      await fetch('/api/logout', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });
    }
  } catch (error) {
    console.error('Lỗi link logout:', error);
  } finally {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_info');
    user.value = null;
    isAdmin.value = false; 
    showNotifications.value = false;
    alert('Đăng xuất thành công!');
    router.push('/');
    setTimeout(() => {
        window.location.reload();
    }, 100);
  }
};
</script>