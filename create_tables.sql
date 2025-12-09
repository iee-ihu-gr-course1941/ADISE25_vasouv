-- create default deck
CREATE TABLE default_deck (
    id MEDIUMINT NOT NULL AUTO_INCREMENT,
    suit ENUM('HEARTS', 'DIAMONDS', 'CLUBS', 'SPADES') NOT NULL,
    rank ENUM('A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K') NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO default_deck (suit, rank) VALUES
('HEARTS', 'A'), ('HEARTS', '2'), ('HEARTS', '3'), ('HEARTS', '4'), ('HEARTS', '5'),
('HEARTS', '6'), ('HEARTS', '7'), ('HEARTS', '8'), ('HEARTS', '9'), ('HEARTS', '10'),
('HEARTS', 'J'), ('HEARTS', 'Q'), ('HEARTS', 'K'),
('DIAMONDS', 'A'), ('DIAMONDS', '2'), ('DIAMONDS', '3'), ('DIAMONDS', '4'), ('DIAMONDS', '5'),
('DIAMONDS', '6'), ('DIAMONDS', '7'), ('DIAMONDS', '8'), ('DIAMONDS', '9'), ('DIAMONDS', '10'),
('DIAMONDS', 'J'), ('DIAMONDS', 'Q'), ('DIAMONDS', 'K'),
('CLUBS', 'A'), ('CLUBS', '2'), ('CLUBS', '3'), ('CLUBS', '4'), ('CLUBS', '5'),
('CLUBS', '6'), ('CLUBS', '7'), ('CLUBS', '8'), ('CLUBS', '9'), ('CLUBS', '10'),
('CLUBS', 'J'), ('CLUBS', 'Q'), ('CLUBS', 'K'),
('SPADES', 'A'), ('SPADES', '2'), ('SPADES', '3'), ('SPADES', '4'), ('SPADES', '5'),
('SPADES', '6'), ('SPADES', '7'), ('SPADES', '8'), ('SPADES', '9'), ('SPADES', '10'),
('SPADES', 'J'), ('SPADES', 'Q'), ('SPADES', 'K');

CREATE TABLE playing_deck LIKE default_deck;
CREATE TABLE table_deck LIKE default_deck;
CREATE TABLE player_hand LIKE default_deck;
CREATE TABLE enemy_hand LIKE default_deck;