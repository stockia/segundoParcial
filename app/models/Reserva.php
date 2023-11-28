<?php
class Reserva {
    public $id;
    public $tipoCliente;
    public $numeroCliente;
    public $fechaEntrada;
    public $fechaSalida;
    public $tipoHabitacion;
    public $importeTotal;
    public $estado;

    // public function InsertarReserva() {
    //     $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
    //     $estado = "activo";
    //     $response = "";

    //     $consulta = $objAccesoDatos->RetornarConsulta(
    //         "INSERT INTO reservas (tipoCliente, numeroCliente, fechaEntrada, fechaSalida, tipoHabitacion, importeTotal, estado) 
    //         VALUES (:tipoCliente, :numeroCliente, :fechaEntrada, :fechaSalida, :tipoHabitacion, :importeTotal, :estado)"
    //     );

    //     $consulta->bindValue(':tipoCliente', $this->tipoCliente, PDO::PARAM_STR);
    //     $consulta->bindValue(':numeroCliente', $this->numeroCliente, PDO::PARAM_INT);
    //     $consulta->bindValue(':fechaEntrada', $this->fechaEntrada, PDO::PARAM_STR);
    //     $consulta->bindValue(':fechaSalida', $this->fechaSalida, PDO::PARAM_STR);
    //     $consulta->bindValue(':tipoHabitacion', $this->tipoHabitacion, PDO::PARAM_STR);
    //     $consulta->bindValue(':importeTotal', $this->importeTotal, PDO::PARAM_STR);
    //     $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);

    //     $consulta->execute();

    //     $id = $objAccesoDatos->RetornarUltimoIdInsertado();

    //     if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    //         $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

    //         $nuevoNombreArchivo = $this->tipoCliente . $this->numeroCliente . $id . "." . $extension;
    
    //         $rutaDestino = __DIR__ . '/../ImagenesDeReservas2023/' . $nuevoNombreArchivo;
    
    //         if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
    //             $response = true;
    //         } else {
    //             return false;
    //         }
    //     } else {
    //         return false;
    //     }

    //     return $response;
    // }

