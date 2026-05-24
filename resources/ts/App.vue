<template>
  <div class="min-h-screen flex flex-col font-sans text-gray-800 bg-gray-50">
    
    <Header v-if="!isAdminRoute" />
    
    <main class="flex-grow">
      <router-view></router-view>
    </main>
    
    <Footer v-if="!isAdminRoute" />
    
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import Header from './components/Header.vue';
import Footer from './components/Footer.vue';

const route = useRoute();

// Hàm kiểm tra xem đường dẫn hiện tại có chữ "/admin" không
const isAdminRoute = computed(() => {
  return route.path.startsWith('/admin');
});
</script>