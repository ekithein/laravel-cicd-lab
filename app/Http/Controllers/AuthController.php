<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fio' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9\s\-\(\)]+$/'],
        ], [
            'fio.required' => 'Введите ФИО.',
            'email.required' => 'Введите email.',
            'email.email' => 'Введите корректный email.',
            'email.unique' => 'Пользователь с таким email уже существует.',
            'password.required' => 'Введите пароль.',
            'password.min' => 'Пароль должен содержать минимум 6 символов.',
            'password.confirmed' => 'Пароли не совпадают.',
            'phone.required' => 'Введите телефон.',
            'phone.regex' => 'Введите корректный номер телефона.',
        ]);

        $user = User::create([
            'fio' => $validated['fio'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => 'visitor',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Регистрация прошла успешно.');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Введите email.',
            'email.email' => 'Введите корректный email.',
            'password.required' => 'Введите пароль.',
        ]);

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Неверный email или пароль.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->role === 'master') {
            return redirect()->route('cabinet');
        }

        return redirect()->route('home')->with('success', 'Вы успешно вошли.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Вы вышли из аккаунта.');
    }
}
