<?php
/**
 * 🔐 Sistema de Autenticación MultiTienda - JSON Storage
 */

// Iniciar sesión solo si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'storage.php';

// Función global de manejo de errores
if (!function_exists('handleError')) {
    function handleError($error) {
        error_log($error);
        if (getenv('APP_ENV') === 'development' || $_GET['debug'] ?? false) {
            echo "<div style='background: #fee; color: #c33; padding: 1rem; border: 1px solid #fcc; border-radius: 4px; margin: 1rem;'>";
            echo "<strong>Error de desarrollo:</strong> " . htmlspecialchars($error);
            echo "</div>";
        }
    }
}

class Auth {
    
    public function login($email, $password) {
        $user = storage()->find('users', 'email', $email);
        
        if ($user && $user['status'] === 'active' && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }
        return false;
    }
    
    public function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getUser() {
        if (!$this->isLoggedIn()) return null;
        
        return storage()->find('users', 'id', $_SESSION['user_id']);
    }
    
    public function hasRole($role) {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }
    
    public function requireRole($role) {
        $this->requireLogin();
        if (!$this->hasRole($role)) {
            die("Acceso denegado. Se requiere rol: $role");
        }
    }
}

// Función global para crear instancia de Auth
function auth() {
    static $auth = null;
    if ($auth === null) {
        $auth = new Auth();
    }
    return $auth;
}
?>