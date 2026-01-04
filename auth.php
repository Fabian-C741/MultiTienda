<?php
/**
 * 🔐 Sistema de Autenticación MultiTienda - JSON Storage
 */

session_start();
require_once 'storage.php';

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