<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class LoggerMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        $before = date('Y-m-d H:i:s');
        
        $response = $handler->handle($request);
        $existingContent = json_decode($response->getBody());
    
        $response = new Response();
        
        $payload = json_encode($existingContent);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

        public function VerificarRol(Request $request, RequestHandler $handler): Response
    {   
        $parametros = $request->getQueryParams();

        $rol = $parametros['rol'];

        if ($rol === 'socio') {
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'Acceso denegado, no es socio'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}