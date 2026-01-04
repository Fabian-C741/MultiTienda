<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MultiTienda - Plataforma Multi-tenant</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: white;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }
        
        .logo {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            margin-top: 2rem;
        }
        
        .subtitle {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin: 3rem 0;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 15px 30px;
            background: rgba(255,255,255,0.15);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 12px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            display: inline-block;
        }
        
        .btn:hover {
            background: rgba(255,255,255,0.25);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .welcome-message {
            margin: 3rem 0;
            padding: 2.5rem;
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .welcome-message h3 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        
        .welcome-message p {
            font-size: 1.1rem;
            line-height: 1.6;
        }
        
        .tenants-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 3rem;
        }
        
        .tenant-card {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 25px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }
        
        .tenant-card:hover {
            transform: translateY(-8px);
            background: rgba(255,255,255,0.15);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        
        .tenant-name {
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .tenant-domain {
            font-size: 1rem;
            opacity: 0.8;
            margin-bottom: 20px;
            background: rgba(255,255,255,0.1);
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
        }
        
        .footer {
            margin-top: 4rem;
            font-size: 0.9rem;
            opacity: 0.7;
            line-height: 1.5;
        }
        
        .section-title {
            font-size: 2rem;
            margin: 3rem 0 2rem 0;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🏪 MultiTienda</div>
        <div class="subtitle">Plataforma Multi-tenant para E-commerce</div>
        
        <div class="actions">
            <a href="/central/dashboard" class="btn">🔧 Panel Central</a>
            <a href="/central/tenants" class="btn">🏪 Gestionar Tiendas</a>
            <a href="/tiendas" class="btn">📋 Ver Todas las Tiendas</a>
        </div>
        
        @if(isset($tenants) && $tenants->count() > 0)
            <div class="section-title">🛍️ Tiendas Disponibles</div>
            <div class="tenants-grid">
                @foreach($tenants as $tenant)
                    <div class="tenant-card">
                        <div class="tenant-name">{{ $tenant->name }}</div>
                        <div class="tenant-domain">{{ $tenant->domain }}</div>
                        <a href="/tienda/{{ $tenant->slug }}" class="btn">
                            🚀 Visitar Tienda
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="welcome-message">
                <h3>👋 ¡Bienvenido a MultiTienda!</h3>
                <p>Esta es tu plataforma multi-tenant para crear y gestionar múltiples tiendas online.</p>
                <p><strong>Aún no hay tiendas creadas.</strong> Ve al panel central para crear tu primera tienda.</p>
            </div>
        @endif
        
        <div class="footer">
            <p><strong>MultiTienda</strong> - Sistema Multi-tenant para comercio electrónico</p>
            <p>Powered by Laravel {{ app()->version() }} • PHP {{ phpversion() }}</p>
        </div>
    </div>
</body>
</html>