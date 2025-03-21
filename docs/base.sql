-- Crée une base de données nommée 'quiz' avec un jeu de caractères UTF-8 si elle n'existe pas déjà
CREATE DATABASE IF NOT EXISTS quiz CHARSET = utf8mb4;

-- Sélectionne la base de données 'quiz' pour y exécuter les requêtes suivantes
USE quiz;

-- Crée une table 'users' pour stocker les informations des utilisateurs
CREATE TABLE users
(
    id_user        INT PRIMARY KEY AUTO_INCREMENT, -- Identifiant unique de l'utilisateur, auto-incrémenté
    firstname_user VARCHAR(50),                    -- Prénom de l'utilisateur
    lastname_user  VARCHAR(50),                    -- Nom de famille de l'utilisateur
    email_user     VARCHAR(50),                    -- Adresse e-mail de l'utilisateur
    password_user  VARCHAR(250),                   -- Mot de passe de l'utilisateur (devrait être haché)
    roles_user     VARCHAR(100),                   -- Rôles de l'utilisateur (par exemple, admin, user)
    avatar_user    VARCHAR(255)                    -- URL ou chemin vers l'avatar de l'utilisateur
) ENGINE = Innodb;

-- Crée une table 'questions' pour stocker les questions du quiz
CREATE TABLE questions
(
    id_question          INT PRIMARY KEY AUTO_INCREMENT, -- Identifiant unique de la question, auto-incrémenté
    title_question       VARCHAR(100),                   -- Titre de la question
    description_question VARCHAR(255),                   -- Description ou texte de la question
    img_question         VARCHAR(255),                   -- URL ou chemin vers l'image associée à la question
    multiple             INT                             -- Indique si la question a plusieurs réponses possibles
) ENGINE = Innodb;

-- Crée une table 'categories' pour stocker les catégories des quiz
CREATE TABLE categories
(
    id_category    INT PRIMARY KEY AUTO_INCREMENT, -- Identifiant unique de la catégorie, auto-incrémenté
    title_category VARCHAR(50)                     -- Titre de la catégorie
) ENGINE = Innodb;

-- Crée une table 'answers' pour stocker les réponses aux questions
CREATE TABLE answers
(
    id_answer    INT PRIMARY KEY AUTO_INCREMENT,                 -- Identifiant unique de la réponse, auto-incrémenté
    text_answer  VARCHAR(100),                                   -- Texte de la réponse
    valid_answer BOOLEAN,                                        -- Indique si la réponse est correcte
    answer_point INT,                                            -- Points attribués pour cette réponse
    id_question  INT NOT NULL,                                   -- Identifiant de la question associée
    FOREIGN KEY (id_question) REFERENCES questions (id_question) -- Clé étrangère vers la table 'questions'
);

-- Crée une table 'quizzies' pour stocker les informations sur les quiz
CREATE TABLE quizzies
(
    id_quiz          INT PRIMARY KEY AUTO_INCREMENT, -- Identifiant unique du quiz, auto-incrémenté
    title_quiz       VARCHAR(255),                   -- Titre du quiz
    description_quiz TEXT,                           -- Description du quiz
    img_quiz         VARCHAR(255)                    -- URL ou chemin vers l'image associée au quiz
);

-- Crée une table 'played' pour stocker les informations sur les quiz joués par les utilisateurs
CREATE TABLE played
(
    id_played         INT PRIMARY KEY AUTO_INCREMENT,            -- Identifiant unique de la partie jouée, auto-incrémenté
    successful_played BOOLEAN,                                   -- Indique si la partie a été réussie
    created_at_played DATETIME,                                  -- Date et heure de la partie jouée
    id_user           INT NOT NULL,                              -- Identifiant de l'utilisateur
    id_quiz           INT NOT NULL,                              -- Identifiant du quiz
    id_question       INT NOT NULL,                              -- Identifiant de la question
    FOREIGN KEY (id_user) REFERENCES users (id_user),            -- Clé étrangère vers la table 'users'
    FOREIGN KEY (id_quiz) REFERENCES quizzies (id_quiz),         -- Clé étrangère vers la table 'quizzies'
    FOREIGN KEY (id_question) REFERENCES questions (id_question) -- Clé étrangère vers la table 'questions'
);

