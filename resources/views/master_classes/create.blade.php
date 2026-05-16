@extends('layouts.app')

@section('title', 'Добавить мастер-класс')

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
                    <div style="color: red; margin-bottom: 15px;">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('master-classes.store') }}" method="POST">
                    @csrf

                    <h2>Форма добавления мастер-класса</h2>

                    <div class="form-group">
                        <label for="creativity_type_id">Вид творчества</label>

                        <select name="creativity_type_id" id="creativity_type_id">
                            <option value="">Выберите вид творчества</option>

                            @foreach($types as $type)
                                <option
                                    value="{{ $type->id }}"
                                    @selected(old('creativity_type_id') == $type->id)
                                >
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="title">Название мастер-класса</label>

                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title') }}"
                        >
                    </div>

                    <div class="form-group">
                        <label for="description">Описание мастер-класса</label>

                        <textarea
                            name="description"
                            id="description"
                        >{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="class_date">Дата</label>

                        <input
                            type="date"
                            name="class_date"
                            min="{{ now()->toDateString() }}"
                            id="class_date"
                            value="{{ old('class_date', $selectedDate) }}"
                            onchange="window.location='{{ route('master-classes.create') }}?class_date=' + this.value"
                        >
                    </div>

                    <div class="form-group">
                        <label for="start_time">Время</label>

                        <select name="start_time" id="start_time">
                            <option value="">Выберите время</option>

                            @foreach($allSlots as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @disabled(in_array($value, $busySlots))
                                    @selected(old('start_time') == $value)
                                >
                                    {{ $label }}
                                    @if(in_array($value, $busySlots))
                                        (занято)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="group_size">Количество человек в группе</label>

                        <input
                            type="number"
                            name="group_size"
                            id="group_size"
                            min="1"
                            value="{{ old('group_size') }}"
                        >
                    </div>

                    <div class="form-group">
                        <label for="price">Стоимость мастер-класса</label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="price"
                            id="price"
                            value="{{ old('price') }}"
                        >
                    </div>

                    <div class="form-group">
                        <button class="btn" type="submit">
                            Добавить мастер-класс
                        </button>
                    </div>

                    <p>
                        <a href="{{ route('cabinet') }}">
                            Назад в кабинет
                        </a>
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