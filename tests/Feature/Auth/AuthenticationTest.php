<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationTest extends TestCase
{
    /**
     * @return void
     */
    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_users_can_logout()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $this->actingAs($user, 'api')->postJson('/auth/logout', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertGuest();
    }
}
