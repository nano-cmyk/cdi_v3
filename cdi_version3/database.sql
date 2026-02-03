-- Base de données version 3 (sécurisée avec système de réservation)
CREATE DATABASE IF NOT EXISTS cdi_v3;
USE cdi_v3;

-- Table des utilisateurs (mots de passe en MD5)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- MD5 hash
    is_admin BOOLEAN DEFAULT FALSE
);

-- Table des livres avec état
CREATE TABLE books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    isbn VARCHAR(13),
    description TEXT,
    status ENUM('disponible', 'reserve') DEFAULT 'disponible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des réservations
CREATE TABLE reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    user_id INT NOT NULL,
    reservation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insertion d'un admin par défaut et d'un utilisateur (mots de passe en MD5)
INSERT INTO users (username, password, is_admin) VALUES 
('admin', '0192023a7bbd73250516f069df18b500', TRUE), -- admin123
('user', '6ad14ba9986e3615423dfca256d04e3f', FALSE); -- user123

-- Insertion de 10 livres d'exemple
INSERT INTO books (title, author, isbn, description, status) VALUES
('Le Petit Prince', 'Antoine de Saint-Exupéry', '9782070612758', 'Un conte poétique qui aborde les thèmes de l''amour, l''amitié et le sens de la vie.', 'disponible'),
('1984', 'George Orwell', '9782070368228', 'Une dystopie décrivant une société sous surveillance où la liberté d''expression est supprimée.', 'disponible'),
('Notre-Dame de Paris', 'Victor Hugo', '9782253096337', 'L''histoire de la belle Esmeralda et du bossu Quasimodo dans le Paris médiéval.', 'disponible'),
('Les Misérables', 'Victor Hugo', '9782253096344', 'L''histoire de Jean Valjean et de la rédemption dans la France du 19e siècle.', 'disponible'),
('Harry Potter à l''école des sorciers', 'J.K. Rowling', '9782070541270', 'Le début des aventures du jeune sorcier Harry Potter.', 'disponible'),
('Le Seigneur des Anneaux', 'J.R.R. Tolkien', '9782075134064', 'L''épopée fantastique de Frodon et de l''Anneau unique.', 'disponible'),
('Les Fleurs du Mal', 'Charles Baudelaire', '9782081207474', 'Recueil de poèmes majeur de la littérature française.', 'disponible'),
('Germinal', 'Émile Zola', '9782253004226', 'La vie des mineurs dans le nord de la France au 19e siècle.', 'disponible'),
('L''Étranger', 'Albert Camus', '9782070360024', 'L''histoire de Meursault et de l''absurdité de la condition humaine.', 'disponible'),
('Madame Bovary', 'Gustave Flaubert', '9782253004868', 'Le destin tragique d''Emma Bovary dans la province française.', 'disponible');
