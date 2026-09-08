{{--
    Reusable User Form Partial
    Props:
    - $user (nullable, for edit)
    - $roles
    - $organizationUnits
    - $mode: 'create' | 'edit'
--}}
@php
    $isEdit = isset($user) && $user->exists;
    $mode = $isEdit ? 'edit' : 'create';
@endphp

<div class="space-y-6" x-data="{ showPass: false, showPassConfirm: false }">
    {{-- Section: Informasi Personal --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-green-50 via-emerald-50 to-teal-50 border-b border-gray-200 flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-green-600 text-white flex items-center justify-center shadow-sm flex-shrink-0">
                <i class="fas fa-user text-sm"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-bold text-gray-900">Informasi Personal</h3>
                <p class="text-xs text-gray-500">Identitas dasar pengguna. Nama dan email akan ditampilkan di sistem.</p>
            </div>
            @if($isEdit)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold border border-amber-200">
                    <i class="fas fa-pen text-[10px]"></i> Mode Edit
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold border border-green-200">
                    <i class="fas fa-plus text-[10px]"></i> Buat Baru
                </span>
            @endif
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label for="name" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    <i class="fas fa-id-badge text-green-600 mr-1"></i> Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" required
                       placeholder="Contoh: Budi Santoso"
                       class="w-full px-3.5 py-2.5 text-sm border {{ $errors->has('name') ? 'border-red-300 focus:ring-red-500 focus:border-red-500 bg-red-50/30' : 'border-gray-300 focus:ring-green-500 focus:border-green-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white transition">
                @error('name') <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-circle-exclamation text-[10px]"></i>{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="nik" class="block text-xs font-semibold text-gray-700 mb-1.5"><i class="fas fa-address-card text-gray-500 mr-1"></i> NIK</label>
                <input type="text" name="nik" id="nik" value="{{ old('nik', $user->nik ?? '') }}"
                       placeholder="16 digit NIK (opsional)"
                       class="w-full px-3.5 py-2.5 text-sm border {{ $errors->has('nik') ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-green-500 focus:border-green-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white">
                @error('nik') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="username" class="block text-xs font-semibold text-gray-700 mb-1.5"><i class="fas fa-at text-gray-500 mr-1"></i> Username <span class="text-red-500">*</span></label>
                <input type="text" name="username" id="username" value="{{ old('username', $user->username ?? '') }}" required
                       placeholder="username unik tanpa spasi"
                       class="w-full px-3.5 py-2.5 text-sm border {{ $errors->has('username') ? 'border-red-300' : 'border-gray-300 focus:ring-green-500 focus:border-green-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white">
                @error('username') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="text-[11px] text-gray-400 mt-1">Digunakan untuk login.</p>
            </div>

            <div class="md:col-span-2">
                <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5"><i class="fas fa-envelope text-gray-500 mr-1"></i> Email <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" required
                           placeholder="nama@email.com"
                           class="w-full pl-10 pr-3 py-2.5 text-sm border {{ $errors->has('email') ? 'border-red-300' : 'border-gray-300 focus:ring-green-500 focus:border-green-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white">
                </div>
                @error('email') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Section: Keamanan --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-violet-50 border-b border-gray-200 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-sm">
                <i class="fas fa-lock text-sm"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">Keamanan Akun</h3>
                <p class="text-xs text-gray-500">
                    @if($isEdit) Kosongkan jika tidak ingin mengubah password. @else Buat password minimal 8 karakter. @endif
                </p>
            </div>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    <i class="fas fa-key text-indigo-600 mr-1"></i>
                    {{ $isEdit ? 'Password Baru (Opsional)' : 'Password' }} @unless($isEdit)<span class="text-red-500">*</span>@endunless
                </label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" id="password" @if(!$isEdit) required @endif
                           placeholder="{{ $isEdit ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}"
                           class="w-full px-3.5 pr-10 py-2.5 text-sm border {{ $errors->has('password') ? 'border-red-300' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white">
                    <button type="button" @click="showPass = !showPass" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-50">
                        <i class="fas text-xs" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                @error('password') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                <div class="mt-2 flex items-center gap-1.5 text-[11px] text-gray-400">
                    <i class="fas fa-circle-info text-[10px]"></i> Gunakan kombinasi huruf, angka & simbol.
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    <i class="fas fa-check-double text-indigo-600 mr-1"></i> Konfirmasi Password @unless($isEdit)<span class="text-red-500">*</span>@endunless
                </label>
                <div class="relative">
                    <input :type="showPassConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" @if(!$isEdit) required @endif
                           placeholder="Ulangi password"
                           class="w-full px-3.5 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <button type="button" @click="showPassConfirm = !showPassConfirm" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-50">
                        <i class="fas text-xs" :class="showPassConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Section: Hak Akses --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-gray-200 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-600 text-white flex items-center justify-center shadow-sm">
                <i class="fas fa-shield-halved text-sm"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">Hak Akses & Organisasi</h3>
                <p class="text-xs text-gray-500">Tentukan role dan unit organisasi pengguna.</p>
            </div>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <x-searchable-dropdown
                    name="role_id"
                    label="Role"
                    :options="$roles"
                    value-field="id"
                    label-field="display_name"
                    :selected="old('role_id', $user->role_id ?? null)"
                    placeholder="Pilih Role"
                    :required="true"
                />
                @error('role_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="text-[11px] text-gray-400 mt-1">Role menentukan permission di sistem.</p>
            </div>

            <div>
                <x-searchable-dropdown
                    name="organization_unit_id"
                    label="Departemen / Unit"
                    :options="$organizationUnits"
                    value-field="id"
                    label-field="name"
                    :selected="old('organization_unit_id', $user->organization_unit_id ?? null)"
                    placeholder="Pilih Departemen"
                    :required="false"
                    empty-option="Tanpa Departemen"
                />
                @error('organization_unit_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="text-[11px] text-gray-400 mt-1">Opsional, untuk pengelompokan.</p>
            </div>
        </div>
    </div>
</div>
