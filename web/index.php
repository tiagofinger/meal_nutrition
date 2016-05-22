<?php
/**
 * Please Install redis for use the complete API
 * @name meal_nutrition
 * @author Tiago Finger <tiagocfinger@gmail.com>
 * @description I created a api restFul to consult data in api.nal.usda.gov and the data returned, you can use for create a recipe and save in a local base using redis
 */
use Silex\Application;
require_once __DIR__.'/../vendor/autoload.php';

$app = new Application();
$app['debug'] = false;

// Redis 
$app->register(new Predis\Silex\ClientsServiceProvider(), array(
    'predis.clients' => array(
        'db' => 'tcp://127.0.0.1',
        'session' => array(
            'parameters' => 'tcp://127.0.0.1',
            'options' => array(
                'prefix' => 'sessions:'
            ),
        ),
    ),
));

// Session
$app->register(new Silex\Provider\SessionServiceProvider(), array(
    'session.storage.handler' => $app->share(function () use ($app) {
        $client = $app['predis']['session'];
        $options = array('gc_maxlifetime' => 300);

        $handler = new Predis\Session\Handler($client, $options);

        return $handler;
    })
));
 
// Controller
$app->mount('/', new MyApp\MyClassController());
$app->run();