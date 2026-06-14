<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Danh sách phòng</h1>
        <p class="text-gray-500 text-sm mt-1">Quản lý toàn bộ homestay và phòng nghỉ</p>
      </div>
      <router-link to="/admin/rooms/create" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
        <Plus class="w-5 h-5" /> Thêm phòng mới
      </router-link>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
      <div class="bg-white rounded-2xl p-4 shadow-sm border border-blue-100 flex items-center gap-3 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xl shrink-0">🏢</div>
        <div class="overflow-hidden">
          <p class="text-xs font-medium text-gray-500 mb-1 truncate">Tổng số phòng</p>
          <h3 class="text-2xl font-bold text-gray-900">{{ stats.total }}</h3>
        </div>
      </div>
 
      <div class="bg-white rounded-2xl p-4 shadow-sm border border-emerald-100 flex items-center gap-3 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 font-bold text-xl shrink-0">✨</div>
        <div class="overflow-hidden">
          <p class="text-xs font-medium text-gray-500 mb-1 truncate">Phòng trống</p>
          <h3 class="text-2xl font-bold text-gray-900">{{ stats.available }}</h3>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-4 shadow-sm border border-amber-100 flex items-center gap-3 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center text-amber-600 font-bold text-xl shrink-0">💳</div>
        <div class="overflow-hidden">
          <p class="text-xs font-medium text-gray-500 mb-1 truncate">Đã đặt cọc</p>
          <h3 class="text-2xl font-bold text-gray-900">{{ stats.deposited }}</h3>
        </div>
      </div>
      
      <div class="bg-white rounded-2xl p-4 shadow-sm border border-red-100 flex items-center gap-3 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-600 font-bold text-xl shrink-0">🛠️</div>
        <div class="overflow-hidden">
          <p class="text-xs font-medium text-gray-500 mb-1 truncate">Dọn dẹp/Bảo trì</p>
          <h3 class="text-2xl font-bold text-gray-900">{{ stats.maintenance }}</h3>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-4 shadow-sm border border-purple-100 flex items-center gap-3 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center text-purple-600 font-bold text-xl shrink-0">🔑</div>
        <div class="overflow-hidden">
          <p class="text-xs font-medium text-gray-500 mb-1 truncate">Đang sử dụng</p>
          <h3 class="text-2xl font-bold text-gray-900">{{ stats.occupied }}</h3>
        </div>
      </div>
    </div>

    <!-- Thanh tìm kiếm & Lọc -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4 flex flex-wrap gap-3 items-center">
      <input
        v-model="searchQuery"
        placeholder="Tìm kiếm theo tên phòng..."
        class="flex-1 min-w-[200px] px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400"
      />
      <select v-model="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400">
        <option value="">Tất cả trạng thái</option>
        <option value="available">Trống / Sẵn sàng</option>
        <option value="booked">Đã đặt cọc</option>
        <option value="in_use">Đang ở</option>
        <option value="maintenance">Bảo trì</option>
      </select>
      <span class="text-sm text-gray-400">{{ filteredRooms.length }} kết quả</span>
      <button @click="resetFilters" class="text-sm text-emerald-600 hover:text-emerald-800 font-medium whitespace-nowrap">Xóa bộ lọc</button>
      
      <div class="ml-auto flex items-center gap-2">
        <button 
          @click="filterType = 'room_based'"
          :class="['flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium border transition-all', 
                   filterType === 'room_based' ? 'bg-purple-600 text-white border-purple-600 shadow-sm' : 'bg-purple-50 text-purple-700 border-purple-100 hover:bg-purple-100']"
        >
          <LayoutGrid class="w-3.5 h-3.5" /> Tòa nhà/Cơ sở
        </button>
        <button 
          @click="filterType = 'whole_house'"
          :class="['flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium border transition-all', 
                   filterType === 'whole_house' ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-blue-50 text-blue-700 border-blue-100 hover:bg-blue-100']"
        >
          <HomeIcon class="w-3.5 h-3.5" /> Nguyên căn
        </button>
        <button 
          @click="filterType = 'home'"
          :class="['flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium border transition-all', 
                   filterType === 'home' ? 'bg-amber-600 text-white border-amber-600 shadow-sm' : 'bg-amber-50 text-amber-700 border-amber-100 hover:bg-amber-100']"
        >
          <HomeIcon class="w-3.5 h-3.5" /> Phòng Home
        </button>
        <button 
          @click="filterType = 'private_room_standalone'"
          :class="['flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium border transition-all', 
                   filterType === 'private_room_standalone' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-emerald-50 text-emerald-700 border-emerald-100 hover:bg-emerald-100']"
        >
          <LayoutGrid class="w-3.5 h-3.5" /> Phòng riêng (Độc lập)
        </button>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
            <th class="p-4 font-semibold w-10"></th>
            <th class="p-4 font-semibold w-24">Hình ảnh</th>
            <th class="p-4 font-semibold">Tên phòng/Homestay</th>
            <th class="p-4 font-semibold">Loại</th>
            <th class="p-4 font-semibold">Giá / đêm</th>
            <th class="p-4 font-semibold">Trạng thái/Quy mô</th>
            <th class="p-4 font-semibold text-center w-36">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="room in filteredRooms" :key="room.id">
            <!-- DÒNG CHA -->
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors text-sm" :class="{'bg-purple-50/30': room.rent_type === 'room_based'}">
              <td class="p-4">
                <button 
                  v-if="room.rent_type === 'room_based'" 
                  @click="toggleExpand(room.id)"
                  class="p-1 hover:bg-white rounded transition-all transform"
                  :class="{'rotate-90': expandedRows.includes(room.id)}"
                >
                  <ChevronRight class="w-4 h-4 text-gray-500" />
                </button>
              </td>
            <td class="p-4">
              <img :src="room.image" alt="Room" class="w-16 h-12 object-cover rounded-md border border-gray-200" referrerpolicy="no-referrer" />
            </td>
            <td class="p-4 font-medium text-gray-900">{{ room.title }}</td>
            <td class="p-4 text-gray-600">
              <div v-if="room.rent_type === 'whole_house'" class="flex items-center gap-1.5 text-blue-600 font-medium">
                <HomeIcon class="w-4 h-4" /> Nguyên căn
              </div>
              <div v-else-if="room.rent_type === 'room_based'" class="flex items-center gap-1.5 text-purple-600 font-medium">
                <LayoutGrid class="w-4 h-4" /> Đang dơn lẻ
              </div>
              <div v-else-if="room.rent_type === 'home'" class="flex items-center gap-1.5 text-amber-600 font-medium">
                <HomeIcon class="w-4 h-4" /> Phòng Home
              </div>
              <div v-else class="flex items-center gap-1.5 text-emerald-600 font-medium">
                <LayoutGrid class="w-4 h-4" /> Phòng riêng (Độc lập)
              </div>
            </td>
            <td class="p-4 text-emerald-600 font-semibold">{{ Number(room.price).toLocaleString('vi-VN') }}đ</td>
            <td class="p-4">
              <span v-if="room.rent_type === 'room_based'" class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold border border-purple-200">
                🏢 Tòa nhà ({{ room.child_rooms_count }} phòng)
              </span>
              <template v-else>
                <span v-if="room.status === 'available'" class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-medium">Trống / Sẵn sàng</span>
                <span v-else-if="room.status === 'booked'" class="bg-amber-100 text-amber-700 px-2 py-1 rounded-full text-xs font-medium">Đã đặt cọc</span>
                <span v-else-if="room.status === 'in_use'" class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">Đang sử dụng</span>
                <span v-else-if="room.status === 'maintenance'" class="bg-orange-100 text-orange-700 px-2 py-1 rounded-full text-xs font-medium">Đang dọn dẹp/Bảo trì</span>
                <span v-else class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs font-medium">Tạm ẩn</span>
              </template>
            </td>
            <td class="p-4 text-center">
              <div class="flex items-center justify-center gap-2">
                <button @click="openRoomDetail(room.id)" class="text-emerald-600 hover:text-emerald-800 bg-emerald-50 p-1.5 rounded-md transition-colors" title="Xem chi tiết">
                  <Eye class="w-4 h-4" />
                </button>
                <button @click="editRoom(room.id)" class="text-blue-600 hover:text-blue-800 bg-blue-50 p-1.5 rounded-md transition-colors" title="Chỉnh sửa">
                  <Edit class="w-4 h-4" />
                </button>
                <button
                  v-if="room.status === 'available' || room.status === 'maintenance'"
                  @click="toggleMaintenance(room)"
                  :title="room.status === 'available' ? 'Chuyển sang Bảo trì' : 'Mở phòng trở lại'"
                  :class="room.status === 'available' ? 'text-orange-500 bg-orange-50 hover:text-orange-700' : 'text-gray-500 bg-gray-100 hover:text-gray-700'"
                  class="p-1.5 rounded-md transition-colors"
                >
                  <Wrench class="w-4 h-4" />
                </button>
                <button
                  v-if="room.rent_type === 'whole_house'"
                  @click="convertToRoomBased(room)"
                  class="text-purple-600 hover:text-purple-800 bg-purple-50 p-1.5 rounded-md transition-colors"
                  title="Chuyển sang cho thuê phòng lẻ"
                >
                  <Repeat class="w-4 h-4" />
                </button>
                <button @click="deleteRoom(room.id)" class="text-red-600 hover:text-red-800 bg-red-50 p-1.5 rounded-md transition-colors" title="Xóa">
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>

          <!-- DÒNG CON (SUB-TABLE) -->
          <tr v-if="room.rent_type === 'room_based' && expandedRows.includes(room.id)" class="bg-gray-50/50">
            <td colspan="7" class="p-0 border-b border-gray-100">
              <div class="pl-14 pr-4 py-3">
                <table class="w-full text-left bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                  <thead class="bg-gray-100 text-[11px] uppercase tracking-wider text-gray-500">
                    <tr>
                      <th class="px-4 py-2 font-bold text-gray-800">Tên phòng con</th>
                      <th class="px-4 py-2 font-bold">Loại</th>
                      <th class="px-4 py-2 font-bold">Giá thuê</th>
                      <th class="px-4 py-2 font-bold">Trạng thái</th>
                      <th class="px-4 py-2 font-bold text-center w-32">Thao tác</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100 italic">
                    <tr v-for="child in room.child_rooms" :key="child.id" class="hover:bg-emerald-50/30 transition-colors">
                      <td class="px-4 py-2.5 font-medium text-gray-800">{{ child.title }}</td>
                      <td class="px-4 py-2.5 text-gray-500">
                        {{ child.max_guests === 2 ? 'Phòng Tiêu chuẩn' : 'Phòng Gia đình' }}
                      </td>
                      <td class="px-4 py-2.5 text-emerald-600 font-semibold">
                        {{ Number(child.price).toLocaleString('vi-VN') }}đ
                      </td>
                      <td class="px-4 py-2.5">
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase shadow-sm"
                          :class="{
                            'bg-emerald-100 text-emerald-700': child.status === 'available',
                            'bg-amber-100 text-amber-700': child.status === 'booked',
                            'bg-blue-100 text-blue-700': child.status === 'in_use',
                            'bg-orange-100 text-orange-700': child.status === 'maintenance'
                          }">
                          {{ child.status === 'available' ? 'Trống' : (child.status === 'booked' ? 'Đã cọc' : 'Đang ở') }}
                        </span>
                      </td>
                      <td class="px-4 py-2.5">
                        <div class="flex items-center justify-center gap-2">
                           <button @click="openRoomDetail(child.id)" class="text-emerald-500 hover:text-emerald-700 p-1 rounded" title="Chi tiết">
                             <Eye class="w-3.5 h-3.5" />
                           </button>
                           <button @click="editRoom(child.id)" class="text-blue-500 hover:text-blue-700 p-1 rounded" title="Sửa">
                             <Edit class="w-3.5 h-3.5" />
                           </button>
                           <button @click="deleteRoom(child.id)" class="text-red-500 hover:text-red-700 p-1 rounded" title="Xóa">
                             <Trash2 class="w-3.5 h-3.5" />
                           </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div v-if="!room.child_rooms || room.child_rooms.length === 0" class="text-xs text-gray-400 py-2 pl-4 italic">
                  Chưa có phòng con nào.
                </div>
              </div>
            </td>
          </tr>
        </template>
          <tr v-if="filteredRooms.length === 0">
            <td colspan="7" class="p-8 text-center text-gray-500 font-medium">Không có phòng nào khớp với tiêu chí tìm kiếm.</td>
          </tr>
        </tbody>
      </table>

      <!-- Phân trang -->
      <div v-if="pagination.last_page > 1" class="p-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
        <p class="text-sm text-gray-500">
          Hiển thị trang {{ pagination.current_page }} / {{ pagination.last_page }} (Tổng {{ pagination.total }} kết quả)
        </p>
        <div class="flex items-center gap-1">
          <button 
            @click="fetchRooms(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-3 py-1.5 rounded border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm font-medium"
          >
            Trước
          </button>
          
          <button 
            v-for="p in pagination.last_page" 
            :key="p"
            @click="fetchRooms(p)"
            :class="['px-3 py-1.5 rounded border text-sm font-medium transition-colors', 
                     p === pagination.current_page ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50']"
          >
            {{ p }}
          </button>

          <button 
            @click="fetchRooms(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1.5 rounded border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm font-medium"
          >
            Sau
          </button>
        </div>
      </div>
    </div>

    <div v-if="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" @click.self="closeDetailModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col transform transition-all">
        
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center shrink-0 bg-gray-50">
          <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <Home class="w-5 h-5 text-emerald-600" /> Chi tiết phòng
          </h2>
          <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600 bg-white p-1.5 rounded-lg border border-gray-200 transition-colors">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6">
          <div v-if="loadingDetail" class="flex justify-center items-center py-20 text-emerald-600">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600"></div>
          </div>
          
          <div v-else-if="selectedRoom" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
              <div class="aspect-[4/3] rounded-xl overflow-hidden border border-gray-100 shadow-sm bg-gray-100 relative">
                <img :src="getModalMainImage(selectedRoom)" class="w-full h-full object-cover" referrerpolicy="no-referrer" />
                <div class="absolute top-3 right-3">
                  <span v-if="selectedRoom.status === 'available'" class="bg-emerald-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">Trống</span>
                  <span v-else-if="selectedRoom.status === 'booked'" class="bg-amber-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">Đã cọc</span>
                  <span v-else-if="selectedRoom.status === 'in_use'" class="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">Đang ở</span>
                  <span v-else class="bg-gray-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">Bảo trì/Ẩn</span>
                </div>
              </div>
              
              <div v-if="selectedRoom.images && selectedRoom.images.length > 1" class="grid grid-cols-4 gap-2">
                <div v-for="(img, idx) in selectedRoom.images" :key="idx" class="aspect-square rounded-lg overflow-hidden border border-gray-200">
                  <img :src="formatUrl(img.image_url)" class="w-full h-full object-cover" referrerpolicy="no-referrer" />
                </div>
              </div>
            </div>

            <div class="space-y-6">
              <div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ selectedRoom.title }}</h3>
                <p class="text-gray-500 text-sm flex items-center gap-1.5 mb-4">
                  <MapPin class="w-4 h-4" /> {{ selectedRoom.location }}
                </p>
                <div v-if="selectedRoom.rent_type !== 'room_based'" class="text-3xl font-extrabold text-emerald-600">
                  {{ Number(selectedRoom.price).toLocaleString('vi-VN') }}<span class="text-base text-gray-500 font-medium">đ / đêm</span>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4 py-4 border-y border-gray-100">
                <div>
                  <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Loại hình</p>
                  <p class="font-medium text-gray-900">
                    {{ selectedRoom.rent_type === 'whole_house' ? 'Nguyên căn' : (selectedRoom.rent_type === 'home' ? 'Phòng Home (Độc lập)' : (selectedRoom.rent_type === 'room_based' ? 'Tòa nhà/Cơ sở' : 'Phòng riêng lẻ')) }}
                  </p>
                </div>
                <div v-if="selectedRoom.rent_type !== 'room_based'">
                  <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Sức chứa</p>
                  <p class="font-medium text-gray-900 flex items-center gap-1.5"><Users class="w-4 h-4 text-emerald-600"/> Tối đa {{ selectedRoom.max_guests }} khách</p>
                </div>
                <div v-else>
                  <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Quy mô</p>
                  <p class="font-medium text-gray-900 flex items-center gap-1.5"><LayoutGrid class="w-4 h-4 text-purple-600"/> {{ selectedRoom.child_rooms_count }} phòng con</p>
                </div>
              </div>

              <div>
                <p class="text-sm text-gray-900 font-bold mb-3 uppercase tracking-wide">Tiện nghi phòng</p>
                <div class="flex flex-wrap gap-2">
                  <template v-if="selectedRoom.amenity_list && selectedRoom.amenity_list.length > 0">
                    <span v-for="am in selectedRoom.amenity_list" :key="am.id" class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-800 border border-emerald-100 px-3 py-1.5 rounded-lg text-sm font-medium">
                      <Star class="w-3.5 h-3.5" /> {{ am.name }}
                    </span>
                  </template>
                  <span v-else class="text-gray-500 text-sm italic">Chưa cập nhật tiện nghi.</span>
                </div>
              </div>

              <div v-if="selectedRoom.rent_type !== 'room_based'">
                <p class="text-sm text-gray-900 font-bold mb-2 uppercase tracking-wide">Mô tả</p>
                <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">
                  {{ selectedRoom.description || 'Chưa có mô tả cho phòng này.' }}
                </p>
              </div>

              <!-- HIỂN THỊ DANH SÁCH PHÒNG CON NẾU LÀ TÒA NHÀ -->
              <div v-if="selectedRoom.rent_type === 'room_based'" class="space-y-3">
                <p class="text-sm text-gray-900 font-bold uppercase tracking-wide border-b pb-2">Danh sách phòng riêng lẻ</p>
                <div v-if="selectedRoom.child_rooms && selectedRoom.child_rooms.length > 0" class="space-y-2">
                  <div v-for="child in selectedRoom.child_rooms" :key="child.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded bg-white border border-gray-200 overflow-hidden shrink-0">
                        <img v-if="child.image" :src="formatUrl(child.image)" class="w-full h-full object-cover" />
                        <div v-else class="w-full h-full flex items-center justify-center text-gray-300">🖼️</div>
                      </div>
                      <div>
                        <p class="font-bold text-gray-800 text-sm">{{ child.title }}</p>
                        <p class="text-xs text-gray-500">{{ child.max_guests === 2 ? 'Phòng Tiêu chuẩn' : 'Phòng Gia đình' }}</p>
                      </div>
                    </div>
                    <div class="text-right">
                      <p class="text-sm font-bold text-emerald-600">{{ Number(child.price).toLocaleString('vi-VN') }}đ</p>
                      <span class="text-[10px] px-1.5 py-0.5 rounded-full" 
                        :class="{
                          'bg-emerald-100 text-emerald-700': child.status === 'available',
                          'bg-amber-100 text-amber-700': child.status === 'booked',
                          'bg-blue-100 text-blue-700': child.status === 'in_use',
                          'bg-orange-100 text-orange-700': child.status === 'maintenance'
                        }">
                        {{ child.status === 'available' ? 'Trống' : (child.status === 'booked' ? 'Đã cọc' : 'Đang ở') }}
                      </span>
                    </div>
                  </div>
                </div>
                <p v-else class="text-sm text-gray-400 italic">Chưa có phòng con nào được thêm.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 shrink-0">
          <button @click="closeDetailModal" class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-white font-medium transition-colors">
            Đóng
          </button>
          <button @click="editRoom(selectedRoom?.id)" v-if="selectedRoom" class="px-5 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium flex items-center gap-2 transition-colors">
            <Edit class="w-4 h-4" /> Chỉnh sửa phòng này
          </button>
        </div>

      </div>
    </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Plus, Edit, Trash2, Eye, X, Home, MapPin, Users, Star, Wrench, Repeat, Home as HomeIcon, LayoutGrid, ChevronRight } from 'lucide-vue-next';

