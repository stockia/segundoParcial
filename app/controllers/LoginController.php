<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once __DIR__ . '/../utils/AutentificadorJWT.php';
require_once __DIR__ . '/../models/Cliente.php';

class LoginController {
    public function Login($request, $response, $args) {
        $datos = $request->getParsedBody();
        $username = $datos['username'];
        $password = $datos['password'];

        $usuario = Usuario::ValidarCredenciales($username, $password);

        if ($usuario) {
            $rol = $usuario->rol;
            $datosParaToken = array('rol' => $rol);
            $token = AutentificadorJWT::CrearToken($datosParaToken);
            $response->getBody()->write(json_encode(['token' => $token]));

            return $response->withHeader('Content-Type', 'application/json');
        } else {
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json')->write(json_encode(['error' => 'Credenciales inválidas']));
        }
    }
}
