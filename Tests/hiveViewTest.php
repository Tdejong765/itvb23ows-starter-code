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
    
        $hiveView = new hiveView($gameController);
        ob_start();
    
        $hiveView->showTiles('player1');
        $output = ob_get_clean();
    
        $expectedOptions = [
            '<option value="stone1">stone1</option>',
            '<option value="stone2">stone2</option>',
        ];
    
        $this->assertEquals(implode('', $expectedOptions), $output);
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
