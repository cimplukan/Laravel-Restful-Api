<?php

namespace Tests\Feature;

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
}
