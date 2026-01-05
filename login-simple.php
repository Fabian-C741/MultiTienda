<?php
/**
 * 🏪 Login Simple - Sin dependencias problemáticas
 */

// Manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión de manera segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'storage.php';

// Función de auth simplificada
class SimpleAuth {
    public function login($email, $password) {
        $storage = new JsonStorage();
        $user = $storage->find('users', 'email', $email);
        
        if ($user && $user['status'] === 'active' && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }
        return false;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getUser() {
        if (!$this->isLoggedIn()) return null;
        $storage = new JsonStorage();
        return $storage->find('users', 'id', $_SESSION['user_id']);
    }
    
    public function logout() {
        session_destroy();
    }
}

$auth = new SimpleAuth();

// Manejo del POST de login
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email y contraseña son requeridos';
    } else {
        try {
            if ($auth->login($email, $password)) {
                $user = $auth->getUser();
                $success = "¡Login exitoso! Bienvenido " . $user['name'];
                
                // Mostrar enlaces de navegación en lugar de redirect automático
                $dashboardUrl = ($user['role'] === 'super_admin' ? '/super-admin' : '/admin');
            } else {
                $error = 'Credenciales incorrectas';
            }
        } catch (Exception $e) {
            $error = 'Error del sistema: ' . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Simple - MultiTienda</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .logo {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            width: 100%;
            padding: 0.75rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #5a6fd8;
        }
        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        .alert-success {
            background: #efe;
            color: #363;
            border: 1px solid #cfc;
        }
        .dashboard-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            padding: 0.75rem;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .dashboard-link:hover {
            background: #218838;
        }
        .test-accounts {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
            text-align: center;
        }
        .test-accounts h4 {
            margin: 0 0 0.5rem 0;
            color: #666;
        }
        .test-accounts small {
            display: block;
            color: #888;
            margin-bottom: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🏪 MultiTienda Pro</div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <strong>❌ Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <strong>✅ <?= htmlspecialchars($success) ?></strong>
            </div>
            <a href="<?= $dashboardUrl ?>" class="dashboard-link">
                🚀 Ir al Dashboard
            </a>
        <?php endif; ?>
        
        <?php if (!$success): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="email">📧 Email</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           placeholder="admin@multitienda.com">
                </div>
                <div class="form-group">
                    <label for="password">🔒 Contraseña</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="••••••••">
                </div>
                <button type="submit">🔐 Iniciar Sesión</button>
            </form>
            
            <div class="test-accounts">
                <h4>Cuentas de Prueba</h4>
                <small><strong>Super Admin:</strong> admin@multitienda.com / admin123</small>
                <small><strong>Admin Tienda:</strong> tienda1@demo.com / demo123</small>
            </div>
        <?php endif; ?>
        
        <?php if ($auth->isLoggedIn()): ?>
            <div style="margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                <strong>Estado de sesión:</strong><br>
                Usuario: <?= $_SESSION['user_name'] ?? 'N/A' ?><br>
                Role: <?= $_SESSION['user_role'] ?? 'N/A' ?><br>
                <a href="/logout" style="color: #dc3545; text-decoration: none;">🚪 Cerrar Sesión</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>