-- Crée une table 'played_answers' pour stocker les réponses données par les utilisateurs lors des parties jouées
CREATE TABLE played_answers
(
    id_answer INT,                                          -- Identifiant de la réponse
    id_played INT,                                          -- Identifiant de la partie jouée
    PRIMARY KEY AUTO_INCREMENT (id_answer, id_played),      -- Clé primaire composite
    FOREIGN KEY (id_answer) REFERENCES answers (id_answer), -- Clé étrangère vers la table 'answers'
    FOREIGN KEY (id_played) REFERENCES played (id_played)   -- Clé étrangère vers la table 'played'
);

-- Crée une table 'quizz_category' pour associer les quiz aux catégories
CREATE TABLE quizz_category
(
    id_category INT,                                               -- Identifiant de la catégorie
    id_quiz     INT,                                               -- Identifiant du quiz
    PRIMARY KEY AUTO_INCREMENT (id_category, id_quiz),             -- Clé primaire composite
    FOREIGN KEY (id_category) REFERENCES categories (id_category), -- Clé étrangère vers la table 'categories'
    FOREIGN KEY (id_quiz) REFERENCES quizzies (id_quiz)            -- Clé étrangère vers la table 'quizzies'
);

-- Crée une table 'quizz_questions' pour associer les questions aux quiz
CREATE TABLE quizz_questions
(
    id_question INT,                                              -- Identifiant de la question
    id_quiz     INT,                                              -- Identifiant du quiz
    PRIMARY KEY AUTO_INCREMENT (id_question, id_quiz),            -- Clé primaire composite
    FOREIGN KEY (id_question) REFERENCES questions (id_question), -- Clé étrangère vers la table 'questions'
    FOREIGN KEY (id_quiz) REFERENCES quizzies (id_quiz)           -- Clé étrangère vers la table 'quizzies'
);

-- Crée une table 'tokens' pour stocker les tokens d'authentification des utilisateurs
CREATE TABLE tokens
(
    id_user    INT       NOT NULL,                   -- Identifiant de l'utilisateur
    token      TEXT      NOT NULL,                   -- Token d'authentification
    expires_at TIMESTAMP NOT NULL,                   -- Date et heure d'expiration du token
    FOREIGN KEY (id_user) REFERENCES users (id_user) -- Clé étrangère vers la table 'users'
);

-- Insère des catégories initiales dans la table 'categories'
INSERT INTO categories (title_category)
VALUES ('Front-End'),
       ('Back-End');

-- Insère des quiz initiaux dans la table 'quizzies'
INSERT INTO quizzies (title_quiz, description_quiz, img_quiz)
VALUES ('HTML', 'Le langage qui structure les pages web. Connaissez-vous bien ses balises ?', ''),
       ('CSS', 'Les styles qui donnent vie aux sites web. Maîtrisez-vous les sélecteurs ?', ''),
       ('JavaScript', 'Le langage de programmation du web. Prêt à tester vos compétences ?', ''),
       ('PHP', 'Le moteur des sites dynamiques. Saurez-vous répondre aux questions ?', ''),
       ('MySQL', 'La gestion des bases de données. Êtes-vous à l’aise avec les requêtes SQL ?', ''),
       ('Frameworks Front-End', 'Des outils comme React ou Vue pour booster le développement web.', ''),
       ('Sécurité Web', 'Protéger un site contre les attaques. Testez vos connaissances !', ''),
       ('API & Web Services', 'L\'échange de données entre applications. Comprenez-vous bien leur fonctionnement ?',
        ''),
       ('DevOps & Hébergement', 'Déploiement, serveurs et CI/CD. Un domaine clé du web moderne !', ''),
       ('Accessibilité & UX/UI', 'Créer des sites accessibles et ergonomiques. Êtes-vous incollable ?', '');

