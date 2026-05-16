@extends('layouts.app')

@section('title', 'Подтверждение записи')

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
                <a href="{{ route('home') }}">Главная</a>
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
                <h2>Подтверждение записи</h2>

                <div class="form-group">
                    <label>ФИО пользователя</label>
                    <input type="text" value="{{ $user->fio }}" disabled>
                </div>

                <div class="form-group">
                    <label>Вид творчества</label>
                    <input type="text" value="{{ $masterClass->creativityType->name }}" disabled>
                </div>

                <div class="form-group">
                    <label>ФИО мастера</label>
                    <input type="text" value="{{ $masterClass->master->fio }}" disabled>
                </div>

                <div class="form-group">
                    <label>Дата</label>
                    <input type="text" value="{{ $masterClass->class_date }}" disabled>
                </div>

                <div class="form-group">
                    <label>Время</label>
                    <input type="text" value="{{ substr($masterClass->start_time, 0, 5) }}" disabled>
                </div>

                <div class="form-group" style="display:flex; gap:10px;">
                    <form action="{{ route('enrollments.store', $masterClass->id) }}" method="POST">
                        @csrf
                        <button class="btn" type="submit">Подтвердить</button>
                    </form>

                    <form action="{{ route('enrollments.cancel', $masterClass->id) }}" method="POST">
                        @csrf
                        <button class="btn" type="submit">Отмена</button>
                    </form>
                </div>

                <p>
                    <a href="{{ route('creativity.show', $masterClass->creativity_type_id) }}">
                        Назад
                    </a>
                </p>
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