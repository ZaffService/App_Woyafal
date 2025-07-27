<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Container;
use App\Repositories\ClientRepository;
use App\Repositories\CompteurRepository;
use App\Repositories\AchatRepository;
use App\Repositories\JournalRepository;
use App\Services\TrancheService;
use App\Services\WoyofalService;
use App\Controllers\WoyofalController;

// Configuration du container
$container = Container::getInstance();

// Enregistrement des repositories
$container->singleton(ClientRepository::class);
$container->singleton(CompteurRepository::class);
$container->singleton(AchatRepository::class);
$container->singleton(JournalRepository::class);

// Enregistrement des services
$container->singleton(TrancheService::class);
$container->bind(WoyofalService::class, function($container) {
    return new WoyofalService(
        $container->resolve(CompteurRepository::class),
        $container->resolve(ClientRepository::class),
        $container->resolve(AchatRepository::class),
        $container->resolve(JournalRepository::class),
        $container->resolve(TrancheService::class)
    );
});

// Enregistrement des controllers
$container->bind(WoyofalController::class, function($container) {
    return new WoyofalController(
        $container->resolve(WoyofalService::class)
    );
});

return $container;
