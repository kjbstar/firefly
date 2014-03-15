<?php

/**
 * TODO every successful POST should have a 'success' flash.
 * TODO every failed POST should have a 'error' flash.
 */
class TestCase extends Illuminate\Foundation\Testing\TestCase
{

    /**
     * Creates the application.
     *
     * @return Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';



        return require __DIR__ . '/../../bootstrap/start.php';


    }

    public function testIsTrue() {
        $this->assertTrue(true);
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
//        $test = new self;
//        $test->setUp();
//        $test->seed();
        echo 1;
    }

    protected function tearDown()
    {
        parent::tearDown();

    }
}
