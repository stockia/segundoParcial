<?php

require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';

class ClienteController extends Cliente implements IApiUsable {

    public function TraerTodos($request, $response, $args) {
        $clientes = Cliente::TraerTodosLosClientes();
        $response->getBody()->write(json_encode($clientes));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args) {
        $id = $args['id'];
        $cliente = Cliente::TraerUnCliente($id);
        
        if (!$cliente) {
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json')->write(json_encode(['error' => 'Cliente no encontrado']));
        }

        $response->getBody()->write(json_encode(['pais' => $cliente->pais, 'ciudad' => $cliente->ciudad, 'telefono' => $cliente->telefono]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response, $args) {
        $datos = $request->getParsedBody();

        $cliente = new Cliente();
        $cliente->id = $datos['id'];
        $cliente->nombreApellido = $datos['nombreApellido'];
        $cliente->tipoDocumento = $datos['tipoDocumento'];
        $cliente->numeroDocumento = $datos['numeroDocumento'];
        $cliente->email = $datos['email'];
        $cliente->tipoCliente = $datos['tipoCliente'] . "-" . $datos['tipoDocumento'];
        $cliente->pais = $datos['pais'];
        $cliente->ciudad = $datos['ciudad'];
        $cliente->telefono = $datos['telefono'];
        $cliente->modalidadPago = $datos['modalidadPago'];
                
        $resultado = $cliente->InsertarCliente();
                
        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args) {
        $datos = $request->getParsedBody();

        $cliente = new Cliente();
        $cliente->nombreApellido = $datos['nombreApellido'];
        $cliente->tipoDocumento = $datos['tipoDocumento'];
        $cliente->numeroDocumento = $datos['numeroDocumento'];
        $cliente->email = $datos['email'];
        $cliente->tipoCliente = $datos['tipoCliente'];
        $cliente->pais = $datos['pais'];
        $cliente->ciudad = $datos['ciudad'];
        $cliente->telefono = $datos['telefono'];
        $cliente->modalidadPago = $datos['modalidadPago'];

        $resultado = $cliente->ModificarCliente();

        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args) {
        $id = $args['id'];
        $datos = $request->getParsedBody();

        $cliente = new Cliente();
        $cliente->id = $id;
        $cliente->tipoCliente = $datos['tipoCliente'];

        $resultado = $cliente->BorrarCliente();

        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>
