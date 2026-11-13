<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JadStock &mdash; CV. Jada Team Sedaya</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulseRing {
            0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.3); }
            50%      { box-shadow: 0 0 0 12px rgba(59, 130, 246, 0); }
        }
        @keyframes progressFill {
            from { width: 0; }
            to   { width: 100%; }
        }
        .fade-up      { animation: fadeInUp 0.7s ease-out forwards; }
        .fade-up-d1   { animation: fadeInUp 0.7s ease-out 0.25s both; }
        .fade-up-d2   { animation: fadeInUp 0.7s ease-out 0.5s  both; }
        .logo-pulse   { animation: pulseRing 2.5s ease-in-out infinite; }
        .progress-bar { animation: progressFill 5s linear forwards; }
    </style>
</head>
<body class="bg-blue-900 min-h-screen flex flex-col items-center justify-center overflow-hidden relative">

    {{-- Logo --}}
    <div class="fade-up logo-pulse w-28 h-28 rounded-2xl bg-blue-800/60 ring-1 ring-blue-700/60 flex items-center justify-center p-5 mb-8">
        <img src="{{ asset('logo.webp') }}" alt="JadStock" class="w-full h-full object-contain">
    </div>

    {{-- Title --}}
    <h1 class="fade-up-d1 text-5xl sm:text-6xl font-bold text-white tracking-tight mb-3">
        JadStock
    </h1>

    {{-- Company name --}}
    <p class="fade-up-d2 text-slate-300 text-base sm:text-lg tracking-wide">
        CV. Jada Team Sedaya
    </p>

    {{-- Progress bar --}}
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-950/60">
        <div class="progress-bar h-full bg-blue-400"></div>
    </div>

    <script>
        setTimeout(function () {
            window.location.href = '{{ route("login") }}';
        }, 5000);
    </script>

</body>
</html>
