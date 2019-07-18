<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function testLogin()
    {
        $user = $this->createUser();

        try {

            $response = $this->login($user);

            $response->assertOk();
            $response->assertJsonStructure(['access_token', 'token_type', 'expires_in']);

        } finally {

            $user->forceDelete();
        }
    }

    public function testMe()
    {
        $user = $this->createUser();

        try {

            $token = $this->generateToken($user);

            $response = $this->withHeader('Authorization',  'Bearer ' . $token)
                ->json('GET', '/api/auth/me');

            $response->assertOk();
            $response->assertJsonStructure(['id', 'name', 'email', 'created_at', 'updated_at']);
            $response->assertJson([
                'name' => $user->name,
                'email' => $user->email
            ]);

        } finally {

            $user->forceDelete();
        }
    }

    public function testLogout()
    {
        $user = $this->createUser();

        try {

            $token = $this->generateToken($user);

            $response = $this->withHeader('Authorization',  'Bearer ' . $token)
                ->json('GET', '/api/auth/logout');

            $response->assertOk();

        } finally {

            $user->forceDelete();
        }
    }

    public function testRefresh()
    {
        $user = $this->createUser();

        try {

            $token = $this->generateToken($user);

            $response = $this->withHeader('Authorization',  'Bearer ' . $token)
                ->json('GET', '/api/auth/refresh');

            $response->assertOk();
            $response->assertJsonStructure(['access_token', 'token_type', 'expires_in']);

        } finally {

            $user->forceDelete();
        }
    }

    public function testWrongLogin()
    {
        $user = $this->createUser();

        try {

            $response = $this->login($user, 'wrongsecret');

            $response->assertStatus(401);

        } finally {

            $user->forceDelete();
        }
    }

    public function testWhithoutToken()
    {
        $user = $this->createUser();

        try {

            // me
            $response = $this->flushHeaders()->json('GET', '/api/auth/me');
            $response->assertStatus(401);

            // refresh
            $response = $this->json('GET', '/api/auth/refresh');
            $response->assertStatus(500);

            // logout
            $response = $this->json('GET', '/api/auth/logout');
            $response->assertStatus(401);

        } finally {

            $user->forceDelete();
        }
    }

    public function testWhithLogout()
    {
        $user = $this->createUser();

        try {

            // generate token and logout
            $token = $this->generateToken($user);
            $this->withHeader('Authorization',  'Bearer ' . $token)
                ->json('GET', '/api/auth/logout');

            // me
            $response = $this->withHeader('Authorization',  'Bearer ' . $token)
                ->json('GET', '/api/auth/me');
            $response->assertStatus(401);

            // refresh
            $response = $this->withHeader('Authorization',  'Bearer ' . $token)
                ->json('GET', '/api/auth/refresh');
            $response->assertStatus(500);

            // logout
            $response = $this->withHeader('Authorization',  'Bearer ' . $token)
                ->json('GET', '/api/auth/logout');
            $response->assertStatus(401);

        } finally {

            $user->forceDelete();
        }
    }

    private function createUser(): User
    {
        return factory(User::class)->create();
    }

    private function login(User $user, $password = 'secret'): TestResponse
    {
        return $this->json('POST', '/api/auth/login', [
            'email' => $user->email,
            'password' => $password
        ]);
    }

    private function generateToken(User $user)
    {
        $response = $this->login($user);
        return $response->decodeResponseJson('access_token');
    }
}
