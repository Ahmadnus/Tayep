<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Game Listener</title>
    @vite(['resources/js/app.js'])
</head>
<body>
    <h1>Realtime Test</h1>

    <ul id="players"></ul>
<h1 id="game-status">Realtime Test</h1>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const gameId = 3;

    if (!window.Echo) {
        console.error('Echo not loaded yet');
        return;
    }

    window.Echo.channel(`game.${gameId}`)
        .listen('PlayerJoinedGame', e => {
            const el = document.createElement('li');
            el.id = `player-${e.playerName}`;
            el.textContent = `Joined: ${e.playerName}`;
            document.getElementById('players').appendChild(el);
        })
        .listen('PlayerLeftGame', e => {
            const playerEl = document.getElementById(`player-${e.playerName}`);
            if (playerEl) playerEl.remove();
        })
        .listen('.game.started', e => {
            console.log("GAME STARTED REALTIME:", e.status);

            const h1 = document.getElementById('game-status'); // ← استخدم هذا id
            if (h1) {
                h1.textContent = "Game Started! Status = " + e.status;
            }
        })
        .listen('.game.started', e => {
            console.log("GAME STARTED REALTIME:", e.status);

            const h1 = document.getElementById('game-status'); // ← استخدم هذا id
            if (h1) {
                h1.textContent = "Game Started! Status = " + e.status;
            }
        })

        .listen('NewOwner', e => {
    console.log("New owner:", e.playerName);
});
     

});
</script>

</body>
</html>
