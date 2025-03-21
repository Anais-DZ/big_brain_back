<?php

namespace controller;

require_once './src/model/QuestionModel.php';
require_once './src/model/AnswerModel.php';
require_once './src/model/QuizzModel.php';
require_once './src/abstracts/Controller.php';

use abstracts\Controller;
use abstracts\Model;
use model\QuestionModel;
use model\AnswerModel;
use model\QuizzModel;

class QuestionController extends Controller
{
    private QuestionModel $questionModel;
    private AnswerModel $answerModel;

    private QuizzModel $quizzModel;

    public function __construct()
    {
        parent::__construct();
        $this->questionModel = new QuestionModel();
        $this->answerModel = new AnswerModel();
        $this->quizzModel = new QuizzModel();
    }

    public function all(): void
    {
        $this->get();
        $this->send($this->questionModel->getAll());
    }

    public function create(): void
    {
        $this->post();

        $data = $this->getJson();

        if(empty($data->quiz_id) || empty($data->title) || empty($data->description) || empty($data->image) || empty($data->multiple) || empty($data->answers)) {
            $this->sendError('All fields are required', 400);
        }

        if(!filter_var($data->quiz_id, FILTER_VALIDATE_INT)) {
            $this->sendError('Quiz ID must be a int', 400);
        }

        if(strlen($data->title) < 3) {
            $this->sendError('Title must be at least 3 characters', 400);
        }

        if(strlen($data->description) < 10) {
            $this->sendError('Description must be at least 10 characters', 400);
        }

        if(!filter_var($data->multiple, FILTER_VALIDATE_INT)) {
            $this->sendError('Multiple must be a int', 400);
        }

        if(!filter_var($data->image, FILTER_VALIDATE_URL)) {
            $this->sendError('Invalid image url', 400);
        }

        if (!is_array($data->answers) || empty($data->answers)) {
            $this->sendError('Answers must be a non-empty array', 400);
        }

        $quizz = $this->quizzModel->getById($data->quiz_id);

        if(!$quizz) {
            $this->sendError('Quizz not found', 404);
        }

        $this->questionModel->setTitleQuestion($data->title);
        $this->questionModel->setDescriptionQuestion($data->description);
        $this->questionModel->setImgQuestion($data->image);
        $this->questionModel->setMultipleQuestion($data->multiple);
        $question = $this->questionModel->add();

        $this->quizzModel->addQuizQuestion($quizz['id_quiz'],$question['id_question']);

        if(!$question) {
            $this->sendError('Question not created', 404);
        }

        foreach ($data->answers as $answer) {

            if(empty($answer->title) || !isset($answer->valid) || empty($answer->point)) {
                $this->sendError('All fields are required for answers', 400);
            }

            if(strlen($answer->title) < 3) {
                $this->sendError('Title must be at least 3 characters', 400);
            }

            if(!filter_var($answer->valid, FILTER_VALIDATE_INT) || $answer->valid != 1 && $answer->valid != 0 ) {
                $this->sendError('Valid must be a boolean', 400);
            }

            if(!filter_var($answer->point, FILTER_VALIDATE_INT)) {
                $this->sendError('Point must be a int', 400);
            }


            $this->answerModel->setTextAnswer($answer->title);
            $this->answerModel->setValidAnswer($answer->valid);
            $this->answerModel->setPointAnswer($answer->point);
            $this->answerModel->setIdQuestion($question['id_question']);
            $answer = $this->answerModel->add();

            if(!$answer) {
                $this->sendError('Answer not created', 404);
            }

        }

        $question['answers'] = $this->answerModel->getAnswersByQuestion($question['id_question']);
        $question['quiz_id'] = $quizz['id_quiz'];

        $this->send($question,201);
    }

    public function show()
    {
        $this->get();

        if(empty($_GET['id'])) {
            $this->sendError('Id is required', 400);
        }

        $question = $this->questionModel->getById($_GET['id']);

        if(!$question) {
            $this->sendError('Question not found', 404);
        }

        $question['answers'] = $this->answerModel->getAnswersByQuestion($question['id_question']);

        $this->send($question);
    }

    public function update()
    {
        $this->put();

        if(empty($_GET['id'])) {
            $this->sendError('Id is required', 400);
        }

        $data = $this->getJson();

        if(empty($data->title) || empty($data->description) || empty($data->image) || empty($data->multiple)) {
            $this->sendError('All fields are required', 400);
        }

        if(strlen($data->title) < 3) {
            $this->sendError('Title must be at least 3 characters', 400);
        }

        if(strlen($data->description) < 10) {
            $this->sendError('Description must be at least 10 characters', 400);
        }

        if(!filter_var($data->multiple, FILTER_VALIDATE_INT)) {
            $this->sendError('Multiple must be a int', 400);
        }

        if(!filter_var($data->image, FILTER_VALIDATE_URL)) {
            $this->sendError('Invalid image url', 400);
        }

        $question = $this->questionModel->getById($_GET['id']);

        if(!$question) {
            $this->sendError('Question not found', 404);
        }


        $this->questionModel->setIdQuestion($question['id_question']);
        $this->questionModel->setTitleQuestion($data->title);
        $this->questionModel->setDescriptionQuestion($data->description);
        $this->questionModel->setImgQuestion($data->image);
        $this->questionModel->setMultipleQuestion($data->multiple);
        $updatedQuestion = $this->questionModel->update();

        if(!$updatedQuestion) {
            $this->sendError('Question not updated', 404);
        }

        $this->send($updatedQuestion);
    }

    public function drop()
    {
        $this->delete();

        if(empty($_GET['id'])) {
            $this->sendError('Id is required', 400);
        }

        $question = $this->questionModel->getById($_GET['id']);

        if(!$question) {
            $this->sendError('Question not found', 404);
        }

        $this->questionModel->setIdQuestion($question['id']);
        $deletedQuestion = $this->questionModel->delete();

        if(!$deletedQuestion) {
            $this->sendError('Question not deleted', 404);
        }

        $this->send([
            'message' => 'Question deleted'
        ]);
    }
}