<?php

namespace Database\Seeders;

use App\Models\Rack;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RackSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed data titik rak penyimpanan gudang mekanik PT Garuda Yamato Steel.
     */
    public function run(): void
    {
        $racks = [
            [
                'nama_rack' => 'RAK-A1 (Bearing & Pillow Block)',
                'lokasi'    => 'Zona A - Lantai 1 Workshop Mekanik',
            ],
            [
                'nama_rack' => 'RAK-A2 (Oil Seal, O-Ring & Gasket)',
                'lokasi'    => 'Zona A - Lantai 1 Workshop Mekanik',
            ],
            [
                'nama_rack' => 'RAK-B1 (V-Belt, Chain & Sprocket)',
                'lokasi'    => 'Zona B - Lantai 1 Workshop Mekanik',
            ],
            [
                'nama_rack' => 'RAK-B2 (Coupling & Gearbox Part)',
                'lokasi'    => 'Zona B - Lantai 1 Workshop Mekanik',
            ],
            [
                'nama_rack' => 'RAK-C1 (Hydraulic Valve & Hose)',
                'lokasi'    => 'Zona C - Area Hydraulic & Pneumatic',
            ],
            [
                'nama_rack' => 'RAK-C2 (Pneumatic Cylinder & Fitting)',
                'lokasi'    => 'Zona C - Area Hydraulic & Pneumatic',
            ],
            [
                'nama_rack' => 'RAK-D1 (Bolt, Nut & Heavy Fastener)',
                'lokasi'    => 'Zona D - Lantai 2 Gudang Spare Part',
            ],
            [
                'nama_rack' => 'RAK-D2 (Filter, Oli & Greasing)',
                'lokasi'    => 'Zona D - Lantai 2 Gudang Spare Part',
            ],
            [
                'nama_rack' => 'RAK-E1 (Roll Mill & Cooling Bed Spare)',
                'lokasi'    => 'Zona E - Area Heavy Duty Bay',
            ],
        ];

        foreach ($racks as $rack) {
            Rack::updateOrCreate(
                ['nama_rack' => $rack['nama_rack']],
                ['lokasi'    => $rack['lokasi']]
            );
        }
    }
}
