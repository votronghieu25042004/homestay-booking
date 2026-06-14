<template>
  <div class="max-w-4xl mx-auto p-6">
    <div class="flex items-center gap-4 mb-6">
      <button @click="router.back()" class="text-gray-500 hover:text-gray-700">
        <ArrowLeft class="w-6 h-6" />
      </button>
      <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Cập nhật phòng' : 'Thêm phòng mới' }}</h1>
    </div>

    <div v-if="isLoadingData" class="text-center py-20 text-gray-500">
      Đang tải thông tin phòng cũ...
    </div>

    <form v-else @submit.prevent="handleSubmit" class="space-y-8">
      <!-- LỰA CHỌN MÔ HÌNH THUÊ -->
      <div class="bg-emerald-50 p-1 rounded-xl flex gap-1 border border-emerald-100 shadow-sm">
        <button 
          type="button"
          @click="rentMode = 'whole_house'"
          :class="[
            'flex-1 py-3 px-4 rounded-lg font-bold transition-all flex items-center justify-center gap-2',
            rentMode === 'whole_house' ? 'bg-white text-emerald-700 shadow-sm' : 'text-emerald-600/70 hover:bg-white/50'
          ]"
        >
          <HomeIcon class="w-5 h-5" /> Thêm Nguyên Căn
        </button>
        <button 
          type="button"
          @click="rentMode = 'private_room'"
          :class="[
            'flex-1 py-3 px-4 rounded-lg font-bold transition-all flex items-center justify-center gap-2',
            rentMode === 'private_room' ? 'bg-white text-emerald-700 shadow-sm' : 'text-emerald-600/70 hover:bg-white/50'
          ]"
        >
          <LayoutGrid class="w-5 h-5" /> Thêm Phòng Riêng
        </button>
        <button 
          type="button"
          @click="rentMode = 'home'"
          :class="[
            'flex-1 py-3 px-4 rounded-lg font-bold transition-all flex items-center justify-center gap-2',
            rentMode === 'home' ? 'bg-white text-emerald-700 shadow-sm' : 'text-emerald-600/70 hover:bg-white/50'
          ]"
        >
          <HomeIcon class="w-5 h-5" /> Thêm Homestay Liên kết
        </button>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">
          {{ rentMode === 'whole_house' ? 'Thông tin Nguyên Căn' : (rentMode === 'home' ? 'Thông tin Homestay Liên kết' : 'Thông tin Phòng Riêng') }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div :class="(rentMode === 'whole_house' || rentMode === 'home') ? 'md:col-span-2' : ''">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ (rentMode === 'whole_house' || rentMode === 'home') ? 'Tên Nguyên Căn / Homestay Đối tác *' : 'Tên phòng (VD: Phòng 101) *' }}
            </label>
            <input v-model="form.title" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" />
          </div>

          <div v-if="rentMode === 'private_room'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Thuộc Homestay (Không bắt buộc)</label>
            <select v-model="form.parent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 bg-white">
              <option value="">-- Chọn Homestay cơ sở --</option>
              <option v-for="h in homestays" :key="h.id" :value="h.id">{{ h.title }}</option>
            </select>
          </div>

          <div v-if="rentMode === 'private_room'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Loại phòng *</label>
            <select v-model="roomTypeStandard" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 bg-white">
              <option value="standard">Phòng Tiêu Chuẩn (2 Người lớn + 1 Trẻ em)</option>
              <option value="family">Phòng Gia Đình (4 Người lớn + 2 Trẻ em)</option>
            </select>
            <div v-if="roomTypeStandard" class="mt-2 p-3 bg-emerald-50 rounded-lg border border-emerald-100 text-xs text-emerald-800 italic">
              💡 Khách hàng sẽ thấy: Tối đa {{ form.max_guests }} người lớn + {{ form.max_children }} trẻ em.
            </div>
          </div>

          <!-- SỨC CHỨA (CHỈ HIỆN CHO NGUYÊN CĂN & PHÒNG HOME) -->
          <div v-if="rentMode === 'whole_house' || rentMode === 'home'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Sức chứa (Người lớn) *</label>
            <input v-model.number="form.max_guests" type="number" min="1" max="50" @keydown="(e) => { if(['-','e','+'].includes(e.key)) e.preventDefault() }" @input="form.max_guests = Math.min(50, Math.max(1, form.max_guests || 1))" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" />
          </div>

          <div v-if="rentMode === 'whole_house' || rentMode === 'home'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Sức chứa (Trẻ em < 10 tuổi) *</label>
            <input v-model.number="form.max_children" type="number" min="0" max="99" @keydown="(e) => { if(['-','e','+'].includes(e.key)) e.preventDefault() }" @input="form.max_children = Math.min(99, Math.max(0, form.max_children || 0))" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" />
            <p class="mt-1 text-xs text-gray-500 italic">Gợi ý: Nhập 99 nếu không giới hạn trẻ em.</p>
          </div>

          <div v-if="rentMode === 'whole_house' || rentMode === 'home' || (rentMode === 'private_room' && !form.parent_id)" class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ cụ thể (Đà Nẵng) *</label>
            <input v-model="form.location" type="text" :required="rentMode === 'whole_house' || rentMode === 'home' || !form.parent_id" placeholder="Nhập địa chỉ..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái hiện tại</label>
            <select v-model="form.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 bg-white">
              <option value="available">Sẵn sàng đón khách</option>
              <option value="maintenance">Bảo trì / Đóng phòng</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Giá thuê / Đêm (VNĐ) *</label>
            <input v-model.number="form.price" type="number" min="0" @keydown="(e) => { if(['-','e','+'].includes(e.key)) e.preventDefault() }" @input="form.price = Math.max(0, form.price || 0)" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" />
          </div>
          
          <div class="md:col-span-2 mt-2 bg-emerald-50 border border-emerald-100 p-4 rounded-lg flex items-center gap-3">
            <input type="checkbox" id="is_visible" v-model="form.is_visible" class="w-5 h-5 text-emerald-600 focus:ring-emerald-500 rounded cursor-pointer" />
            <label for="is_visible" class="font-medium text-emerald-900 cursor-pointer">
              Bật hiển thị phòng này trên trang web cho khách xem
            </label>
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả chi tiết</label>
            <textarea v-model="form.description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" placeholder="Nhập mô tả phòng..."></textarea>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Tiện nghi</h2>
        
        <div class="flex gap-2 mb-6">
          <input 
            v-model="newAmenity" 
            @keyup.enter.prevent="addCustomAmenity"
            type="text" 
            placeholder="Nhập tiện nghi khác (VD: Bồn tắm sục...)" 
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 text-sm"
          />
          <button type="button" @click="addCustomAmenity" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Thêm tiện nghi
          </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg">
          <div v-for="amenity in availableAmenities" :key="amenity.id" class="flex items-center justify-between bg-white px-3 py-2 border border-gray-200 rounded-md shadow-sm">
            <label class="flex items-center gap-2 cursor-pointer flex-1">
              <input type="checkbox" :value="amenity.id" v-model="form.amenities" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded" />
              <span class="text-sm text-gray-700 whitespace-nowrap overflow-hidden text-ellipsis">{{ amenity.name }}</span>
            </label>
            
            <button type="button" @click.prevent="removeAmenity(amenity.id)" class="text-red-400 hover:text-red-600 p-1 rounded hover:bg-red-50" title="Xóa">
              <X class="w-4 h-4" />
            </button>
          </div>
          
          <div v-if="availableAmenities.length === 0" class="col-span-2 md:col-span-3 text-center py-4 text-sm text-gray-500">
            Chưa có tiện nghi nào. Hãy thêm tiện nghi mới ở ô bên trên!
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Hình ảnh phòng</h2>
        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:bg-gray-50 transition-colors cursor-pointer relative">
          <input type="file" multiple accept="image/*" @change="handleFileUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
          <UploadCloud class="w-10 h-10 text-gray-400 mx-auto mb-2" />
          <p class="text-gray-600 font-medium">Nhấn hoặc kéo thả ảnh vào đây</p>
          <p class="text-gray-500 text-sm mt-1">Hỗ trợ JPG, PNG (Tối đa 5MB/ảnh)</p>
        </div>

        <div v-if="imagePreviews.length > 0" class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
          <div v-for="(img, index) in imagePreviews" :key="index" class="relative group aspect-[4/3] rounded-lg overflow-hidden border border-gray-200">
            <img :src="img.url" class="w-full h-full object-cover" />
            <button @click.prevent="removeImage(index)" class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
              <X class="w-4 h-4" />
            </button>
            <div v-if="!img.isNew" class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-xs text-center py-1">Ảnh cũ</div>
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-4 pb-10">
        <button type="button" @click="router.back()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">Hủy</button>
        <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium flex items-center gap-2">
          <Save class="w-5 h-5" /> {{ isEdit ? 'Cập nhật phòng' : 'Lưu thông tin' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ArrowLeft, UploadCloud, X, Save, Home as HomeIcon, LayoutGrid } from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();
const isEdit = ref(route.path.includes('edit'));
const isLoadingData = ref(false);

const rentMode = ref<'whole_house' | 'private_room' | 'home'>('whole_house');
const homestays = ref<any[]>([]); // Danh sách homestay cha
const roomTypeStandard = ref('standard');

const newAmenity = ref('');
const availableAmenities = ref<any[]>([]);

const form = ref({
  title: '', location: '', rent_type: 'whole_house', parent_id: '', 
  type: 'house', price: 0,
  max_guests: 20, max_children: 0, beds: 1, description: '', status: 'available',
  is_visible: true,
  amenities: [] as number[],
});

// LOGIC CẬP NHẬT MÔ HÌNH THUÊ
watch(rentMode, (newMode) => {
  if (isLoadingData.value) return;
  form.value.rent_type = newMode;
  if (newMode === 'whole_house') {
    form.value.type = 'house';
    form.value.max_guests = 20;
    form.value.max_children = 99;
    form.value.parent_id = '';
  } else if (newMode === 'home') {
    form.value.type = 'room';
    form.value.rent_type = 'home';
    form.value.parent_id = '';
    // Không force max_children = 99 ở đây nếu là đang sửa hoặc muốn nhập thủ công
    if (!isEdit.value) form.value.max_children = 99; 
  } else {
    form.value.type = 'room';
    form.value.rent_type = 'private_room';
    updatePrivateRoomCapacity();
  }
});

// LOGIC CẬP NHẬT SỨC CHỨA PHÒNG RIÊNG
const updatePrivateRoomCapacity = () => {
  if (roomTypeStandard.value === 'standard') {
    form.value.max_guests = 2;
    form.value.max_children = 1;
  } else {
    form.value.max_guests = 4;
    form.value.max_children = 2;
  }
};

watch(roomTypeStandard, () => {
  if (isLoadingData.value) return;
  // CHỈ cập nhật tự động cho Phòng Riêng (tab 2), còn Phòng Home (tab 3) cho phép sửa tay
  if (rentMode.value === 'private_room') updatePrivateRoomCapacity();
});

const selectedFiles = ref<File[]>([]);
const imagePreviews = ref<{url: string, isNew: boolean}[]>([]);
onMounted(async () => {
  // 1. TẢI TIỆN NGHI VÀ LỌC SẠCH "BÓNG MA" TRONG DATABASE
  try {
    const amRes = await fetch('/api/amenities');
    if (amRes.ok) {
      const rawAmenities = await amRes.json();
      // Chỉ lấy những tiện nghi thực sự có tên đàng hoàng
      availableAmenities.value = rawAmenities
        .filter((a: any) => a && a.name && a.name.trim() !== '' && a.name !== 'null')
        .map((a: any) => ({ ...a, id: Number(a.id) }));
    }
  } catch (err) {
    console.error('Lỗi tải tiện nghi:', err);
  }

  // 1.1 TẢI DANH SÁCH HOMESTAY (GỐC)
  try {
    const hsRes = await fetch('/api/admin/rooms/homestays');
    if (hsRes.ok) {
      homestays.value = await hsRes.json();
    }
  } catch (err) {
    console.error('Lỗi tải danh sách homestay:', err);
  }

  // 2. TẢI DỮ LIỆU CŨ LÊN FORM NẾU ĐANG LÀ SỬA PHÒNG
  if (isEdit.value) {
    isLoadingData.value = true;
    try {
      const roomId = route.params.id;
      const response = await fetch(`/api/rooms/${roomId}`);
      if (!response.ok) throw new Error('Không lấy được dữ liệu');
      
      const data = await response.json();

      form.value.title = data.title || '';
      form.value.location = data.location || '';
      form.value.rent_type = data.rent_type || (data.type === 'house' ? 'whole_house' : 'private_room');
      form.value.parent_id = data.parent_id || '';
      form.value.type = data.type || 'room';
      form.value.price = data.price || 0;
      form.value.max_guests = data.max_guests || 2;
      form.value.max_children = data.max_children || 0;

      // Xác định rentMode và roomTypeStandard
      if (form.value.rent_type === 'private_room') {
        rentMode.value = 'private_room';
      } else if (form.value.rent_type === 'home') {
        rentMode.value = 'home';
      } else {
        rentMode.value = 'whole_house';
      }
      if (rentMode.value === 'private_room' || rentMode.value === 'home') {
        roomTypeStandard.value = form.value.max_guests <= 2 ? 'standard' : 'family';
      }
      
      // XÓA TẬN GỐC LỖI HIỆN CHỮ "null"
      if (data.description === null || data.description === 'null' || data.description === 'undefined' || !data.description) {
        form.value.description = ''; 
      } else {
        form.value.description = data.description;
      }
      
      form.value.status = data.status || 'available';
      // Trạng thái hiển thị (Mặc định bật nếu không có data)
      form.value.is_visible = data.is_visible !== 0; 

      if (data.amenity_list) {
        form.value.amenities = data.amenity_list.map((a: any) => Number(a.id));
        
        // Đẩy thêm các tiện nghi phòng đang có vào list tổng (vẫn phải qua lưới lọc)
        data.amenity_list.forEach((dbAmenity: any) => {
          if (dbAmenity && dbAmenity.name && dbAmenity.name.trim() !== '' && dbAmenity.name !== 'null') {
            dbAmenity.id = Number(dbAmenity.id);
            if (!availableAmenities.value.some(a => a.id === dbAmenity.id)) {
              availableAmenities.value.push(dbAmenity);
            }
          }
        });
      }

      if (data.images) {
        data.images.forEach((img: any) => {
          imagePreviews.value.push({ url: img.image_url, isNew: false });
        });
      }
    } catch (error) {
      console.error('Lỗi khi tải thông tin phòng cũ:', error);
    } finally {
      isLoadingData.value = false;
    }
  }
});

const addCustomAmenity = async () => {
  if (newAmenity.value.trim() !== '') {
    try {
      const response = await fetch('/api/amenities', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ name: newAmenity.value.trim() })
      });
      if (!response.ok) {
        throw new Error('Không thể tạo tiện nghi này.');
      }
      const newAm = await response.json();
      newAm.id = Number(newAm.id);
      if (!availableAmenities.value.some(a => a.id === newAm.id)) {
        availableAmenities.value.push(newAm);
      }
      if (!form.value.amenities.includes(newAm.id)) {
        form.value.amenities.push(newAm.id);
      }
      newAmenity.value = '';
    } catch (error) {
      console.error(error);
      alert('Không thể lưu tiện nghi mới!');
    }
  }
};

