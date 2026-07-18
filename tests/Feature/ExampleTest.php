<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_public_application_includes_social_preview_metadata(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertViewIs('app')
            ->assertSee('<title>Bang — Open-Source Firearm Inventory</title>', false)
            ->assertSee('name="description" content="An open-source app for tracking firearms, ammunition, magazines, accessories, training, and range activity."', false)
            ->assertSee('property="og:title" content="Bang — Open-Source Firearm Inventory"', false)
            ->assertSee('property="og:image" content="'.asset('images/social-preview.png').'"', false)
            ->assertSee('property="og:image:width" content="1200"', false)
            ->assertSee('property="og:image:height" content="630"', false)
            ->assertSee('name="twitter:card" content="summary_large_image"', false);
    }
}