const router = useRouter();

// State quản lý danh sách và thống kê
const rooms = ref<any[]>([]);
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0
});
const stats = ref({
  total: 0, available: 0, deposited: 0, occupied: 0, maintenance: 0
});

const expandedRows = ref<number[]>([]);
const toggleExpand = (id: number) => {
  const index = expandedRows.value.indexOf(id);
  if (index > -1) expandedRows.value.splice(index, 1);
  else expandedRows.value.push(id);
};

// Search / Filter
const searchQuery = ref('');
const filterStatus = ref('');
const filterType = ref('');

const filteredRooms = computed(() => {
  // Với Laravel pagination, filtering được xử lý tại backend.
  // Ở đây chúng ta chỉ trả về rooms.value (đã được fetch theo filter).
  return rooms.value;
});

// Watchers cho bộ lọc để fetch lại dữ liệu
import { watch } from 'vue';
watch([searchQuery, filterStatus, filterType], () => {
  fetchRooms(1);
}, { debounce: 500 } as any);

const resetFilters = () => {
  searchQuery.value = '';
  filterStatus.value = '';
  filterType.value = '';
};

// State quản lý Modal Quick View
const showDetailModal = ref(false);
const selectedRoom = ref<any>(null);
const loadingDetail = ref(false);

const formatUrl = (url: string) => {
  if (!url) return '';
  if (!url.startsWith('http') && !url.startsWith('/storage/') && !url.startsWith('data:')) {
    return url.startsWith('/') ? `/storage${url}` : `/storage/${url}`;
  }
  return url;
};

