<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class DevToolsExposureTest extends TestCase
{
    public function test_liveness_test_page_is_not_registered_outside_local(): void
    {
        $this->assertFalse(Route::has('dev.liveness.show'));
        $this->assertFalse(Route::has('dev.liveness.session'));
        $this->assertFalse(Route::has('dev.liveness.result'));

        $this->post('/dev/liveness/'.Str::uuid().'/session')->assertNotFound();
    }
}
