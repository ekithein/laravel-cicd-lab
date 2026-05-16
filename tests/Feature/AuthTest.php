<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_register(): void
    {
        $response = $this->post(route('register.submit'), [
            'fio' => 'Иван Иванов',
            'email' => 'ivan@example.com',
            'phone' => '+79991234567',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'fio' => 'Иван Иванов',
            'email' => 'ivan@example.com',
            'phone' => '+79991234567',
            'role' => 'visitor',
        ]);

        $this->assertAuthenticated();
    }

    public function test_visitor_can_login(): void
    {
        $user = User::create([
            'fio' => 'Петр Петров',
            'email' => 'petya@example.com',
            'password' => Hash::make('password'),
            'phone' => '+79990000000',
            'role' => 'visitor',
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'petya@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_master_redirects_to_cabinet_after_login(): void
    {
        $master = User::create([
            'fio' => 'Мастер Иванов',
            'email' => 'master@example.com',
            'password' => Hash::make('password'),
            'phone' => '+79991111111',
            'role' => 'master',
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'master@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('cabinet'));
        $this->assertAuthenticatedAs($master);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::create([
            'fio' => 'Петр Петров',
            'email' => 'petya@example.com',
            'password' => Hash::make('password'),
            'phone' => '+79990000000',
            'role' => 'visitor',
        ]);

        $response = $this->from(route('login'))->post(route('login.submit'), [
            'email' => 'petya@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
