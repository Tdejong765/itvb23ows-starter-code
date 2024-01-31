<?php

class gameController {

    private array $board;
    private int $player;
    private array $hand;
    private int $game_id;
    private $sessionController;
    private int $last_move;
    private string $ERROR;

    public function __construct($hiveModel, $sessionController, $boardController){
        
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

    public function getHand(): array
    {
        return $this->hand;
    }

    function refreshGame(){
        $sql = 'SELECT * FROM moves WHERE game_id = ';
        $params = $this->game_id;
        return $stmt = $this->hiveModel->dbRefresh($sql, $params);
    }
    
    public function restartGame(){
        $this->board = [];
        $this->hand = [0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], 1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]];
        $this->game_id = 0;
        $this->player = 0;
        $this->last_move=-1;
        $this->ERROR="";
       
        $sql = 'INSERT INTO games () VALUES ()';
        $game_id = $this->hiveModel->dbRestart($sql);
        $this->sessionController->refreshState($this->game_id ,$this->board, $this->player, $this->hand , $this->last_move, $this->ERROR);
    }


    function pass(){
        $game_id = $this->game_id;
        $last_move = $this->last_move;
        $state = $this->sessionController->getState();
        $sql = 'INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state) VALUES (?, "pass", null, null, ?, ?)';

        $this->last_move = $this->hiveModel->dbPass($sql, $game_id, $last_move, $state);
        $this->player = -1;
        $this->sessionController->refreshState($game_id, $this->board, $this->player, $this->hand, $last_move, $this->ERROR);
    }

    function play(){
        $piece = $_POST['piece'];
        $to = $_POST['to'];
        $player = $this->player;
        $board = $this->board;
        $hand = $this->sessionController->getSessionVariable('hand')[$player];

        if (!$hand[$piece]){
            $this->ERROR = "Player does not have tile";
        }
        elseif (isset($board[$to])){
            $this->ERROR  = 'Board position is not empty';
        }
        elseif (count($board) && !hasNeighBour($to, $board)){
            $this->ERROR  = "board position has no neighbour";
        }
        elseif (array_sum($hand) < 11 && !neighboursAreSameColor($player, $to, $board)){
            $this->ERROR  = "Board position has opposing neighbour";
        }
        elseif (array_sum($hand) <= 8 && $hand['Q']) {
            $this->ERROR  = 'Must play queen bee';
        } else {
            $board[$to] = [[$player, $piece]];
            $hand[$player][$piece]--;
            $hand = 1 - $player;

            $state = $this->sessionController->getState();
            $sql = 'INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state) VALUES (?, "play", ?, ?, ?, ?)';  
            $this->last_move = $this->hiveModel->dbPlay($sql, $this->game_id, $piece, $to, $this->last_move, $state);
        }
    }
    

    function move(){
        session_start();

        $from = $_POST['from'];
        $to = $_POST['to'];

        $player = $_SESSION['player'];
        $board = $_SESSION['board'];
        $hand = $_SESSION['hand'][$player];
        unset($_SESSION['error']);

        if (!isset($board[$from])){
            $_SESSION['error'] = 'Board position is empty';
        }
        elseif($board[$from][count($board[$from])-1][0] != $player){
            $_SESSION['error'] = "Tile is not owned by player";
        }
        elseif($hand['Q']){
            $_SESSION['error'] = "Queen bee is not played";
        }
        else {
            $tile = array_pop($board[$from]);
            if (!hasNeighBour($to, $board)){
                $_SESSION['error'] = "Move would split hive";
            }
            else {
                $all = array_keys($board);
                $queue = [array_shift($all)];
                while ($queue) {
                    $next = explode(',', array_shift($queue));
                    foreach ($GLOBALS['OFFSETS'] as $pq) {
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
                    $_SESSION['error'] = "Move would split hive";
                } else {
                    if ($from == $to){
                        $_SESSION['error'] = 'Tile must move';
                    }
                    elseif (isset($board[$to]) && $tile[1] != "B"){
                        $_SESSION['error'] = 'Tile not empty';
                    }
                    elseif ($tile[1] == "Q" || $tile[1] == "B") {
                        if (!slide($board, $from, $to)){
                            $_SESSION['error'] = 'Tile must slide';
                        }
                    }
                }
            }
            if (isset($_SESSION['error'])) {
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
                $_SESSION['player'] = 1 - $_SESSION['player'];
                $db = include_once 'database.php';
                $stmt = $db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "move", ?, ?, ?, ?)');
                $stmt->bind_param('issis', $_SESSION['game_id'], $from, $to, $_SESSION['last_move'], get_state());
                $stmt->execute();
                $_SESSION['last_move'] = $db->insert_id;
            }
            $_SESSION['board'] = $board;
        }

        header('Location: index.php');
    }
    
    function undo(){
        session_start();
        $db = include_once 'database.php';
        $stmt = $db->prepare('SELECT * FROM moves WHERE id = '.$_SESSION['last_move']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_array();
        $_SESSION['last_move'] = $result[5];
        set_state($result[6]);
        header('Location: index.php');
    }
}