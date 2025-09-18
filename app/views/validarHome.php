<?php
// validarHome.php - Validación y configuración para homepage con sistema multi-hotel
require_once __DIR__ . '/../../config/conexionGlobal.php';

class DashboardData {
    private $db;
    private $hotel_id;
    private $user_role;
    
    public function __construct($hotel_id = null, $user_role = 'Usuario') {
        $this->db = conexionDB();
        $this->hotel_id = $hotel_id;
        $this->user_role = $user_role;
    }
    
    /**
     * Construye la condición WHERE para filtrar por hotel
     */
    private function getHotelFilter() {
        // Super administradores pueden ver todo
        if ($this->user_role === 'Administrador' && empty($this->hotel_id)) {
            return "";
        }
        
        // Otros usuarios solo ven su hotel
        if ($this->hotel_id) {
            return " AND id_hotel = " . intval($this->hotel_id);
        }
        
        // Si no hay hotel asignado y no es super admin, no mostrar nada
        return " AND 1=0";
    }
    
    // MÉTODOS PARA RESERVAS
    public function getReservasHoyInician() {
        try {
            $hotelFilter = $this->getHotelFilter();
            $sql = "SELECT COUNT(*) as count FROM tp_reservas 
                   WHERE DATE(fechainicio) = CURDATE() AND estado = 'Activa' {$hotelFilter}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Error getReservasHoyInician: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getReservasHoyTerminan() {
        try {
            $hotelFilter = $this->getHotelFilter();
            $sql = "SELECT COUNT(*) as count FROM tp_reservas 
                   WHERE DATE(fechaFin) = CURDATE() AND estado = 'Activa' {$hotelFilter}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Error getReservasHoyTerminan: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getReservasActivas() {
        try {
            $hotelFilter = $this->getHotelFilter();
            $sql = "SELECT COUNT(*) as count FROM tp_reservas WHERE estado = 'Activa' {$hotelFilter}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Error getReservasActivas: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getReservasPendientes() {
        try {
            $hotelFilter = $this->getHotelFilter();
            $sql = "SELECT COUNT(*) as count FROM tp_reservas WHERE estado = 'Pendiente' {$hotelFilter}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Error getReservasPendientes: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getReservasInactivas() {
        try {
            $hotelFilter = $this->getHotelFilter();
            $sql = "SELECT COUNT(*) as count FROM tp_reservas 
                   WHERE estado IN ('Cancelada', 'Finalizada') {$hotelFilter}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Error getReservasInactivas: " . $e->getMessage());
            return 0;
        }
    }
    
    // MÉTODOS PARA MANTENIMIENTO
    public function getMantenimientoPendientes() {
        try {
            $hotelFilter = $this->getHotelFilter();
            $sql = "SELECT COUNT(*) as count FROM tp_mantenimiento WHERE estado = 'Pendiente' {$hotelFilter}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Error getMantenimientoPendientes: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getMantenimientoEnProceso() {
        try {
            $hotelFilter = $this->getHotelFilter();
            // Mantenimiento de alta prioridad pendiente se considera "en proceso"
            $sql = "SELECT COUNT(*) as count FROM tp_mantenimiento 
                   WHERE estado = 'Pendiente' AND prioridad = 'Alto' {$hotelFilter}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Error getMantenimientoEnProceso: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getMantenimientoFinalizados() {
        try {
            $hotelFilter = $this->getHotelFilter();
            $sql = "SELECT COUNT(*) as count FROM tp_mantenimiento WHERE estado = 'Finalizado' {$hotelFilter}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Error getMantenimientoFinalizados: " . $e->getMessage());
            return 0;
        }
    }
    
    // MÉTODOS PARA PQRS
    public function getPQRSGravedadAlta() {
        try {
            $hotelFilter = $this->getHotelFilter();
            $sql = "SELECT COUNT(*) as count FROM tp_pqrs 
                   WHERE prioridad = 'Alto' AND estado = 'Pendiente' {$hotelFilter}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Error getPQRSGravedadAlta: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getPQRSGravedadMedia() {
        try {
            $hotelFilter = $this->getHotelFilter();
            // PQRS de prioridad baja pero con más de 2 días de antigüedad
            $sql = "SELECT COUNT(*) as count FROM tp_pqrs 
                   WHERE prioridad = 'Bajo' AND estado = 'Pendiente' 
                   AND DATEDIFF(CURDATE(), DATE(fechaRegistro)) > 2 {$hotelFilter}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Error getPQRSGravedadMedia: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getPQRSGravedadBaja() {
        try {
            $hotelFilter = $this->getHotelFilter();
            $sql = "SELECT COUNT(*) as count FROM tp_pqrs 
                   WHERE prioridad = 'Bajo' AND estado = 'Pendiente'
                   AND DATEDIFF(CURDATE(), DATE(fechaRegistro)) <= 2 {$hotelFilter}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Error getPQRSGravedadBaja: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getPQRSRespondidos() {
        try {
            $hotelFilter = $this->getHotelFilter();
            $sql = "SELECT COUNT(*) as count FROM tp_pqrs WHERE estado = 'Finalizado' {$hotelFilter}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Error getPQRSRespondidos: " . $e->getMessage());
            return 0;
        }
    }
    
    // MÉTODO ADICIONAL: Obtener estadísticas de habitaciones por hotel
    public function getEstadisticasHabitaciones() {
        try {
            $hotelFilter = $this->getHotelFilter();
            $sql = "SELECT 
                        COUNT(*) as total_habitaciones,
                        SUM(CASE WHEN estado = 'Disponible' THEN 1 ELSE 0 END) as disponibles,
                        SUM(CASE WHEN estado = 'Ocupada' THEN 1 ELSE 0 END) as ocupadas,
                        SUM(CASE WHEN estado = 'Reservada' THEN 1 ELSE 0 END) as reservadas,
                        SUM(CASE WHEN estado = 'Mantenimiento' THEN 1 ELSE 0 END) as en_mantenimiento
                    FROM tp_habitaciones 
                    WHERE estadoMantenimiento = 'Activo' {$hotelFilter}";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error getEstadisticasHabitaciones: " . $e->getMessage());
            return [
                'total_habitaciones' => 0,
                'disponibles' => 0,
                'ocupadas' => 0,
                'reservadas' => 0,
                'en_mantenimiento' => 0
            ];
        }
    }
    
    // MÉTODO PARA OBTENER TODOS LOS DATOS DE UNA VEZ
    public function getAllDashboardData() {
        $habitaciones = $this->getEstadisticasHabitaciones();
        
        return [
            'reservas' => [
                'hoy_inician' => $this->getReservasHoyInician(),
                'hoy_terminan' => $this->getReservasHoyTerminan(),
                'activas' => $this->getReservasActivas(),
                'pendientes' => $this->getReservasPendientes(),
                'inactivas' => $this->getReservasInactivas()
            ],
            'mantenimiento' => [
                'pendientes' => $this->getMantenimientoPendientes(),
                'en_proceso' => $this->getMantenimientoEnProceso(),
                'finalizados' => $this->getMantenimientoFinalizados()
            ],
            'pqrs' => [
                'gravedad_alta' => $this->getPQRSGravedadAlta(),
                'gravedad_media' => $this->getPQRSGravedadMedia(),
                'gravedad_baja' => $this->getPQRSGravedadBaja(),
                'respondidos' => $this->getPQRSRespondidos()
            ],
            'habitaciones' => $habitaciones,
            'hotel_info' => [
                'id' => $this->hotel_id,
                'filtrado_por_hotel' => !empty($this->hotel_id)
            ]
        ];
    }
}

/**
 * Función para obtener el hotel actual del usuario logueado
 */
function obtenerHotelActualUsuario() {
    // Verificar si hay sesión iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user'])) {
        return null;
    }
    
    $usuario = $_SESSION['user'];
    $hotelInfo = null;
    
    try {
        switch ($usuario['roles']) {
            case 'Administrador':
                // La información del hotel activo ya está en la sesión desde el login.
                // No es necesario hacer más consultas a la base de datos aquí.
                if (isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id'])) {
                    $hotelInfo = [
                        'id' => $_SESSION['hotel_id'],
                        'nombre' => $_SESSION['hotel_nombre'] ?? 'Nombre no disponible',
                        'nit' => $_SESSION['hotel']['nit'] ?? 'NIT no disponible',
                        'direccion' => $_SESSION['hotel']['direccion'] ?? 'Dirección no disponible',
                        'telefono' => $_SESSION['hotel']['telefono'] ?? 'N/A',
                        'correo' => $_SESSION['hotel']['correo'] ?? 'N/A',
                        'foto' => $_SESSION['hotel']['foto'] ?? null,
                        'descripcion' => $_SESSION['hotel']['descripcion'] ?? '',
                        'tipo_admin' => $_SESSION['tipo_admin'] ?? 'hotel'
                    ];
                } else {
                    // Es un Super Administrador sin hotel asignado. No se carga ningún hotel.
                    // Esto permite que el homepage muestre el banner para crear el primer hotel.
                    $hotelInfo = null;
                }
                break;
                
            case 'Colaborador':
                // La lógica para colaboradores también puede usar la sesión directamente.
                if (isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id'])) {
                    $hotelInfo = $_SESSION['hotel'] ?? null;
                    $hotelInfo['tipo_admin'] = 'colaborador';
                }
                break;
                
            case 'Usuario':
                // Los usuarios finales no tienen hotel asignado por defecto
                $hotelInfo = null;
                break;
        }
        
    } catch (Exception $e) {
        error_log("Error obtenerHotelActualUsuario: " . $e->getMessage());
        $hotelInfo = null;
    }
    
    return $hotelInfo;
}

/**
 * Función para validar si el usuario puede acceder al dashboard del hotel
 */
function validarAccesoHotel($hotel_id = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user'])) {
        return false;
    }
    
    $usuario = $_SESSION['user'];
    
    // Super administradores pueden acceder a cualquier hotel
    if ($usuario['roles'] === 'Administrador' && (!isset($_SESSION['hotel_id']) || empty($_SESSION['hotel_id']))) {
        return true;
    }
    
    // Otros usuarios solo pueden acceder a su hotel asignado
    if ($hotel_id && isset($_SESSION['hotel_id'])) {
        return intval($hotel_id) === intval($_SESSION['hotel_id']);
    }
    
    return isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id']);
}

/**
 * Función para obtener información de contexto del usuario y hotel
 */
function obtenerContextoUsuarioHotel() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Simplificado: Usar directamente la información de la sesión.
    $hotelInfo = $_SESSION['hotel'] ?? null;
    $tipoAdmin = $_SESSION['tipo_admin'] ?? null;

    // Añadir el tipo de admin a la información del hotel para usarlo en el banner
    if ($hotelInfo && $tipoAdmin) {
        $hotelInfo['tipo_admin'] = $tipoAdmin;
    }
    
    return [
        'usuario' => $_SESSION['user'] ?? null,
        'hotel' => $hotelInfo,
        'puede_gestionar_multiples_hoteles' => isset($_SESSION['tipo_admin']) && $_SESSION['tipo_admin'] === 'super',
        'hotel_id_filtro' => $hotelInfo ? $hotelInfo['id'] : null,
        'rol_usuario' => $_SESSION['user']['roles'] ?? 'Usuario'
    ];
}
?>