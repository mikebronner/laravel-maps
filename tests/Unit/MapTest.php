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
}
