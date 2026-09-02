<?php

namespace Database\Seeders;

use App\Models\BotMessage;
use Illuminate\Database\Seeder;

class BotMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing messages for a clean seed
        BotMessage::truncate();

        // --- ROOT LEVEL OPTIONS (x, y, z) ---
        
        $sales = BotMessage::create([
            'text' => 'Sales Inquiries',
            'response' => 'What kind of property are you interested in?',
            'parent_id' => null
        ]);

        $support = BotMessage::create([
            'text' => 'Technical Support',
            'response' => 'Please select the issue you are facing:',
            'parent_id' => null
        ]);

        $billing = BotMessage::create([
            'text' => 'Billing & Payments',
            'response' => 'How can we help with your billing?',
            'parent_id' => null
        ]);

        // --- LEVEL 2 OPTIONS (x.1, x.2) ---

        // Sales Children
        $residential = BotMessage::create([
            'text' => 'Residential Properties',
            'response' => 'Great! Are you looking to Buy or Rent?',
            'parent_id' => $sales->id
        ]);
        
        $commercial = BotMessage::create([
            'text' => 'Commercial Properties',
            'response' => 'We have retail spaces and offices. What do you need?',
            'parent_id' => $sales->id
        ]);

        // Support Children
        BotMessage::create([
            'text' => 'I cannot login',
            'response' => 'Please try resetting your password using the "Forgot Password" link on the login page. If the issue persists, call us at 19000.',
            'parent_id' => $support->id
        ]);

        BotMessage::create([
            'text' => 'The app is crashing',
            'response' => 'Please ensure you have the latest version installed from the App Store or Google Play.',
            'parent_id' => $support->id
        ]);

        // --- LEVEL 3 OPTIONS (x.1.1, x.1.2) ---

        // Residential Children
        BotMessage::create([
            'text' => 'I want to Buy',
            'response' => 'Please visit our "Buy" section on the app to see all available residential units for sale.',
            'parent_id' => $residential->id
        ]);

        BotMessage::create([
            'text' => 'I want to Rent',
            'response' => 'We have many apartments for rent. Please check the "Rent" tab.',
            'parent_id' => $residential->id
        ]);
        
        // Commercial Children
        BotMessage::create([
            'text' => 'Office Space',
            'response' => 'We have office spaces ranging from 50sqm to 1000sqm. A representative will contact you.',
            'parent_id' => $commercial->id
        ]);
        
        BotMessage::create([
            'text' => 'Retail / Shop',
            'response' => 'Check our commercial listings under the "Compounds" section.',
            'parent_id' => $commercial->id
        ]);
    }
}
