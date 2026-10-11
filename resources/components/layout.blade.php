@props(['title' => 'Trade & Business SAS - Plataforma BL'])
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @livewireStyles
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f5f7;
            margin: 0;
            color: #1a1a1a;
        }
        header.topbar {
            background: #1e3a5f;
            color: #fff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        header.topbar .brand { font-weight: bold; font-size: 15px; }
        main { max-width: 1000px; margin: 24px auto; padding: 0 16px; }
        .card { background: #fff; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,.1); padding: 24px; }
        .btn {
            display: inline-block; padding: 8px 18px; border-radius: 4px; border: none;
            font-size: 14px; cursor: pointer; font-weight: bold;
        }
        .btn-primary { background: #1e6fb8; color: #fff; }
        .btn-secondary { background: #e2e5e9; color: #333; }
        .btn-success { background: #2fae4e; color: #fff; }
        .btn-danger { background: #c0392b; color: #fff; }
        input, select, textarea {
            width: 100%; padding: 7px 9px; border: 1px solid #ccc; border-radius: 4px;
            font-size: 13px; font-family: inherit;
        }
        label { display: block; font-size: 12px; font-weight: bold; margin-bottom: 3px; color: #444; }
        .field { margin-bottom: 14px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .error { color: #c0392b; font-size: 11px; margin-top: 3px; }
        .alert-success { background: #eafaf0; color: #1a7a3c; border: 1px solid #bfe8cc; padding: 10px 14px; border-radius: 4px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <header class="topbar">
        <div style="display:flex; align-items:center; gap:24px;">
            <div class="brand">
                @auth
                    {{ auth()->user()->activeCompany()->short_name ?? 'Plataforma BL' }}
                @else
                    Plataforma BL
                @endauth
            </div>
            @auth
                <nav style="display:flex; gap:16px;">
                    <a href="{{ route('dashboard') }}" style="color:#fff; font-size:13px; text-decoration:none; opacity:.9;">Dashboard</a>
                    <a href="{{ route('bl.index') }}" style="color:#fff; font-size:13px; text-decoration:none; opacity:.9;">BL</a>
                    @can('users.manage')
                        <a href="{{ route('users.index') }}" style="color:#fff; font-size:13px; text-decoration:none; opacity:.9;">Usuarios</a>
                    @endcan
                    <a href="{{ route('profile.edit') }}" style="color:#fff; font-size:13px; text-decoration:none; opacity:.9;">Mi perfil</a>
                    @if (auth()->user()->companies()->count() > 1)
                        <a href="{{ route('company.select') }}" style="color:#fff; font-size:13px; text-decoration:none; opacity:.9;">Cambiar empresa</a>
                    @endif
                </nav>
            @endauth
        </div>
        <div style="display:flex; align-items:center; gap:14px;">
            @auth
                @php $unread = auth()->user()->unreadNotifications; @endphp
                <div style="position:relative;">
                    <a href="{{ route('notifications.index') }}" style="color:#fff; text-decoration:none; font-size:16px; position:relative;">
                        🔔
                        @if ($unread->count() > 0)
                            <span style="position:absolute; top:-6px; right:-10px; background:#c0392b; color:#fff; font-size:10px; font-weight:bold; border-radius:10px; padding:1px 5px;">
                                {{ $unread->count() }}
                            </span>
                        @endif
                    </a>
                </div>
                <span>{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="background:none; border:1px solid rgba(255,255,255,.4); color:#fff; padding:5px 14px; border-radius:4px; font-size:12px; cursor:pointer;">
                        Cerrar sesión
                    </button>
                </form>
            @endauth
        </div>
    </header>
    <main>
        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div style="background:#fdecea; color:#c0392b; border:1px solid #f5c6cb; padding:10px 14px; border-radius:4px; margin-bottom:16px;">{{ session('error') }}</div>
        @endif
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>
