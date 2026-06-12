<template>
  <div class="p-8 bg-gray-50 min-h-screen">
    <h1 class="text-3xl font-bold text-gray-900 mb-8 font-['Playfair_Display']">Báo Cáo Thống Kê</h1>

    <div v-if="isLoading" class="text-center py-10">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#4A7055] mx-auto"></div>
    </div>

    <div v-else>
      <div class="mb-8 flex items-center gap-4">
        <label class="font-bold text-gray-700">Chọn năm:</label>
        <select v-model="selectedYear" @change="fetchStatistics" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-[#4A7055] outline-none">
           <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
        </select>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-blue-500">
          <p class="text-gray-500 font-medium mb-1">Tổng Booking</p>
          <h3 class="text-3xl font-bold text-gray-900">{{ overview.total_bookings }} <span class="text-sm font-normal text-gray-500">đơn</span></h3>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-green-500">
          <p class="text-gray-500 font-medium mb-1">Tổng Doanh Thu</p>
          <h3 class="text-3xl font-bold text-green-600">{{ formatMoney(overview.total_revenue) }}</h3>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-orange-500">
           <p class="text-gray-500 font-medium mb-1">Tỷ Lệ Phòng Trống(Hiện tại)</p>
           <h3 class="text-3xl font-bold text-orange-500">{{ overview.vacancy_rate }}</h3>
         </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-amber-500">
          <p class="text-gray-500 font-medium mb-1">Đánh Giá Trung Bình</p>
          <h3 class="text-3xl font-bold text-amber-500 flex items-center gap-2">
            {{ overview.avg_rating }} 
            <Star class="w-6 h-6 fill-amber-500" />
            <span class="text-sm font-normal text-gray-500">({{ overview.total_reviews }} lượt)</span>
          </h3>
        </div>
       </div>

      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Doanh Thu Các Tháng Trong Năm</h2>
        <div class="h-[400px]">
          <Bar v-if="chartDataLoaded" :data="chartData" :options="chartOptions" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Bar } from 'vue-chartjs';
import { Star } from 'lucide-vue-next';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';

// Đăng ký các thành phần của ChartJS
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const isLoading = ref(true);
const chartDataLoaded = ref(false);
const selectedYear = ref(new Date().getFullYear().toString());

const overview = ref({
  total_bookings: 0,
  total_revenue: 0,
  vacancy_rate: '0%',
  total_reviews: 0,
  avg_rating: 0
});

const chartData = ref({
  labels: [],
  datasets: [{ label: 'Doanh thu (VNĐ)', backgroundColor: '#4A7055', data: [] }]
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
};

const formatMoney = (amount) => {
  return Number(amount).toLocaleString('vi-VN') + 'đ';
};

// Thêm vào dưới phần khai báo các biến (bên dưới const selectedYear = ...)
const startYear = 2024;
const currentYear = new Date().getFullYear();
// Tạo mảng động: [2024, 2025, 2026, 2027...]
const availableYears = ref(Array.from(
  { length: currentYear - startYear + 2 }, 
  (value, index) => startYear + index
));


const fetchStatistics = async () => {
  isLoading.value = true;
  chartDataLoaded.value = false;
  
  try {
    // Đừng quên truyền Token nếu API yêu cầu auth
    const token = localStorage.getItem('auth_token');
    const response = await fetch(`/api/admin/dashboard/statistics?year=${selectedYear.value}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    if (response.ok) {
      const resData = await response.json();
      
      // Cập nhật Overview
      overview.value = resData.data.overview;
      
      // Cập nhật Biểu đồ
      chartData.value = {
        labels: resData.data.chart.labels,
        datasets: [
          {
            label: 'Doanh thu (VNĐ)',
            backgroundColor: '#4A7055',
            borderRadius: 6,
            data: resData.data.chart.data
          }
        ]
      };
      
      chartDataLoaded.value = true;
    }
  } catch (error) {
    console.error("Lỗi tải thống kê:", error);
    alert('Không thể tải dữ liệu thống kê');
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchStatistics();
});
</script>