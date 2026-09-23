<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserLockoutTest extends TestCase
{
    public function test_user_is_not_locked_by_default()
    {
        $user = new User([
            'status' => 'active',
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);

        $this->assertFalse($user->isLocked());
    }

    public function test_user_is_locked_when_locked_until_is_in_the_future()
    {
        $user = new User([
            'status' => 'locked',
            'failed_login_attempts' => 5,
            'locked_until' => now()->addMinutes(15),
        ]);

        $this->assertTrue($user->isLocked());
    }

    public function test_user_is_not_locked_when_lockout_has_expired()
    {
        $user = new User([
            'status' => 'active',
            'failed_login_attempts' => 5,
            'locked_until' => now()->subMinutes(1),
        ]);

        $this->assertFalse($user->isLocked());
    }

    public function test_lockout_seconds_remaining_calculation()
    {
        $user = new User([
            'status' => 'locked',
            'locked_until' => now()->addSeconds(600),
        ]);

        $seconds = $user->lockoutSecondsRemaining();
        $this->assertGreaterThan(580, $seconds);
        $this->assertLessThanOrEqual(600, $seconds);
    }

    public function test_security_headers_middleware()
    {
        $middleware = new \App\Http\Middleware\SecurityHeadersMiddleware();
        $request = \Illuminate\Http\Request::create('/login', 'GET');
        $response = $middleware->handle($request, function ($req) {
            return new \Illuminate\Http\Response('OK');
        });

        $this->assertEquals('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
        $this->assertEquals('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertEquals('1; mode=block', $response->headers->get('X-XSS-Protection'));
        $this->assertEquals('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
    }
}
