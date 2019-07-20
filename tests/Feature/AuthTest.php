<?php

namespace Tests\Feature;

use App\Http\Controllers\AuthController;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class AuthTest extends TestCase
{
    const JSON_STRUCTURE_LOGIN_RESPONSE = ['access_token', 'token_type', 'expires_in'];

    public function testLogin()
    {
        $user = $this->createUser();

        try {

            $response = $this->login($user);

            $response->assertOk();
            $response->assertJsonStructure(self::JSON_STRUCTURE_LOGIN_RESPONSE);

        } finally {

            $user->roles()->detach();
            $user->forceDelete();
        }
    }

    public function testHighLevelLogin()
    {
        $user = $this->createUser(Role::MASTER);

        try {

            $response = $this->login($user);

            $response->assertOk();
            $response->assertJsonStructure(self::JSON_STRUCTURE_LOGIN_RESPONSE);

        } finally {

            $user->roles()->detach();
            $user->forceDelete();
        }
    }

    public function testAdminLogin()
    {
        $users = [
            $this->createUser(Role::STAFF),
            $this->createUser(Role::ADMIN),
            $this->createUser(Role::MASTER)
        ];

        try {

            foreach($users as $user) {

                $response = $this->login($user, AuthController::REALM_ADMIN);

                $response->assertOk();
                $response->assertJsonStructure(self::JSON_STRUCTURE_LOGIN_RESPONSE);
            }

        } finally {

            foreach($users as $user) {

                $user->roles()->detach();
                $user->forceDelete();
            }
        }
    }

    public function testWrongAccessAdminLogin()
    {
        $user = $this->createUser();

        try {

            $response = $this->login($user, AuthController::REALM_ADMIN);

            $response->assertStatus(401);

        } finally {

            $user->roles()->detach();
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

            $user->roles()->detach();
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

            $user->roles()->detach();
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
            $response->assertJsonStructure(self::JSON_STRUCTURE_LOGIN_RESPONSE);

        } finally {

            $user->roles()->detach();
            $user->forceDelete();
        }
    }

    public function testWrongLogin()
    {
        $user = $this->createUser();

        try {

            $response = $this->login($user, false, 'wrongsecret');

            $response->assertStatus(401);

        } finally {

            $user->roles()->detach();
            $user->forceDelete();
        }
    }

    public function testWithoutToken()
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

            $user->roles()->detach();
            $user->forceDelete();
        }
    }

    public function testWithLogout()
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

            $user->roles()->detach();
            $user->forceDelete();
        }
    }

    private function createUser($roleId = Role::CUSTOMER): User
    {
        $user = factory(User::class)->create();
        $user->roles()->attach($roleId);

        return $user;
    }

    private function login(User $user, $realm = false, $password = 'secret'): TestResponse
    {
        $data = [
            'email' => $user->email,
            'password' => $password
        ];

        if ($realm) {

            $data['realm'] = $realm;
        }

        return $this->json('POST', '/api/auth/login', $data);
    }

    private function generateToken(User $user)
    {
        $response = $this->login($user);
        return $response->decodeResponseJson('access_token');
    }
}
