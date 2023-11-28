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

require_once './controllers/ClienteController.php';
require_once './controllers/ReservaController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// $authMiddlewareSocio = new AuthMiddleware('socio');
// $authMiddlewareMozo = new AuthMiddleware('mozo');

// Routes
$app->post('/login', \LoginController::class . ':Login');

$app->group('/clientes', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ClienteController::class . ':TraerTodos');
  $group->get('/{id}', \ClienteController::class . ':TraerUno');
  $group->post('[/]', \ClienteController::class . ':CargarUno');
  $group->put('/{id}', \ClienteController::class . ':ModificarUno');
  $group->delete('/{id}', \ClienteController::class . ':BorrarUno');
});

// $app->group('/reservas', function (RouteCollectorProxy $group) {
//   // $group->get('/downloadCSV', \PedidoController::class . ':DescargarComoCSV');
//   // $group->post('/uploadCSV', \PedidoController::class . ':CargarDesdeCSV');
//   $group->post('[/]', \ReservaController::class . ':CargarUno');
//   $group->get('[/]', \ReservaController::class . ':TraerTodos');
//   $group->get('/cliente', \ReservaController::class . ':BuscarPorCliente');
//   $group->get('/entre-fechas', \ReservaController::class . ':BuscarPorRangoDeFechas');
//   $group->get('/fecha', \ReservaController::class . ':BuscarPorFechaParticular');
//   $group->get('/habitacion', \ReservaController::class . ':BuscarPorTipoHabitacion');
//   $group->put('/{id}', \ReservaController::class . ':AjustarReservaUna');
//   $group->delete('/{id}', ReservaController::class . ':BorrarUno');

//   // consultas punto 10
//   $group->get('/cancelacion/completa', \ReservaController::class . ':BuscarCancelacionCompleta');
//   $group->get('/cancelacion/{numeroCliente}', \ReservaController::class . ':BuscarCancelacionPorCliente');
//   $group->get('/cancelacion/entre-fechas', \ReservaController::class . ':BuscarCancelacionEntreFechas');
//   $group->get('/cancelacion/tipo-cliente', \ReservaController::class . ':BuscarPorTipoCliente');
//   $group->get('/operaciones/{numeroCliente}', \ReservaController::class . ':BuscarOperacionesPorUsuario');
//   $group->get('/modalidad', \ReservaController::class . ':BuscarPorModalidad');
// });
$app->group('/reservas', function (RouteCollectorProxy $group) {
  // $group->get('/downloadCSV', \PedidoController::class . ':DescargarComoCSV');
  // $group->post('/uploadCSV', \PedidoController::class . ':CargarDesdeCSV');
  $group->post('[/]', \ReservaController::class . ':CargarUno');
  $group->get('[/]', \ReservaController::class . ':TraerTodos');
  $group->get('/cliente', \ReservaController::class . ':BuscarPorCliente');
  $group->get('/entre-fechas', \ReservaController::class . ':BuscarPorRangoDeFechas');
  $group->get('/fecha', \ReservaController::class . ':BuscarPorFechaParticular');
  $group->get('/habitacion', \ReservaController::class . ':BuscarPorTipoHabitacion');
  $group->put('/{id}', \ReservaController::class . ':AjustarReservaUna');
  $group->delete('/{id}', \ReservaController::class . ':BorrarUno');

  // Rutas actualizadas para evitar la colisión
  $group->get('/cancelacion/completa', \ReservaController::class . ':BuscarCancelacionCompleta');
  $group->get('/cancelacion/cliente/{numeroCliente}', \ReservaController::class . ':BuscarCancelacionPorCliente');
  $group->get('/cancelacion/entre-fechas', \ReservaController::class . ':BuscarCancelacionEntreFechas');
  $group->get('/cancelacion/tipo-cliente', \ReservaController::class . ':BuscarPorTipoCliente');
  $group->get('/operaciones/{numeroCliente}', \ReservaController::class . ':BuscarOperacionesPorUsuario');
  $group->get('/modalidad', \ReservaController::class . ':BuscarPorModalidad');
});


$app->run();