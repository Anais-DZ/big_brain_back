<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once 'env.php';

require_once './src/controller/UserController.php';
require_once './src/controller/QuestionController.php';
require_once './src/controller/QuizzController.php';
require_once './src/controller/CategoryController.php';
require_once './src/controller/PlayController.php';


use controller\UserController;
use controller\QuestionController;
use controller\QuizzController;
use controller\CategoryController;
use controller\PlayController;

$url = parse_url($_SERVER['REQUEST_URI']);

$path = $url['path'] ?? '/';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

switch($path){
    case $path === "/api/auth/logout" :
        echo (new UserController())->logout();
        break;
    case $path === "/api/auth/login" :
        echo (new UserController())->login();
        break;
    case $path === "/api/auth/register" :
        echo (new UserController())->register();
        break;
    case $path === "/api/user/me" :
        echo (new UserController())->me();
        break;
    case $path === "/api/user/playedgames":
        echo (new UserController())->getPlayedGames();
        break;
    case $path === "/api/user/playedgame":
        echo (new UserController())->getPlayedGame();
        break;
    case $path === "/api/user/playedgame/create":
        echo (new UserController())->createPlayedGameAnswer();
        break;
    case $path === "/api/user/create" :
        echo (new UserController())->create();
        break;
    case $path === "/api/users" :
        echo (new UserController())->all();
        break;
    case $path === "/api/user" :
        echo (new UserController())->show();
        break;
    case $path === "/api/user/update" :
        echo (new UserController())->update();
        break;
    case $path === "/api/user/delete" :
        echo (new UserController())->drop();
        break;
    case $path === "/api/quizz/create" :
        echo (new QuizzController())->create();
        break;
    case $path === "/api/quizzes" :
        echo (new QuizzController())->all();
        break;
    case $path === "/api/quizz" :
        echo (new QuizzController())->show();
        break;
    case $path === "/api/quizz/update" :
        echo (new QuizzController())->update();
        break;
    case $path === "/api/quizz/delete" :
        echo (new QuizzController())->drop();
        break;
    case $path === "/api/category/create" :
        echo (new CategoryController())->create();
        break;
    case $path === "/api/categories" :
        echo (new CategoryController())->all();
        break;
    case $path === "/api/category" :
        echo (new CategoryController())->show();
        break;
    case $path === "/api/category/update" :
        echo (new CategoryController())->update();
        break;
    case $path === "/api/category/delete" :
        echo (new CategoryController())->drop();
        break;
    case $path === "/api/question/create" :
        echo (new QuestionController())->create();
        break;
    case $path === "/api/questions" :
        echo (new QuestionController())->all();
        break;
    case $path === "/api/question" :
        echo (new QuestionController())->show();
        break;
    case $path === "/api/question/update" :
        echo (new QuestionController())->update();
        break;
    case $path === "/api/question/delete" :
        echo (new QuestionController())->drop();
        break;
    case $path === "/api/play/create":
        echo (new PlayController())->create();
        break;
    case $path === "/api/plays":
        echo (new PlayController())->all();
        break;
    case $path === "/api/play":
        echo (new PlayController())->show();
        break;
    case $path === "/api/play/update":
        echo (new PlayController())->update();
        break;
    case $path === "/api/play/delete":
        echo (new PlayController())->drop();
        break;
    default:
        echo "No routing found for this API.";
        break;
}


