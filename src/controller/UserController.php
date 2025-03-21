<?php

namespace controller;

require_once './src/model/UserModel.php';
require_once './src/abstracts/Controller.php';
require_once './src/model/PlayModel.php';
require_once './src/model/QuizzModel.php';
require_once './src/model/QuestionModel.php';
require_once './src/model/AnswerModel.php';

use abstracts\Controller;
use model\AnswerModel;
use model\PlayModel;
use model\QuestionModel;
use model\QuizzModel;

class UserController extends Controller
{
    private PlayModel $playModel;
    private QuizzModel $quizzModel;
    private QuestionModel $questionModel;
    private AnswerModel $answerModel;

    public function __construct()
    {
        parent::__construct();
        $this->playModel = new PlayModel();
        $this->quizzModel = new QuizzModel();
        $this->questionModel = new QuestionModel();
        $this->answerModel = new AnswerModel();
    }

    public function all(): void
    {
        $this->get();
        $this->send($this->userModel->getAll());
    }

    public function me(): void
    {
        $this->get();
        $this->send($this->makeAuthenticated());
    }

    public function register(): void
    {
        $this->post();

        $data = $this->getJson();


        if(empty($data->email) || empty($data->password) || empty($data->password_confirm) || empty($data->lastname) || empty($data->firstname)) {
            $this->sendError('All fields are required', 400);
        }

        if($data->password !== $data->password_confirm) {
            $this->sendError('Passwords do not match', 400);
        }

        if(!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            $this->sendError('Invalid email', 400);
        }

        $existingUser = $this->userModel->getByEmail($data->email);

        if($existingUser) {
            $this->sendError('User already exists', 400);
        }

        if(strlen($data->password) < 8) {
            $this->sendError('Password must be at least 8 characters', 400);
        }

        if(strlen($data->firstname) < 3) {
            $this->sendError('Firstname must be at least 3 characters', 400);
        }

        if(strlen($data->lastname) < 3) {
            $this->sendError('Lastname must be at least 3 characters', 400);
        }


        $this->userModel->setFirstnameUser($data->firstname);
        $this->userModel->setLastnameUser($data->lastname);
        $this->userModel->setEmailUser($data->email);
        $this->userModel->setPasswordUser($data->password);
        $this->userModel->setRolesUser(['ROLE_USER']);
        $this->userModel->setAvatarUser('default.png');
        $user = $this->userModel->add();

        if(!$user) {
            $this->sendError('User not created', 404);
        }

        $this->send($user,201);
    }

    public function logout(): void
    {
        $this->post();
        $user = $this->makeAuthenticated();
        $this->userModel->logout($user['id_user']);
        $this->send(['message' => 'User logged out']);
    }

    public function login(): void
    {
     $this->post();

     $data = $this->getJson();

     if(empty($data->email) || empty($data->password)) {
         $this->sendError('All fields are required', 400);
     }

     $user = $this->userModel->getByEmail($data->email);

     if(!$user) {
         $this->sendError('User not found', 404);
     }

     $token = $this->userModel->login($data->email, $data->password);

     if(!$token) {
         $this->sendError('Invalid credentials', 401);
     }

     $this->send($token);
    }

    public function create(): void
    {
        $this->post();

        $data = $this->getJson();

        if(empty($data->email) || empty($data->password) || empty($data->firstname) || empty($data->lastname)) {
            $this->sendError('All fields are required', 400);
        }

        $existingUser = $this->userModel->getByEmail($data->email);

        if($existingUser) {
            $this->sendError('User already exists', 400);
        }

        if(!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            $this->sendError('Invalid email', 400);
        }

        $this->userModel->setFirstnameUser($data->firstname);
        $this->userModel->setLastnameUser($data->lastname);
        $this->userModel->setEmailUser($data->email);
        $this->userModel->setPasswordUser($data->password);
        $this->userModel->setRolesUser(['ROLE_USER']);
        $this->userModel->setAvatarUser('default.png');
        $user = $this->userModel->add();

        if(!$user) {
            $this->sendError('User not created', 404);
        }

        $this->send($user,201);
    }

    public function show()
    {
        $this->get();

        if(empty($_GET['id'])) {
            $this->sendError('Id is required', 400);
        }

        if(!filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
            $this->sendError('Invalid id', 400);
        }

        $user = $this->userModel->getById($_GET['id']);

        if(!$user) {
            $this->sendError('User not found', 404);
        }

        $this->send($user);
    }

