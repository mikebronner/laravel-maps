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

    public function test_map_can_be_resolved_from_container(): void
    {
        $map = app('map');

        $this->assertInstanceOf(Map::class, $map);
    }

    public function test_map_can_be_initialized_with_config(): void
    {
        $map = new Map(['center' => '40.7128, -74.0060']);

        $this->assertSame('40.7128, -74.0060', $map->center);
    }

    public function test_map_creates_output(): void
    {
        $map = new Map();
        $output = $map->create_map();

        $this->assertIsArray($output);
        $this->assertArrayHasKey('js', $output);
        $this->assertArrayHasKey('html', $output);
    }
}
