<?php

namespace abstracts;

use model\UserModel;

abstract class Controller
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();

    }

    public function getJson()
    {
        return json_decode(file_get_contents('php://input'));
    }

    public function send($data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function sendError($message, $code): void
    {
        $this->send(['error' => $message,'code' => $code], $code);
    }

    public function makeAuthenticated(): array
    {
        if(!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $this->sendError('Unauthorized', 401);
        }

        $user = $this->userModel->getByToken($_SERVER['HTTP_AUTHORIZATION']);

        if(isset($user['code']) && $user['code'] === 'EXPIRED_TOKEN') {
            $this->sendError('Token expired', 401);
        }

        if(!$user) {
            $this->sendError('Unauthorized', 401);
        }

        return $user;
    }

    public function get(): void
    {
        if(!($_SERVER['REQUEST_METHOD'] === 'GET')) {
            $this->sendError('Only GET request accepted', 405);
        }
    }

    public function post(): void
    {
        if(!($_SERVER['REQUEST_METHOD'] === 'POST')) {
            $this->sendError('Only POST request accepted', 405);
        }
    }

    public function put(): void
    {
        if(!($_SERVER['REQUEST_METHOD'] === 'PUT')) {
            $this->sendError('Only PUT request accepted', 405);
        }
    }

    public function patch(): void
    {
        if(!($_SERVER['REQUEST_METHOD'] === 'PATCH')) {
            $this->sendError('Only PATCH request accepted', 405);
        }
    }

    public function delete(): void
    {
        if(!($_SERVER['REQUEST_METHOD'] === 'DELETE')) {
            $this->sendError('Only DELETE request accepted', 405);
        }
    }

    abstract public function all();
    abstract public function create();
    abstract public function show();
    abstract public function update();
    abstract public function drop();

}