    public function InsertarReserva() {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $estado = "activo";
        $response = "";
        $tipoCliente = "";
    
        $consultaCliente = $objAccesoDatos->RetornarConsulta(
            "SELECT tipoCliente FROM clientes WHERE id = :numeroCliente"
        );
        $consultaCliente->bindValue(':numeroCliente', $this->numeroCliente, PDO::PARAM_INT);
        $consultaCliente->execute();
    
        $resultado = $consultaCliente->fetch(PDO::FETCH_ASSOC);
    
        if ($resultado) {
            $tipoCliente = $resultado['tipoCliente'];
    
            $consulta = $objAccesoDatos->RetornarConsulta(
                "INSERT INTO reservas (tipoCliente, numeroCliente, fechaEntrada, fechaSalida, tipoHabitacion, importeTotal, estado) 
                VALUES (:tipoCliente, :numeroCliente, :fechaEntrada, :fechaSalida, :tipoHabitacion, :importeTotal, :estado)"
            );
    
            $consulta->bindValue(':tipoCliente', $tipoCliente, PDO::PARAM_STR);
            $consulta->bindValue(':numeroCliente', $this->numeroCliente, PDO::PARAM_INT);
            $consulta->bindValue(':fechaEntrada', $this->fechaEntrada, PDO::PARAM_STR);
            $consulta->bindValue(':fechaSalida', $this->fechaSalida, PDO::PARAM_STR);
            $consulta->bindValue(':tipoHabitacion', $this->tipoHabitacion, PDO::PARAM_STR);
            $consulta->bindValue(':importeTotal', $this->importeTotal, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
    
            $consulta->execute();
    
            $id = $objAccesoDatos->RetornarUltimoIdInsertado();
    
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
                $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    
                $nuevoNombreArchivo = $this->tipoCliente . $this->numeroCliente . $id . "." . $extension;
        
                $rutaDestino = __DIR__ . '/../ImagenesDeReservas2023/' . $nuevoNombreArchivo;
        
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
                    $response = true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
            return $response;
        } else {
            return false;
        }
    }
    
    
    public function BorrarReserva() {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta(
            "UPDATE reservas
            SET estado = 'cancelada'
            WHERE id = :id 
            AND tipoCliente = :tipoCliente
            AND numeroCliente = :numeroCliente"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':tipoCliente', $this->tipoCliente, PDO::PARAM_STR);
        $consulta->bindValue(':numeroCliente', $this->numeroCliente, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public static function TraerTodasLasReservas() {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM reservas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
    }

    // public function AjustarReserva() {
    //     $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
    //     $estado = "ajustada";

    //     $consulta = $objAccesoDatos->RetornarConsulta(
    //         "UPDATE reservas
    //         SET importeTotal = :importeTotal, 
    //             estado = :estado
    //         WHERE id = :id"
    //     );

    //     $consulta->bindValue(':importeTotal', $this->importeTotal, PDO::PARAM_STR);
    //     $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
    //     $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);

    //     $consulta->execute();

    //     return $response;
    // }
    public function AjustarReserva($motivo) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $estado = "ajustada";
        $importeNuevo = $this->importeTotal;
        $idReserva = $this->id;
    
        $consulta = $objAccesoDatos->RetornarConsulta(
            "UPDATE reservas
            SET importeTotal = :importeTotal, 
                estado = :estado
            WHERE id = :id"
        );
    
        $consulta->bindValue(':importeTotal', $this->importeTotal, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
    
        $consulta->execute();

        if ($consulta->rowCount() > 0) {
            $consultaAjuste = $objAccesoDatos->RetornarConsulta(
                "INSERT INTO ajuste_reservas (idReserva, motivo, importeNuevo) 
                VALUES (:idReserva, :motivo, :importeNuevo)"
            );
        
            $consultaAjuste->bindValue(':idReserva', $this->id, PDO::PARAM_INT);
            // $consultaAjuste->bindValue(':motivo', $this->motivo, PDO::PARAM_STR);
            $consultaAjuste->bindValue(':motivo', $motivo, PDO::PARAM_STR);
            $consultaAjuste->bindValue(':importeNuevo', $importeNuevo, PDO::PARAM_STR);
        
            $consultaAjuste->execute();
    
            if ($consulta->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    
    }

    public static function buscarReservaTipoHabitacion($tipoHabitacion) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        
        $consulta = $objAccesoDatos->RetornarConsulta(
            "SELECT * 
            FROM reservas 
            WHERE tipoHabitacion = :tipoHabitacion"
        );

        $consulta->bindValue(':tipoHabitacion', $tipoHabitacion, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Reserva');
    }

    public static function TraerUnaReserva($id) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        
        $consulta = $objAccesoDatos->RetornarConsulta(
            "SELECT * FROM reservas WHERE id = :id"
        );

        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Reserva');
    }

    public static function TraerReservasPorFechaParticular($fecha) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        
        $consulta = $objAccesoDatos->RetornarConsulta(
            "SELECT SUM(importeTotal) AS importeTotal
            FROM reservas 
            WHERE fechaEntrada = :fecha"
        );

        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->execute();
        
        // return $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return $resultado['importeTotal'];
    }

    public static function TraerReservasPorTipoHabitacion($tipoHabitacion) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        
        $consulta = $objAccesoDatos->RetornarConsulta(
            "SELECT * 
            FROM reservas 
            WHERE tipoHabitacion = :tipoHabitacion"
        );

        $consulta->bindValue(':tipoHabitacion', $tipoHabitacion, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
    }

    public static function TraerReservasPorCliente($numeroCliente) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        
        $consulta = $objAccesoDatos->RetornarConsulta(
            "SELECT * 
            FROM reservas 
            WHERE numeroCliente = :numeroCliente"
        );

        $consulta->bindValue(':numeroCliente', $numeroCliente, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
    }

    public static function TraerReservasPorRangoDeFechas($fechaDesde, $fechaHasta) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        
        $consulta = $objAccesoDatos->RetornarConsulta(
            "SELECT * 
            FROM reservas 
            WHERE fechaEntrada >= :fechaDesde 
            AND fechaSalida <= :fechaHasta"
        );

        $consulta->bindValue(':fechaDesde', $fechaDesde, PDO::PARAM_STR);
        $consulta->bindValue(':fechaHasta', $fechaHasta, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
    }

    public function BuscarCancelacionFechaUsuario() {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $estado = "cancelada";
        
        $consulta = $objAccesoDatos->RetornarConsulta(
            "SELECT * 
            FROM reservas 
            WHERE estado = 'cancelada'
            AND fechaEntrada = :fechaEntrada
            AND tipoCliente = :tipoCliente"
        );

        $consulta->bindValue(':fechaEntrada', $this->fechaEntrada, PDO::PARAM_STR);
        $consulta->bindValue(':tipoCliente', $this->tipoCliente, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
    }

    public function BuscarCancelacionesPorCliente() {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $estado = "cancelada";
        
        $consulta = $objAccesoDatos->RetornarConsulta(
            "SELECT * 
            FROM reservas 
            WHERE estado = 'cancelada'
            AND numeroCliente = :numeroCliente"
        );

        $consulta->bindValue(':numeroCliente', $this->numeroCliente, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
    }

    public static function TraerCancelacionesPorRangoDeFechas($fechaEntrada, $fechaSalida) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $estado = "cancelada";
        
        $consulta = $objAccesoDatos->RetornarConsulta(
            "SELECT * 
            FROM reservas 
            WHERE estado = 'cancelada'
            AND fechaEntrada >= :fechaEntrada 
            AND fechaSalida <= :fechaSalida"
        );

        $consulta->bindValue(':fechaEntrada', $fechaEntrada, PDO::PARAM_STR);
        $consulta->bindValue(':fechaSalida', $fechaSalida, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
    }

    public static function TraerReservasPorTipoCliente($tipoCliente) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $estado = "cancelada";
        
        $consulta = $objAccesoDatos->RetornarConsulta(
            "SELECT * 
            FROM reservas 
            WHERE estado = 'cancelada'
            AND tipoCliente = :tipoCliente"
        );

        $consulta->bindValue(':tipoCliente', $tipoCliente, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
    }

    public static function TraerReservasPorModalidad($modalidad) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objAccesoDatos->RetornarConsulta(
            "SELECT * 
            FROM clientes 
            WHERE modalidadPago = :modalidad"
        );

        $consulta->bindValue(':modalidad', $modalidad, PDO::PARAM_STR);
        $consulta->execute();

        $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Cliente');

        if ($resultado) {
            $reservas = [];

            foreach ($resultado as $cliente) {
                $consultaReservas = $objAccesoDatos->RetornarConsulta(
                    "SELECT * 
                    FROM reservas 
                    WHERE numeroCliente = :numeroCliente"
                );
        
                $consultaReservas->bindValue(':numeroCliente', $cliente->id, PDO::PARAM_INT);
                $consultaReservas->execute();
        
                $resultadoReservas = $consultaReservas->fetchAll(PDO::FETCH_CLASS, 'Reserva');

                foreach ($resultadoReservas as $reserva) {
                    array_push($reservas, $reserva);
                }
            }

            return $reservas;
        } else {
            return false;
        }

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
    }
}