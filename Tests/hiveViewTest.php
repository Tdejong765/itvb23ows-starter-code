<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../src/MVC/Controller/gameController.php';
require_once __DIR__ . '/../src/MVC/Controller/boardController.php';
require_once __DIR__ . '/../src/MVC/Controller/sessionController.php';
require_once __DIR__ . '/../src/MVC/View/hiveView.php';
require_once __DIR__ . '/../src/MVC/Model/hiveModel.php';


class hiveViewTest extends TestCase {

    public function testShowTiles() {
        // Create real instances of the dependencies (sessionController, boardController)
        $sessionController = new sessionController();
        $boardController = new boardController();

        $dbHost = getenv('DB_HOST');

        // Instantiate a real hiveModel object with actual database connection details
        $hiveModel = new hiveModel($dbHost, 'username', 'password', 'hive', 3306);

        // Instantiate GameController with the real hiveModel and mock dependencies
        $gameController = new gameController($hiveModel, $sessionController, $boardController);

        // Set up the hand property with test data
        $gameController->setHand([
            'player1' => ['stone1' => 2, 'stone2' => 1],
            'player2' => ['stone3' => 3, 'stone4' => 1],
        ]);
    
        // Create an instance of hiveView with the actual GameController
        $hiveView = new hiveView($gameController);
    
        // Call the method being tested
        $options = $hiveView->showTiles('player1');
    
        // Define the expected options based on the expected hand
        $expectedOptions = [
            '<option value="stone1">stone1</option>',
            '<option value="stone2">stone2</option>',
        ];
    
        // Assert that the returned options match the expected options
        $this->assertEquals($expectedOptions, $options);
    }




    public function testShowAvailablePositions(): void
    {   
        $boardControllerMock = $this->createMock(boardController::class);
        $boardControllerMock->expects($this->once())
            ->method('getOffsets')
            ->willReturn([[1, 0], [0, 1]]);

        $gameControllerMock = $this->createMock(gameController::class);
        $gameControllerMock->expects($this->once())
            ->method('getOffsets')
            ->willReturn([[1, 0], [0, 1]]);

        $hiveView = $this->getMockBuilder(HiveView::class)
            ->setConstructorArgs([$gameControllerMock])
            ->getMock();

        $hiveView->expects($this->once())->method('getAvailablePositions');
        $hiveView->showAvailablePositions();
    }
}
