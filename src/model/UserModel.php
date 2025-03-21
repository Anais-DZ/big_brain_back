<?php

namespace model;

require_once './src/utils/utils.php';
require_once './src/abstracts/Model.php';

use abstracts\Model;
use PDO;
use PDOException;
use function utils\sanitize;

class UserModel extends Model
{
    private ?int $id_user;
    private ?string $firstname_user;
    private ?string $lastname_user;
    private ?string $email_user;
    private ?string $password_user;
    private ?array $roles_user;
    private ?string $avatar_user;

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(?int $id_user): void
    {
        $this->id_user = sanitize($id_user);
    }

    public function getFirstnameUser(): ?string
    {
        return $this->firstname_user;
    }

    public function setFirstnameUser(?string $firstname_user): void
    {
        $this->firstname_user = sanitize($firstname_user);
    }

    public function getLastnameUser(): ?string
    {
        return $this->lastname_user;
    }

    public function setLastnameUser(?string $lastname_user): void
    {
        $this->lastname_user = sanitize($lastname_user);
    }

    public function getEmailUser(): ?string
    {
        return $this->email_user;
    }

    public function setEmailUser(?string $email_user): void
    {
        $this->email_user = sanitize($email_user);
    }

    public function getRolesUser(): array
    {
        return $this->roles_user;
    }

    public function setRolesUser(?array $roles_user): void
    {
        $this->roles_user = $roles_user;
    }

    public function getAvatarUser(): ?string
    {
        return $this->avatar_user;
    }

    public function setAvatarUser(?string $avatar_user): void
    {
        $this->avatar_user = sanitize($avatar_user);
    }

    public function getPasswordUser(): ?string
    {
        return $this->password_user;
    }

    public function setPasswordUser(?string $password_user): void
    {
        $this->password_user = password_hash($password_user, PASSWORD_BCRYPT);
    }

