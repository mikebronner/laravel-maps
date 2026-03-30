<?php namespace GeneaLabs\LaravelMaps\Tests\Unit;

use GeneaLabs\LaravelMaps\Map;
use GeneaLabs\LaravelMaps\Tests\UnitTestCase;

class InfoWindowTest extends UnitTestCase
{
    public function test_infowindow_is_created_inside_initialize_function(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $output = $map->create_map();

        $js = $output['js'];

        // InfoWindow must be created inside initialize_ function, not before it
        $initializePos = strpos($js, 'function initialize_map()');
        $infoWindowPos = strpos($js, 'iw_map = new google.maps.InfoWindow(');

        $this->assertNotFalse($initializePos, 'initialize_ function should exist in JS output');
        $this->assertNotFalse($infoWindowPos, 'InfoWindow creation should exist in JS output');
        $this->assertGreaterThan(
            $initializePos,
            $infoWindowPos,
            'InfoWindow should be created inside the initialize_ function, not before it'
        );
    }

    public function test_marker_with_infowindow_content_generates_click_listener(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $map->add_marker([
            'position' => '37.4419, -122.1419',
            'infowindow_content' => 'Hello World',
        ]);
        $output = $map->create_map();

        $js = $output['js'];

        $this->assertStringContainsString('marker_0.set("content", "Hello World")', $js);
        $this->assertStringContainsString('iw_map.setContent(this.get("content"))', $js);
        $this->assertStringContainsString('iw_map.open(map, this)', $js);
    }

    public function test_marker_without_infowindow_content_has_no_infowindow_listener(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $map->add_marker([
            'position' => '37.4419, -122.1419',
        ]);
        $output = $map->create_map();

        $js = $output['js'];

        $this->assertStringNotContainsString('marker_0.set("content"', $js);
    }

    public function test_infowindow_content_with_html_is_properly_escaped(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $map->add_marker([
            'position' => '37.4419, -122.1419',
            'infowindow_content' => '<div class="info">Test</div>',
        ]);
        $output = $map->create_map();

        $js = $output['js'];

        $this->assertStringContainsString('marker_0.set("content", "', $js);
        $this->assertStringContainsString('<div class=', $js);
    }

    public function test_infowindow_content_with_double_quotes_is_escaped(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $map->add_marker([
            'position' => '37.4419, -122.1419',
            'infowindow_content' => 'He said "hello"',
        ]);
        $output = $map->create_map();

        $js = $output['js'];

        $this->assertStringContainsString('He said \\"hello\\"', $js);
    }

    public function test_infowindow_content_with_newlines_is_escaped(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $map->add_marker([
            'position' => '37.4419, -122.1419',
            'infowindow_content' => "Line 1\nLine 2",
        ]);
        $output = $map->create_map();

        $js = $output['js'];

        // Newlines should be escaped as \n in the JS string
        $this->assertStringContainsString('Line 1\nLine 2', $js);
        // Should NOT contain an actual newline inside the JS string literal
        $this->assertStringNotContainsString("\"Line 1\nLine 2\"", $js);
    }

    public function test_map_click_closes_infowindow(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $map->add_marker([
            'position' => '37.4419, -122.1419',
            'infowindow_content' => 'Test',
        ]);
        $output = $map->create_map();

        $js = $output['js'];

        // Map should have a click listener that closes the info window
        $this->assertStringContainsString('iw_map.close()', $js);
    }

    public function test_infowindow_close_listener_exists_even_without_markers(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $output = $map->create_map();

        $js = $output['js'];

        // The close-on-click listener should always be present
        $this->assertStringContainsString('iw_map.close()', $js);
    }

    public function test_multiple_markers_share_single_infowindow(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $map->add_marker([
            'position' => '37.4419, -122.1419',
            'infowindow_content' => 'Marker 1',
        ]);
        $map->add_marker([
            'position' => '40.7128, -74.0060',
            'infowindow_content' => 'Marker 2',
        ]);
        $output = $map->create_map();

        $js = $output['js'];

        // Both markers should use the same iw_map instance
        $this->assertStringContainsString('marker_0.set("content", "Marker 1")', $js);
        $this->assertStringContainsString('marker_1.set("content", "Marker 2")', $js);

        // Only one InfoWindow should be created
        $this->assertSame(
            1,
            substr_count($js, 'new google.maps.InfoWindow('),
            'Only one InfoWindow instance should be created'
        );
    }

    public function test_infowindow_max_width_is_applied(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $map->infowindowMaxWidth = 300;
        $map->add_marker([
            'position' => '37.4419, -122.1419',
            'infowindow_content' => 'Test',
        ]);
        $output = $map->create_map();

        $js = $output['js'];

        $this->assertStringContainsString('maxWidth: 300', $js);
    }

    public function test_infowindow_with_marker_onclick_both_fire(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $map->add_marker([
            'position' => '37.4419, -122.1419',
            'infowindow_content' => 'Test Content',
            'onclick' => 'console.log("clicked")',
        ]);
        $output = $map->create_map();

        $js = $output['js'];

        // Both the info window open and the onclick should be in the same listener
        $this->assertStringContainsString('iw_map.setContent(this.get("content"))', $js);
        $this->assertStringContainsString('console.log("clicked")', $js);
    }

    public function test_infowindow_with_async_loading_works(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $map->loadAsynchronously = true;
        $map->add_marker([
            'position' => '37.4419, -122.1419',
            'infowindow_content' => 'Async Test',
        ]);
        $output = $map->create_map();

        $js = $output['js'];

        // InfoWindow must still be inside initialize_ function
        $initializePos = strpos($js, 'function initialize_map()');
        $infoWindowPos = strpos($js, 'iw_map = new google.maps.InfoWindow(');

        $this->assertGreaterThan(
            $initializePos,
            $infoWindowPos,
            'InfoWindow should be created inside initialize_ even with async loading'
        );
    }

    public function test_infowindow_content_with_backslashes_is_escaped(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $map->add_marker([
            'position' => '37.4419, -122.1419',
            'infowindow_content' => 'path\\to\\file',
        ]);
        $output = $map->create_map();

        $js = $output['js'];

        $this->assertStringContainsString('path\\\\to\\\\file', $js);
    }

    public function test_infowindow_content_with_carriage_returns_is_escaped(): void
    {
        $map = new Map(['apiKey' => 'test-key']);
        $map->add_marker([
            'position' => '37.4419, -122.1419',
            'infowindow_content' => "Line 1\r\nLine 2\rLine 3",
        ]);
        $output = $map->create_map();

        $js = $output['js'];

        $this->assertStringContainsString('Line 1\nLine 2\nLine 3', $js);
    }
}
