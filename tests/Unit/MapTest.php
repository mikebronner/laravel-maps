<?php namespace GeneaLabs\LaravelMaps\Tests\Unit;

use GeneaLabs\LaravelMaps\Map;
use GeneaLabs\LaravelMaps\Tests\UnitTestCase;

class MapTest extends UnitTestCase
{
    public function test_map_can_be_instantiated(): void
    {
        $map = new Map();

        $this->assertInstanceOf(Map::class, $map);
    }

    public function test_map_can_be_instantiated_with_config(): void
    {
        $map = new Map(['apiKey' => 'test-key']);

        $this->assertSame('test-key', $map->apiKey);
    }

    public function test_map_can_be_resolved_from_container(): void
    {
        $map = app('map');

        $this->assertInstanceOf(Map::class, $map);
    }

    public function test_map_has_default_center(): void
    {
        $map = new Map();

        $this->assertSame('37.4419, -122.1419', $map->center);
    }

    public function test_map_can_initialize_with_config(): void
    {
        $map = new Map();
        $map->initialize(['center' => '40.7128, -74.0060']);

        $this->assertSame('40.7128, -74.0060', $map->center);
    }

    public function test_map_can_create_map_output(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $map->initialize(['center' => '37.4419, -122.1419']);
        $output = $map->create_map();

        $this->assertArrayHasKey('js', $output);
        $this->assertArrayHasKey('html', $output);
    }

    public function test_map_handles_empty_config(): void
    {
        $map = new Map([]);

        $this->assertInstanceOf(Map::class, $map);
        $this->assertSame('', $map->apiKey);
    }

    public function test_create_map_throws_exception_without_api_key(): void
    {
        $map = new Map();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('A Google Maps API key is required');

        $map->create_map();
    }

    public function test_create_map_uses_weekly_api_version(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $output = $map->create_map();

        $this->assertStringContainsString('v=weekly', $output['js']);
        $this->assertStringNotContainsString('v=3', $output['js']);
    }

    public function test_create_map_uses_correct_api_domain(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $output = $map->create_map();

        $this->assertStringContainsString(
            'https://maps.googleapis.com/maps/api/js',
            $output['js']
        );
    }

    public function test_create_map_includes_api_key_in_script(): void
    {
        $map = new Map(['apiKey' => 'my-test-key-123']);
        $output = $map->create_map();

        $this->assertStringContainsString('key=my-test-key-123', $output['js']);
    }

    public function test_geolocation_checks_secure_context(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $map->initialize(['center' => 'auto']);
        $output = $map->create_map();

        $this->assertStringContainsString('window.isSecureContext', $output['js']);
        $this->assertStringContainsString(
            'Geolocation requires a secure origin',
            $output['js']
        );
    }

    public function test_directions_geolocation_checks_secure_context(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $map->initialize([
            'directions' => true,
            'directionsStart' => 'auto',
            'directionsEnd' => 'some destination',
        ]);
        $output = $map->create_map();

        $this->assertStringContainsString('window.isSecureContext', $output['js']);
    }
}