// Hàm lấy dữ liệu danh sách phòng
const fetchRooms = async (page = 1) => {
  try {
    const url = new URL('/api/admin/rooms', window.location.origin);
    url.searchParams.append('page', page.toString());
    if (searchQuery.value) url.searchParams.append('search', searchQuery.value);
    if (filterStatus.value) url.searchParams.append('status', filterStatus.value);
    if (filterType.value) url.searchParams.append('type', filterType.value);
    
    const response = await fetch(url.toString(), {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
        'Accept': 'application/json'
      }
    }); 
    const data = await response.json();
    
    rooms.value = data.data.map((room: any) => {
      let thumb = 'https://picsum.photos/seed/room/600/400';
      if (room.images && room.images.length > 0) {
        thumb = room.images[0].image_url;
      } else if (room.image) {
        thumb = room.image;
      }
      return { ...room, image: formatUrl(thumb) };
    });

    pagination.value = {
      current_page: data.meta.current_page,
      last_page: data.meta.last_page,
      total: data.meta.total
    };
  } catch (error) {
    console.error('Lỗi khi tải danh sách phòng:', error);
  }
};

const fetchStats = async () => {
  try {
    const response = await fetch('/api/admin/rooms/stats');
    if (response.ok) {
      stats.value = await response.json();
    }
  } catch (error) {
    console.error('Lỗi khi lấy dữ liệu thống kê:', error);
  }
};

