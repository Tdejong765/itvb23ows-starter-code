<?php

class gameController {

    private array $board;
    private int $player;
    private array $hand;
    private int $game_id;
    private int $last_move;
    private string $ERROR;
    private $sessionController;
    private $boardController;
    private $hiveModel;

    public function __construct($hiveModel, $sessionController, $boardController){
        
        $this->board = [];
        $this->hand = [0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], 1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]];
        $this->game_id = 0;
        $this->player = 0;
        $this->last_move=-1;
        $this->ERROR="";
        $this->hiveModel = $hiveModel;
        $this->sessionController = $sessionController;
        $this->boardController = $boardController;
        $requiredVariables = ['board', 'hand', 'game_id', 'last_move', 'ERROR', 'player'];

        foreach ($requiredVariables as $variable) {
            if (!isset($_SESSION[$variable])) {
                $this->restartGame();
                break;
            }
            $this->sessionController->refreshState($this->game_id ,$this->board, $this->player, $this->hand , $this->last_move, $this->ERROR);
        }
    }

    public function getBoard(): array
    {
        return $this->board;
    }

    public function getPlayer(): int
    {
        return $this->player;
    }

    public function setHand(array $hand): void {
        $this->hand = $hand;
    }
    
    public function getHand(): array
    {
        return $this->hand;
    }

    public function getERROR(): string
    {
        return $this->ERROR;
    }

    public function getOffsets(): array
    {
        return $this->boardController->getOffsets();
    }

    public function refreshGame(){
        $sql = 'SELECT * FROM moves WHERE game_id = ';
        $params = $this->game_id;
        return $stmt = $this->hiveModel->dbRefresh($sql, $params);
        header('Location: index.php');
    }
    
    public function restartGame(){
        $this->board = [];
        $this->hand = [0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], 1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]];
        $this->game_id = 0;
        $this->player = 0;
        $this->last_move=-1;
        $this->ERROR="";
       
        $game_id = $this->hiveModel->dbRestart();
        $this->sessionController->refreshState($this->game_id ,$this->board, $this->player, $this->hand , $this->last_move, $this->ERROR);
    }

    public function pass(){
        $game_id = $this->game_id;
        $last_move = $this->last_move;
        $state = $this->sessionController->getState();
        $sql = 'INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state) VALUES (?, "pass", null, null, ?, ?)';

        $this->last_move = $this->hiveModel->dbPass($sql, $game_id, $last_move, $state);
        $this->player = -1;
        $this->sessionController->refreshState($game_id, $this->board, $this->player, $this->hand, $last_move, $this->ERROR);
    }

    public function undo(){
        $sql = 'SELECT * FROM moves WHERE id = ';
        $last_move = $this->last_move;
        $result = $this->hiveModel->dbUndo($sql, $last_move);
        
        $this->last_move = $result;
        $this->sessionController->setState($result);
        $this->sessionController->refreshState($this->game_id, $this->board, $this->player, $this->hand, $this->last_move, $this->ERROR);
    }

    public function play(){
        $piece = $_POST['piece'];
        $to = $_POST['to'];
        $hand = $this->hand[$this->player];

        var_dump($_POST);

        if (!$hand[$piece]){
            $this->ERROR = "Player does not have tile";
        }
        elseif (isset($this->board[$to])){
            $this->ERROR  = 'Board position is not empty';
        }
        elseif (count($this->board) && !$this->boardController->hasNeighBour($to, $this->board)){
            $this->ERROR  = "board position has no neighbour";
        }
        elseif (array_sum($hand) < 11 && !$this->boardController->neighboursAreSameColor($this->player, $to, $this->board)){
            $this->ERROR  = "Board position has opposing neighbour";
        }
        elseif (array_sum($hand) <= 8 && $hand['Q']) {
            $this->ERROR  = 'Must play queen bee';
        } else {
            $this->board[$to] = [[$this->player, $piece]];
            $this->hand[$this->player][$piece]--;
            $hand = 1 - $this->player;

            $state = $this->sessionController->getState();
            $sql = 'INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state) VALUES (?, "play", ?, ?, ?, ?)';  
            $this->last_move = $this->hiveModel->dbPlay($sql, $this->game_id, $piece, $to, $this->last_move, $state);
            $this->sessionController->refreshState($this->game_id, $this->board, $this->player, $hand, $this->last_move, $this->ERROR);
        }
    }
    
    
    public function move(){
        $from = $_POST['from'];
        $to = $_POST['to'];
        $player = $this->player;
        $board = $this->board;
        $hand = $this->hand[$player];
        $this->sessionController->unSetSessionVariable('ERROR');

        if (!isset($board[$from])){
            $this->ERROR = 'Board position is empty';
        }
        elseif($board[$from][count($board[$from])-1][0] != $player){
            $this->ERROR = "Tile is not owned by player";
        }
        elseif($hand['Q']){
            $this->ERROR = "Queen bee is not played";
        }
        else {
            $tile = array_pop($board[$from]);
            if (!$this->boardController->hasNeighBour($to, $board)){
                $this->ERROR = "Move would split hive";
            }
            else {
                $all = array_keys($board);
                $queue = [array_shift($all)];
                while ($queue) {
                    $next = explode(',', array_shift($queue));
                    foreach ($this->boardController->getOffsets() as $pq) {
                        list($p, $q) = $pq;
                        $p += $next[0];
                        $q += $next[1];
                        if (in_array("$p,$q", $all)) {
                            $queue[] = "$p,$q";
                            $all = array_diff($all, ["$p,$q"]);
                        }
                    }
                }
                if ($all) {
                    $this->ERROR = "Move would split hive";
                } else {
                    if ($from == $to){
                        $this->ERROR = 'Tile must move';
                    }
                    elseif (isset($board[$to]) && $tile[1] != "B"){
                        $this->ERROR = 'Tile not empty';
                    }
                    elseif ($tile[1] == "Q" || $tile[1] == "B") {
                        if (!slide($board, $from, $to)){
                            $this->ERROR = 'Tile must slide';
                        }
                    }
                }
            }
            
            if (isset($this->ERROR)) {
                if (isset($board[$from])){
                    array_push($board[$from], $tile);
                }
                else {
                    $board[$from] = [$tile];
                }
            } else {
                if (isset($board[$to])){ 
                    array_push($board[$to], $tile);
                }
                else {
                    $board[$to] = [$tile];
                }
                $state = $this->sessionController->getState();
                $player = 1 - $player;
                $sql = 'INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state) VALUES (?, "move", ?, ?, ?, ?)';
                $this->last_move = $this->hiveModel->dbMove($sql, $this->game_id, $piece, $to, $this->last_move, $state);
            }
            $this->board = $board;
        }
        $this->sessionController->refreshState($this->game_id, $board, $player, $hand, $this->last_move, $this->ERROR);
    }
}