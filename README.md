# ADISE25_vasouv

## Authentication
Mock authentication, acceptable names **player**, **enemy** for **X-Player-Name** header. The code checks if the playerName is one of these values.

## Gameplay
1. Player initializes the game.
2. Enemy and player play cards in succession.
3. If one collects cards, the score is calculated and added to their total score.
4. If their hands are empty, they are re-dealt cards.
5. Game finishes when all remaining deck cards are played.

## Endpoints

### Game initialize
`curl --location --request POST 'https://users.it.teithe.gr/~it052781/xeri/index.php/initialize' \
--header 'X-Player-Name: player'`

### Deck
Shows the deck (top card)

`curl --location 'https://users.it.teithe.gr/~it052781/xeri/index.php/deck' \
--header 'X-Player-Name: player'`

### Hand
Shows the hand of each player

`curl --location 'https://users.it.teithe.gr/~it052781/xeri/index.php/hand' \
--header 'X-Player-Name: player'`

### Play
Play the card from hand

`curl --location --request PUT 'https://users.it.teithe.gr/~it052781/xeri/index.php/play' \
--header 'X-Player-Name: player' \
--header 'Content-Type: application/json' \
--data '{
    "suit": "SPADES",
    "rank": "Q"
}'`

## Database Structure

### Default Deck
The default deck of cards. It's used to be copied over the Playing Deck.

### Playing Deck
The playing deck of hards. Instead of shuffling, cards are dealt randomly.

### Table Deck
The cards on the table. It's used as a stack so only the top card is displayed.

### Player & Enemy Hand
Cards on player and enemy's hand.

### Game Status
Holds the game status (INITIALIZED, PLAYING, FINISHED), the scores and the last player.

## Improvements
* User registration/login and proper authentication with HTTP Basic or JWT
* Usage of Laravel instead of vanilla PHP
* Different DB structure, better normalization
* Gameplay overhaul