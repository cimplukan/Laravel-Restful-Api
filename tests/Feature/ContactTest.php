<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function test_create_success()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            "/api/contacts",
            [
                "first_name" => "Boy",
                "last_name" => "Jr",
                "email" => "boyjr@email.com",
                "phone" => "08123123",
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(201)
            ->assertJson([
                "data" => [
                    "first_name" => "Boy",
                    "last_name" => "Jr",
                    "email" => "boyjr@email.com",
                    "phone" => "08123123",
                ]
            ]);
    }

    public function test_create_failed()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            "/api/contacts",
            [
                "first_name" => "",
                "last_name" => "Jr",
                "email" => "boyjr",
                "phone" => "08123123",
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => ['The first name field is required.'],
                    'email' => ['The email field must be a valid email address.']
                ]
            ]);
    }

    public function test_create_unauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            "/api/contacts",
            [
                "first_name" => "",
                "last_name" => "Jr",
                "email" => "boyjr",
                "phone" => "08123123",
            ],
            [
                "Authorization" => "salah"
            ]
        )->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['Unauthorized']
                ]
            ]);
    }

    public function test_get_success()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    "first_name" => "Boy",
                    "last_name" => "Jr",
                    "email" => "boyjr@email.com",
                    "phone" => "08123123"
                ]
            ]);
    }

    public function test_get_not_found()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $this->get('/api/contacts/' . ($contact->id + 1), [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    "message" => [
                        'Not found.'
                    ]
                ]
            ]);
    }
    public function test_get_other_user_contact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'test2'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    "message" => [
                        'Not found.'
                    ]
                ]
            ]);
    }

    public function test_update_success()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $this->put('/api/contacts/' . $contact->id, [
            "first_name" => "Boy Kata",
            "last_name" => "Junior",
            "email" => "boykatajr@email.com",
            "phone" => "08123123"
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    "first_name" => "Boy Kata",
                    "last_name" => "Junior",
                    "email" => "boykatajr@email.com",
                    "phone" => "08123123"
                ]
            ]);
    }
    public function test_update_validation_error()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $this->put('/api/contacts/' . $contact->id, [
            "first_name" => "",
            "last_name" => "Junior",
            "email" => "boykatajr@email.com",
            "phone" => "08123123"
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    "first_name" => ['The first name field is required.']
                ]
            ]);
    }

    public function test_delete_success()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $this->delete('/api/contacts/' . $contact->id, [], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }
    public function test_delete_not_found()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $this->delete('/api/contacts/' . ($contact->id + 1), [], [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['Not found.']
                ]
            ]);
    }

    public function test_search_firstname_by_name()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=first', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }
    public function test_search_lastname_by_name()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=last', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }
    public function test_search_by_email()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?email=test', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }
    public function test_search_by_phone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?phone=08111', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }
    public function test_search_not_found()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=tidakada', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(0, count($response['data']));
        self::assertEquals(0, $response['meta']['total']);
    }
    public function test_search_with_page()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?size=5&page=2', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(5, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
        self::assertEquals(2, $response['meta']['current_page']);
    }
}
