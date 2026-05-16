@extends('layouts.app')

@section('title', 'Очумелые ручки')

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
                @auth
                    @if(auth()->user()->role === 'master')
                        <a href="{{ route('cabinet') }}">Кабинет</a>
                    @else
                        <span>{{ auth()->user()->fio }}</span>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" style="border:none; background:none; cursor:pointer;">
                            Выход
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Вход</a> /
                    <a href="{{ route('register') }}">Регистрация</a>
                @endauth
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
            <div class="hover"></div>

            <div class="title">ОчУмелые ручки</div>

            <div class="row--small grid between">
                <div class="content">
                    @if(session('success'))
                        <p style="color: green;">{{ session('success') }}</p>
                    @endif

                    <h2>О компании</h2>

                    <p>
                        Компания «Очумелые ручки» проводит мастер-классы по различным видам творчества.
                        На нашем сайте вы можете познакомиться с направлениями занятий, посмотреть расписание
                        и записаться на интересующий мастер-класс.
                    </p>

                    <h2>Виды творчества</h2>

                    @if($types->isEmpty())
                        <p>Виды творчества пока не добавлены.</p>
                    @else
                        <ul>
                            @foreach($types as $type)
                                <li>
                                    <a href="{{ route('creativity.show', $type->id) }}">
                                        {{ $type->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @auth
                        @if(auth()->user()->role === 'visitor')
                            <h2>Мои записи на мастер-классы</h2>

                            @if($enrolledMasterClasses->isEmpty())
                                <p>Вы пока никуда не записаны.</p>
                            @else
                                <table class="driver-page-table">
                                    <tbody>
                                        @foreach($enrolledMasterClasses as $masterClass)
                                            <tr>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($masterClass->class_date)->format('d.m.Y') }}
                                                    {{ substr($masterClass->start_time, 0, 5) }}
                                                </td>
                                                <td>
                                                    <b>{{ $masterClass->title }}</b><br>
                                                    Вид творчества: {{ $masterClass->creativityType->name }}<br>
                                                    Ведущий: {{ $masterClass->master->fio }}<br>
                                                    Стоимость: {{ $masterClass->price }} руб.
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        @endif
                    @endauth
                </div>

                <ul class="menu">
                    @foreach($types as $type)
                        <li>
                            <a href="{{ route('creativity.show', $type->id) }}">
                                {{ $type->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
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