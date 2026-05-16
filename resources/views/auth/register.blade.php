@extends('layouts.app')

@section('title', 'Регистрация')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('template/css/styles.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('template/css/responsive.css') }}">
@endsection

@section('content')
    <div class="header">
        <div class="row grid middle between">
            <div class="logo">
                <img src="{{ asset('template/img/logo.png') }}" alt="Логотип">
            </div>

            <div class="title">
                Клуб любителей творчества «ОчУмелые ручки»
            </div>

            <div class="auth">
                <a href="{{ route('login') }}">Вход</a>
            </div>
        </div>
    </div>

    <div class="row row--nogutter">
        <div class="menu-burger">
            <div class="burger">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>

    <div class="row row--nogutter top-line">
        <div class="line"></div>
    </div>

    <div class="main">
        <div class="row">
            <div class="row--small">
                <form action="{{ route('register.submit') }}" method="POST">
                    @csrf

                    <h2>Форма регистрации</h2>

                    @if($errors->any())
                        <div style="color: red; margin-bottom: 15px;">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="fio">ФИО</label>
                        <input type="text" name="fio" id="fio" value="{{ old('fio') }}">
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}">
                    </div>

                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" name="password" id="password">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Повторите пароль</label>
                        <input type="password" name="password_confirmation" id="password_confirmation">
                    </div>

                    <div class="form-group">
                        <label for="phone">Номер телефона</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}">
                    </div>

                    <div class="form-group">
                        <button class="btn" type="submit">Зарегистрироваться</button>
                    </div>

                    <p>
                        Уже есть аккаунт?
                        <a href="{{ route('login') }}">Войти</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <div class="row row--nogutter">
        <div class="line"></div>
    </div>

    <div class="footer">
        <div class="row">
            <div class="row--small grid between">
                <div class="address">Наш адрес: ВДНХ, 120в</div>
                <div class="tel">Тел: 89123456765</div>
                <div class="copy">(с) Copyright, 2017</div>
            </div>
        </div>
    </div>
@endsection