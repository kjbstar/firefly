<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 13/03/14
 * Time: 09:03
 */

class PageControllerTest extends TestCase {
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);

    }

    public function testRecalculate() {
        $response = $this->call(
            'GET', 'home/recalc'
        );
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('index');
    }

} 