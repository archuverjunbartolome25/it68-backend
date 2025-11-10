<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuppliersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('suppliers')->insert([
            [
                'id' => 1,
                'name' => 'Mc Bride Corporation',
                'contact_person' => null,
                'email' => 'petbottles@mcbridecorporation.com',
                'phone' => '8367-1480 to 81',
                'address' => '10 University Avenue Ext., Portrero 1475 City of Malabon , NCR, Third District, Philippines',
                'tin' => '209-255-438-00000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Filpet, Inc.',
                'contact_person' => null,
                'email' => 'sales@filpet.com.ph',
                'phone' => '+63928 373 9797',
                'address' => '#404 M.H. Del Pilar St., Maysilo, 1477 City of Malabon, NCR, Third District, Philippines',
                'tin' => '000-117-868-00000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Royalseal',
                'contact_person' => null,
                'email' => null,
                'phone' => null,
                'address' => null,
                'tin' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Shrinkpack',
                'contact_person' => 'Not Available',
                'email' => 'Not Available',
                'phone' => '8519-47-35 | 8519-48-02 | 8519-4787 | 8519-4804 | 8519-4795 | 8764-0301',
                'address' => 'Lot 4, 5, 6 Phoenix Road Meycauayan Industrial Subdivision, Iba, 3020 City of Meycauayan, Bulacan Philippines',
                'tin' => '000-240-521-00000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Synergy',
                'contact_person' => null,
                'email' => 'westernsynergy@gmail.com',
                'phone' => '(046) 865-2773 to 74 | (046) 686-3438',
                'address' => '#8 Balete St., Lalaan I, Silang 4118, Cavite, CALABARZON, Philippines',
                'tin' => '005-774-732-00000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Polyflex',
                'contact_person' => 'Not Available',
                'email' => 'Not Available',
                'phone' => '+63906 513 4308 | +63917 791 1765',
                'address' => 'Unit V Warehouse 103 Kamachile St., Tiaong, Guiguinto, Bulacan',
                'tin' => '638-260-448-00000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