onMounted(() => {
  fetchRooms();
  fetchStats();
});

// Chuyển trang sửa
const editRoom = (id: number) => {
  if (id) router.push(`/admin/rooms/edit/${id}`);
};

// Mở Modal xem chi tiết
const openRoomDetail = async (id: number) => {
  showDetailModal.value = true;
  loadingDetail.value = true;
  try {
    // Gọi API lấy dữ liệu chi tiết của đúng phòng này (bao gồm cả mảng images và amenity_list)
    const response = await fetch(`/api/rooms/${id}`);
    if (response.ok) {
      const data = await response.json();
      selectedRoom.value = data.data || data;
    } else {
      alert('Không thể tải chi tiết phòng!');
      showDetailModal.value = false;
    }
  } catch (error) {
    console.error(error);
  } finally {
    loadingDetail.value = false;
  }
};

const closeDetailModal = () => {
  showDetailModal.value = false;
  selectedRoom.value = null;
};

// Lấy ảnh chính cho Modal
const getModalMainImage = (roomData: any) => {
  if (roomData.images && roomData.images.length > 0) {
    const primary = roomData.images.find((img: any) => img.is_primary);
    return formatUrl(primary ? primary.image_url : roomData.images[0].image_url);
  }
  return 'https://picsum.photos/seed/fallback/600/400';
};

