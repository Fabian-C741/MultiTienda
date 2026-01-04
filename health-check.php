<?php
/**
 * 🧪 MultiTienda Health Check & Testing Suite
 * Ejecuta pruebas automatizadas para validar el sistema
 */

class MultiTiendaHealthCheck {
    private $results = [];
    private $baseUrl;
    
    public function __construct($baseUrl = null) {
        $this->baseUrl = $baseUrl ?: 'https://' . $_SERVER['HTTP_HOST'];
    }
    
    public function runAllTests() {
        echo "<h1>🧪 MultiTienda Health Check</h1>";
        echo "<p><strong>Testing URL:</strong> {$this->baseUrl}</p>";
        echo "<hr>";
        
        $this->testFileStructure();
        $this->testPHPSyntax();
        $this->testRoutes();
        $this->testContent();
        $this->testSecurity();
        
        $this->displayResults();
    }
    
    private function testFileStructure() {
        $this->addTest("📁 File Structure", function() {
            $requiredFiles = [
                'index.php',
                'multitienda-simple.php',
                '.htaccess'
            ];
            
            foreach ($requiredFiles as $file) {
                if (!file_exists(__DIR__ . '/' . $file)) {
                    return "❌ Missing file: $file";
                }
            }
            
            // Check file permissions
            if (!is_readable(__DIR__ . '/multitienda-simple.php')) {
                return "❌ multitienda-simple.php not readable";
            }
            
            return "✅ All required files present and readable";
        });
    }
    
    private function testPHPSyntax() {
        $this->addTest("🔍 PHP Syntax", function() {
            $phpFiles = glob(__DIR__ . '/*.php');
            $errors = [];
            
            foreach ($phpFiles as $file) {
                $output = shell_exec("php -l \"$file\" 2>&1");
                if (strpos($output, 'No syntax errors') === false) {
                    $errors[] = basename($file) . ": " . trim($output);
                }
            }
            
            if (empty($errors)) {
                return "✅ No PHP syntax errors found";
            } else {
                return "❌ PHP syntax errors: " . implode('; ', $errors);
            }
        });
    }
    
    private function testRoutes() {
        $routes = [
            '/' => 'Home page',
            '/central' => 'Central dashboard',
            '/central/tenants' => 'Tenants page',
            '/central/stats' => 'Stats page'
        ];
        
        foreach ($routes as $route => $name) {
            $this->addTest("🔗 Route: $name", function() use ($route) {
                $url = $this->baseUrl . $route;
                
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 10,
                        'ignore_errors' => true
                    ]
                ]);
                
                $content = @file_get_contents($url, false, $context);
                
                if ($content === false) {
                    return "❌ Route unreachable: $route";
                }
                
                // Check for PHP errors in content
                if (strpos($content, 'Fatal error') !== false || 
                    strpos($content, 'Parse error') !== false) {
                    return "❌ PHP errors in response";
                }
                
                return "✅ Route accessible and working";
            });
        }
    }
    
    private function testContent() {
        $contentTests = [
            ['/', 'MultiTienda', 'Home page title'],
            ['/central', 'Panel Central', 'Central dashboard header'],
            ['/central/tenants', 'Gestionar Tiendas', 'Tenants page header'],
            ['/central/stats', 'Estadísticas', 'Stats page header']
        ];
        
        foreach ($contentTests as [$route, $expectedText, $description]) {
            $this->addTest("📄 Content: $description", function() use ($route, $expectedText) {
                $url = $this->baseUrl . $route;
                
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 10,
                        'ignore_errors' => true
                    ]
                ]);
                
                $content = @file_get_contents($url, false, $context);
                
                if ($content === false) {
                    return "❌ Cannot fetch content from $route";
                }
                
                if (strpos($content, $expectedText) === false) {
                    return "❌ Expected text '$expectedText' not found";
                }
                
                return "✅ Content validation passed";
            });
        }
    }
    
    private function testSecurity() {
        $this->addTest("🔒 Security Check", function() {
            $issues = [];
            
            // Check for exposed sensitive files
            $sensitiveFiles = ['.env', 'composer.json'];
            foreach ($sensitiveFiles as $file) {
                $url = $this->baseUrl . '/' . $file;
                $headers = @get_headers($url);
                if ($headers && strpos($headers[0], '200') !== false) {
                    $issues[] = "Exposed file: $file";
                }
            }
            
            // Check for error disclosure
            $testUrl = $this->baseUrl . '/non-existent-page-test';
            $content = @file_get_contents($testUrl, false, stream_context_create([
                'http' => ['ignore_errors' => true]
            ]));
            
            if ($content && (strpos($content, $_SERVER['DOCUMENT_ROOT']) !== false ||
                           strpos($content, 'stack trace') !== false)) {
                $issues[] = "Error information disclosure";
            }
            
            if (empty($issues)) {
                return "✅ No obvious security issues found";
            } else {
                return "⚠️ Security concerns: " . implode('; ', $issues);
            }
        });
    }
    
    private function addTest($name, $testFunction) {
        try {
            $startTime = microtime(true);
            $result = $testFunction();
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);
            
            $this->results[] = [
                'name' => $name,
                'result' => $result,
                'duration' => $duration . 'ms',
                'passed' => strpos($result, '✅') === 0
            ];
        } catch (Exception $e) {
            $this->results[] = [
                'name' => $name,
                'result' => "❌ Error: " . $e->getMessage(),
                'duration' => '0ms',
                'passed' => false
            ];
        }
    }
    
    private function displayResults() {
        $passed = array_filter($this->results, function($r) { return $r['passed']; });
        $failed = array_filter($this->results, function($r) { return !$r['passed']; });
        
        echo "<h2>📊 Test Results Summary</h2>";
        echo "<p><strong>Passed:</strong> " . count($passed) . " | ";
        echo "<strong>Failed:</strong> " . count($failed) . " | ";
        echo "<strong>Total:</strong> " . count($this->results) . "</p>";
        
        if (count($failed) == 0) {
            echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h3>🎉 All tests passed!</h3>";
            echo "<p>Your MultiTienda system is working correctly.</p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h3>⚠️ Some tests failed</h3>";
            echo "<p>Please review the failed tests below and fix the issues.</p>";
            echo "</div>";
        }
        
        echo "<h3>📋 Detailed Results</h3>";
        echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse;'>";
        echo "<tr style='background: #f8f9fa;'>";
        echo "<th>Test</th><th>Result</th><th>Duration</th>";
        echo "</tr>";
        
        foreach ($this->results as $result) {
            $bgColor = $result['passed'] ? '#d4edda' : '#f8d7da';
            echo "<tr style='background: $bgColor;'>";
            echo "<td>{$result['name']}</td>";
            echo "<td>{$result['result']}</td>";
            echo "<td>{$result['duration']}</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        echo "<hr>";
        echo "<p><small>Test completed at: " . date('Y-m-d H:i:s') . "</small></p>";
        
        // Return JSON result for automation
        if (isset($_GET['json'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'summary' => [
                    'total' => count($this->results),
                    'passed' => count($passed),
                    'failed' => count($failed),
                    'success' => count($failed) == 0
                ],
                'details' => $this->results
            ]);
            exit;
        }
    }
}

// Auto-run if accessed directly
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    $healthCheck = new MultiTiendaHealthCheck();
    $healthCheck->runAllTests();
}
?>