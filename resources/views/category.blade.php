@php
    /** @var \App\Models\CreativityType $type */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\CreativityType[] $allTypes */
@endphp

@extends('layouts.app')

@section('title')
    {{ $type->name }}
@endsection

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
                    <a href="{{ route('home') }}">Главная</a>
                @else
                    <a href="{{ route('login') }}">Вход</a>
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

    <div class="main">
        <div class="row">
            <div class="hover"></div>

            <div class="title">{{ $type->name }}</div>

            <div class="row--small grid between">
                <div class="content">
                    <img src="{{ asset('template/img/elifant.png') }}" alt="{{ $type->name }}">
                    <p>{{ $type->description }}</p>
                </div>

                <ul class="menu">
                    @foreach($allTypes as $menuType)
                        @php
                            /** @var \App\Models\CreativityType $menuType */
                        @endphp

                        <li>
                            <a href="{{ route('creativity.show', $menuType->id) }}">
                                {{ $menuType->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            @if(session('success'))
                <div class="row" style="margin-top: 20px;">
                    <div class="row--small">
                        <p style="color: green;">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="row" style="margin-top: 20px;">
                    <div class="row--small">
                        <p style="color: red;">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="row shedule">
                <div class="row--small">
                    <h2>Расписание</h2>

                    <div class="drivers">
                        @forelse($type->masterClasses as $masterClass)
                            @php
                                /** @var \App\Models\MasterClass $masterClass */

                                $freePlaces = $masterClass->group_size - $masterClass->enrollments->count();

                                $alreadyEnrolled = false;
                                $hasClassAtSameTime = false;

                                if (auth()->check() && auth()->user()->role === 'visitor') {
                                    $alreadyEnrolled = $masterClass->enrollments->contains('user_id', auth()->id());

                                    $hasClassAtSameTime = auth()->user()
                                        ->enrolledMasterClasses()
                                        ->where('class_date', $masterClass->class_date)
                                        ->where('start_time', $masterClass->start_time)
                                        ->exists();
                                }
                            @endphp

                            <div class="driver grid">
                                <div class="driver-left grid">
                                    <div class="driver-photo">
                                        <img src="{{ asset('template/img/driver1.png') }}" alt="Ведущий">
                                    </div>

                                    <div class="driver-text">
                                        <div class="driver-name">{{ $masterClass->master->fio }}</div>

                                        <div class="driver-desc">
                                            <strong>{{ $masterClass->title }}</strong><br>
                                            {{ $masterClass->description }}<br><br>
                                            Стоимость: {{ $masterClass->price }} руб.<br>
                                            Свободных мест: {{ $freePlaces }}
                                        </div>
                                    </div>
                                </div>

                                <div class="driver-right">
                                    @auth
                                        @if(auth()->user()->role === 'visitor')
                                            @if($alreadyEnrolled)
                                                <div class="driver-btn" style="background:#ccc; pointer-events:none;">
                                                    Вы записаны
                                                </div>
                                            @elseif($hasClassAtSameTime)
                                                <div class="driver-btn" style="background:#ccc; pointer-events:none;">
                                                    Занято время
                                                </div>
                                            @elseif($freePlaces <= 0)
                                                <div class="driver-btn" style="background:#ccc; pointer-events:none;">
                                                    Мест нет
                                                </div>
                                            @else
                                                <a href="{{ route('enrollments.confirm', $masterClass->id) }}">
                                                    <button class="driver-btn">записаться</button>
                                                </a>
                                            @endif
                                        @endif
                                    @endauth

                                    <div class="driver-time">
                                        {{ \Carbon\Carbon::parse($masterClass->class_date)->format('d.m.Y') }}
                                        {{ substr($masterClass->start_time, 0, 5) }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p>Для этого вида творчества пока нет мастер-классов.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
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