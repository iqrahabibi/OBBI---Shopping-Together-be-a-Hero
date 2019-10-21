<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase {
    /**
     * A basic test example.
     *
     * @return void
     */
    /*public function testError() {
        $resp = $this->post('api/mobile/login', []);
        $resp->assertExactJson([
            'meta' => [
                'code'    => 500,
                'message' => 'Akun belum terdaftarx.'
            ]
        ]);
    }*/

    public function testBasicTest () {
        $resp = $this->post('api/mobile/login', [
            'email'    => 'jokopriyono@gmail.com',
            'password' => 'jokojoko'
        ]);

        $resp->assertJsonStructure([
            'meta' => [
                'code',
                'message'
            ]
        ]);
    }
}
