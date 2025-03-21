<?php

namespace model;

require_once './src/utils/utils.php';
require_once './src/abstracts/Model.php';

use abstracts\Model;
use PDO;
use PDOException;
use function utils\sanitize;

class CategoryModel extends Model
{
    private ?int $id_category;
    private ?string $title_category;

    public function getIdCategory(): ?int
    {
        return $this->id_category;
    }

    public function setIdCategory(?int $id_category): void
    {
        $this->id_category = sanitize($id_category);
    }

    public function getTitleCategory(): ?string
    {
        return $this->title_category;
    }

    public function setTitleCategory(?string $title_category): void
    {
        $this->title_category = sanitize($title_category);
    }

    public function add(): array|false
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('INSERT INTO categories (title_category) VALUES (:title_category)');
            $query->bindValue(':title_category', $this->getTitleCategory());
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
            $query = $pdo->query('SELECT * FROM categories');
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function update(): array|false
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('UPDATE categories SET title_category = :title_category WHERE id_category = :id_category');
            $query->bindValue(':title_category', $this->getTitleCategory());
            $query->bindValue(':id_category', $this->getIdCategory());
            $query->execute();
            return $this->getById($this->getIdCategory());
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete(): bool {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('DELETE FROM categories WHERE id_category = :id_category');
            $query->bindValue(':id_category', $this->getIdCategory());
            $query->execute();
            return !$this->getById($this->getIdCategory());
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getById(int $id): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT * FROM categories WHERE id_category = :id_category');
            $query->bindValue(':id_category', sanitize($id));
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getQuizzies(?int $idCategory): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('
                SELECT q.*
                FROM quizzies q
                INNER JOIN quizz_category qc ON q.id_quiz = qc.id_quiz
                WHERE qc.id_category = :id_category
            ');
            $query->bindValue(':id_category', sanitize($idCategory), PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}