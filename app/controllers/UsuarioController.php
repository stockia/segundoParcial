<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable {

    public function TraerTodos($request, $response, $args) {
        $usuarios = Usuario::TraerTodosLosUsuarios();
        $response->getBody()->write(json_encode($usuarios));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args) {
        $id = $args['id'];
        $usuario = Usuario::TraerUnUsuario($id);
        
        if (!$usuario) {
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json')->write(json_encode(['error' => 'Usuario no encontrado']));
        }

        $response->getBody()->write(json_encode($usuario));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response, $args) {
        $datos = $request->getParsedBody();
        
        $usuario = new Usuario();
        $usuario->rol = $datos['rol'];
        $usuario->username = $datos['username'];
        $usuario->password = $datos['password'];

        $resultado = $usuario->InsertarUsuario();

        if ($resultado > 0) {
            $response->getBody()->write(json_encode(['mensaje' => 'Usuario creado']));
        } else {
            $response->getBody()->write(json_encode(['error' => 'No se pudo crear el usuario']));
        }

        // $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args) {
        $id = $args['id'];
        $datos = $request->getParsedBody();

        $usuario = new Usuario();
        $usuario->id = $id;
        $usuario->nombre = $datos['nombre'];
        $usuario->rol = $datos['rol'];
        $usuario->username = $datos['username'];
        $usuario->password = $datos['password'];

        $resultado = $usuario->ModificarUsuario();

        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args) {
        $id = $args['id'];

        $usuario = new Usuario();
        $usuario->id = $id;

        $resultado = $usuario->BorrarUsuario();

        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>
