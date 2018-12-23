<?php

unlink('error_log');

error_reporting(0);

require_once __DIR__ . '/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\App;

class User {

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createUser($attributes = [])
    {
        $attributes['age'] = (int) $attributes['age'];

        $stmt = $this->db->prepare('SELECT `id`, `name`, `age`, `gender`, `coin`, `diamond`, `created_at`, `updated_at` FROM `users` WHERE `name` = :name AND `age` = :age AND `gender` = :gender LIMIT 1');
        $stmt->bindParam(':name', $attributes['name']);
        $stmt->bindParam(':age', $attributes['age']);
        $stmt->bindParam(':gender', $attributes['gender']);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($result)) {
            return $result[0];
        }

        $attributes['created_at'] = $attributes['updated_at'] = date('Y-m-d H:i:s');
        $attributes['coin'] = 200;
        $attributes['diamond'] = 0;

        $stmt = $this->db->prepare('INSERT INTO `users` (`name`, `age`, `gender`, `coin`, `diamond`, `created_at`, `updated_at`) VALUES (:name, :age, :gender, :coin, :diamond, :created_at, :updated_at)');
        $stmt->bindParam(':name', $attributes['name']);
        $stmt->bindParam(':age', $attributes['age']);
        $stmt->bindParam(':gender', $attributes['gender']);
        $stmt->bindParam(':coin', $attributes['coin']);
        $stmt->bindParam(':diamond', $attributes['diamond']);
        $stmt->bindParam(':created_at', $attributes['created_at']);
        $stmt->bindParam(':updated_at', $attributes['updated_at']);
        $stmt->execute();

        $attributes['id'] = $this->db->lastInsertId();

        return $attributes;
    }

    public function setCoin($userId, $coin)
    {
        $updatedAt = date('Y-m-d H:i:s');

        $stmt = $this->db->prepare('UPDATE `users` SET `coin` = :coin, `updated_at` = :updated_at WHERE `id` = :id');
        $stmt->bindParam(':id', $userId);
        $stmt->bindParam(':coin', $coin);
        $stmt->bindParam(':updated_at', $updatedAt);
        $stmt->execute();

        return true;
    }

    public function setDiamond($userId, $diamond)
    {
        $updatedAt = date('Y-m-d H:i:s');

        $stmt = $this->db->prepare('UPDATE `users` SET `diamond` = :diamond, `updated_at` = :updated_at WHERE `id` = :id');
        $stmt->bindParam(':id', $userId);
        $stmt->bindParam(':diamond', $diamond);
        $stmt->bindParam(':updated_at', $updatedAt);
        $stmt->execute();

        return true;
    }
}

$config = require __DIR__ . '/config.php';
$app = new App($config);
$container = $app->getContainer();

// databases
$container['db'] = function ($config) {
    $db = $config['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'], $db['user'], $db['pass'], [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    ]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
};

$app->post('/register', function (Request $request, Response $response, array $args) {
    $attributes = $request->getParsedBody();

    $user = (new User($this->db))->createUser($attributes);

    $response = $response
        ->withStatus(201)
        ->withJson($user);

    return $response;
});

$app->post('/set-coin', function (Request $request, Response $response, array $args) {
    $attributes = $request->getParsedBody();
    $userId = $attributes['id'];
    $coin = $attributes['coin'];

    $user = (new User($this->db))->setCoin($userId, $coin);

    $response = $response
        ->withStatus(206);

    return $response;
});

$app->post('/set-diamond', function (Request $request, Response $response, array $args) {
    $attributes = $request->getParsedBody();
    $userId = $attributes['id'];
    $diamond = $attributes['diamond'];

    $user = (new User($this->db))->setDiamond($userId, $diamond);

    $response = $response
        ->withStatus(206);

    return $response;
});

$app->run();
