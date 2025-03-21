<?php

namespace model;

require_once './src/utils/utils.php';
require_once './src/abstracts/Model.php';

use abstracts\Model;
use PDO;
use PDOException;
use function utils\sanitize;

class QuestionModel extends Model
{
    private ?int $id_question;
    private ?string $title_question;
    private ?string $description_question;
    private ?string $img_question;
    private ?int $multiple_question;

    public function getIdQuestion(): ?int
    {
        return $this->id_question;
    }

    public function setIdQuestion(?int $id_question): void
    {
        $this->id_question = sanitize($id_question);
    }

    public function getTitleQuestion(): ?string
    {
        return $this->title_question;
    }

    public function setTitleQuestion(?string $title_question): void
    {
        $this->title_question = sanitize($title_question);
    }

    public function getDescriptionQuestion(): ?string
    {
        return $this->description_question;
    }

    public function setDescriptionQuestion(?string $description_question): void
    {
        $this->description_question = sanitize($description_question);
    }

    public function getImgQuestion(): ?string
    {
        return $this->img_question;
    }

    public function setImgQuestion(?string $img_question): void
    {
        $this->img_question = sanitize($img_question);
    }

    public function getMultipleQuestion(): ?int
    {
        return $this->multiple_question;
    }

    public function setMultipleQuestion(?int $multiple_question): void
    {
        $this->multiple_question = sanitize($multiple_question);
    }

    public function add(): array|false
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('INSERT INTO questions (title_question, description_question, img_question, multiple) VALUES (:title_question, :description_question, :img_question, :multiple)');
            $query->bindValue(':title_question', $this->getTitleQuestion());
            $query->bindValue(':description_question', $this->getDescriptionQuestion());
            $query->bindValue(':img_question', $this->getImgQuestion());
            $query->bindValue(':multiple', $this->getMultipleQuestion());
            $query->execute();
            return $this->getById($pdo->lastInsertId());
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAll(): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->query('SELECT * FROM questions');
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function update(): array|false
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('UPDATE questions SET title_question = :title_question, description_question = :description_question, img_question = :img_question, multiple = :multiple WHERE id_question = :id_question');
            $query->bindValue(':title_question', $this->getTitleQuestion());
            $query->bindValue(':description_question', $this->getDescriptionQuestion());
            $query->bindValue(':img_question', $this->getImgQuestion());
            $query->bindValue(':multiple', $this->getMultipleQuestion());
            $query->bindValue(':id_question', $this->getIdQuestion());
            $query->execute();
            return $this->getById($this->getIdQuestion());
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete(): bool {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('DELETE FROM questions WHERE id_question = :id_question');
            $query->bindValue(':id_question', $this->getIdQuestion());
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
            $query = $pdo->prepare('SELECT * FROM questions WHERE id_question = :id_question');
            $query->bindValue(':id_question', sanitize($id));
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

}