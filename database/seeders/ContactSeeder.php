<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'test')->first();
        Contact::create([
            "first_name" => "Boy",
            "last_name" => "Jr",
            "email" => "boyjr@email.com",
            "phone" => "08123123",
            "user_id" => $user->id
        ]);
    }
}