    public function add(): array|false
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('INSERT INTO users (firstname_user, lastname_user, email_user,password_user,roles_user,avatar_user) VALUES (:firstname_user,:lastname_user,:email_user,:password_user,:roles_user,:avatar_user)');
            $query->bindValue(':firstname_user', $this->getFirstnameUser());
            $query->bindValue(':lastname_user', $this->getLastnameUser());
            $query->bindValue(':email_user', $this->getEmailUser());
            $query->bindValue(':password_user', $this->getPasswordUser());
            $query->bindValue(':roles_user', json_encode($this->getRolesUser()));
            $query->bindValue(':avatar_user', $this->getAvatarUser());
            $query->execute();
            return $this->getByEmail($this->getEmailUser());
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAll(): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->query('SELECT id_user,firstname_user, lastname_user, email_user,roles_user,avatar_user FROM users');
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getPlayedQuizzes(): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('
                SELECT p.*, qz.title AS quiz_title, q.content AS question_content
                FROM played p
                INNER JOIN quizzies qz ON p.id_quiz = qz.id_quiz
                INNER JOIN questions q ON p.id_question = q.id_question
                WHERE p.id_user = :id_user
            ');
            $query->bindValue(':id_user', $this->getIdUser(), PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getPasswordHashByEmail(string $email)
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT password_user FROM users WHERE email_user = :email_user');
            $query->bindValue(':email_user', sanitize($email));
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getByEmail(string $email): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT id_user,firstname_user, lastname_user, email_user,roles_user,avatar_user FROM users WHERE email_user = :email_user');
            $query->bindValue(':email_user', sanitize($email));
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function update(): array|false
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('UPDATE users SET 
                 firstname_user = :firstname_user,
                 lastname_user = :lastname_user,
                email_user = :email_user,
                roles_user = :roles_user,
                avatar_user = :avatar_user
                WHERE
                id_user = :id_user
                 ');
            $query->bindValue(':firstname_user', $this->getFirstnameUser());
            $query->bindValue(':lastname_user', $this->getLastnameUser());
            $query->bindValue(':email_user', $this->getEmailUser());
            $query->bindValue(':roles_user', $this->getRolesUser());
            $query->bindValue(':avatar_user', $this->getAvatarUser());
            $query->bindValue(':id_user', $this->getIdUser());
            $query->execute();
            return $this->getById($this->getIdUser());
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete(): bool
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('DELETE FROM users WHERE id_user = :id');
            $query->bindValue(':id_user', $this->getIdUser());
            $query->execute();
            return !$this->getById($this->getIdUser());
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getById(int $id): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT id_user,firstname_user, lastname_user, email_user,roles_user,avatar_user FROM users WHERE id_user = :id_user');
            $query->bindValue(':id_user', sanitize($id));
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getByToken(string $token) {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('
                SELECT u.id_user, u.firstname_user, u.lastname_user, u.email_user, u.roles_user, u.avatar_user, t.*
                FROM tokens t
                INNER JOIN users u ON t.id_user = u.id_user
                WHERE t.token = :token
            ');
            $query->bindValue(':token', sanitize($token));
            $query->execute();
            
            $user = $query->fetch(PDO::FETCH_ASSOC) ?: [];

            //if token expires return error
            if ($user && strtotime($user['expires_at']) > time()) {
                return $user;
            } elseif ($user && strtotime($user['expires_at']) <= time()) {
                return ['error' => 'Token has expired.','code' => 'EXPIRED_TOKEN'];
            } else {
                return [];
            }
         } catch (PDOException $e) {
                return [];
            }
    }

    public function login(string $email, string $password): array|false
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT id_user,firstname_user, lastname_user, email_user,roles_user,avatar_user FROM users WHERE email_user = :email_user');
            $query->bindValue(':email_user', sanitize($email));
            $query->execute();
            $user = $query->fetch(PDO::FETCH_ASSOC) ?: [];

            $passwordHash = $this->getPasswordHashByEmail($email);

            if(!$passwordHash) {
                return false;
            }

            if ($user && password_verify($password, $passwordHash['password_user'])) {


                $queryDelete = $pdo->prepare('DELETE FROM tokens WHERE id_user = :id_user');
                $queryDelete->bindValue(':id_user', sanitize($user['id_user']));
                $queryDelete->execute();

                $token = bin2hex(random_bytes(16));
                $expiresAt = date('Y-m-d H:i:s', strtotime('+1 day'));
                $queryInsert = $pdo->prepare('INSERT INTO tokens (token, expires_at, id_user) VALUES (:token, :expires_at, :id_user)');
                $queryInsert->bindValue(':token', sanitize($token));
                $queryInsert->bindValue(':expires_at', sanitize($expiresAt));
                $queryInsert->bindValue(':id_user', sanitize($user['id_user']));
                $queryInsert->execute();

                $user['token'] = $token;

                return $user;
            }

            return false;
        }
        catch (PDOException $e) {}
    }

    public function verifyToken(string $token): bool
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT id_user,expires_at FROM tokens WHERE token = :token');
            $query->bindValue(':token', sanitize($token));
            $query->execute();
            $tokenData = $query->fetch(PDO::FETCH_ASSOC) ?: [];

            if ($tokenData && strtotime($tokenData['expires_at']) > time()) {
                return $tokenData['user_id'];
            }

            $queryDelete = $pdo->prepare('DELETE FROM tokens WHERE token = :token');
            $queryDelete->bindValue(':token', sanitize($token));
            $queryDelete->execute();
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAllPlayedById(int $idUser): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT * FROM played WHERE id_user = :id_user');
            $query->bindValue(':id_user', sanitize($idUser), PDO::PARAM_INT);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getPlayedById(int $idUser, int $id): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT * FROM played WHERE id_user = :id_user AND id_quiz = :id_quiz');
            $query->bindValue(':id_user', sanitize($idUser), PDO::PARAM_INT);
            $query->bindValue(':id_played', sanitize($id), PDO::PARAM_INT);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getPlayedAnswersById(int $idUser, int $id): array
    {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('SELECT * FROM played_answers WHERE id_played = :id_played');
            $query->bindValue(':id_played', sanitize($id), PDO::PARAM_INT);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function logout(string $idUser): bool {
        try {
            $pdo = $this->getBdd();
            $query = $pdo->prepare('DELETE FROM tokens WHERE id_user = :id_user');
            $query->bindValue(':id_user', sanitize($idUser));
            $query->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

}