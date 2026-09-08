@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-green-600 text-white flex items-center justify-center shadow-sm"><i class="fas fa-user-plus text-sm"></i></span>
                Tambah User Baru
            </h2>
            <p class="text-sm text-gray-500 mt-1">Buat akun pengguna baru. Pastikan data unik dan valid.</p>
        </div>
        <a href="{{ route('users.index') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i> Kembali
        </a>
    </div>

    <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
        @csrf
        @include('users._form', ['user' => new \App\Models\User(), 'roles' => $roles, 'organizationUnits' => $organizationUnits])

        <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500"><i class="fas fa-circle-info mr-1"></i> Pastikan role dan departemen sudah benar sebelum menyimpan.</p>
            <div class="flex items-center gap-3">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-sm transition">
                    <i class="fas fa-save text-xs"></i> Simpan User
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
