<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GateKeeper - Smart Employee Check-in System')</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            min-height: 100dvh;
            overflow-x: hidden;
        }
        
        body {
            display: flex;
            flex-direction: column;
        }
        .floating-shape {
            position: fixed;
            border-radius: 50%;
            opacity: 0.08;
            pointer-events: none;
            z-index: 0;
        }
        .floating-shape-1 {
            width: 400px;
            height: 400px;
            background: #3b82f6;
            top: -150px;
            right: -100px;
        }
        .floating-shape-2 {
            width: 300px;
            height: 300px;
            background: #8b5cf6;
            bottom: -100px;
            left: -100px;
        }
        .floating-shape-3 {
            width: 200px;
            height: 200px;
            background: #06b6d4;
            top: 50%;
            right: -50px;
            transform: translateY(-50%);
        }
        .floating-shape-4 {
            width: 150px;
            height: 150px;
            background: #f59e0b;
            bottom: 20%;
            right: 20%;
        }
        .floating-shape-5 {
            width: 100px;
            height: 100px;
            background: #ec4899;
            top: 20%;
            left: 10%;
        }
        .main-wrapper {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 100vh;
            min-height: 100dvh;
            position: relative;
            z-index: 1;
        }
        .content-wrapper {
            flex: 1;
            position: relative;
            z-index: 1;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-linear-to-br from-primary-50 via-white to-purple-50">
    <div class="floating-shape floating-shape-1"></div>
    <div class="floating-shape floating-shape-2"></div>
    <div class="floating-shape floating-shape-3"></div>
    <div class="floating-shape floating-shape-4"></div>
    <div class="floating-shape floating-shape-5"></div>
    
    <div class="main-wrapper">
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>