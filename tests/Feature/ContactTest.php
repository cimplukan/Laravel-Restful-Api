<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function test_create_success()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            "/api/contacts/create",
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
            "/api/contacts/create",
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
            "/api/contacts/create",
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
}
