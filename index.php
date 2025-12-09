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

?>