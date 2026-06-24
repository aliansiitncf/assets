<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        Permission::firstOrCreate(['name' => 'lihat user']);
        Permission::firstOrCreate(['name' => 'tambah user']);
        Permission::firstOrCreate(['name' => 'ubah user']);
        Permission::firstOrCreate(['name' => 'hapus user']);

        Permission::firstOrCreate(['name' => 'lihat role']);
        Permission::firstOrCreate(['name' => 'tambah role']);
        Permission::firstOrCreate(['name' => 'ubah role']);
        Permission::firstOrCreate(['name' => 'hapus role']);

        Permission::firstOrCreate(['name' => 'lihat permission']);
        Permission::firstOrCreate(['name' => 'tambah permission']);
        Permission::firstOrCreate(['name' => 'ubah permission']);
        Permission::firstOrCreate(['name' => 'hapus permission']);

        Permission::firstOrCreate(['name' => 'lihat kategori']);
        Permission::firstOrCreate(['name' => 'tambah kategori']);
        Permission::firstOrCreate(['name' => 'ubah kategori']);
        Permission::firstOrCreate(['name' => 'hapus kategori']);

        Permission::firstOrCreate(['name' => 'lihat lokasi']);
        Permission::firstOrCreate(['name' => 'tambah lokasi']);
        Permission::firstOrCreate(['name' => 'ubah lokasi']);
        Permission::firstOrCreate(['name' => 'hapus lokasi']);

        Permission::firstOrCreate(['name' => 'lihat aset']);
        Permission::firstOrCreate(['name' => 'tambah aset']);
        Permission::firstOrCreate(['name' => 'ubah aset']);
        Permission::firstOrCreate(['name' => 'hapus aset']);
        Permission::firstOrCreate(['name' => 'lihat detail aset']);
        Permission::firstOrCreate(['name' => 'pindah aset']);

        Permission::firstOrCreate(['name' => 'lihat komponen']);
        Permission::firstOrCreate(['name' => 'tambah komponen']);
        Permission::firstOrCreate(['name' => 'ubah komponen']);
        Permission::firstOrCreate(['name' => 'hapus komponen']);

        Permission::firstOrCreate(['name' => 'cetak laporan aset']);
        Permission::firstOrCreate(['name' => 'cetak QR code aset']);
        Permission::firstOrCreate(['name' => 'cetak laporan aset rusak']);
        Permission::firstOrCreate(['name' => 'cetak laporan aset perbaikan']);

        Permission::firstOrCreate(['name' => 'lihat aset rusak']);
        Permission::firstOrCreate(['name' => 'lihat aset perbaikan']);

        Permission::firstOrCreate(['name' => 'scan aset']);

        Permission::findOrCreate('tandai aset perbaikan');
        Permission::findOrCreate('tandai aset rusak');

        Permission::firstOrCreate(['name' => 'lihat audit log']);
        Permission::firstOrCreate(['name' => 'cetak audit log']);

    }
}
