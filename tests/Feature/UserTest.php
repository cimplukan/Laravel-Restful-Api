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

    public function test_update_password_succes()
    {
        $this->seed([UserSeeder::class]);
        $user_old = User::where("username", "test")->first();

        $this->patch(
            "/api/users/current",
            [
                "password" => "password"
            ],
            ["Authorization" => "test"]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test"
                ]
            ]);

        $user_new = User::where("username", "test")->first();
        self::assertNotEquals($user_old->password, $user_new->password);
    }

    public function test_update_name_succes()
    {
        $this->seed([UserSeeder::class]);
        $user_old = User::where("username", "test")->first();

        $this->patch(
            "/api/users/current",
            [
                "name" => "Ganti Nama"
            ],
            ["Authorization" => "test"]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "Ganti Nama"
                ]
            ]);

        $user_new = User::where("username", "test")->first();
        self::assertNotEquals($user_old->name, $user_new->name);
    }

    public function test_update_failed()
    {
        $this->seed([UserSeeder::class]);

        $this->patch(
            "/api/users/current",
            ["name" => "Lorem ipsum dolor sit amet consectetur, adipisicing elit. Totam iusto omnis reprehenderit harum excepturi nisi corrupti deserunt ab accusamus, at fuga laudantium voluptate illum quas animi consequatur vero quaerat et qui natus perspiciatis voluptatum eligendi. Ex praesentium minima perferendis laudantium fuga, nemo repudiandae corrupti, vel cum veniam incidunt dolores ratione dolorum et nihil ullam qui iure dicta tempora necessitatibus facere. Similique voluptas quaerat labore ad, quam sunt repellendus accusamus doloremque ullam quisquam, voluptates possimus commodi iure facilis nisi et perspiciatis quae qui alias. Aliquid quibusdam natus qui eaque blanditiis inventore nisi. Quibusdam tempora recusandae deserunt totam eos vitae dolorem saepe! ccc"],
            ["Authorization" => "test"]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field must not be greater than 100 characters."
                    ]
                ]
            ]);
    }

    public function test_logout_success()
    {
        $this->seed([UserSeeder::class]);

        $this->delete("/api/users/logout", [], [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);

        $user = User::where("username", "test")->first();
        self::assertNull($user->token);
    }

    public function test_logout_failed()
    {
        $this->seed([UserSeeder::class]);

        $this->delete("/api/users/logout", [], [
            "Authorization" => "salah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Unauthorized"
                    ]
                ]
            ]);
    }
}
