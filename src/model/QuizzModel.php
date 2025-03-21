<?php

namespace model;

require_once './src/utils/utils.php';
require_once './src/abstracts/Model.php';

use abstracts\Model;
use PDO;
use PDOException;
use function utils\sanitize;

class QuizzModel extends Model
{
    private ?int $id_quiz;

    private ?string $title_quiz;

    private ?string $description_quiz;

    private ?string $img_quiz;

    public function getTitleQuiz(): ?string
    {
        return $this->title_quiz;
    }

    public function setTitleQuiz(?string $title_quiz): void
    {
        $this->title_quiz = sanitize($title_quiz);
    }

    public function getImgQuiz(): ?string
    {
        return $this->img_quiz;
    }

    public function setImgQuiz(?string $img_quiz): void
    {
        $this->img_quiz = sanitize($img_quiz);
    }

    public function getIdQuiz(): ?int
    {
        return $this->id_quiz;
    }

    public function setIdQuiz(?int $id_quiz): void
    {
        $this->id_quiz = sanitize($id_quiz);
    }

    public function getDescriptionQuiz(): ?string
    {
        return $this->description_quiz;
    }

    public function setDescriptionQuiz(?string $description_quiz): void
    {
        $this->description_quiz = sanitize($description_quiz);
    }


    public function add(): array|false
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('INSERT INTO quizzies (title_quiz, description_quiz, img_quiz) VALUES (:title_quiz,:description_quiz,:img_quiz)');
            $query->bindValue(':title_quiz', $this->getTitleQuiz());
            $query->bindValue(':description_quiz', $this->getDescriptionQuiz());
            $query->bindValue(':img_quiz', $this->getImgQuiz());
            $query->execute();
            return $this->getById($pdo->lastInsertId());
        } catch (PDOException $e) {
            return false;
        }
    }

    public function addQuizCategory(int $idQuizz, int $idCategory): bool
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('INSERT INTO quizz_category (id_quiz, id_category) VALUES (:id_quiz, :id_category)');
            $query->bindValue(':id_quiz', sanitize($idQuizz), PDO::PARAM_INT);
            $query->bindValue(':id_category', sanitize($idCategory), PDO::PARAM_INT);
            return $query->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function addQuizQuestion(int $idQuiz,int $idQuestion): bool
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('INSERT INTO quizz_questions (id_quiz, id_question) VALUES (:id_quiz, :id_question)');
            $query->bindValue(':id_quiz', sanitize($idQuiz), PDO::PARAM_INT);
            $query->bindValue(':id_question', sanitize($idQuestion), PDO::PARAM_INT);
            return $query->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAll(): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->query('SELECT * FROM quizzies');
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getCategories(?int $idQuizz): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('
                SELECT c.*
                FROM categories c
                INNER JOIN quizz_category qc ON c.id_category = qc.id_category
                WHERE qc.id_quiz = :id_quiz
            ');
            $query->bindValue(':id_quiz', sanitize($idQuizz), PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getQuestions(int $idQuiz): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('
                SELECT q.*
                FROM questions q
                INNER JOIN quizz_questions qq ON q.id_question = qq.id_question
                WHERE qq.id_quiz = :id_quiz
            ');
            $query->bindValue(':id_quiz', sanitize($idQuiz), PDO::PARAM_INT);
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
            $query = $pdo->prepare('UPDATE quizzies SET title_quiz = :title_quiz, description_quiz = :description_quiz, img_quiz = :img_quiz WHERE id_quiz = :id_quiz');
            $query->bindValue(':title_quiz', $this->getTitleQuiz());
            $query->bindValue(':description_quiz', $this->getDescriptionQuiz());
            $query->bindValue(':img_quiz', $this->getImgQuiz());
            $query->bindValue(':id_quiz', $this->getIdQuiz());
            $query->execute();
            return $this->getById($this->getIdQuiz());
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete(): bool {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('DELETE FROM quizzies WHERE id_quiz = :id_quiz');
            $query->bindValue(':id_quiz', $this->getIdQuiz());
            $query->execute();
            return !$this->getById($this->getIdQuiz());
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getById(int $id): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT * FROM quizzies WHERE id_quiz = :id_quiz');
            $query->bindValue(':id_quiz', sanitize($id));
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

}