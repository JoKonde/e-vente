CREATE DATABASE electro;

USE electro;

CREATE TABLE role (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    etat BOOLEAN DEFAULT 1,
    role_id INT,
    noms VARCHAR(255),
    sexe VARCHAR(10),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES role(id)
);

CREATE TABLE categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE produit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prix FLOAT NOT NULL,
    image VARCHAR(255),
    categorie_id INT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categorie(id)
);

CREATE TABLE commande (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_id INT,
    user_id INT,
    etat_cmd VARCHAR(50) NOT NULL,
    est_paye BOOLEAN DEFAULT 0,
    est_livre BOOLEAN DEFAULT 0,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produit_id) REFERENCES produit(id),
    FOREIGN KEY (user_id) REFERENCES user(id)
);

CREATE TABLE paiement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    montant DECIMAL(10, 2) NOT NULL,
    commande_id INT,
    user_id INT,
    date_paiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commande_id) REFERENCES commande(id),
    FOREIGN KEY (user_id) REFERENCES user(id)
);
