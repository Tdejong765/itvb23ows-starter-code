<?php

class SessionManager {

    public function setState($game_id, $board, $player, $hand) {
        $this->setSessionVariable('game_id', $game_id);
        $this->setSessionVariable('board', $board);
        $this->setSessionVariable('player', $player);
        $this->setSessionVariable('hand', $hand);
        header('Location: index.php');
    }

    public function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function setSessionVariable($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function getSessionVariable($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function unsetSessionVariable($key) {
        unset($_SESSION[$key]);
    }

    public function destroySession() {
        session_unset();
        session_destroy();
    }
}