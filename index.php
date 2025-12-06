<?php

session_start();

if (!isset($_SESSION['startingDeck'])) $_SESSION['startingDeck'] = [];
if (!isset($_SESSION['playerHand'])) $_SESSION['playerHand'] = [];
if (!isset($_SESSION['enemyHand'])) $_SESSION['enemyHand'] = [];

function initializeDeck() {
    $suits = ["Hearts", "Diamonds", "Clubs", "Spades"];
    $ranks = ["A", "2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K"];

    $card_deck = [];

    foreach ($suits as $suit) {
        foreach ($ranks as $rank) {
            $card_deck[] = [
                "suit" => $suit,
                "rank" => $rank
            ];
        }
    }
    shuffle($card_deck);
    return $card_deck;
}

function dealCards(&$deck, $noOfCards) {
    // 
    $hand = array_slice($deck, 0, $noOfCards);
    $deck = array_slice($deck, $noOfCards);
    return $hand;
}

header("Content-Type: application/json");

$path = $_SERVER['PATH_INFO'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'];

if ($path === "/initialize" && $method === "POST") {
    $_SESSION['playerHand'] = [];
    $_SESSION['enemyHand'] = [];
    $_SESSION['startingDeck'] = initializeDeck();
    echo json_encode([
        "status" => "ok",
        "message" => "Deck initialized"
    ]);
    exit;
}

if ($path === "/deck" && $method === "GET") {
    echo json_encode($_SESSION['startingDeck']);
    exit;
}

if ($path === "/deal" && $method === "POST") {
    $target = $_GET['target'] ?? null;
    if ($target === "player") {
        $_SESSION['playerHand'] = array_merge($_SESSION['playerHand'], dealCards($_SESSION['startingDeck'], 6));
    } 
    if ($target === "enemy") {
        $_SESSION['enemyHand'] = array_merge($_SESSION['enemyHand'], dealCards($_SESSION['startingDeck'], 6));
    }
}

if ($path === "/hand" && $method === "GET") {
    $target = $_GET['target'] ?? null;
    if ($target === 'player') {
        echo json_encode($_SESSION['playerHand']);
    }
    if ($target === 'enemy') {
        echo json_encode($_SESSION['enemyHand']);
    }
}

?>