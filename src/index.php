<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;


// Use Loader() to autoload our model
$loader = new Loader();

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/html/');

require_once APP_PATH . '/vendor/autoload.php';

$loader->registerDirs(
    [
        APP_PATH . "/models/",
    ]
);

$loader->registerNamespaces(
    [
        'Store\Toys' => APP_PATH . '/models/',
    ]
);

$loader->register();

$container = new FactoryDefault();

// Set up the database service


$container->set(
    'mongo',
    function () {
        $mongo = new MongoDB\Client(
            'mongodb+srv://deekshapandey:Deeksha123@cluster0.whrrrpj.mongodb.net/?retryWrites=true&w=majority'
        );

        return $mongo->testing;
    },
    true
);

$app = new Micro($container);



// // Retrieves all products

$app->get(
    '/api/products',
    function () use ($app) {

        $robot = $this->mongo->products->find();

        $data = [];
        foreach ($robot as $value) {

            $data[] = [
                "name" => $value->name,
                "id" => $value->id,
                "type" => $value->type,
                "price" => $value->price,
            ];
        }
        echo json_encode($data);
    }
);
// Searches for products with $name in their name
$app->get(
    '/api/products/search/{name}',
    function ($name) use ($app) {
        $robot = $this->mongo->products->findOne(["name" => $name]);

        $data = [];
        $data[] = [
            "name" => $robot->name,
            "id" => $robot->id,
            "type" => $robot->type,
            "price" => $robot->price,
        ];


        echo json_encode($data);
    }

);

// Retrieves products based on primary key
$app->get(
    '/api/products/{id:[0-9]+}',
    function ($id) use ($app) {
        $product = $this->mongo->products->findOne(['id' => $id]);
        $data = [];
        $data[] = [
            'id'   => $product->id,
            'name' => $product->name,
            'type' => $product->type,
            'price'=> $product->price
        ];
        echo json_encode($data);
    }
);

// Adds a new product
$app->post(
    '/api/products',
    function () use ($app) {

        $robot = $app->request->getJsonRawBody();

        $collection = $this->mongo->products;

        $value = $collection->insertOne(
            ['id' => $robot[0]->id, 'name' => $robot[0]->name, 'type' => $robot[0]->type, 'price' => $robot[0]->price]
        );

        print_r($value);
        die;
    }
);

// Updates product based on primary key
$app->put(
    '/api/products/{id:[0-9]+}',
    function ($id) use ($app) {
        $robot = $app->request->getJsonRawBody();
        $collection = $this->mongo->products;
        $updateResult = $collection->updateOne(
            ['id'  =>  $id],
            ['$set' => [
                "name" => $robot[0]->name,
                'name' => $robot[0]->name,
                'type' => $robot[0]->type,
                'price'=> $robot[0]->price

            ]]
        );
        print_r($updateResult);
        die;
    }
);

// Deletes product based on primary key
$app->delete(
    '/api/products/{id:[0-9]+}',
    function ($id) use ($app) {
        $collection = $this->mongo->products;
        $deleted = $collection->deleteOne(['id' => $id]);
        print_r($deleted);
        die;
    }
);
$app->handle(
    $_SERVER["REQUEST_URI"]
);
