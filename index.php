<?php

require_once "lib/dbconnect.php";
require_once "lib/dbservice.php";

session_start();

header("Content-Type: application/json");
$path = $_SERVER['PATH_INFO'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'];

$headers = getallheaders();
$playerName = $headers['X-Player-Name'] ?? null;

function auth() {
    $allowedPlayers = ['player', 'enemy'];
    global $playerName;
    if (!$playerName || !in_array($playerName, $allowedPlayers)) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        exit;
    }
}

if ($path === "/initialize" && $method === "POST") {
    initializeDeck();
    echo json_encode([
        "status" => "ok",
        "message" => "Deck initialized"
    ]);
    exit;
}

if ($path === "/deck" && $method === "GET") {
    auth();
    checkGamePlaying();
    $card = getTableStackCard();
    echo json_encode($card);
    exit;
}

if ($path === "/hand" && $method === "GET") {
    auth();
    checkGamePlaying();
    $hand = getHand($playerName);
    echo json_encode($hand, JSON_PRETTY_PRINT);
    exit;
}

if ($path === "/play" && $method === "POST") {
    auth();
    checkGamePlaying();
    checkPlayerTurn($playerName);

    $raw = file_get_contents("php://input");
    $payload = json_decode($raw, true);

    $suit = $payload["suit"];
    $rank = $payload["rank"];

    checkCardInHand($playerName, $suit, $rank);

    if (xeriMeVale($rank)) {
        $score = 20;
        updateScore($playerName, $score);
        clearTableDeck();
    } elseif (xeriMeRank($rank)) {
        $score = 10;
        updateScore($playerName, $score);
    } elseif (suitsMatch($suit)) {
        playCardOnDeck($playerName, $suit, $rank);
        $score = calculateScore();
        updateScore($playerName, $score);
    } else {
        playCardOnDeck($playerName, $suit, $rank);
    }

    if (tableDeckIsEmpty()) {
        updateGameStatus('FINISHED');
        echo "Game finished";
        exit;
    }
    if (playerHandIsEmpty() && enemyHandIsEmpty()) dealCards();

}

function checkPlayerTurn($playerName) {
    if ($playerName === getLastPlayer()) {
        echo "Not your turn";
        exit;
    }
}

function checkCardInHand($playerName, $suit, $rank) {
    if (!cardInHand($playerName, $suit, $rank)) {
        echo "You don't have this card";
        exit;
    }
}

function xeriMeVale($playedRank) {
    return tableDeckOneCard() && getTableStackCard()[0]["rank"] === "J" && $playedRank === "J";
}

function xeriMeRank($playedRank) {
    return tableDeckOneCard() && getTableStackCard()[0]["rank"] === $playedRank;
}

function suitsMatch($playedSuit) {
    return getTableStackCard()[0]["suit"] === $playedSuit;
}

function calculateScore() {
    $score = 0;
    $deck = getTableDeck();
    foreach ($deck as $card) {
        if (in_array($card['rank'],['J','Q','K'])) {
            $score += 1;
        }
        if ($card['rank'] === '10' && $card['suit'] != 'DIAMONDS') {
            $score += 1;
        }
    }
    return $score;
}

function checkGamePlaying() {
    if ('FINISHED' === getGameStatus()) {
        echo "Game finished";
        exit;
    }
}

?>