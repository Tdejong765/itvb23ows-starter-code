<?php

class BoardController {

    public function getOffsets() {
        $OFFSETS = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];
        return $OFFSETS;
    }

    public function isNeighbour($a, $b) {
        $a = explode(',', $a);
        $b = explode(',', $b);
        if ($a[0] == $b[0] && abs($a[1] - $b[1]) == 1){ 
            return true;
        }
        if ($a[1] == $b[1] && abs($a[0] - $b[0]) == 1){
            return true;
        }
        if ($a[0] + $a[1] == $b[0] + $b[1]){
            return true;
        }
        return false;
    }
    

    public function hasNeighBour($a, $board) {
        if (!is_string($a)) {
            $a = implode(',', $a);
        }
    
        foreach ($board as $b => $value) {
            if ($this->isNeighbour($a, $b)) {
                return true;
            }
        }
        return false;
    }
    

    public function neighboursAreSameColor($player, $a, $board) {
        foreach ($board as $b => $st) {
            if (!$st) {
                continue;
            }
            $c = $st[count($st) - 1][0];
            if ($c != $player && $this->isNeighbour($a, $b)){ 
                return false;
            }
        }
        return true;
    }
    

    public function len($tile) {
        return $tile ? count($tile) : 0;
    }
    

    public function slide($board, $from, $to) {
        if (!$this->hasNeighBour($to, $board)){
            return false;
        }
        if (!$this->isNeighbour($from, $to)){
            return false;
        }
    
        $b = explode(',', $to);
        $common = [];
        foreach ($GLOBALS['OFFSETS'] as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            if ($this->isNeighbour($from, $p.",".$q)){
                $common[] = $p.",".$q;
            }
        }
        if (!$board[$common[0]] && !$board[$common[1]] && !$board[$from] && !$board[$to]){
            return false;
        }
        return min(len($board[$common[0]]), len($board[$common[1]])) <= max(len($board[$from]), len($board[$to]));
    }


    public function getAdjacentTiles($position) {
        $adjacentOffsets = [
            [-1, 0], [1, 0], 
            [0, -1], [0, 1], 
            [-1, 1], [1, -1] 
        ];
    
        $position = explode(',', $position);
        $adjacentTiles = [];
    
        foreach ($adjacentOffsets as $offset) {
            $adjRow = $position[0] + $offset[0];
            $adjCol = $position[1] + $offset[1];
            $adjacentTiles[] = [$adjRow, $adjCol];
        }
    
        return $adjacentTiles;
    }


    public function moveGrasshopper($board, $from, $to) {
        
        $fromCoords = explode(',', $from);
        $toCoords = explode(',', $to);
    
        $rowDiff = $toCoords[0] - $fromCoords[0];
        $colDiff = $toCoords[1] - $fromCoords[1];
    
        if ($rowDiff == 0 || $colDiff == 0 || abs($rowDiff) == abs($colDiff)) {
            $rowDirection = $rowDiff == 0 ? 0 : $rowDiff / abs($rowDiff);
            $colDirection = $colDiff == 0 ? 0 : $colDiff / abs($colDiff);
    
            $nextRow = $fromCoords[0] + $rowDirection;
            $nextCol = $fromCoords[1] + $colDirection;
            while ($nextRow != $toCoords[0] || $nextCol != $toCoords[1]) {
                $pos = $nextRow . ',' . $nextCol;
    
                if (isset($board[$pos])) {
                    return true;
                }
    
                $nextRow += $rowDirection;
                $nextCol += $colDirection;
            }
        }
        return false;
    }


    public function moveAnt($board, $from, $to) {

        $fromCoords = explode(',', $from);
        $toCoords = explode(',', $to);
    
        if ($fromCoords == $toCoords) {
            return false;
        }
    
        if (isset($board[$to])) {
            return false;
        }
    
        $rowDiff = $toCoords[0] - $fromCoords[0];
        $colDiff = $toCoords[1] - $fromCoords[1];
        $validDirections = [[0, 1], [0, -1], [-1, 0], [1, 0], [-1, 1], [1, -1]];

        $direction = [$rowDiff, $colDiff];
        if (!in_array($direction, $validDirections)) {
            return false;
        }

        $nextTo = 0;
        foreach ($this->getAdjacentTiles($toCoords) as $tile) {
            [$x, $y] = $tile;
            if (isset($board["$x,$y"]) && empty($board["$x,$y"])) {
                $nextTo++;
            }
        }
    
        return $nextTo > 0 && $nextTo < 5;
    }
}