{{--
    Main layout for the Borrower Trust Score app: shared navigation and flash UI.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | {{ config('app.name') }}</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="brand">
                <div class="brand-mark">BTS</div>
                <div>
                    <div class="brand-title">{{ config('app.name') }}</div>
                    <div class="brand-sub">Borrower trust, simplified</div>
                </div>
            </div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('borrowers.index') }}" class="{{ request()->routeIs('borrowers.*') ? 'active' : '' }}">Borrowers</a>
                <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.*') ? 'active' : '' }}">Items</a>
                <a href="{{ route('loans.index') }}" class="{{ request()->routeIs('loans.*') ? 'active' : '' }}">Loans</a>
            </nav>
        </div>
    </header>

    <main class="container">
        @if (session('success') || session('error'))
            <div class="flash-stack">
                @if (session('success'))
                    <div class="flash flash-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="flash flash-error">{{ session('error') }}</div>
                @endif
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
