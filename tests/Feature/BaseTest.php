<?php

namespace Tests\Feature;

use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Tests\TestCase;

class BaseTest extends TestCase
{
    use DatabaseTruncation;
    use FeatureTestTrait;

//    protected $authenticatedUser;
//
//    /**
//     * Set up method to be run before each test.
//     *
//     * This method creates an authenticated user for use in testing.
//     */
//    protected function setUp(): void
//    {
//        parent::setUp();
//
//        $this->authenticatedUser = User::factory()->create();
//        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);
//
//        Sanctum::actingAs($this->authenticatedUser);
//    }
}
