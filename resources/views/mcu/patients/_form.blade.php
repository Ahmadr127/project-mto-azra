{{--
    Patient form partial
    Props: $patient (nullable)
--}}
@php
    $isEdit = isset($patient) && $patient->exists;
@endphp

<div class="space-y-6" x-data="patientForm()">
    {{-- Identitas Pasien --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-sm font-bold text-gray-900">Identitas Pasien</h3>
            <p class="text-xs text-gray-500">Lengkapi data identitas, kontak, dan foto pasien.</p>
        </div>
        <div class="p-6 space-y-6">
            {{-- Foto --}}
            <div class="flex flex-col sm:flex-row gap-6">
                <div class="flex flex-col items-center gap-3">
                    <div class="relative w-28 h-28 rounded-xl overflow-hidden border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center">
                        <template x-if="photoPreview">
                            <img :src="photoPreview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!photoPreview">
                            <div class="text-center p-2">
                                @if($isEdit && $patient->photo_url)
                                    <img src="{{ $patient->photo_url }}" alt="Foto" class="w-full h-full object-cover absolute inset-0">
                                @else
                                    <p class="text-xs text-gray-400">Belum ada foto</p>
                                @endif
                            </div>
                        </template>
                        <button type="button" x-show="photoPreview" @click="clearPhoto()" class="absolute top-1 right-1 w-6 h-6 bg-red-600 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-700">
                            ×
                        </button>
                    </div>
                    <div class="flex flex-col gap-1 w-full max-w-[180px]">
                        <label class="block text-xs font-semibold text-gray-700">Foto Pasien <span class="text-gray-400 font-normal">(opsional, max 2MB)</span></label>
                        <input type="file" name="photo" accept="image/*" @change="onPhotoChange($event)"
                               class="block w-full text-xs text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-green-600 file:text-white hover:file:bg-green-700 file:cursor-pointer border border-gray-200 rounded-md cursor-pointer">
                        @error('photo') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        @if($isEdit && $patient->photo)
                            <label class="flex items-center gap-1.5 text-xs text-gray-600 mt-1 cursor-pointer">
                                <input type="checkbox" name="remove_photo" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500"> Hapus foto saat ini
                            </label>
                        @endif
                    </div>
                </div>
                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">No. Rekam Medis <span class="text-red-500">*</span></label>
                        <input type="text" name="patient_code" value="{{ old('patient_code', $patient->patient_code ?? '') }}" required
                               placeholder="Contoh: RM-001 atau DEMO-RM-001"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white">
                        @error('patient_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">NIK <span class="text-gray-400 font-normal">(16 digit)</span></label>
                        <input type="text" name="nik" value="{{ old('nik', $patient->nik ?? '') }}" maxlength="16" inputmode="numeric"
                               placeholder="327101..."
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        @error('nik') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $patient->name ?? '') }}" required
                               placeholder="Nama sesuai KTP"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
                        <select name="gender" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white">
                            <option value="">Pilih</option>
                            <option value="L" @selected(old('gender', $patient->gender ?? '')=='L')>Laki-laki</option>
                            <option value="P" @selected(old('gender', $patient->gender ?? '')=='P')>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">BPJS</label>
                        <input type="text" name="bpjs" value="{{ old('bpjs', $patient->bpjs ?? '') }}" placeholder="No. BPJS (opsional)"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place', $patient->birth_place ?? '') }}" placeholder="Kota kelahiran"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        @error('birth_place') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="birth_date" x-model="birthDate" @change="calcAge()" value="{{ old('birth_date', isset($patient) && $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        @error('birth_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Umur (otomatis)</label>
                        <div class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50 flex items-center gap-2">
                            <span x-text="ageText" class="font-medium text-gray-800"></span>
                            <span x-show="!ageText" class="text-gray-400 text-xs">Isi tanggal lahir untuk menghitung umur</span>
                            @if($isEdit && $patient->birth_date)
                                <span class="ml-auto text-xs text-gray-500 hidden sm:inline">{{ $patient->age_formatted }}</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1">Dihitung otomatis: tahun, bulan, hari.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">No. HP / Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $patient->phone ?? '') }}" placeholder="0812xxxxxxx"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Darurat</label>
                        <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $patient->emergency_phone ?? '') }}" placeholder="No. kontak darurat"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        @error('emergency_phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alamat --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-sm font-bold text-gray-900">Alamat Lengkap</h3>
            <p class="text-xs text-gray-500">Alamat utama dan detail wilayah administratif.</p>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat (jalan, RT/RW, No. rumah)</label>
                <textarea name="address" rows="2" placeholder="Jl. Contoh No. 12 RT 01 RW 02..."
                          class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">{{ old('address', $patient->address ?? '') }}</textarea>
                @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kelurahan / Desa</label>
                    <input type="text" name="kelurahan" value="{{ old('kelurahan', $patient->kelurahan ?? '') }}" placeholder="Kelurahan"
                           class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kecamatan</label>
                    <input type="text" name="kecamatan" value="{{ old('kecamatan', $patient->kecamatan ?? '') }}" placeholder="Kecamatan"
                           class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kabupaten / Kota</label>
                    <input type="text" name="kabupaten_kota" value="{{ old('kabupaten_kota', $patient->kabupaten_kota ?? '') }}" placeholder="Kota Bogor"
                           class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Provinsi</label>
                    <input type="text" name="provinsi" value="{{ old('provinsi', $patient->provinsi ?? '') }}" placeholder="Jawa Barat"
                           class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Pos</label>
                    <input type="text" name="kode_pos" value="{{ old('kode_pos', $patient->kode_pos ?? '') }}" maxlength="10" inputmode="numeric" placeholder="161xx"
                           class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Tambahan (opsional) --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-sm font-bold text-gray-900">Informasi Tambahan</h3>
            <p class="text-xs text-gray-500">Departemen / status kepegawaian (opsional).</p>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Departemen</label>
                <input type="text" name="department" value="{{ old('department', $patient->department ?? '') }}" placeholder="Departemen (opsional)"
                       class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Status Kepegawaian</label>
                <select name="employee_status" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">-- Pilih --</option>
                    <option value="Tetap" @selected(old('employee_status', $patient->employee_status ?? '')=='Tetap')>Tetap</option>
                    <option value="Kontrak" @selected(old('employee_status', $patient->employee_status ?? '')=='Kontrak')>Kontrak</option>
                    <option value="Magang" @selected(old('employee_status', $patient->employee_status ?? '')=='Magang')>Magang</option>
                    <option value="Lainnya" @selected(old('employee_status', $patient->employee_status ?? '')=='Lainnya')>Lainnya</option>
                </select>
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
function patientForm() {
    return {
        birthDate: @json(old('birth_date', isset($patient) && $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '')),
        ageText: '',
        photoPreview: null,
        init() {
            this.calcAge();
        },
        calcAge() {
            if (!this.birthDate) { this.ageText = ''; return; }
            const birth = new Date(this.birthDate);
            if (isNaN(birth)) { this.ageText = ''; return; }
            const now = new Date();
            if (birth > now) { this.ageText = 'Tanggal lahir tidak valid'; return; }
            let years = now.getFullYear() - birth.getFullYear();
            let months = now.getMonth() - birth.getMonth();
            let days = now.getDate() - birth.getDate();
            if (days < 0) {
                months--;
                const prevMonth = new Date(now.getFullYear(), now.getMonth(), 0);
                days += prevMonth.getDate();
            }
            if (months < 0) {
                years--;
                months += 12;
            }
            let parts = [];
            if (years > 0) parts.push(years + ' tahun');
            if (months > 0) parts.push(months + ' bulan');
            if (days > 0) parts.push(days + ' hari');
            if (parts.length === 0) parts.push('0 hari');
            this.ageText = parts.join(' ');
        },
        onPhotoChange(event) {
            const file = event.target.files[0];
            if (!file) { this.photoPreview = null; return; }
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran foto maksimal 2MB');
                event.target.value = '';
                this.photoPreview = null;
                return;
            }
            const reader = new FileReader();
            reader.onload = e => this.photoPreview = e.target.result;
            reader.readAsDataURL(file);
        },
        clearPhoto() {
            this.photoPreview = null;
            const input = document.querySelector('input[name="photo"]');
            if (input) input.value = '';
        }
    }
}
</script>
@endpush
@endonce
