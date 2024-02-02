<?php

use PHPUnit\Framework\TestCase;

use Controller\boardController;
use Controller\gameController;
use Controller\sessionController;
use View\hiveView;
use Model\hiveModel;

class hiveViewTest extends TestCase {

    public function testShowTiles() {
        // Mocking gameController for the test
        $gameControllerMock = $this->getMockBuilder(gameController::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Set up expectations for getHand
        $gameControllerMock->method('getHand')
            ->willReturn([
                'player1' => ['stone1' => 2, 'stone2' => 1],
                'player2' => ['stone3' => 3, 'stone4' => 1],
            ]);

        // Capture the output of the showtilesfunction
        $hiveView = new hiveView($gameControllerMock);
        $options = $hiveView->showTiles('player1');
        
        // Assert that the output contains the expected options
        $expectedOptions = [
            '<option value="stone1">stone1</option>',
            '<option value="stone2">stone2</option>',
        ];

        $this->assertEquals($expectedOptions, $options);
    } 
}
