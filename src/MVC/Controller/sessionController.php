<?php

class sessionController {

    public function refreshState($game_id, $board, $player, $hand, $last_move, $ERROR) {
        $this->setSessionVariable('game_id', $game_id);
        $this->setSessionVariable('board', $board);
        $this->setSessionVariable('player', $player);
        $this->setSessionVariable('hand', $hand);
        $this->setSessionVariable('last_move', $last_move);
        $this->setSessionVariable('ERROR', $ERROR);
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

    public function getState() {
        return serialize([
            $this->getSessionVariable('hand'),
            $this->getSessionVariable('board'),
            $this->getSessionVariable('player'),
            $this->getSessionVariable('game_id'),
            $this->getSessionVariable('last_move')
        ]);
    }
    
    public function setState($state) {
        list($hand, $board, $player) = unserialize($state);
        $this->setSessionVariable('game_id', $game_id);
        $this->setSessionVariable('board', $board);
        $this->setSessionVariable('player', $player);
    }

}