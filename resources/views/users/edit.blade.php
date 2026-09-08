@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-sm"><i class="fas fa-user-pen text-sm"></i></span>
                Edit User: {{ $user->name }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">Perbarui informasi akun <span class="font-semibold text-gray-700">@{{ $user->username }}</span></p>
        </div>
        <a href="{{ route('users.index') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i> Kembali
        </a>
    </div>

    <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        @include('users._form', ['user' => $user, 'roles' => $roles, 'organizationUnits' => $organizationUnits])

        <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500"><i class="fas fa-shield-halved mr-1"></i> Perubahan akan langsung berlaku saat login berikutnya.</p>
            <div class="flex items-center gap-3">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-sm transition">
                    <i class="fas fa-floppy-disk text-xs"></i> Update User
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
