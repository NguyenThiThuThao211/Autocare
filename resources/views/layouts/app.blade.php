<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AutoCare')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                navy: '#0B2545', navylight: '#123B6B',
                aiblue: '#3B82F6', aipurple: '#4438C9', aipurplebg: '#EEF2FF',
            }}}
        }
    </script>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800">

    @include('partials.header')

    @yield('content')

    @include('partials.footer')

    {{-- Widget chatbot + hotline nổi — hiện trên MỌI trang khách hàng --}}
    @include('partials.chatbot-widget')
    @include('partials.hotline-widget')

    @stack('scripts')
</body>
</html>