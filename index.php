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
    $card = getTableStackCard();
    echo json_encode($card);
    exit;
}

if ($path === "/hand" && $method === "GET") {
    auth();
    $hand = $playerName === 'player' ? getPlayerHand() : getEnemyHand();
    echo json_encode($hand, JSON_PRETTY_PRINT);
    exit;
}

if ($path === "/play" && $method === "POST") {
    auth();
    $playerName === 'player' ? getPlayerHand() : getEnemyHand();

    $raw = file_get_contents("php://input");
    $payload = json_decode($raw, true);

    $suit = $payload["suit"];
    $rank = $payload["rank"];

    if (xeriMeVale($rank)) {
        // mazepse kai metra pontous
        echo "Ekanes xeri me vale";
        exit;
    } elseif (xeriMeRank($rank)) {
        // mazepse kai metra pontous
        echo "Ekanes apli xeri";
        exit;
    } elseif (suitsMatch($suit)) {
        // mazepse kai metra pontous
        echo "Mazeueis ta fylla";
        exit;
    } else {
        echo "Apla paizeis";
        exit;
        // playCardOnDeck($playerName, $suit, $rank);
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

?>