<?php

namespace Database\Seeders;

use App\Models\ResidentialData;
use App\Models\CommercialData;
use App\Models\AdministrativeData;
use Illuminate\Database\Seeder;

class SellRequestConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Residential Fields
        $resFields = [
            ['field_name' => 'rooms_no', 'label_en' => 'Number of Rooms', 'type' => 'number', 'is_required' => true],
            ['field_name' => 'bathrooms_no', 'label_en' => 'Number of Bathrooms', 'type' => 'number', 'is_required' => true],
            ['field_name' => 'space', 'label_en' => 'Space (m2)', 'type' => 'number', 'is_required' => true],
            ['field_name' => 'floor_no', 'label_en' => 'Floor Number', 'type' => 'number', 'is_required' => true],
            ['field_name' => 'garden_area', 'label_en' => 'Garden Area', 'type' => 'boolean', 'is_required' => false],
            ['field_name' => 'notes', 'label_en' => 'Notes', 'type' => 'text', 'is_required' => false],
            ['field_name' => 'execution_date', 'label_en' => 'Execution Date', 'type' => 'select', 'is_required' => true, 'options' => ['immediately', '6_months', '1_year', '2_years', '3_years', '4_years_or_more']],
        ];

        foreach ($resFields as $field) {
            ResidentialData::create($field);
        }

        // Commercial Fields
        $comFields = [
            ['field_name' => 'space', 'label_en' => 'Space (m2)', 'type' => 'number', 'is_required' => true],
            ['field_name' => 'floor_no', 'label_en' => 'Floor Number', 'type' => 'number', 'is_required' => true],
            ['field_name' => 'notes', 'label_en' => 'Notes', 'type' => 'text', 'is_required' => false],
            ['field_name' => 'execution_date', 'label_en' => 'Execution Date', 'type' => 'select', 'is_required' => true, 'options' => ['immediately', '6_months', '1_year', '2_years', '3_years', '4_years_or_more']],
        ];

        foreach ($comFields as $field) {
            CommercialData::create($field);
        }

        // Administrative Fields
        $admFields = [
            ['field_name' => 'space', 'label_en' => 'Space (m2)', 'type' => 'number', 'is_required' => true],
            ['field_name' => 'floor_no', 'label_en' => 'Floor Number', 'type' => 'number', 'is_required' => true],
            ['field_name' => 'notes', 'label_en' => 'Notes', 'type' => 'text', 'is_required' => false],
            ['field_name' => 'execution_date', 'label_en' => 'Execution Date', 'type' => 'select', 'is_required' => true, 'options' => ['immediately', '6_months', '1_year', '2_years', '3_years', '4_years_or_more']],
        ];

        foreach ($admFields as $field) {
            AdministrativeData::create($field);
        }
    }
}
