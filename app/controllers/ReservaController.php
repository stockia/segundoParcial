<?php

require_once __DIR__ . '/../models/Reserva.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';

class ReservaController extends Reserva implements IApiUsable {

    public function TraerTodos($request, $response, $args) {
        $reservas = Reserva::TraerTodasLasReservas();
        $response->getBody()->write(json_encode($reservas));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args) {
        $id = $args['id'];
        $reserva = Reserva::TraerUnaReserva($id);
        
        if (!$reserva) {
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json')->write(json_encode(['error' => 'Reserva no encontrada']));
        }

        $response->getBody()->write(json_encode($Reserva));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response, $args) {
        $datos = $request->getParsedBody();

        $reserva = new Reserva();
        // $Reserva->id = $datos['id'];
        // $reserva->tipoCliente = $datos['tipoCliente'];
        $reserva->numeroCliente = $datos['numeroCliente'];
        $reserva->fechaEntrada = $datos['fechaEntrada'];
        $reserva->fechaSalida = $datos['fechaSalida'];
        $reserva->tipoHabitacion = $datos['tipoHabitacion'];
        $reserva->importeTotal = $datos['importeTotal'];

        $resultado = $reserva->InsertarReserva();

        if ($resultado === true) {
            $response->getBody()->write(json_encode(['mensaje' => 'Reserva creada']));
        } else {
            $response->getBody()->write(json_encode(['error' => 'No se pudo crear la reserva']));
        }
                
        // $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args) {
        $datos = $request->getParsedBody();

        $reserva = new Reserva();
        $reserva->id = $datos['id'];
        $reserva->importeTotal = $datos['importeTotal'];
    }

    public function BorrarUno($request, $response, $args) {
        $id = $args['id'];
        $datos = $request->getParsedBody();
    
        $reserva = new Reserva();
        $reserva->id = $id;
        $reserva->tipoCliente = $datos['tipoCliente'];
        $reserva->numeroCliente = $datos['numeroCliente'];

        $resultado = $reserva->BorrarReserva();

        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BuscarPorFechaParticular($request, $response, $args) {
        $datos = $request->getParsedBody();
        $fecha = $datos['fecha'];

        $reservas = Reserva::TraerReservasPorFechaParticular($fecha);

        $response->getBody()->write(json_encode(['fecha' => $fecha,'importeTotal' => $reservas]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BuscarPorCliente($request, $response, $args) {
        $datos = $request->getParsedBody();
        $numeroCliente = $datos['numeroCliente'];

        $reservas = Reserva::TraerReservasPorCliente($numeroCliente);

        $response->getBody()->write(json_encode($reservas));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BuscarPorRangoDeFechas($request, $response, $args) {
        $params = $request->getQueryParams();
        $fechaInicio = $params['fechaInicio'] ?? null; 
        $fechaFin = $params['fechaFin'] ?? null;

        if ($fechaInicio && $fechaFin) {
            $reservas = Reserva::TraerReservasPorRangoDeFechas($fechaInicio, $fechaFin);

            $response->getBody()->write(json_encode($reservas));
        } else {
            $response->getBody()->write(json_encode(['error' => 'Se requieren la fecha de inicio y la fecha de fin.']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BuscarPorTipoHabitacion($request, $response, $args) {
        $datos = $request->getParsedBody();
        $tipoHabitacion = $datos['tipoHabitacion'];

        $reservas = Reserva::TraerReservasPorTipoHabitacion($tipoHabitacion);

        $response->getBody()->write(json_encode($reservas));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function AjustarReservaUna($request, $response, $args) {
        $id = $args['id'];
        $datos = $request->getParsedBody();
        $importeTotal = $datos['importeTotal'];


        $reserva = new Reserva();
        $reserva->id = $id;
        $reserva->importeTotal = $importeTotal;
        // $reserva->motivo = $datos['motivo'];

        $resultado = $reserva->AjustarReserva($datos['motivo']);
        if ($resultado === true) {
            $response->getBody()->write(json_encode(['mensaje' => 'Reserva ajustada']));
        } else {
            $response->getBody()->write(json_encode(['error' => 'No se pudo ajustar la reserva']));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BuscarCancelacionCompleta($request, $response, $args) {
        $params = $request->getQueryParams();
        $fecha = $params['fecha'] ?? null;
        if (!$fecha) {
            $fecha = date('Y-m-d', strtotime('-1 day'));
        }

        $datos = $request->getParsedBody();

        $reserva = new Reserva();
        $reserva->fechaEntrada = $fecha;
        $reserva->tipoCliente = $datos['tipoCliente'];

        $resultado = $reserva->BuscarCancelacionFechaUsuario();
        if ($resultado === null || $resultado === []) {
            $response->getBody()->write(json_encode(['error' => 'No se encontraron reservas canceladas para los parametros ingresados']));
        } else {
            $response->getBody()->write(json_encode($resultado));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BuscarCancelacionPorCliente($request, $response, $args) {
        $numeroCliente = $args['numeroCliente'];

        $reserva = new Reserva();
        $reserva->numeroCliente = $numeroCliente;

        $resultado = $reserva->BuscarCancelacionesPorCliente();
        if ($resultado === null || $resultado === []) {
            $response->getBody()->write(json_encode(['error' => 'No se encontraron reservas canceladas para los parametros ingresados']));
        } else {
            $response->getBody()->write(json_encode($resultado));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BuscarCancelacionEntreFechas($request, $response, $args) {
        $params = $request->getQueryParams();
        $fechaEntrada = $params['fechaEntrada'] ?? null; 
        $fechaSalida = $params['fechaSalida'] ?? null;

        if ($fechaEntrada && $fechaSalida) {
            $reservas = Reserva::TraerCancelacionesPorRangoDeFechas($fechaEntrada, $fechaSalida);

            $response->getBody()->write(json_encode($reservas));
        } else {
            $response->getBody()->write(json_encode(['error' => 'Se requieren la fecha de inicio y la fecha de fin.']));
            return $response->withStatus(400);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BuscarPorTipoCliente($request, $response, $args) {
        $params = $request->getQueryParams();
        $tipoCliente = $params['tipoCliente'] ?? null; 

        if ($tipoCliente) {
            $reservas = Reserva::TraerReservasPorTipoCliente($tipoCliente);

            $response->getBody()->write(json_encode($reservas));
        } else {
            $response->getBody()->write(json_encode(['error' => 'Se requiere el tipo de cliente.']));
            return $response->withStatus(400);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BuscarOperacionesPorUsuario($request, $response, $args) {
        $numeroCliente = $args['numeroCliente'];

        if ($numeroCliente) {
            $reservas = Reserva::TraerReservasPorCliente($numeroCliente);

            $response->getBody()->write(json_encode($reservas));
        } else {
            $response->getBody()->write(json_encode(['error' => 'Se requiere el numero de cliente.']));
            return $response->withStatus(400);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function BuscarPorModalidad($request, $response, $args) {
        $params = $request->getQueryParams();
        $modalidad = $params['modalidad'] ?? null;
    
        if ($modalidad) {
            $reservas = Reserva::TraerReservasPorModalidad($modalidad);
            $response->getBody()->write(json_encode($reservas));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(['error' => 'Se requiere la modalidad.']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }
    
}
?>
