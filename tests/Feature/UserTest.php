<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        $newUser = [
            "name" => "Alex Pitfall",
		    "email" => "alex_pp7@randomemail.com",
		    "password" => "uttdis8766",
		    "password_confirmation" => "uttdis8766"
        ];
        $this->postJson('/api/signup', $newUser);
    }
    
    public function test_post_signup_new_user_and_returns_api_token(): void
    {
        $newUser = [
            "name" => "Jake Arthur",
            "email" => "gf6yh@email.com",
            "password" => "hexagon98_sd",
            "password_confirmation" => "hexagon98_sd"
        ];

        $response = $this->postJson('/api/signup', $newUser);

        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('token')
                     ->whereType('token', 'string')
                     ->has('user', fn(AssertableJson $json) => 
                        $json->where('name', 'Jake Arthur')
                             ->where('email', fn (string $email) => str($email)->is('gf6yh@email.com'))
                             ->missing('password')
                             ->etc()
                     )
            );
    }

    public function test_post_registered_user_can_login_and_receives_new_token()
    {
        $loginDetails = [
		    "email" => "alex_pp7@randomemail.com",
		    "password" => "uttdis8766"
        ];

        $this->postJson('/api/signup', $loginDetails);

        $response = $this->postJson('/api/login', [
            'email' => $loginDetails['email'], 
            'password' => $loginDetails['password']
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('token')
                     ->whereType('token', 'string')
                     ->has('user', fn(AssertableJson $json) => 
                        $json->where('name', 'Alex Pitfall')
                             ->where('email', fn (string $email) => str($email)->is('alex_pp7@randomemail.com'))
                             ->etc()
                     )
            );
    }

}