<?php
/**
 * 📁 Sistema de Almacenamiento JSON - MultiTienda
 * Funciona sin base de datos, usando archivos JSON
 */

class JsonStorage {
    private $dataDir = 'data';
    
    public function __construct() {
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
        
        // Crear archivos iniciales si no existen
        $this->initializeData();
    }
    
    private function initializeData() {
        // Usuarios por defecto
        if (!file_exists($this->dataDir . '/users.json')) {
            $defaultUsers = [
                [
                    'id' => 1,
                    'email' => 'admin@multitienda.com',
                    'password' => password_hash('admin123', PASSWORD_DEFAULT),
                    'name' => 'Super Administrator',
                    'role' => 'super_admin',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 2,
                    'email' => 'tienda1@demo.com',
                    'password' => password_hash('demo123', PASSWORD_DEFAULT),
                    'name' => 'Admin Tienda Demo',
                    'role' => 'admin',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            $this->save('users', $defaultUsers);
        }
        
        // Tiendas por defecto
        if (!file_exists($this->dataDir . '/stores.json')) {
            $defaultStores = [
                [
                    'id' => 1,
                    'admin_id' => 2,
                    'name' => 'Tienda Electrónica Demo',
                    'slug' => 'electronica-demo',
                    'description' => 'La mejor tienda de productos electrónicos',
                    'theme_color' => '#667eea',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            $this->save('stores', $defaultStores);
        }
        
        // Productos por defecto
        if (!file_exists($this->dataDir . '/products.json')) {
            $defaultProducts = [
                [
                    'id' => 1,
                    'store_id' => 1,
                    'name' => 'Smartphone Premium',
                    'slug' => 'smartphone-premium',
                    'description' => 'El último modelo con tecnología avanzada',
                    'price' => 999.99,
                    'stock' => 50,
                    'status' => 'active',
                    'featured' => true,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 2,
                    'store_id' => 1,
                    'name' => 'Laptop Gaming',
                    'slug' => 'laptop-gaming',
                    'description' => 'Perfecta para gaming y trabajo profesional',
                    'price' => 1299.99,
                    'stock' => 25,
                    'status' => 'active',
                    'featured' => false,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            $this->save('products', $defaultProducts);
        }
        
        // Pedidos por defecto
        if (!file_exists($this->dataDir . '/orders.json')) {
            $this->save('orders', []);
        }
    }
    
    public function load($table) {
        $file = $this->dataDir . '/' . $table . '.json';
        if (!file_exists($file)) {
            return [];
        }
        
        $content = file_get_contents($file);
        return json_decode($content, true) ?: [];
    }
    
    public function save($table, $data) {
        $file = $this->dataDir . '/' . $table . '.json';
        return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    public function find($table, $field, $value) {
        $data = $this->load($table);
        foreach ($data as $item) {
            if (isset($item[$field]) && $item[$field] == $value) {
                return $item;
            }
        }
        return null;
    }
    
    public function findAll($table, $field = null, $value = null) {
        $data = $this->load($table);
        if ($field === null) {
            return $data;
        }
        
        $result = [];
        foreach ($data as $item) {
            if (isset($item[$field]) && $item[$field] == $value) {
                $result[] = $item;
            }
        }
        return $result;
    }
    
    public function insert($table, $item) {
        $data = $this->load($table);
        
        // Auto-increment ID
        $maxId = 0;
        foreach ($data as $existing) {
            if (isset($existing['id']) && $existing['id'] > $maxId) {
                $maxId = $existing['id'];
            }
        }
        $item['id'] = $maxId + 1;
        
        // Add timestamp
        if (!isset($item['created_at'])) {
            $item['created_at'] = date('Y-m-d H:i:s');
        }
        
        $data[] = $item;
        $this->save($table, $data);
        return $item;
    }
    
    public function update($table, $id, $updates) {
        $data = $this->load($table);
        
        foreach ($data as &$item) {
            if ($item['id'] == $id) {
                foreach ($updates as $key => $value) {
                    $item[$key] = $value;
                }
                $item['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        
        $this->save($table, $data);
        return $this->find($table, 'id', $id);
    }
    
    public function delete($table, $id) {
        $data = $this->load($table);
        
        $data = array_filter($data, function($item) use ($id) {
            return $item['id'] != $id;
        });
        
        // Re-index array
        $data = array_values($data);
        $this->save($table, $data);
        return true;
    }
    
    public function count($table, $field = null, $value = null) {
        $data = $this->findAll($table, $field, $value);
        return count($data);
    }
}

// Función global
function storage() {
    static $storage = null;
    if ($storage === null) {
        $storage = new JsonStorage();
    }
    return $storage;
}
?>