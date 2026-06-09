<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SendjatiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // 2. Create Default Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@sendjati.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'), // password default
            ]
        );
        $admin->assignRole($adminRole);

        // 3. Create Default Categories
        $categories = [
            ['name' => 'Biji Kopi', 'type' => 'raw_material', 'department' => 'bar'],
            ['name' => 'Susu & Sirup', 'type' => 'raw_material', 'department' => 'bar'],
            ['name' => 'Pastry', 'type' => 'ready_to_sell', 'department' => 'kitchen'],
            ['name' => 'Merchandise', 'type' => 'ready_to_sell', 'department' => 'bar'],
            ['name' => 'Peralatan Bar', 'type' => 'raw_material', 'department' => 'bar'],
            ['name' => 'Peralatan Kitchen', 'type' => 'raw_material', 'department' => 'kitchen'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // 4. Create Default Units
        $units = ['Kg', 'Gram', 'Liter', 'mL', 'Pcs', 'Box', 'Pack'];
        foreach ($units as $unit) {
            Unit::firstOrCreate(['name' => $unit]);
        }

        $equipmentCat = Category::where('name', 'Peralatan Bar')->first();
        $pcsUnit = Unit::where('name', 'Pcs')->first();

        if ($equipmentCat && $pcsUnit) {
            \App\Models\Item::firstOrCreate(
                ['sku' => 'BAR-EQP-001'],
                [
                    'name'          => 'Mesin Espresso',
                    'type'          => 'equipment',
                    'department'    => 'bar',
                    'category_id'   => $equipmentCat->id,
                    'unit_id'       => $pcsUnit->id,
                    'minimum_stock' => 1,
                    'current_stock' => 1,
                    'harga_satuan'  => 15000000,
                ]
            );
        }
    }
}
