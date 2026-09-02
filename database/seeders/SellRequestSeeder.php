<?php

namespace Database\Seeders;

use App\Enums\ActivationStatus;
use App\Enums\SellRequestExecutionDate;
use App\Models\SellRequest;
use App\Models\UnitSubType;
use App\Models\UptownType;
use App\Models\User;
use Illuminate\Database\Seeder;

class SellRequestSeeder extends Seeder
{
    /**
     * Seed a sample sell request with required related records.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'seller@delar.test'],
            [
                'first_name' => 'Sample',
                'last_name' => 'Seller',
                'phone' => '01111111111',
                'password' => 'Seller@123456',
                'role' => 'user',
                'provider' => 'local',
                'provider_id' => 'local',
                'status' => ActivationStatus::Active,
            ]
        );

        $uptownType = UptownType::firstOrCreate(
            ['name_en' => 'Apartment'],
            [
                'name_ar' => 'شقة',
                'status' => 'active',
            ]
        );

        $unitSubType = UnitSubType::firstOrCreate(
            [
                'uptown_type_id' => $uptownType->id,
                'name_en' => 'Studio',
            ],
            [
                'name_ar' => 'استوديو',
            ]
        );

        $sellRequest = SellRequest::updateOrCreate(
            [
                'user_id' => $user->id,
                'notes' => 'Seeded sample sell request',
            ],
            [
                'age' => 32,
                'identity_front_image' => 'sell_requests/identity/seed_front.png',
                'identity_back_image' => 'sell_requests/identity/seed_back.png',
                'country' => 'Egypt',
                'city' => 'Cairo',
                'area' => 'New Cairo',
                'uptown_type_id' => $uptownType->id,
                'unit_sub_type_id' => $unitSubType->id,
                'detailed_pdf' => 'sell_requests/pdfs/seed_details.pdf',
                'price' => 2500000,
                'installments' => true,
                'installments_years' => 8,
                'installments_years_left' => 6,
                'installments_total_price' => 2800000,
                'installments_price_per_year' => 350000,
                'rooms_no' => 3,
                'bathrooms_no' => 2,
                'space' => 145,
                'floor_no' => 4,
                'garden_area' => false,
                'garden_space' => null,
                'finishing' => 'semi_finished',
                'execution_date' => SellRequestExecutionDate::OneYear,
                'delivery_date' => null,
                'status' => 'pending',
                'visibility' => 'private',
            ]
        );

        $this->command?->info('Sell request seeded.');
        $this->command?->info('ID: '.$sellRequest->id);
        $this->command?->info('User phone: 01111111111 / password: Seller@123456');
        $this->command?->info('Execution: '.$sellRequest->execution_date?->value);
        $this->command?->info('Finishing: '.$sellRequest->finishing);
    }
}
