<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- <title>Kooponline.com</title>--}}
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('imgs/favicon.png')}}">

    {!! Meta::toHtml() !!}

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.2.4/dist/cdn.min.js"></script>
    
    @vite('resources/css/app.css')

    <style>
        
        [x-cloak] {
            display: none !important;
        }

        <style>
        [x-cloak] {
            display: none !important;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        input:checked~.dot {
            transform: translateX(100%);
            background-color: #48bb78;
        }

        /* Toggle B */
        input:checked~.dot {
            transform: translateX(100%);
            background-color: #48bb78;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #c4c4c4;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #21BEDE;
            border-radius: 25px;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            color: rgb(229 231 235);
            right: 0;
            position: absolute;
            display: flex;
            padding-right: 0.75rem
        }
    
    </style>

    @livewireStyles

</head>

<body class="h-screen w-full font-sans bg-mb custom-scrollbar ">
    
    @include('layouts.navigation')

    <div class="z-0">
        @yield('content')
    </div>

    @include('layouts.footer')

    @livewireScripts

</body>

</html>