<?php

namespace model;

require_once './src/utils/utils.php';
require_once './src/abstracts/Model.php';

use abstracts\Model;
use PDO;
use PDOException;
use function utils\sanitize;

class PlayModel extends Model
{
    private ?int $id_played;
    private ?int $id_user;
    private ?int $id_quizz;
    private ?int $id_question;
    private ?bool $successful_played;
    private ?string $created_at_played;

    public function getCreatedAtPlayed(): ?string
    {
        return $this->created_at_played;
    }

    public function setCreatedAtPlayed(?string $created_at_played): void
    {
        $this->created_at_played = sanitize($created_at_played);
    }

    public function getIdPlayed(): ?int
    {
        return $this->id_played;
    }

    public function setIdPlayed(?int $id_played): void
    {
        $this->id_played = sanitize($id_played);
    }

    public function getIdQuestion(): ?int
    {
        return $this->id_question;
    }

    public function setIdQuestion(?int $id_question): void
    {
        $this->id_question = sanitize($id_question);
    }

    public function getIdQuizz(): ?int
    {
        return $this->id_quizz;
    }

    public function setIdQuizz(?int $id_quizz): void
    {
        $this->id_quizz = sanitize($id_quizz);
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(?int $id_user): void
    {
        $this->id_user = sanitize($id_user);
    }

    public function getSuccessfulPlayed(): ?bool
    {
        return $this->successful_played;
    }

    public function setSuccessfulPlayed(?bool $successful_played): void
    {
        $this->successful_played = sanitize($successful_played);
    }

    public function add(): array|false
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('INSERT INTO played (successful_played, created_at_played, id_user, id_quiz, id_question) VALUES (:successful_played, :created_at_played, :id_user, :id_quiz, :id_question)');
            $query->bindValue(':successful_played', $this->getSuccessfulPlayed());
            $query->bindValue(':created_at_played', $this->getCreatedAtPlayed());
            $query->bindValue(':id_user', $this->getIdUser());
            $query->bindValue(':id_quiz', $this->getIdQuizz());
            $query->bindValue(':id_question', $this->getIdQuestion());
            $query->execute();
            return $this->getById($pdo->lastInsertId());
        } catch (PDOException $e) {
            return false;
        }
    }

    public function addPlayedAnswer(int $idPlayed,int $idAnswer): bool
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('INSERT INTO played_answers (id_played, id_answer) VALUES (:id_played, :id_answer)');
            $query->bindValue(':id_played', sanitize($idPlayed), PDO::PARAM_INT);
            $query->bindValue(':id_answer', sanitize($idAnswer), PDO::PARAM_INT);
            return $query->execute();
        } catch (PDOException $e) {
            return false;
        }
    }


    public function getPlayedAnswer(int $idPlayed, int $idAnswer): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT * FROM played_answers WHERE id_played = :id_played AND id_answer = :id_answer');
            $query->bindValue(':id_played', sanitize($idPlayed), PDO::PARAM_INT);
            $query->bindValue(':id_answer', sanitize($idAnswer), PDO::PARAM_INT);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function getAll(): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT * FROM played');
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById(int $id): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT * FROM played WHERE id_played = :id_played');
            $query->bindValue(':id_played', sanitize($id), PDO::PARAM_INT);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getPlayedAnswers(int $idPlayed): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT * FROM played_answers WHERE id_played = :id_played');
            $query->bindValue(':id_played', sanitize($idPlayed), PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getPlayedGamesByUser(int $idUser): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT * FROM played WHERE id_user = :id_user');
            $query->bindValue(':id_user', sanitize($idUser), PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getPlayedGameByUser(int $idUser, int $idQuiz): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT * FROM played WHERE id_user = :id_user AND id_quiz = :id_quiz');
            $query->bindValue(':id_user', sanitize($idUser), PDO::PARAM_INT);
            $query->bindValue(':id_quiz', sanitize($idQuiz), PDO::PARAM_INT);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

}