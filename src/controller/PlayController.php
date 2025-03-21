<?php

namespace controller;

require_once './src/model/PlayModel.php';
require_once './src/abstracts/Controller.php';

use abstracts\Controller;
use model\PlayModel;

class PlayController extends Controller
{
    private PlayModel $playModel;

    public function __construct()
    {
        parent::__construct();
        $this->playModel = new PlayModel();
    }

    public function all(): void
    {
        $this->get();
        $this->send($this->playModel->getAll());
    }

    public function create(): void
    {
        $this->post();
        $this->send(['message' => 'Not implemented']);
    }

    public function show()
    {
        $this->get();
        $this->send(['message' => 'Not implemented']);
    }

    public function update()
    {
        $this->put();
        $this->send(['message' => 'Not implemented']);
    }

    public function drop()
    {
        $this->delete();
        $this->send(['message' => 'Not implemented']);
    }
}