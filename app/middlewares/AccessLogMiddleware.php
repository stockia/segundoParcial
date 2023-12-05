<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;

class AccessLogMiddleware implements MiddlewareInterface {
    public function process(Request $request, RequestHandler $handler): Response {
        $response = $handler->handle($request);
        $this->logAccess($request);
        return $response;
    }

    private function logAccess(Request $request) {
        $metodo = $request->getMethod();
        $uri = $request->getUri();
        $date = date('Y-m-d H:i:s');

        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDatos->RetornarConsulta(
            "INSERT INTO log (metodo, path, date) VALUES (:metodo, :path, :date)"
        );

        $consulta->bindValue(':metodo', $metodo, PDO::PARAM_STR);
        $consulta->bindValue(':path', $uri, PDO::PARAM_STR);
        $consulta->bindValue(':date', $date, PDO::PARAM_STR);

        $consulta->execute();
    }
}

