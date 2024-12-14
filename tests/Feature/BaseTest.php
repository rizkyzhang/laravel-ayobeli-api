<?php

namespace Tests\Feature;

use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Tests\TestCase;

class BaseTest extends TestCase
{
    use DatabaseTruncation;
    use FeatureTestTrait;

    /**
     * Set up method to be run before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }
}
