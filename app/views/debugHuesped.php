<?php
// Archivo temporal para debug - coloca esto al inicio de tu HuespedController.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log para debugging
function logDebug($mensaje, $datos = null) {
    $log = "[" . date('Y-m-d H:i:s') . "] " . $mensaje;
    if ($datos) {
        $log .= " - Datos: " . print_r($datos, true);
    }
    error_log($log . "\n", 3, "debug_huesped.log");
}

// Test de conexión
function testConexion() {
    try {
        require_once __DIR__ . '/../../config/conexionGlobal.php';
        $db = conexionDB();
        
        if ($db) {
            echo "✅ Conexión exitosa\n";
            
            // Test de la tabla
            $stmt = $db->prepare("DESCRIBE tp_huespedes");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "📋 Estructura de la tabla:\n";
            foreach ($columns as $column) {
                echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
            }
            
        } else {
            echo "❌ Error de conexión\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

// Ejecutar test si se llama directamente
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    testConexion();
}
?>