-- Insère des questions initiales dans la table 'questions'
INSERT INTO questions (title_question, description_question, img_question, multiple)
VALUES ('HTML', 'Quel élément HTML est utilisé pour créer un lien hypertexte ?', '', 0),
       ('HTML', 'Quelle est la balise correcte pour insérer une image dans une page HTML ?', '', 0),
       ('HTML', 'Quelle balise HTML est utilisée pour créer un titre de niveau 1 ?', '', 0),
       ('HTML', 'Quelle est la balise correcte pour une liste ordonnée ?', '', 0),
       ('HTML', 'Quelle balise permet d’insérer du JavaScript dans une page HTML ?', '', 0),
       ('CSS', 'Quelle propriété CSS permet de changer la couleur du texte ?', '', 0),
       ('CSS', 'Quelle valeur de position permet de fixer un élément en haut de la page même en scrollant ?', '', 0),
       ('CSS', 'Quelle unité est relative à la taille de la police du parent ?', '', 0),
       ('CSS', 'Quelle propriété CSS est utilisée pour arrondir les bords d’un élément ?', '', 0),
       ('CSS', 'Quel sélecteur CSS permet de cibler tous les éléments d’une page ?', '', 0),
       ('JavaScript', 'Quelle est la bonne syntaxe pour déclarer une variable en JavaScript (ES6) ?', '', 0),
       ('JavaScript', 'Quelle fonction permet d\'afficher un message dans la console du navigateur ?', '', 0),
       ('JavaScript', 'Quelle méthode permet d\'ajouter un élément à la fin d\'un tableau en JavaScript ?', '', 0),
       ('JavaScript', 'Que retourne typeof [] en JavaScript ?', '', 0),
       ('JavaScript', 'Quelle structure est utilisée pour parcourir un tableau en JavaScript ?', '', 0),
       ('PHP', 'Quelle syntaxe est correcte pour afficher "Hello World" en PHP ?', '', 0),
       ('PHP', 'Quelle extension de fichier est utilisée pour les scripts PHP ?', '', 0),
       ('PHP', 'Quelle superglobale PHP contient les données envoyées par un formulaire en méthode POST ?', '', 0),
       ('PHP', 'Quel mot-clé est utilisé pour définir une fonction en PHP ?', '', 0),
       ('PHP', 'Quelle est la bonne manière de déclarer une variable en PHP ?', '', 0),
       ('MySQL', 'Quelle commande SQL est utilisée pour récupérer des données dans une table ?', '', 0),
       ('MySQL', 'Quelle clause SQL est utilisée pour filtrer les résultats d\'une requête ?', '', 0),
       ('MySQL', 'Quel type de jointure retourne uniquement les lignes ayant une correspondance dans les deux tables ?',
        '', 0),
       ('MySQL', 'Quelle commande SQL est utilisée pour insérer une nouvelle ligne dans une table ?', '', 0),
       ('MySQL', 'Quelle commande SQL permet de supprimer une table entière ?', '', 0),
       ('Frameworks Front-End', 'Quel langage est principalement utilisé avec React, Vue et Angular ?', '', 0),
       ('Frameworks Front-End', 'Quel framework utilise un fichier package.json pour gérer les dépendances ?', '', 0),
       ('Frameworks Front-End', 'Quel framework utilise le concept de "directives" pour manipuler le DOM ?', '', 0),
       ('Frameworks Front-End', 'Quelle bibliothèque React est utilisée pour gérer le state global d’une application ?',
        '', 0),
       ('Frameworks Front-End', 'Quel framework front-end utilise un fichier angular.json pour la configuration ?', '',
        0),
       ('Sécurité Web',
        'Quelle attaque consiste à injecter du code malveillant dans un site web via des champs de formulaire ?', '',
        0),
       ('Sécurité Web', 'Quel protocole est utilisé pour sécuriser les échanges de données sur le web ?', '', 0),
       ('Sécurité Web', 'Que signifie "XSS" en sécurité web ?', '', 0),
       ('Sécurité Web', 'Quel en-tête HTTP est utilisé pour empêcher l\'exécution de scripts non autorisés ?', '', 0),
       ('Sécurité Web', 'Quelle est la meilleure pratique pour stocker des mots de passe en base de données ?', '', 0),
       ('API & Web Services', 'Quelle est la différence entre REST et SOAP ?', '', 0),
       ('API & Web Services', 'Quel verbe HTTP est utilisé pour récupérer des données depuis une API ?', '', 0),
       ('API & Web Services', 'Quel code de statut HTTP indique une requête réussie ?', '', 0),
       ('API & Web Services', 'Quel format est couramment utilisé pour échanger des données avec une API REST ?', '',
        0),
       ('API & Web Services', 'Quel est l’objectif principal d’une API ?', '', 0),
       ('DevOps & Hébergement', 'Quel outil est couramment utilisé pour la conteneurisation d\'applications?', '', 0),
       ('DevOps & Hébergement', 'Quel système est utilisé pour le déploiement continu (CI/CD) ?', '', 0),
       ('DevOps & Hébergement',
        'Quelle commande permet de vérifier l’état des services en cours d’exécution sur un serveur Linux ?', '', 0),
       ('DevOps & Hébergement', 'Quelle est la différence entre un serveur web Apache et Nginx ?', '', 0),
       ('DevOps & Hébergement', 'Quelle est la principale utilité d’un CDN (Content Delivery Network) ?', '', 0),
       ('Accessibilité & UX/UI', 'Que signifie l’acronyme UX ?', '', 0),
       ('Accessibilité & UX/UI', 'Quel outil est utilisé pour tester l’accessibilité d’un site web ?', '', 0),
       ('Accessibilité & UX/UI', 'Que permet la propriété CSS :focus ?', '', 0),
       ('Accessibilité & UX/UI', 'Quelle est la meilleure couleur pour assurer un bon contraste pour l’accessibilité ?',
        '', 0),
       ('Accessibilité & UX/UI', 'Quel attribut HTML améliore l’accessibilité des images ?', '', 0);

