<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            ['name' => 'manage_roles', 'display_name' => 'Kelola Roles', 'description' => 'Mengelola roles dan permissions'],
            ['name' => 'manage_permissions', 'display_name' => 'Kelola Permissions', 'description' => 'Mengelola permissions'],
            ['name' => 'view_dashboard', 'display_name' => 'Lihat Dashboard', 'description' => 'Melihat halaman dashboard'],
            ['name' => 'manage_users', 'display_name' => 'Kelola Users', 'description' => 'Mengelola pengguna'],
            ['name' => 'manage_organization_types', 'display_name' => 'Kelola Tipe Organisasi', 'description' => 'Mengelola tipe organisasi'],
            ['name' => 'manage_organization_units', 'display_name' => 'Kelola Unit Organisasi', 'description' => 'Mengelola unit organisasi'],
            ['name' => 'manage_patients', 'display_name' => 'Kelola Pasien', 'description' => 'Mengelola data pasien MCU'],
            ['name' => 'manage_mcu_registrations', 'display_name' => 'Kelola Pendaftaran MCU', 'description' => 'Membuat dan mengelola pendaftaran MCU (Daftarkan MCU)'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // Create Roles (idempotent)
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrator', 'description' => 'Role dengan akses penuh ke sistem']
        );

        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            ['display_name' => 'Pengguna', 'description' => 'Role untuk pengguna umum']
        );

        // Assign permissions to roles (sync without detaching existing extraneous is safe)
        $adminRole->permissions()->syncWithoutDetaching(Permission::all()->pluck('id')->toArray());

        if ($userRole->permissions()->count() === 0) {
            $userRole->permissions()->attach(
                Permission::whereIn('name', ['view_dashboard'])->get()
            );
        }
    }
}
