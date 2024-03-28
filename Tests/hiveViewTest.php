<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../src/MVC/Controller/gameController.php';
require_once __DIR__ . '/../src/MVC/Controller/boardController.php';
require_once __DIR__ . '/../src/MVC/Controller/sessionController.php';
require_once __DIR__ . '/../src/MVC/View/hiveView.php';
require_once __DIR__ . '/../src/MVC/Model/hiveModel.php';


class hiveViewTest extends TestCase {

    //Test to see if hiveView shows tiles
    public function testShowTiles() {
        $sessionController = new sessionController();
        $boardController = new boardController();
        $dbHost = getenv('DB_HOST');
        $hiveModel = new hiveModel($dbHost, 'username', 'password', 'hive', 3306);
        $gameController = new gameController($hiveModel, $sessionController, $boardController);
    
        $gameController->setHand([
            'player1' => ['stone1' => 2, 'stone2' => 1],
            'player2' => ['stone3' => 3, 'stone4' => 1],
        ]);
    
        // Arrange: Create an instance of HiveView with the real GameController
        $hiveView = new hiveView($gameController);
        ob_start();
    
        // Act: Call the showTiles method for player1
        $hiveView->showTiles('player1');
        $output = ob_get_clean();
        
        // Assert: Check if the generated HTML matches the expected HTML
        $expectedOptions = [
            '<option value="stone1">stone1</option>',
            '<option value="stone2">stone2</option>',
        ];
    
        $this->assertEquals(implode('', $expectedOptions), $output);
    }


    //Test if dropdown options behave properly
    public function testFirstPlayPositions(): void {
        $sessionController = new sessionController();
        $boardController = new boardController();
        $dbHost = getenv('DB_HOST');
        $hiveModel = new hiveModel($dbHost, 'username', 'password', 'hive', 3306);
        $gameController = new gameController($hiveModel, $sessionController, $boardController);
    
        // Arrange: Create an instance of HiveView with the real GameController
        $hiveView = new hiveView($gameController);
        ob_start();
    
        // Act: Call the showAvailblePositions method
        $hiveView->showAvailablePositions();
        $output = ob_get_clean();
    
        // Assert: Check if the generated HTML matches the expected HTML
        $expectedOptions = [
            '<option value="0,0">0,0</option>',
        ];
    
        $this->assertEquals(implode('', $expectedOptions), $output);
    }


     //Test if dropdown options behave properly
     public function testAfterFirstMovePositions(): void {
        $sessionController = new sessionController();
        $boardController = new boardController();
        $dbHost = getenv('DB_HOST');
        $hiveModel = new hiveModel($dbHost, 'username', 'password', 'hive', 3306);
        $gameController = new gameController($hiveModel, $sessionController, $boardController);
    

        // Make the first move to position (0,0)
        $_POST['piece'] = 'A';
        $_POST['to'] = '0,0';
        $gameController->play();

        // Arrange: Create an instance of HiveView with the real GameController
        $hiveView = new hiveView($gameController);
        ob_start();
    
        // Act: Call the showAvailblePositions method
        $hiveView->showAvailablePositions();
        $output = ob_get_clean();
    
        // Assert: Check if the generated HTML matches the expected HTML
        $expectedOptions = [
            '<option value="0,1">0,1</option>',
            '<option value="0,-1">0,-1</option>',
            '<option value="1,0">1,0</option>',
            '<option value="-1,0">-1,0</option>',
            '<option value="-1,1">-1,1</option>',
            '<option value="1,-1">1,-1</option>',
        ];
    
        $this->assertEquals(implode('', $expectedOptions), $output);
    }
}