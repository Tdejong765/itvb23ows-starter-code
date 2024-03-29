<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../src/MVC/Controller/gameController.php';
require_once __DIR__ . '/../src/MVC/Controller/boardController.php';
require_once __DIR__ . '/../src/MVC/Controller/sessionController.php';
require_once __DIR__ . '/../src/MVC/View/hiveView.php';
require_once __DIR__ . '/../src/MVC/Model/hiveModel.php';


class boardControllerTest extends TestCase {


    public function testGrassHopperAbleToMove() {

        // Initialize necessary dependencies
        $sessionController = new sessionController();
        $boardController = new boardController();
        $dbHost = getenv('DB_HOST');
        $hiveModel = new hiveModel($dbHost, 'username', 'password', 'hive', 3306);
        $gameController = new gameController($hiveModel, $sessionController, $boardController);

        // Arrange: Define a sample board configuration
        $board = [
            "0,0" => [[0, "G"]],
            "0,1" => [[1, "A"]],
            "0,-2" => [[0, "Q"]],
            "1,-2" => [[1, "Q"]]
        ];

        $from = "0,0"; 
        $to = "0,3";   

        // Act: Call the method being tested from boardController
        $result = $boardController->moveGrasshopper($board, $from, $to);

        // Assert: Check the result
        $this->assertTrue($result, "Grasshopper should be able to move to the destination.");
    }


    public function testGrassHopperEmptyTiles() {
        
        // Initialize necessary dependencies
        $sessionController = new sessionController();
        $boardController = new boardController();
        $dbHost = getenv('DB_HOST');
        $hiveModel = new hiveModel($dbHost, 'username', 'password', 'hive', 3306);
        $gameController = new gameController($hiveModel, $sessionController, $boardController);
    
        // Arrange:  Define a sample board configuration
        $board = [
            "0,0" => [[0, "G"]],
            "0,1" => [[1, "A"]],
            "0,-2" => [[0, "Q"]],
            "1,-2" => [[1, "Q"]]
        ];
    
        $from = "0,0"; 
        $to = "0,4";   
    
        // Act: Call the method being tested from boardController
        $result = $boardController->moveGrasshopper($board, $from, $to);
    
        // Assert: Check the result
        $this->assertTrue($result, "Grasshopper should not be able jump over empty tiles.");
    }
    

    public function testGrassHopperTileOccupied() {

        // Initialize necessary dependencies
        $sessionController = new sessionController();
        $boardController = new boardController();
        $dbHost = getenv('DB_HOST');
        $hiveModel = new hiveModel($dbHost, 'username', 'password', 'hive', 3306);
        $gameController = new gameController($hiveModel, $sessionController, $boardController);
    
        // Arrange: Define a sample board configuration
        $board = [
            "0,0" => [[0, "G"]],
            "0,1" => [[1, "A"]],
            "0,-2" => [[0, "Q"]],
            "1,-2" => [[1, "Q"]]
        ];

        $from = "0,0"; 
        $to = "0,1";   
    
        // Act: Call the method being tested from boardController
        $result = $boardController->moveGrasshopper($board, $from, $to);
    
        // Assert: Check the result
        $this->assertFalse($result, "Grasshopper should not be able to move to occupied tile.");
    }


    public function testAntValidMoveBorder() {

        // Initialize necessary dependencies
        $sessionController = new sessionController();
        $boardController = new boardController();
        $dbHost = getenv('DB_HOST');
        $hiveModel = new hiveModel($dbHost, 'username', 'password', 'hive', 3306);
        $gameController = new gameController($hiveModel, $sessionController, $boardController);
    
        // Arrange: Define a sample board configuration
         $board = [
            "0,0" => [[0, "Q"]],
            "-1,0" => [[1, "A"]],
            "-1,-1" => [[0, "B"]],
            "1,0" => [[1, "Q"]],
            "0,-1" => [[0, "S"]],
            "0,-2" => [[1, "A"]],
            "1,-2" => [[0, "B"]],
        ];

        $from = "-1,0"; 
        $to = "1,2"; 
    
        // Act: Call the method being tested from boardController
        $result = $boardController->moveAnt($board, $from, $to);
    
        // Assert: Check the result
        $this->assertFalse($result);
    }


    public function testAntInvalidMoveSurrounded() {
        
        // Initialize necessary dependencies
        $sessionController = new sessionController();
        $boardController = new boardController();
        $dbHost = getenv('DB_HOST');
        $hiveModel = new hiveModel($dbHost, 'username', 'password', 'hive', 3306);
        $gameController = new gameController($hiveModel, $sessionController, $boardController);
    
        // Arrange: Define a sample board configuration
        $board = [
            "0,0" => [[0, "Q"]],
            "-1,0" => [[1, "A"]],
            "-1,-1" => [[0, "B"]],
            "1,0" => [[1, "Q"]],
            "0,-1" => [[0, "S"]],
            "0,-2" => [[1, "A"]],
            "1,-2" => [[0, "B"]],
        ];

        $from = "-1,0"; 
        $to = "1,2"; 
    
        // Act: Call the method being tested from boardController
        $result = $boardController->moveAnt($board, $from, $to);
    
        // Assert: Check the result
        $this->assertFalse($result);
    }
}