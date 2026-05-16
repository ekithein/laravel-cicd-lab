@php
    /** @var \App\Models\CreativityType $type */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\CreativityType[] $allTypes */
@endphp

@extends('layouts.app')

@section('title', 'Личный кабинет')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('template/css/styles.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('template/css/responsive.css') }}">
@endsection

@section('body_class', 'dp')

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

    <div class="main">
        <div class="row">
            <div class="hover"></div>
            <div class="title">Личный кабинет</div>

            <div class="row--small grid between">
                <div class="content driver-page">
                    @if(session('success'))
                        <p style="color: green;">{{ session('success') }}</p>
                    @endif

                    <div class="driver-page-photo">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Фото ведущего">
                        @else
                            <img src="{{ asset('template/img/driver-page.png') }}" alt="Фото ведущего">
                        @endif
                    </div>

                    <div class="driver-page-name">
                        {{ $user->fio }}
                    </div>

                    <div class="driver-page-text">
                        <div class="driver-page-my">Мои мастер-классы</div>

                        @if($masterClasses->isEmpty())
                            <p>У вас пока нет мастер-классов.</p>
                        @else
                            <table class="driver-page-table">
                                <tbody>
                                    @foreach($masterClasses as $masterClass)
                                        <tr>
                                            <td>
                                                {{ \Carbon\Carbon::parse($masterClass->class_date)->format('d.m.Y') }}
                                                {{ substr($masterClass->start_time, 0, 5) }}
                                            </td>

                                            <td>
                                                <b>{{ $masterClass->title }}</b><br>
                                                Вид творчества: {{ $masterClass->creativityType->name }}<br>
                                                Описание: {{ $masterClass->description }}<br>
                                                Стоимость: {{ $masterClass->price }} руб.<br>
                                                Размер группы: {{ $masterClass->group_size }}<br>
                                                Записано участников: {{ $masterClass->participants->count() }}<br>

                                                <p>
                                                    <a href="{{ route('master-classes.edit', $masterClass->id) }}">
                                                        Редактировать
                                                    </a>
                                                </p>

                                                @if($masterClass->participants->isEmpty())
                                                    <p>Пока никто не записался.</p>
                                                @else
                                                    @foreach($masterClass->participants as $index => $participant)
                                                        <p>
                                                            {{ $index + 1 }}. {{ $participant->fio }}<br>
                                                            email: {{ $participant->email }}<br>
                                                            tel: {{ $participant->phone }}
                                                        </p>
                                                    @endforeach
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    <div class="driver-page-btn-wrapper">
                        <a href="{{ route('master-classes.create') }}">
                            <div class="driver-page-btn btn">
                                Добавить мастер-класс
                            </div>
                        </a>
                    </div>
                </div>

                <ul class="menu">
                    @foreach($allTypes ?? [] as $menuType)
                        <li>
                            <a href="{{ route('creativity.show', $menuType->id) }}">
                                {{ $menuType->name }}
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