const deleteRoom = async (id: number) => {
  if (confirm('Bạn có chắc chắn muốn xóa phòng này vĩnh viễn không? Hành động này không thể hoàn tác!')) {
    try {
      const response = await fetch(`/api/admin/rooms/${id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
      });

      if (response.ok) {
        rooms.value = rooms.value.filter(r => r.id !== id);
        fetchStats(); 
        alert('Đã xóa phòng thành công!');
      } else {
        const data = await response.json();
        alert('Lỗi: ' + (data.message || 'Không thể xóa phòng này!'));
      }
    } catch (error) {
      console.error('Lỗi khi xóa phòng:', error);
      alert('Lỗi kết nối đến máy chủ!');
    }
  }
};

// Quick Maintenance Toggle
const toggleMaintenance = async (room: any) => {
  const nextStatus = room.status === 'available' ? 'Bảo Trì' : 'Sẵn sàng';
  if (!confirm(`Chuyển phòng này sang trạng thái ${nextStatus}?`)) return;
  try {
    const res = await fetch(`/api/admin/rooms/${room.id}/toggle-maintenance`, {
      method: 'PATCH',
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}`, 'Accept': 'application/json' }
    });
    if (res.ok) {
      const data = await res.json();
      // Cập nhật trực tiếp trên giao diện không cần reload
      const idx = rooms.value.findIndex(r => r.id === room.id);
      if (idx !== -1) rooms.value[idx].status = data.room.status;
      fetchStats();
    } else {
      const d = await res.json();
      alert(d.message || 'Lỗi khi thay đổi trạng thái phòng');
    }
  } catch (error) {
    alert('Lỗi kết nối đến máy chủ!');
  }
};
const convertToRoomBased = async (room: any) => {
  if (!confirm(`Bạn có chắc chắn muốn chuyển '${room.title}' từ hình thức 'Thuê nguyên căn' sang 'Cho thuê phòng lẻ' không?`)) return;
  
  try {
    const res = await fetch(`/api/admin/rooms/${room.id}/convert-to-room-based`, {
      method: 'POST',
      headers: { 'Accept': 'application/json' }
    });
    
    if (res.ok) {
      alert('Đã chuyển đổi mô hình thành công!');
      fetchRooms(); // Load lại để thấy thay đổi
    } else {
      const data = await res.json();
      alert('Lỗi: ' + (data.message || 'Không thể chuyển đổi'));
    }
  } catch (error) {
    alert('Lỗi kết nối server!');
  }
};
</script>