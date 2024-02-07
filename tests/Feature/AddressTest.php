<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function test_create_success(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            'street' => 'test',
            'city' => 'test',
            'province' => 'test',
            'country' => 'test',
            'postal_code' => 'test',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'street' => 'test',
                    'city' => 'test',
                    'province' => 'test',
                    'country' => 'test',
                    'postal_code' => 'test',
                ]
            ]);
    }

    public function test_create_not_found(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/' . ($contact->id + 1) . '/addresses', [
            'street' => 'test',
            'city' => 'test',
            'province' => 'test',
            'country' => 'test',
            'postal_code' => 'test',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['Not found.'],
                ]
            ]);
    }

    public function test_create_failed(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            'street' => 'test',
            'city' => 'test',
            'province' => 'test',
            'country' => '',
            'postal_code' => 'test',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    "country" => [
                        "The country field is required."
                    ]
                ]
            ]);
    }
}
