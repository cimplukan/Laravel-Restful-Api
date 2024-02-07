<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
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

    public function test_get_success()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->get('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'test',
                    'city' => 'test',
                    'province' => 'test',
                    'country' => 'test',
                    'postal_code' => '55555',
                ]
            ]);
    }
    public function test_get_not_found()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->get('/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1), [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Not found.',
                    ],
                ]
            ]);
    }

    public function test_update_success()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                'street' => 'test update',
                'city' => 'test update',
                'province' => 'test update',
                'country' => 'test update',
                'postal_code' => '99999',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'test update',
                    'city' => 'test update',
                    'province' => 'test update',
                    'country' => 'test update',
                    'postal_code' => '99999',
                ]
            ]);
    }
    public function test_update_failed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                'street' => 'test update',
                'city' => 'test update',
                'province' => 'test update',
                'country' => '',
                'postal_code' => '99999',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => ['The country field is required.'],
                ]
            ]);
    }
    public function test_update_not_found()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1),
            [
                'street' => 'test update',
                'city' => 'test update',
                'province' => 'test update',
                'country' => 'test update',
                'postal_code' => '99999',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['Not found.'],
                ]
            ]);
    }
}
