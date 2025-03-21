<?php

namespace controller;

require_once './src/model/QuizzModel.php';
require_once './src/abstracts/Controller.php';

use abstracts\Controller;
use model\QuizzModel;

class QuizzController extends Controller
{
    private QuizzModel $quizzModel;
    public function __construct()
    {
        parent::__construct();
        $this->quizzModel = new QuizzModel();
    }

    public function all(): void
    {
        $this->get();

        $quizzes = $this->quizzModel->getAll();

        foreach ($quizzes as $key => $quizz) {
            $quizzes[$key]['categories'] = $this->quizzModel->getCategories($quizz['id_quiz']);
        }

        $this->send($quizzes);
    }

    public function create(): void
    {
        $this->post();

        $data = $this->getJson();

        if(empty($data->title) || empty($data->description) || empty($data->image) || empty($data->categories)) {
            $this->sendError('All fields are required', 400);
        }

        if(strlen($data->title) < 3) {
            $this->sendError('Title must be at least 3 characters', 400);
        }

        if(strlen($data->description) < 10) {
            $this->sendError('Description must be at least 10 characters', 400);
        }

        if(!filter_var($data->image, FILTER_VALIDATE_URL)) {
            $this->sendError('Invalid image url', 400);
        }

        if (!is_array($data->categories) || empty($data->categories)) {
            $this->sendError('Categories must be a non-empty array', 400);
        }

        $this->quizzModel->setTitleQuiz($data->title);
        $this->quizzModel->setDescriptionQuiz($data->description);
        $this->quizzModel->setImgQuiz($data->image);
        $quizz = $this->quizzModel->add();

        if (!$quizz) {
            $this->sendError('Quizz not created', 404);
        }

        foreach ($data->categories as $categoryId) {
            if (!is_int($categoryId) || $categoryId <= 0) {
                $this->sendError('Invalid category ID', 400);
            }

            if (!$this->quizzModel->addQuizCategory($quizz['id_quiz'], $categoryId)) {
                $this->sendError('Failed to add category to quiz', 500);
            }
        }

        $quizz['categories'] = $this->quizzModel->getCategories($quizz['id_quiz']);

        $this->send($quizz,201);
    }

    public function show()
    {
        $this->get();

        if(empty($_GET['id'])) {
            $this->sendError('Id is required', 400);
        }

        $quizz = $this->quizzModel->getById($_GET['id']);

        if(!$quizz) {
            $this->sendError('Quizz not found', 404);
        }

        $quizz['categories'] = $this->quizzModel->getCategories($quizz['id_quiz']);
        $quizz['questions'] = $this->quizzModel->getQuestions($quizz['id_quiz']);


        $this->send($quizz);
    }

    public function update()
    {
        $this->put();

        if(empty($_GET['id'])) {
            $this->sendError('Id is required', 400);
        }

        $data = $this->getJson();

        if(empty($data->title) || empty($data->description) || empty($data->image)) {
            $this->sendError('All fields are required', 400);
        }

        if(strlen($data->title) < 3) {
            $this->sendError('Title must be at least 3 characters', 400);
        }

        if(strlen($data->description) < 10) {
            $this->sendError('Description must be at least 10 characters', 400);
        }

        if(!filter_var($data->image, FILTER_VALIDATE_URL)) {
            $this->sendError('Invalid image url', 400);
        }

        $quizz = $this->quizzModel->getById($_GET['id']);

        if(!$quizz) {
            $this->sendError('Quizz not found', 404);
        }

        $this->quizzModel->setIdQuiz($quizz['id_quiz']);
        $this->quizzModel->setTitleQuiz($data->title);
        $this->quizzModel->setDescriptionQuiz($data->description);
        $this->quizzModel->setImgQuiz($data->image);
        $updatedQuizz = $this->quizzModel->update();

        if(!$updatedQuizz) {
            $this->sendError('Quizz not updated', 404);
        }

        $this->send($updatedQuizz);
    }

    public function drop()
    {
        $this->delete();

        if(empty($_GET['id'])) {
            $this->sendError('Id is required', 400);
        }

        $quizz = $this->quizzModel->getById($_GET['id']);

        if(!$quizz) {
            $this->sendError('Quizz not found', 404);
        }

        $this->quizzModel->setIdQuiz($quizz['id']);
        $deletedQuizz = $this->quizzModel->delete();

        if(!$deletedQuizz) {
            $this->sendError('Quizz not deleted', 404);
        }

        $this->send([
            'message' => 'Quizz deleted'
        ]);
    }
}