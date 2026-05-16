@extends('layouts.app')

@section('title', 'Редактирование мастер-класса')

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
                <a href="{{ route('cabinet') }}">Кабинет</a>
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

                @if($errors->any())
                    <div style="color:red; margin-bottom: 15px;">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('master-classes.update', $masterClass->id) }}" method="POST">
                    @csrf

                    <h2>Редактирование мастер-класса</h2>

                    <div class="form-group">
                        <label>Название мастер-класса</label>
                        <input
                            type="text"
                            value="{{ $masterClass->title }}"
                            disabled
                        >
                    </div>

                    <div class="form-group">
                        <label>Дата</label>
                        <input
                            type="text"
                            value="{{ $masterClass->class_date }}"
                            disabled
                        >
                    </div>

                    <div class="form-group">
                        <label>Время</label>
                        <input
                            type="text"
                            value="{{ substr($masterClass->start_time, 0, 5) }}"
                            disabled
                        >
                    </div>

                    <div class="form-group">
                        <label for="description">Описание</label>
                        <textarea
                            name="description"
                            id="description"
                        >{{ old('description', $masterClass->description) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="price">Стоимость</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="price"
                            id="price"
                            value="{{ old('price', $masterClass->price) }}"
                        >
                    </div>

                    <div class="form-group">
                        <button class="btn" type="submit">
                            Сохранить изменения
                        </button>
                    </div>

                    <p>
                        <a href="{{ route('cabinet') }}">Назад в кабинет</a>
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