-- Insère des réponses initiales dans la table 'answers'
INSERT INTO answers (text_answer, valid_answer, answer_point, id_question)
VALUES ('<link>', 0, 0, 1),
       ('<a>', 1, 1, 1),
       ('<href>', 0, 0, 1),
       ('<href>', 0, 0, 1),
       ('<image>', 0, 0, 2),
       ('<img> ', 1, 1, 2),
       ('<picture>', 0, 0, 2),
       ('<src>', 0, 0, 2),
       ('<header>', 0, 0, 3),
       ('<h1>', 1, 1, 3),
       ('<title>', 0, 0, 3),
       ('<head>', 0, 0, 3),
       ('<ul>', 0, 0, 4),
       ('<li>', 0, 0, 4),
       ('<ol>', 1, 1, 4),
       ('<list>', 0, 0, 4),
       ('<js>', 0, 0, 5),
       ('<script>', 1, 1, 5),
       ('<javascript>', 0, 0, 5),
       ('<code>', 0, 0, 5),
       ('background-color', 0, 0, 6),
       ('text-color', 0, 0, 6),
       ('color', 1, 1, 6),
       ('font-color', 0, 0, 6),
       ('relative', 0, 0, 7),
       ('absolute', 0, 0, 7),
       ('fixed', 1, 1, 7),
       ('sticky', 0, 0, 7),
       ('px', 0, 0, 8),
       ('em', 1, 1, 8),
       ('%', 0, 0, 8),
       ('vh', 0, 0, 8),
       ('border-color', 0, 0, 9),
       ('border-radius ', 1, 1, 9),
       ('border-style', 0, 0, 9),
       ('border-width', 0, 0, 9),
       ('#all', 0, 0, 10),
       ('*', 1, 1, 10),
       ('all', 0, 0, 10),
       ('body', 0, 0, 10),
       ('var myVar', 0, 0, 11),
       ('let myVar ', 1, 1, 11),
       ('const myVar', 0, 0, 11),
       ('variable myVar ', 0, 0, 11),
       ('console.log()', 1, 1, 12),
       ('print()', 0, 0, 12),
       ('echo()', 0, 0, 12),
       ('display()', 0, 0, 12),
       ('push()', 1, 1, 13),
       ('add()', 0, 0, 13),
       ('append()', 0, 0, 13),
       ('insert()', 0, 0, 13),
       ('array', 0, 0, 14),
       ('object', 1, 1, 14),
       ('list', 0, 0, 14),
       ('undefined', 0, 0, 14),
       ('while', 0, 0, 15),
       ('for', 1, 1, 15),
       ('forEach', 0, 0, 15),
       ('Toutes ces réponses sont valides', 0, 0, 15),
       ('echo "Hello World"', 0, 0, 16),
       ('print("Hello World")', 1, 1, 16),
       ('console.log("Hello World")', 0, 0, 16),
       ('System.out.println("Hello World")', 0, 0, 16),
       ('.html', 0, 0, 17),
       ('.js', 1, 1, 17),
       ('.php ', 0, 0, 17),
       ('.css', 0, 0, 17),
       ('$_SESSION', 0, 0, 18),
       ('$_GET', 0, 0, 18),
       ('$_POST', 1, 1, 18),
       ('$_COOKIE', 0, 0, 18),
       ('define', 0, 0, 19),
       ('function', 1, 1, 19),
       ('method', 0, 0, 19),
       ('def', 0, 0, 19),
       ('let name = "John"', 0, 0, 20),
       ('$name = "John"', 1, 1, 20),
       ('var name = "John"', 0, 0, 20),
       ('name := "John"', 0, 0, 20),
       ('FETCH', 0, 0, 21),
       ('SELECT', 1, 1, 21),
       ('GET', 0, 0, 21),
       ('RETRIEVE', 0, 0, 21),
       ('WHERE', 0, 0, 22),
       ('HAVING', 1, 1, 22),
       ('FILTER', 0, 0, 22),
       ('ORDER', 0, 0, 22),
       ('LEFT JOIN', 0, 0, 23),
       ('RIGHT JOIN', 0, 0, 23),
       ('INNER JOIN', 1, 1, 23),
       ('OUTER JOIN', 0, 0, 23),
       ('ADD', 0, 0, 24),
       ('INSERT INTO ', 1, 1, 24),
       ('NEW ENTRY', 0, 0, 24),
       ('UPDATE', 0, 0, 24),
       ('REMOVE TABLE', 0, 0, 25),
       ('DELETE TABLE', 0, 0, 25),
       ('DROP TABLE', 1, 1, 25),
       ('ERASE TABLE', 0, 0, 25),
       ('Python', 0, 0, 26),
       ('PHP', 0, 0, 26),
       ('JavaScript', 1, 1, 26),
       ('Ruby', 0, 0, 26),
       ('React', 1, 1, 27),
       ('Django', 0, 0, 27),
       ('Laravel', 0, 0, 27),
       ('Symfony', 0, 0, 27),
       ('Vue.js', 0, 0, 28),
       ('Angular', 0, 0, 28),
       ('Les deux', 1, 1, 28),
       ('Aucun', 0, 0, 28),
       ('Redux', 1, 1, 29),
       ('jQuery', 0, 0, 29),
       ('Lodash', 0, 0, 29),
       ('Express', 0, 0, 29),
       ('React', 1, 1, 30),
       ('Vue.js', 0, 0, 30),
       ('Angular', 0, 0, 30),
       ('Svelte', 0, 0, 30),
       ('DDoS', 0, 0, 31),
       ('SQL Injection', 1, 1, 31),
       ('Man-in-the-Middle', 0, 0, 31),
       ('Phishing', 0, 0, 31),
       ('FTP', 0, 0, 32),
       ('HTTP', 1, 1, 32),
       ('HTTPS', 0, 0, 32),
       ('SSH', 0, 0, 32),
       ('Cross-Site Scripting', 1, 1, 33),
       ('XML Secure System', 0, 0, 33),
       ('Xtreme Server Security', 0, 0, 33),
       ('eXtended Safety Shield', 0, 0, 33),
       ('X-Frame-Options', 0, 0, 34),
       ('Content-Security-Policy', 1, 1, 34),
       ('Strict-Transport-Security', 0, 0, 34),
       ('Access-Control-Allow-Origin', 0, 0, 34),
       ('En clair', 0, 0, 35),
       ('Chiffrés avec base64', 1, 1, 35),
       ('Hashés avec bcrypt', 0, 0, 35),
       ('Encodés en JSON', 0, 0, 35),
       ('REST est basé sur XML, SOAP est plus flexible', 0, 0, 36),
       ('SOAP est basé sur HTTP, REST utilise uniquement TCP', 0, 0, 36),
       ('REST est plus léger et utilise généralement JSON', 1, 1, 36),
       ('SOAP est utilisé uniquement en front-end', 0, 0, 36),
       ('GET', 1, 1, 37),
       ('POST', 0, 0, 37),
       ('PUT', 0, 0, 37),
       ('DELETE', 0, 0, 37),
       ('200', 1, 1, 38),
       ('404', 0, 0, 38),
       ('500', 0, 0, 38),
       ('301', 0, 0, 38),
       ('XML', 0, 0, 39),
       ('CSV', 0, 0, 39),
       ('JSON', 1, 1, 39),
       ('TXT', 0, 0, 39),
       ('Sauvegarder des fichiers', 0, 0, 40),
       ('Permettre la communication entre applications', 1, 1, 40),
       ('Gérer l\'affichage des pages web', 0, 0, 40),
       ('Compiler du code', 0, 0, 40),
       ('Apache', 0, 0, 41),
       ('Docker', 0, 0, 41),
       ('Nginx', 1, 1, 41),
       ('MySQL', 0, 0, 41),
       ('Jenkins', 0, 0, 42),
       ('GitHub Actions', 0, 0, 42),
       ('GitLab CI/CD', 0, 0, 42),
       ('Toutes ces réponses sont valides', 1, 1, 42),
       ('ls', 0, 0, 43),
       ('top', 1, 1, 43),
       ('pwd', 0, 0, 43),
       ('cd', 0, 0, 43),
       ('Apache est plus rapide qu\'Nginx', 0, 0, 44),
       ('Nginx est plus performant pour la gestion des connexions simultanées', 1, 1, 44),
       ('Apache ne supporte pas PHP', 0, 0, 44),
       ('Nginx ne fonctionne qu’avec MySQL', 0, 0, 44),
       ('Héberger une base de données', 0, 0, 45),
       ('Améliorer la vitesse de chargement des sites web', 1, 1, 45),
       ('Remplacer un serveur d’hébergement', 0, 0, 45),
       ('Servir du code backend', 0, 0, 45),
       ('Universal XML', 0, 0, 46),
       ('User Xperience', 1, 1, 46),
       ('Ultimate eXperience', 0, 0, 46),
       ('User Extension', 0, 0, 46),
       ('WebPageTest', 0, 0, 47),
       ('Lighthouse', 1, 1, 47),
       ('Postman', 0, 0, 47),
       ('Photoshop', 0, 0, 47),
       ('Ajouter un effet lors du clic', 0, 0, 48),
       ('Modifier l’apparence d’un élément quand il est sélectionné', 1, 1, 48),
       ('Changer la couleur du texte', 0, 0, 48),
       ('Agrandir la police automatiquement', 0, 0, 48),
       ('Gris clair sur blanc', 0, 0, 49),
       ('Bleu foncé sur jaune', 1, 1, 49),
       ('Rouge sur rose', 0, 0, 49),
       ('Vert clair sur blanc', 0, 0, 49),
       ('alt', 1, 1, 50),
       ('title', 0, 0, 50),
       ('href', 0, 0, 50),
       ('src', 0, 0, 50);

-- Insère des associations initiales entre les quiz et les catégories dans la table 'quizz_category'
INSERT INTO quizz_category (id_category, id_quiz)
VALUES (1, 1),
       (1, 2),
       (1, 3),
       (2, 4),
       (2, 5),
       (1, 6),
       (2, 7),
       (2, 8),
       (2, 9),
       (1, 10);
