<?php

namespace model;

require_once './src/utils/utils.php';
require_once './src/abstracts/Model.php';

use abstracts\Model;
use PDO;
use PDOException;
use function utils\sanitize;

class AnswerModel extends Model
{
    private ?int $id_answer;
    private ?string $text_answer;
    private ?int $valid_answer;
    private ?int $point_answer;
    private ?int $id_question;

    public function getIdAnswer(): ?int
    {
        return $this->id_answer;
    }

    public function setIdAnswer(?int $id_answer): void
    {
        $this->id_answer = sanitize($id_answer);
    }

    public function getIdQuestion(): ?int
    {
        return $this->id_question;
    }

    public function setIdQuestion(?int $id_question): void
    {
        $this->id_question = sanitize($id_question);
    }

    public function getPointAnswer(): ?int
    {
        return $this->point_answer;
    }

    public function setPointAnswer(?int $point_answer): void
    {
        $this->point_answer = sanitize($point_answer);
    }

    public function getTextAnswer(): ?string
    {
        return $this->text_answer;
    }

    public function setTextAnswer(?string $text_answer): void
    {
        $this->text_answer = sanitize($text_answer);
    }

    public function getValidAnswer(): ?int
    {
        return $this->valid_answer;
    }

    public function setValidAnswer(?int $valid_answer): void
    {
        $this->valid_answer = sanitize($valid_answer);
    }

    public function add(): array|false
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('INSERT INTO answers (text_answer, valid_answer, answer_point, id_question) VALUES (:text_answer, :valid_answer, :answer_point, :id_question)');
            $query->bindValue(':text_answer', $this->getTextAnswer());
            $query->bindValue(':valid_answer', $this->getValidAnswer());
            $query->bindValue(':answer_point', $this->getPointAnswer());
            $query->bindValue(':id_question', $this->getIdQuestion());
            $query->execute();
            return $this->getById($pdo->lastInsertId());
        } catch (PDOException $e) {
            var_dump($e);
            return false;
        }
    }

    public function getAll(): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT * FROM answers WHERE id_question = :id_question');
            $query->bindValue(':id_question', $this->getIdQuestion(), PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAnswersByQuestion(int $idQuestion): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT * FROM answers WHERE id_question = :id_question');
            $query->bindValue(':id_question', sanitize($idQuestion), PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function update(): array|false
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('UPDATE answers SET text_answer = :text_answer, valid_answer = :valid_answer, answer_point = :answer_point WHERE id_answer = :id_answer');
            $query->bindValue(':text_answer', $this->getTextAnswer());
            $query->bindValue(':valid_answer', $this->getValidAnswer());
            $query->bindValue(':answer_point', $this->getPointAnswer());
            $query->bindValue(':id_answer', $this->getIdAnswer());
            $query->execute();
            return $this->getById($this->getIdQuestion());
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete(): bool {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('DELETE FROM answers WHERE id_answer = :id_answer');
            $query->bindValue(':id_answer', $this->getIdQuestion());
            $query->execute();
            return !$this->getById($this->getIdQuestion());
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getById(int $id): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT * FROM answers WHERE id_answer = :id_answer');
            $query->bindValue(':id_answer', sanitize($id), PDO::PARAM_INT);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

}