    public function update()
    {
        $this->put();
        $data = $this->getJson();

        if(empty($data->email) || empty($data->firstname) || empty($data->lastname) || empty($data->avatar)) {
            $this->sendError('All fields are required', 400);
        }

        $user = $this->userModel->getById($_GET['id']);

        if(!$user) {
            $this->sendError('User not found', 404);
        }

        if(!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            $this->sendError('Invalid email', 400);
        }

        $this->userModel->setIdUser($user['id_user']);
        $this->userModel->setFirstnameUser($data->firstname);
        $this->userModel->setLastnameUser($data->lastname);
        $this->userModel->setEmailUser($data->email);
        $this->userModel->setAvatarUser($data->avatar);
        $this->userModel->setRolesUser(['ROLE_USER']); //not impl :/
        $updatedUser = $this->userModel->update();

        if(!$updatedUser) {
            $this->sendError('User not updated', 404);
        }

        $this->send($updatedUser);
    }

    public function drop()
    {
        $this->delete();

        if(empty($_GET['id'])) {
            $this->sendError('Id is required', 400);
        }

        $user = $this->userModel->getById($_GET['id']);

        if(!$user) {
            $this->sendError('User not found', 404);
        }

        $this->userModel->setIdUser($user['id']);
        $deletedUser = $this->userModel->delete();

        if(!$deletedUser) {
            $this->sendError('User not deleted', 404);
        }

        $this->send([
            'message' => 'User deleted'
        ]);
    }

    public function getPlayedGames()
    {
        $this->get();
        $user = $this->makeAuthenticated();

        $playedGames = $this->playModel->getPlayedGamesByUser($user['id_user']);

        if(!$playedGames) {
            $this->send([]);
        }

        foreach($playedGames as $key => $playedGame) {

            $quizz = $this->quizzModel->getById($playedGame['id_quiz']);

            if(!$quizz) {
                $this->sendError('Quizz not found for this played game', 404);
            }

            $question = $this->questionModel->getById($playedGame['id_question']);

            if(!$question) {
                $this->sendError('Question not found for this played game', 404);
            }

            $answer = $this->answerModel->getAnswersByQuestion($playedGame['id_question']);

            if(!$answer) {
                $this->sendError('Answer not found for this played game', 404);
            }

            $playedGames[$key] = $playedGame;
            $playedGames[$key]['answer'] = $answer;
            $playedGames[$key]['question'] = $question;
            $playedGames[$key]['quizz'] = $quizz;

        }


        $this->send($playedGames);

    }

    public function getPlayedGame()
        {
        $this->get();
        $user = $this->makeAuthenticated();

        if(empty($_GET['id'])) {
            $this->sendError('Id is required', 400);
        }

            $playedGame = $this->playModel->getPlayedGameByUser($user['id_user'],$_GET['id']);

            if(!$playedGame) {
                $this->sendError('Played game not found', 404);
            }

            $quizz = $this->quizzModel->getById($playedGame['id_quiz']);

            if(!$quizz) {
                $this->sendError('Quizz not found for this played game', 404);
            }

            $question = $this->questionModel->getById($playedGame['id_question']);

            if(!$question) {
                $this->sendError('Question not found for this played game', 404);
            }

            $answer = $this->answerModel->getAnswersByQuestion($playedGame['id_question']);

            if(!$answer) {
                $this->sendError('Answer not found for this played game', 404);
            }

            $playedGame['answer'] = $answer;
            $playedGame['question'] = $question;
            $playedGame['quizz'] = $quizz;

            $this->send($playedGame);
        }

    public function createPlayedGameAnswer() {
        $this->post();
        $user = $this->makeAuthenticated();
        $data = $this->getJson();

        if(empty($data->id_answer) || empty($data->id_question) || empty($data->id_quiz)) {
            $this->sendError('All fields are required', 400);
        }

        if(!filter_var($data->id_quiz, FILTER_VALIDATE_INT)) {
            $this->sendError('Invalid id quiz', 400);
        }

        if(!filter_var($data->id_question, FILTER_VALIDATE_INT)) {
            $this->sendError('Invalid id question', 400);
        }

        if(!filter_var($data->id_answer, FILTER_VALIDATE_INT)) {
            $this->sendError('Invalid id answer', 400);
        }

        $quizz = $this->quizzModel->getById($data->id_quiz);

        if(!$quizz) {
            $this->sendError('Quizz not found', 404);
        }

        $question = $this->questionModel->getById($data->id_question);

        if(!$question) {
            $this->sendError('Question not found', 404);
        }

        $answer = $this->answerModel->getById($data->id_answer);

        if(!$answer) {
            $this->sendError('Answer not found', 404);
        }

        $this->playModel->setIdQuestion($data->id_question);
        $this->playModel->setIdUser($user['id_user']);
        $this->playModel->setIdQuizz($data->id_quiz);
        $this->playModel->setSuccessfulPlayed(1);
        $this->playModel->setCreatedAtPlayed(date('Y-m-d H:i:s'));

        $playedGame = $this->playModel->add();

        if(!$playedGame) {
            $this->sendError('Played game not created', 404);
        }

        $this->playModel->addPlayedAnswer($playedGame['id_played'], $answer['id_answer']);

        $playedGame['answer'] = $answer;

        $this->send($playedGame);
    }
}