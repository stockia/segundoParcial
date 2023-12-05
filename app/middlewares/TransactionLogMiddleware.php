<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;

require_once __DIR__ . '/../utils/AutentificadorJWT.php';

class TransactionLogMiddleware implements MiddlewareInterface {
    public function process(Request $request, RequestHandler $handler): Response {
        $response = $handler->handle($request);
        $this->logTransaction($request);
        return $response;
    }

    private function logTransaction(Request $request): void {
        $token = $this->extractToken($request);
        $userData = AutentificadorJWT::ObtenerData($token);
        $username = $userData->username ?? 'unknown';
        $method = $request->getMethod();
        $path = $request->getUri();
        $date = date('Y-m-d H:i:s');

        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDatos->RetornarConsulta(
            "INSERT INTO log_reservas (metodo, path, username, fecha) VALUES (:metodo, :path, :username, :fecha)"
        );

        $consulta->bindValue(':metodo', $method, PDO::PARAM_STR);
        $consulta->bindValue(':path', $path, PDO::PARAM_STR);
        $consulta->bindValue(':username', $username, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', $date, PDO::PARAM_STR);

        $consulta->execute();
    }

    private function extractToken(Request $request) {
        $header = $request->getHeaderLine('Authorization');
        list($jwt) = sscanf($header, 'Bearer %s');
        return $jwt;
    }
}
