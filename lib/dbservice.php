<?php

function flushMultiQuery($mysqli) {
    while ($mysqli->more_results()) {
        $mysqli->next_result();
        if ($res = $mysqli->store_result()) {
            $res->free();
        }
    }
}

function initializeDeck() {
    global $mysqli;

    // truncates the tables
    $sqlTruncate = '
        TRUNCATE TABLE playing_deck;
        TRUNCATE TABLE table_deck;
        TRUNCATE TABLE player_hand;
        TRUNCATE TABLE enemy_hand;
    ';
    $mysqli->multi_query($sqlTruncate);
    flushMultiQuery($mysqli);

    // initializes the playing deck
    $sqlInit = '
        INSERT INTO playing_deck (suit, rank)
        SELECT suit, rank
        FROM default_deck;
    ';
    $mysqli->query($sqlInit);

    // deals cards to the player
    $sqlDealPlayer = '
        CREATE TEMPORARY TABLE temp_draw AS
        SELECT suit, rank
        FROM playing_deck
        ORDER BY RAND()
        LIMIT 6;

        INSERT INTO player_hand (suit, rank)
        SELECT suit, rank FROM temp_draw;

        DELETE FROM playing_deck
        WHERE (suit, rank) IN (SELECT suit, rank FROM temp_draw);

        DROP TEMPORARY TABLE temp_draw;
    ';
    $mysqli->multi_query($sqlDealPlayer);
    flushMultiQuery($mysqli);

    // deals card to the enemy
    $sqlDealEnemy = '
        CREATE TEMPORARY TABLE temp_draw AS
        SELECT suit, rank
        FROM playing_deck
        ORDER BY RAND()
        LIMIT 6;

        INSERT INTO enemy_hand (suit, rank)
        SELECT suit, rank FROM temp_draw;

        DELETE FROM playing_deck
        WHERE (suit, rank) IN (SELECT suit, rank FROM temp_draw);

        DROP TEMPORARY TABLE temp_draw;
    ';
    $mysqli->multi_query($sqlDealEnemy);
    flushMultiQuery($mysqli);

    // sets the table stack
    $sqlTableDeck = '
        CREATE TEMPORARY TABLE temp_draw AS
        SELECT suit, rank
        FROM playing_deck
        ORDER BY RAND()
        LIMIT 4;

        INSERT INTO table_deck (suit, rank)
        SELECT suit, rank FROM temp_draw;

        DELETE FROM playing_deck
        WHERE (suit, rank) IN (SELECT suit, rank FROM temp_draw);

        DROP TEMPORARY TABLE temp_draw;
    ';
    $mysqli->multi_query($sqlTableDeck);
    flushMultiQuery($mysqli);
}

function getPlayerHand() {
    global $mysqli;
    $sql = "SELECT * FROM player_hand";
    $st = $mysqli->prepare($sql);
    $st->execute();
    $data = $st->get_result()->fetch_all(MYSQLI_ASSOC);
    return $data;
}

function getEnemyHand() {
    global $mysqli;
    $sql = "SELECT * FROM enemy_hand";
    $st = $mysqli->prepare($sql);
    $st->execute();
    $data = $st->get_result()->fetch_all(MYSQLI_ASSOC);
    return $data;
}

function getTableStackCard() {
    global $mysqli;
    $sql = "select * from table_deck order by id desc limit 1";
    $st = $mysqli->prepare($sql);
    $st->execute();
    $data = $st->get_result()->fetch_all(MYSQLI_ASSOC);
    return $data;
}

function tableIsEmpty($tableName) {
    global $mysqli;
    $sql = "select count(*) from {$tableName}";
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    return $row['count(*)'] == 0;
}

function playerHandIsEmpty() {
    return tableIsEmpty("player_hand");
}

function enemyHandIsEmpty() {
    return tableIsEmpty("enemy_hand");
}

function tableDeckIsEmpty() {
    return tableIsEmpty("table_deck");
}

function tableDeckOneCard() {
    global $mysqli;
    $sql = "select count(*) from table_deck";
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    return $row['count(*)'] == 1;
}

function playCardOnDeck($playerName, $suit, $rank) {
    global $mysqli;
    $deleteFromHand = "delete from {$playerName}_hand where suit = ? and rank = ?";
    $st = $mysqli->prepare($deleteFromHand);
    $st->bind_param("ss", $suit, $rank);
    $st->execute();

    $addToDeck = "insert into table_deck(suit,rank) values(?,?)";
    $st = $mysqli->prepare($addToDeck);
    $st->bind_param("ss", $suit, $rank);
    $st->execute();
}

?>