<?php

namespace Database\Seeders;

use App\Models\Plant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with a default admin and initial plant inventory.
     */
    public function run(): void
    {
        // Create default administrator account for checking
        $admin = User::updateOrCreate(
            ['email' => 'admin@nursery.com'],
            [
                'name' => 'Andre Gorrero (Admin)',
                'username' => 'admin',
                'password' => Hash::make('password123'),
            ]
        );

        // Seed sample plants across categories and stock statuses
        $samplePlants = [
            [
                'plant_name' => 'Monstera Deliciosa',
                'category' => 'Ornamental',
                'unit_price' => 450.00,
                'description' => 'Tropical split-leaf philodendron ideal for indoor shaded spaces.',
                'stock_status' => 'in_stock',
            ],
            [
                'plant_name' => 'Peace Lily (Spathiphyllum)',
                'category' => 'Flowering',
                'unit_price' => 280.00,
                'description' => 'Low-maintenance air-purifying flowering plant with white spathes.',
                'stock_status' => 'in_stock',
            ],
            [
                'plant_name' => 'Echeveria Elegans',
                'category' => 'Succulent',
                'unit_price' => 120.00,
                'description' => 'Rosette-forming Mexican snowball succulent requiring minimal watering.',
                'stock_status' => 'in_stock',
            ],
            [
                'plant_name' => 'Sweet Basil (Ocimum basilicum)',
                'category' => 'Herb',
                'unit_price' => 85.00,
                'description' => 'Aromatic culinary herb suited for sunny kitchen windowsills.',
                'stock_status' => 'out_of_stock',
            ],
            [
                'plant_name' => 'Bougainvillea Spectabilis',
                'category' => 'Flowering',
                'unit_price' => 350.00,
                'description' => 'Vibrant sun-loving ornamental vine with magenta bracts.',
                'stock_status' => 'out_of_stock',
            ],
            [
                'plant_name' => 'Snake Plant (Sansevieria)',
                'category' => 'Ornamental',
                'unit_price' => 220.00,
                'description' => 'Hardy upright foliage plant tolerant of low light and irregular watering.',
                'stock_status' => 'in_stock',
            ],
        ];

        foreach ($samplePlants as $plantData) {
            $admin->plants()->updateOrCreate(
                ['plant_name' => $plantData['plant_name']],
                $plantData
            );
        }
    }
}
