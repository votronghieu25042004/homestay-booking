<template>
  <div class="space-y-6">
    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div v-for="card in kpiCards" :key="card.key"
        class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 cursor-pointer hover:shadow-md transition-shadow"
        :class="activeTab === card.tab ? 'ring-2 ring-emerald-500' : ''"
        @click="setTab(card.tab)">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ card.label }}</span>
          <span class="text-lg">{{ card.icon }}</span>
        </div>
        <div class="text-2xl font-bold" :class="card.color">{{ stats[card.key] ?? 0 }}</div>
        <div v-if="card.sub" class="text-xs text-gray-400 mt-1">{{ card.sub }}</div>
      </div>
    </div>

    <!-- Alert Banner -->
    <div v-if="stats.unpaid_count > 0 || stats.dirty_rooms > 0" class="flex flex-wrap gap-3">
      <div v-if="stats.unpaid_count > 0" class="flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg px-4 py-2 text-sm">
        💰 <strong>{{ stats.unpaid_count }}</strong> booking chưa thanh toán đủ
      </div>
      <div v-if="stats.dirty_rooms > 0" class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-2 text-sm">
        🧹 <strong>{{ stats.dirty_rooms }}</strong> phòng cần dọn
      </div>
    </div>

    <!-- Toolbar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
      <div class="flex flex-wrap items-center gap-3 mb-4">
        <!-- Quick Filter Tabs -->
        <div class="flex flex-wrap gap-1">
          <button v-for="t in tabs" :key="t.key"
            @click="setTab(t.key)"
            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
            :class="activeTab === t.key ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
            {{ t.label }}
            <span v-if="t.key === 'pending' && stats.pending" class="ml-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ stats.pending }}</span>
          </button>
        </div>
        <div class="flex-1"></div>
        <!-- View Toggle -->
        <div class="flex border border-gray-200 rounded-lg overflow-hidden">
          <button @click="viewMode='table'" class="px-3 py-1.5 text-sm" :class="viewMode==='table'?'bg-emerald-600 text-white':'text-gray-600 hover:bg-gray-50'">
            📋 Bảng
          </button>
          <button @click="viewMode='calendar'" class="px-3 py-1.5 text-sm" :class="viewMode==='calendar'?'bg-emerald-600 text-white':'text-gray-600 hover:bg-gray-50'">
            📅 Lịch
          </button>
        </div>
        <button @click="showCreateModal = true"
          class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium flex items-center gap-2 transition-colors">
          <Plus class="w-4 h-4" /> Tạo booking
        </button>
      </div>

      <!-- Advanced Filter Row -->
      <div class="flex flex-wrap gap-3">
        <input v-model="filters.booking_code" @input="debounceFetch" placeholder="Mã booking..." class="input-sm w-32" />
        <input v-model="filters.customer_search" @input="debounceFetch" placeholder="Tên / SĐT / Email..." class="input-sm w-44" />
        <select v-model="filters.status" @change="debounceFetch" class="input-sm w-36">
          <option value="">Trạng thái...</option>
          <option value="pending">Chờ xử lý</option>
          <option value="deposited">Đã cọc</option>
          <option value="checked_in">Đang ở</option>
          <option value="checked_out">Hoàn thành</option>
          <option value="cancelled">Đã hủy</option>
        </select>
        <select v-model="filters.date_type" @change="debounceFetch" class="input-sm w-32">
          <option value="">Theo ngày...</option>
          <option value="check_in">Check-in</option>
          <option value="check_out">Check-out</option>
          <option value="created">Ngày tạo</option>
        </select>
        <input v-if="filters.date_type" v-model="filters.date_from" type="date" @change="debounceFetch" class="input-sm" />
        <input v-if="filters.date_type" v-model="filters.date_to" type="date" @change="debounceFetch" class="input-sm" />
        <select v-model="filters.source" @change="debounceFetch" class="input-sm w-36">
          <option value="">Nguồn đặt...</option>
          <option value="website">Website</option>
          <option value="booking_com">Booking.com</option>
          <option value="agoda">Agoda</option>
          <option value="walkin">Walk-in</option>
          <option value="phone">Điện thoại</option>
        </select>
        <button @click="resetFilters" class="px-3 py-1.5 text-sm text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50">↺ Reset</button>
        <button @click="exportCSV" class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 flex items-center gap-1">
          📤 Export CSV
        </button>
      </div>
    </div>

    <!-- Table View -->
    <div v-if="viewMode === 'table'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div v-if="loading" class="flex items-center justify-center py-16 text-gray-400">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600 mr-3"></div>Đang tải...
      </div>
      <div v-else-if="bookings.length === 0" class="text-center py-16 text-gray-400">
        <CalendarDays class="w-12 h-12 mx-auto mb-3 opacity-30" />
        <p>Không có booking nào</p>
      </div>
      <table v-else class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
          <tr>
            <th class="th-cell">Mã Booking</th>
            <th class="th-cell">Khách hàng</th>
            <th class="th-cell">Phòng</th>
            <th class="th-cell">Check-in</th>
            <th class="th-cell">Check-out</th>
            <th class="th-cell">Đêm</th>
            <th class="th-cell">Tổng tiền</th>
            <th class="th-cell">Thanh toán</th>
            <th class="th-cell">Trạng thái</th>
            <th class="th-cell">Nguồn</th>
            <th class="th-cell">Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="b in bookings" :key="b.id"
            class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
            <td class="td-cell">
              <button @click="openDetail(b.id)" class="font-mono text-emerald-700 hover:text-emerald-900 font-medium hover:underline">
                {{ b.booking_code }}
              </button>
            </td>
            <td class="td-cell">
              <div class="font-medium text-gray-900">{{ b.customer?.name || b.customer_name || 'Khách vãng lai' }}</div>
              <div class="text-xs text-gray-400">{{ b.customer?.phone || b.customer_phone || 'Không có SĐT' }}</div>
            </td>
            <td class="td-cell">
              <div class="font-medium">{{ b.room?.room_number || b.room?.title || b.room_name || 'Không rõ' }}</div>
              <div class="text-xs text-gray-400">{{ b.room?.type || 'N/A' }}</div>
            </td>
            <td class="td-cell" :class="isToday(b.check_in_date)?'text-red-600 font-semibold':''">{{ fmtDate(b.check_in_date) }}</td>
            <td class="td-cell" :class="isToday(b.check_out_date)?'text-orange-500 font-semibold':''">{{ fmtDate(b.check_out_date) }}</td>
            <td class="td-cell text-center">{{ nightsCount(b.check_in_date, b.check_out_date) }}</td>
            <td class="td-cell font-medium">{{ fmtMoney(b.total_amount) }}</td>
            <td class="td-cell">
              <span :class="paymentBadge(b)">{{ paymentLabel(b) }}</span>
            </td>
            <td class="td-cell">
              <StatusBadge :status="b.status" />
            </td>
            <td class="td-cell">
              <span class="text-xs text-gray-500">{{ sourceLabel(b.source) }}</span>
            </td>
            <td class="td-cell">
              <div class="flex items-center gap-1">
                <button v-if="b.status === 'deposited'" @click="checkInBooking(b.id)" class="px-2 py-1 bg-emerald-600 text-white text-xs rounded hover:bg-emerald-700 font-medium transition-colors">
                  Nhận phòng
                </button>
                <button v-if="b.status === 'deposited'" @click="cancelBooking(b.id)" class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 font-medium transition-colors">
                  Hủy Booking
                </button>
                <button v-if="b.status === 'checked_in' && b.room?.type === 'room'" @click="openTransfer(b)" class="px-2 py-1 bg-orange-500 text-white text-xs rounded hover:bg-orange-600 font-medium transition-colors">
                  🔄 Đổi Phòng
                </button>
                <button @click="openDetail(b.id)" class="action-btn" title="Xem chi tiết">
                  <Eye class="w-3.5 h-3.5" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 bg-gray-50">
        <span class="text-sm text-gray-500">Tổng {{ pagination.total }} booking</span>
        <div class="flex items-center gap-2">
          <button :disabled="pagination.currentPage <= 1" @click="changePage(pagination.currentPage - 1)"
            class="px-3 py-1 rounded border text-sm disabled:opacity-40 hover:bg-white transition-colors">‹ Trước</button>
          <span class="text-sm text-gray-600">Trang {{ pagination.currentPage }} / {{ pagination.lastPage }}</span>
          <button :disabled="pagination.currentPage >= pagination.lastPage" @click="changePage(pagination.currentPage + 1)"
            class="px-3 py-1 rounded border text-sm disabled:opacity-40 hover:bg-white transition-colors">Sau ›</button>
        </div>
      </div>
    </div>

    <!-- Calendar View -->
    <div v-if="viewMode === 'calendar'" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <div class="flex items-center justify-between mb-4">
        <button @click="changeMonth(-1)" class="p-2 hover:bg-gray-100 rounded-lg"><ChevronLeft class="w-5 h-5"/></button>
        <h3 class="text-base font-semibold text-gray-800">{{ calendarTitle }}</h3>
        <button @click="changeMonth(1)" class="p-2 hover:bg-gray-100 rounded-lg"><ChevronRight class="w-5 h-5"/></button>
      </div>
      <div class="grid grid-cols-7 gap-1 mb-2">
        <div v-for="d in ['CN','T2','T3','T4','T5','T6','T7']" :key="d" class="text-center text-xs font-semibold text-gray-400 py-2">{{ d }}</div>
      </div>
      <div class="grid grid-cols-7 gap-1">
        <div v-for="(cell, i) in calendarCells" :key="i"
          class="aspect-square p-1 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors relative group"
          :class="cell.isToday ? 'ring-2 ring-emerald-400' : cell.date ? '' : 'opacity-0 pointer-events-none'"
          @click="cell.date && applyDateFilter(cell.date)">
          <div v-if="cell.date" class="text-xs text-gray-600 font-medium">{{ cell.day }}</div>
          <div v-if="cell.data && cell.data.bookings > 0">
            <div class="text-xs font-bold" :class="occupancyColor(cell.data.occupancy_rate)">
              {{ cell.data.bookings }}
            </div>
            <div class="h-1 rounded-full mt-0.5" :class="occupancyBg(cell.data.occupancy_rate)" :style="`width:${Math.min(100,cell.data.occupancy_rate)}%`"></div>
          </div>
          <div v-if="cell.data" class="hidden group-hover:block absolute bottom-full left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10 mb-1">
            {{ cell.data.bookings }} booking · {{ cell.data.occupancy_rate }}% occupancy
          </div>
        </div>
      </div>
      <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-200 inline-block"></span> Thấp</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-yellow-200 inline-block"></span> Trung bình</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-300 inline-block"></span> Cao</span>
      </div>
    </div>

    <!-- Modals -->
    <AdminBookingCreate v-if="showCreateModal" @close="showCreateModal=false" @created="onCreated" />
    <AdminBookingDetail v-if="detailBookingId" :booking-id="detailBookingId" @close="detailBookingId=null" @updated="fetchBookings" />
    <RoomTransferModal v-if="transferBooking" :booking="transferBooking" @close="transferBooking=null" @done="onTransferDone" />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { Plus, Eye, CalendarDays, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import StatusBadge from './StatusBadge.vue';
import StatusChangeBtn from './StatusChangeBtn.vue';
import AdminBookingCreate from './AdminBookingCreate.vue';
import AdminBookingDetail from './AdminBookingDetail.vue';
import RoomTransferModal from './RoomTransferModal.vue';

const API = '/api/admin';
const token = () => localStorage.getItem('auth_token') || '';

// ─── State ───────────────────────────────────────
const loading      = ref(false);
const bookings     = ref<any[]>([]);
const stats        = ref<Record<string,number>>({});
const activeTab    = ref('');
const viewMode     = ref('table');
const showCreateModal = ref(false);
const detailBookingId = ref<number|null>(null);
const transferBooking  = ref<any>(null);
const calendarData    = ref<Record<string,any>>({});
const calendarYear    = ref(new Date().getFullYear());
const calendarMonth   = ref(new Date().getMonth() + 1);

const pagination = reactive({ currentPage: 1, lastPage: 1, total: 0 });
const filters    = reactive({ booking_code:'', customer_search:'', status:'', date_type:'', date_from:'', date_to:'', source:'' });

let debounceTimer: ReturnType<typeof setTimeout>;
const debounceFetch = () => { clearTimeout(debounceTimer); debounceTimer = setTimeout(() => { pagination.currentPage = 1; fetchBookings(); }, 400); };

// ─── Tabs ─────────────────────────────────────────
const tabs = [
  { key:'', label:'Tất cả' },
  { key:'checkin_today', label:'Hôm nay CI' },
  { key:'checkout_today', label:'Hôm nay CO' },
  { key:'inhouse', label:'Đang ở' },
  { key:'upcoming', label:'Sắp tới' },
  { key:'pending', label:'Chờ xác nhận' },
  { key:'cancelled', label:'Đã hủy' },
];
const kpiCards = [
  { key:'checkin_today', label:'Check-in hôm nay', icon:'🛎', color:'text-blue-600', tab:'checkin_today' },
  { key:'checkout_today', label:'Check-out hôm nay', icon:'🚪', color:'text-orange-500', tab:'checkout_today' },
  { key:'inhouse', label:'Đang ở', icon:'🏠', color:'text-emerald-600', tab:'inhouse', sub:'phòng' },
  { key:'pending', label:'Chờ xác nhận', icon:'⏳', color:'text-red-500', tab:'pending' },
];

function setTab(tab: string) {
  activeTab.value = tab;
  pagination.currentPage = 1;
  fetchBookings();
}

// ─── Fetch ────────────────────────────────────────
async function fetchBookings() {
  loading.value = true;
  const params = new URLSearchParams();
  if (activeTab.value) params.append('tab', activeTab.value);
  if (filters.booking_code)  params.append('booking_code', filters.booking_code);
  if (filters.customer_search) params.append('customer_search', filters.customer_search);
  if (filters.status)     params.append('status', filters.status);
  if (filters.date_type)  params.append('date_type', filters.date_type);
  if (filters.date_from)  params.append('date_from', filters.date_from);
  if (filters.date_to)    params.append('date_to', filters.date_to);
  if (filters.source)     params.append('source', filters.source);
  params.append('page', String(pagination.currentPage));

  const res = await fetch(`${API}/bookings?${params}`, { headers: { Authorization: `Bearer ${token()}` } });
  if (res.ok) {
    const data = await res.json();
    bookings.value = data.data.map((b: any) => {
      if (b.room && b.room.data) b.room = b.room.data;
      if (b.customer && b.customer.data) b.customer = b.customer.data;
      return b;
    });
    pagination.currentPage = data.meta.current_page;
    pagination.lastPage    = data.meta.last_page;
    pagination.total       = data.meta.total;
  }
  loading.value = false;
}

async function fetchStats() {
  const res = await fetch(`${API}/bookings/stats`, { headers: { Authorization: `Bearer ${token()}` } });
  if (res.ok) stats.value = await res.json();
}

async function fetchCalendar() {
  const res = await fetch(`${API}/bookings/calendar?year=${calendarYear.value}&month=${calendarMonth.value}`, { headers: { Authorization: `Bearer ${token()}` } });
  if (res.ok) calendarData.value = await res.json();
}

// ─── Calendar ────────────────────────────────────
const calendarTitle = computed(() => {
  const d = new Date(calendarYear.value, calendarMonth.value - 1, 1);
  return d.toLocaleDateString('vi-VN', { month:'long', year:'numeric' });
});

const calendarCells = computed(() => {
  const cells: any[] = [];
  const firstDay = new Date(calendarYear.value, calendarMonth.value - 1, 1).getDay();
  const daysInMonth = new Date(calendarYear.value, calendarMonth.value, 0).getDate();
  const today = new Date().toISOString().slice(0, 10);

  for (let i = 0; i < firstDay; i++) cells.push({ date: null });
  for (let d = 1; d <= daysInMonth; d++) {
    const ds = `${calendarYear.value}-${String(calendarMonth.value).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    cells.push({ date: ds, day: d, isToday: ds === today, data: calendarData.value[ds] });
  }
  return cells;
});

function changeMonth(delta: number) {
  calendarMonth.value += delta;
  if (calendarMonth.value > 12) { calendarMonth.value = 1; calendarYear.value++; }
  if (calendarMonth.value < 1)  { calendarMonth.value = 12; calendarYear.value--; }
  fetchCalendar();
}
function applyDateFilter(date: string) {
  viewMode.value = 'table';
  filters.date_type = 'check_in';
  filters.date_from = date;
  filters.date_to   = date;
  fetchBookings();
}

// ─── Helpers ─────────────────────────────────────
function fmtDate(d: string) {
  if (!d) return '—';
  const cleanDate = d.slice(0, 10);
  const [y, m, day] = cleanDate.split('-');
  return `${day}/${m}/${y}`;
}
function fmtMoney(n: number) {
  return new Intl.NumberFormat('vi-VN').format(n ?? 0) + 'đ';
}
function isToday(d: string) {
  if (!d) return false;
  return d?.slice(0,10) === new Date().toISOString().slice(0,10);
}
function nightsCount(ci: string, co: string) {
  if (!ci || !co) return 0;
  const [cy,cm,cd] = ci.slice(0,10).split('-').map(Number);
  const [oy,om,od] = co.slice(0,10).split('-').map(Number);
  return Math.round((new Date(oy,om-1,od).getTime() - new Date(cy,cm-1,cd).getTime()) / 86400000);
}
function paymentBadge(b: any) {
  const rem = (b.total_amount ?? 0) - (b.paid_amount ?? 0);
  if (rem <= 0) return 'badge-green';
  if ((b.paid_amount ?? 0) > 0) return 'badge-yellow';
  return 'badge-red';
}
function paymentLabel(b: any) {
  const rem = (b.total_amount ?? 0) - (b.paid_amount ?? 0);
  if (rem <= 0) return 'Đã đủ';
  if ((b.paid_amount ?? 0) > 0) return 'Còn thiếu';
  return 'Chưa cọc';
}
function sourceLabel(src: string) {
  const m: Record<string,string> = { website:'Web', booking_com:'Booking.com', agoda:'Agoda', walkin:'Walk-in', phone:'Điện thoại', other:'Khác' };
  return m[src] ?? src;
}
function occupancyColor(rate: number) { return rate >= 80 ? 'text-red-500' : rate >= 50 ? 'text-yellow-500' : 'text-green-500'; }
function occupancyBg(rate: number)    { return rate >= 80 ? 'bg-red-400' : rate >= 50 ? 'bg-yellow-400' : 'bg-green-400'; }

function resetFilters() {
  Object.assign(filters, { booking_code:'', customer_search:'', status:'', date_type:'', date_from:'', date_to:'', source:'' });
  fetchBookings();
}

function exportCSV() {
  const headers = ['Mã booking','Khách','SĐT','Phòng','Check-in','Check-out','Tổng tiền','Trạng thái'];
  const rows = bookings.value.map(b => [
    b.booking_code, b.customer?.name, b.customer?.phone,
    b.room?.room_number || b.room?.title,
    b.check_in_date, b.check_out_date, b.total_amount, b.status
  ]);
  const csv = [headers, ...rows].map(r => r.join(',')).join('\n');
  const a = document.createElement('a'); a.href = 'data:text/csv;charset=utf-8,\uFEFF' + encodeURIComponent(csv);
  a.download = `bookings_${new Date().toISOString().slice(0,10)}.csv`; a.click();
}

async function checkInBooking(id: number) {
  if (!confirm('Bạn có chắc chắn muốn xác nhận Check-in cho phòng này?')) return;
  loading.value = true;
  const res = await fetch(`${API}/bookings/${id}/checkin`, {
    method: 'POST',
    headers: { Authorization: `Bearer ${token()}`, 'Content-Type': 'application/json' }
  });
  loading.value = false;
  if (res.ok) {
    alert('Check-in thành công!');
    fetchBookings();
    fetchStats();
  } else {
    const d = await res.json();
    alert(d.message || 'Lỗi khi check-in');
  }
}

async function cancelBooking(id: number) {
  if (!confirm('Bạn có chắc chắn muốn hủy booking này? Tiền cọc sẽ được tính làm phí phạt.')) return;
  loading.value = true;
  const res = await fetch(`${API}/bookings/${id}/cancel`, {
    method: 'POST',
    headers: { Authorization: `Bearer ${token()}`, 'Content-Type': 'application/json' },
    body: JSON.stringify({ cancel_reason: 'Khách bùng cọc/báo hủy' })
  });
  loading.value = false;
  if (res.ok) {
    alert('Hủy booking thành công. Phòng đã sẵn sàng đón khách mới.');
    fetchBookings();
    fetchStats();
  } else {
    const d = await res.json();
    alert(d.message || 'Lỗi khi hủy booking');
  }
}

function changePage(page: number) { pagination.currentPage = page; fetchBookings(); }
function openDetail(id: number) { detailBookingId.value = id; }
function openTransfer(booking: any) { transferBooking.value = booking; }
function onTransferDone() { fetchBookings(); fetchStats(); }
function onCreated() { showCreateModal.value = false; fetchBookings(); fetchStats(); }

watch(viewMode, (v) => { if (v === 'calendar') fetchCalendar(); });

onMounted(() => { fetchBookings(); fetchStats(); });
</script>

<style scoped>
.input-sm {
  border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.375rem 0.75rem;
  font-size: 0.875rem; color: #374151; background: white;
}
.input-sm:focus { outline: none; box-shadow: 0 0 0 2px #10b981; }
.th-cell  { padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
.td-cell  { padding: 0.75rem 1rem; color: #374151; }
.action-btn { padding: 0.375rem; border-radius: 0.375rem; color: #9ca3af; transition: all 0.15s; }
.action-btn:hover { color: #047857; background: #ecfdf5; }
.badge-green  { display: inline-flex; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: #dcfce7; color: #15803d; }
.badge-yellow { display: inline-flex; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: #fef9c3; color: #a16207; }
.badge-red    { display: inline-flex; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: #fee2e2; color: #dc2626; }
</style>