const removeAmenity = async (id: number) => {
  if (confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa tiện nghi này không?')) {
    try {
      await fetch(`/api/amenities/${id}`, { method: 'DELETE' });
      availableAmenities.value = availableAmenities.value.filter(a => a.id !== id);
      form.value.amenities = form.value.amenities.filter(aId => aId !== id);
    } catch (error) {
      console.error(error);
    }
  }
};

const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files) {
    Array.from(target.files).forEach(file => {
      // Validate giới hạn dung lượng ảnh (3MB)
      if (file.size > 3 * 1024 * 1024) {
        alert(`Ảnh [${file.name}] vượt quá giới hạn 3MB, vui lòng chọn ảnh nhẹ hơn`);
        return;
      }
      selectedFiles.value.push(file);
      imagePreviews.value.push({ url: URL.createObjectURL(file), isNew: true });
    });
  }
};

const removeImage = (index: number) => {
  if (imagePreviews.value[index].isNew) {
    const newFiles = imagePreviews.value.filter(img => img.isNew);
    const fileIndex = newFiles.findIndex((_, i) => i === index);
    if(fileIndex !== -1) selectedFiles.value.splice(fileIndex, 1);
  }
  imagePreviews.value.splice(index, 1);
};

const handleSubmit = async () => {
  try {
    const formData = new FormData();
    formData.append('title', form.value.title);
    formData.append('location', form.value.location || '');
    formData.append('rent_type', form.value.rent_type);
    formData.append('parent_id', form.value.parent_id || '');
    formData.append('type', form.value.type);
    formData.append('price', form.value.price.toString());
    formData.append('max_guests', form.value.max_guests.toString());
    formData.append('max_children', form.value.max_children.toString());
    
    // Đảm bảo không ném chữ "null" xuống Database nữa
    formData.append('description', form.value.description || '');
    
    formData.append('status', form.value.status);
    formData.append('is_visible', form.value.is_visible ? '1' : '0');
    
    form.value.amenities.forEach(id => formData.append('amenities[]', id.toString()));
    selectedFiles.value.forEach(file => formData.append('images[]', file));
    imagePreviews.value.filter(img => !img.isNew).forEach(img => formData.append('retained_images[]', img.url));

    let url = '/api/admin/rooms';
    if (isEdit.value) {
      url = `/api/admin/rooms/${route.params.id}`;
      formData.append('_method', 'PUT'); 
    }

    const response = await fetch(url, {
      method: 'POST', 
      body: formData,
      headers: { 'Accept': 'application/json' }
    });

    if (!response.ok) throw new Error('Lỗi server');
    
    alert(isEdit.value ? 'Cập nhật thành công!' : 'Tạo phòng mới thành công!');
    router.push('/admin/rooms');

  } catch (error) {
    console.error('Lỗi khi lưu:', error);
    alert('Có lỗi xảy ra khi lưu dữ liệu!');
  }
};
</script>