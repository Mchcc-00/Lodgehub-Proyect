<?php
// dashboard_data.php - Funciones para obtener datos del dashboard
require_once '../../config/conexionGlobal.php';

class DashboardData {
    private $db;
    
    public function __construct() {
        $this->db = conexionDB();
    }
    
    // MÉTODOS PARA RESERVAS
    public function getReservasHoyInician() {
        try {
            $sql = "SELECT COUNT(*) as count FROM tp_reservas 
                   WHERE DATE(fechainicio) = CURDATE() AND estado = 'Activa'";
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
            $sql = "SELECT COUNT(*) as count FROM tp_reservas 
                   WHERE DATE(fechaFin) = CURDATE() AND estado = 'Activa'";
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
            $sql = "SELECT COUNT(*) as count FROM tp_reservas WHERE estado = 'Activa'";
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
            $sql = "SELECT COUNT(*) as count FROM tp_reservas WHERE estado = 'Pendiente'";
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
            $sql = "SELECT COUNT(*) as count FROM tp_reservas 
                   WHERE estado IN ('Cancelada', 'Finalizada')";
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
            $sql = "SELECT COUNT(*) as count FROM tp_mantenimiento WHERE estado = 'Pendiente'";
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
            // Asumiendo que "En Proceso" sería mantenimiento pendiente con prioridad alta
            // o que se ha actualizado recientemente
            $sql = "SELECT COUNT(*) as count FROM tp_mantenimiento 
                   WHERE estado = 'Pendiente' AND prioridad = 'Alto'";
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
            $sql = "SELECT COUNT(*) as count FROM tp_mantenimiento WHERE estado = 'Finalizado'";
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
            $sql = "SELECT COUNT(*) as count FROM tp_pqrs 
                   WHERE prioridad = 'Alto' AND estado = 'Pendiente'";
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
            // Asumiendo que "Gravedad Media" son PQRS pendientes de prioridad baja 
            // pero con cierta antigüedad
            $sql = "SELECT COUNT(*) as count FROM tp_pqrs 
                   WHERE prioridad = 'Bajo' AND estado = 'Pendiente' 
                   AND DATEDIFF(CURDATE(), DATE(fechaRegistro)) > 2";
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
            $sql = "SELECT COUNT(*) as count FROM tp_pqrs 
                   WHERE prioridad = 'Bajo' AND estado = 'Pendiente'
                   AND DATEDIFF(CURDATE(), DATE(fechaRegistro)) <= 2";
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
            $sql = "SELECT COUNT(*) as count FROM tp_pqrs WHERE estado = 'Finalizado'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Error getPQRSRespondidos: " . $e->getMessage());
            return 0;
        }
    }
    
    // MÉTODO PARA OBTENER TODOS LOS DATOS DE UNA VEZ
    public function getAllDashboardData() {
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
            ]
        ];
    }
}
?>