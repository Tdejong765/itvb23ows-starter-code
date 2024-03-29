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


    public function moveGrasshopper($board, $from, $to) {
        
        $fromCoords = explode(',', $from);
        $toCoords = explode(',', $to);
    
        // Calculate the differences in rows and columns
        $rowDiff = $toCoords[0] - $fromCoords[0];
        $colDiff = $toCoords[1] - $fromCoords[1];
    
        // Check if the move is along a straight line
        if ($rowDiff == 0 || $colDiff == 0 || abs($rowDiff) == abs($colDiff)) {
            // Determine the direction of movement
            $rowDirection = $rowDiff == 0 ? 0 : $rowDiff / abs($rowDiff);
            $colDirection = $colDiff == 0 ? 0 : $colDiff / abs($colDiff);
    
            // Iterate through the positions between the start and end
            $nextRow = $fromCoords[0] + $rowDirection;
            $nextCol = $fromCoords[1] + $colDirection;
            while ($nextRow != $toCoords[0] || $nextCol != $toCoords[1]) {
                $pos = $nextRow . ',' . $nextCol;
    
                // Check if the position exists on the board
                if (isset($board[$pos])) {
                    return true;
                }
    
                // Move to the next position
                $nextRow += $rowDirection;
                $nextCol += $colDirection;
            }
        }
        return false;
    }

}