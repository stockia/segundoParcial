<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';

require_once './controllers/LoginController.php';
require_once './utils/AutentificadorJWT.php';

require_once './middlewares/LoggerMiddleware.php';
require_once './middlewares/AuthMiddleware.php';
require_once './middlewares/AccessLogMiddleware.php';
require_once './middlewares/TransactionLogMiddleware.php';

require_once './controllers/ClienteController.php';
require_once './controllers/ReservaController.php';
require_once './controllers/UsuarioController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// $authMiddlewareGerente = new AuthMiddleware('gerente');
$authMiddlewareGerente = new AuthMiddleware(['gerente']);
$authMiddlewareRecepcionistaCliente = new AuthMiddleware(['recepcionista', 'cliente']);
$authMiddlewareCliente = new AuthMiddleware('cliente');

// Routes
$app->add(new AccessLogMiddleware());
$app->post('/login', \LoginController::class . ':Login');

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/{id}', \UsuarioController::class . ':TraerUno');
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
  $group->put('/{id}', \UsuarioController::class . ':ModificarUno');
  $group->delete('/{id}', \UsuarioController::class . ':BorrarUno');
});

$app->group('/clientes', function (RouteCollectorProxy $group) use ($authMiddlewareGerente) {
  $group->get('[/]', \ClienteController::class . ':TraerTodos');
  $group->get('/{id}', \ClienteController::class . ':TraerUno');
  $group->post('[/]', \ClienteController::class . ':CargarUno')
    ->add($authMiddlewareGerente);
  $group->put('/{id}', \ClienteController::class . ':ModificarUno');
  $group->delete('/{id}', \ClienteController::class . ':BorrarUno')
    ->add($authMiddlewareGerente);
});

$app->group('/reservas', function (RouteCollectorProxy $group) use ($authMiddlewareRecepcionistaCliente) {
  $group->post('[/]', \ReservaController::class . ':CargarUno')
    ->add(new TransactionLogMiddleware())
    ->add($authMiddlewareRecepcionistaCliente);
  $group->get('[/]', \ReservaController::class . ':TraerTodos')
    ->add(new TransactionLogMiddleware())
    ->add($authMiddlewareRecepcionistaCliente);
  $group->get('/cliente', \ReservaController::class . ':BuscarPorCliente')
    ->add(new TransactionLogMiddleware())
    ->add($authMiddlewareRecepcionistaCliente);
  $group->get('/entre-fechas', \ReservaController::class . ':BuscarPorRangoDeFechas')
    ->add(new TransactionLogMiddleware())
    ->add($authMiddlewareRecepcionistaCliente);
  $group->get('/fecha', \ReservaController::class . ':BuscarPorFechaParticular')
    ->add(new TransactionLogMiddleware())
    ->add($authMiddlewareRecepcionistaCliente);
  $group->get('/habitacion', \ReservaController::class . ':BuscarPorTipoHabitacion')
    ->add(new TransactionLogMiddleware())
    ->add($authMiddlewareRecepcionistaCliente);
  $group->put('/{id}', \ReservaController::class . ':AjustarReservaUna')
    ->add(new TransactionLogMiddleware())
    ->add($authMiddlewareRecepcionistaCliente);
  $group->delete('/{id}', \ReservaController::class . ':BorrarUno')
    ->add(new TransactionLogMiddleware())
    ->add($authMiddlewareRecepcionistaCliente);

  $group->get('/cancelacion/completa', \ReservaController::class . ':BuscarCancelacionCompleta')
    ->add(new TransactionLogMiddleware())
    ->add($authMiddlewareRecepcionistaCliente);
  $group->get('/cancelacion/cliente/{numeroCliente}', \ReservaController::class . ':BuscarCancelacionPorCliente')
    ->add(new TransactionLogMiddleware())
    ->add($authMiddlewareRecepcionistaCliente);
  $group->get('/cancelacion/entre-fechas', \ReservaController::class . ':BuscarCancelacionEntreFechas')
    ->add(new TransactionLogMiddleware())
    ->add($authMiddlewareRecepcionistaCliente);
  $group->get('/cancelacion/tipo-cliente', \ReservaController::class . ':BuscarPorTipoCliente')
    ->add(new TransactionLogMiddleware())
    ->add($authMiddlewareRecepcionistaCliente);
  $group->get('/operaciones/{numeroCliente}', \ReservaController::class . ':BuscarOperacionesPorUsuario')
    ->add(new TransactionLogMiddleware())
    ->add($authMiddlewareRecepcionistaCliente);
  $group->get('/modalidad', \ReservaController::class . ':BuscarPorModalidad')
    ->add(new TransactionLogMiddleware())
    ->add($authMiddlewareRecepcionistaCliente);
});


$app->run();