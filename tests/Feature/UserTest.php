<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_register_success()
    {
        $this->post("/api/users", [
            "username" => "ucok",
            "password" => "rahasia",
            "name" => "Ucok Tralala",
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "username" => "ucok",
                    "name" => "Ucok Tralala",
                ]
            ]);
    }

    public function test_register_failed()
    {
        $this->post("/api/users", [
            "username" => "",
            "password" => "",
            "name" => "",
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => ["The username field is required."],
                    "password" => ["The password field is required."],
                    "name" => ["The name field is required."],
                ]
            ]);
    }

    public function test_register_username_already_exists()
    {
        $this->test_register_success();
        $this->post("/api/users", [
            "username" => "ucok",
            "password" => "rahasia",
            "name" => "Ucok Tralala",
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "The username is already registered."
                    ]
                ]
            ]);
    }

    public function test_login_success()
    {
        $this->seed([UserSeeder::class]);
        $this->post("/api/users/login", [
            "username" => "test",
            "password" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test"
                ]
            ]);

        $user = User::where("username", "test")->first();
        self::assertNotNull($user->token);
    }

    public function test_login_failed_username_not_found()
    {
        $this->post("/api/users/login", [
            "username" => "test",
            "password" => "test"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => ["Username or password wrong."]
                ]
            ]);
    }

    public function test_login_failed_password_wrong()
    {
        $this->seed([UserSeeder::class]);
        $this->post("/api/users/login", [
            "username" => "test",
            "password" => "salah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => ["Username or password wrong."]
                ]
            ]);
    }

    public function test_get_success()
    {
        $this->seed([UserSeeder::class]);
        $this->get("/api/users/current", [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test"
                ]
            ]);
    }

    public function test_get_unauthorizated()
    {
        $this->seed([UserSeeder::class]);
        $this->get("/api/users/current")->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => ["Unauthorized"]
                ]
            ]);
    }
    public function test_get_invalid_token()
    {
        $this->seed([UserSeeder::class]);
        $this->get("/api/users/current", [
            "Authorization" => "salah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => ["Unauthorized"]
                ]
            ]);
    }
}
