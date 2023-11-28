<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once __DIR__ . '/../utils/AutentificadorJWT.php';

class AuthMiddleware
{
    private $rolPermitido;

    public function __construct($rolPermitido) {
        $this->rolPermitido = $rolPermitido;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
    $response = new Response();

    $header = $request->getHeaderLine('Authorization');

    $tokenParts = explode("Bearer ", $header);
    $token = isset($tokenParts[1]) ? trim($tokenParts[1]) : null;

    if ($token) {
        try {
            AutentificadorJWT::VerificarToken($token);
            $datosUsuario = AutentificadorJWT::ObtenerData($token);
            
            if ($datosUsuario->rol === $this->rolPermitido) {
                $response = $handler->handle($request);
            } else {
                $payload = json_encode(array('mensaje' => 'ERROR: Acceso no autorizado'));
                $response->getBody()->write($payload);
                return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
            }
        } catch (Exception $e) {
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el TOKEN - ' . $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    } else {
        $payload = json_encode(array('mensaje' => 'ERROR: Token no proporcionado'));
        $response->getBody()->write($payload);
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }

    return $response->withHeader('Content-Type', 'application/json');
    }

}
