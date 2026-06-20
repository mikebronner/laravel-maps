# laravel-maps

[![GitHub (pre-)release](https://img.shields.io/github/release/GeneaLabs/laravel-maps/all.svg)](https://github.com/GeneaLabs/laravel-maps)
[![Packagist](https://img.shields.io/packagist/dt/GeneaLabs/laravel-maps.svg)](https://packagist.org/packages/genealabs/laravel-maps)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/GeneaLabs/laravel-maps/master/LICENSE)

## Version Support

| Laravel | PHP |
|---------|-----|
| 11.x    | 8.2, 8.3, 8.4, 8.5 |
| 12.x    | 8.2, 8.3, 8.4, 8.5 |
| 13.x    | 8.3, 8.4, 8.5 |

## Prerequisites
- PHP >= 8.2
- Laravel >= 11.0
- A [Google Maps API key](https://developers.google.com/maps/documentation/javascript/get-api-key)

## Installation

```sh
composer require genealabs/laravel-maps
```

Add your Google Maps API key to `.env`:

```
GOOGLE_MAPS_API_KEY=your-api-key-here
```

Add the following entry to your `config/services.php` file:

```php
'google' => [
    'maps' => [
        'api-key' => env('GOOGLE_MAPS_API_KEY'),
    ],
],
```

## Usage

The package provides a `Map` facade and an `app('map')` helper. Both
give you the same API — use whichever you prefer.

Every map follows the same pattern:

1. **Initialize** the map with a config array.
2. **Add overlays** (markers, polylines, polygons, etc.).
3. **Create** the map to get the HTML and JavaScript output.
4. **Render** the output in your Blade view.

### Basic Map with Geolocation

This prompts the user for their location and centers the map on it:

```php
use GeneaLabs\LaravelMaps\Facades\Map;

Route::get('/map', function () {
    Map::initialize([
        'center' => 'auto',
        'onboundschanged' => 'if (!centreGot) {
            var mapCentre = map.getCenter();
            marker_0.setOptions({
                position: new google.maps.LatLng(mapCentre.lat(), mapCentre.lng())
            });
        }
        centreGot = true;',
    ]);

    Map::add_marker([]);

    return view('map', ['map' => Map::create_map()]);
});
```

### Single Marker

```php
Map::initialize([
    'center' => '37.4419, -122.1419',
    'draggableCursor' => 'default',
]);

Map::add_marker([
    'position' => '37.4419, -122.1419',
]);

$map = Map::create_map();
```

### Multiple Markers

```php
Map::initialize([
    'center' => '37.4419, -122.1419',
    'zoom' => 'auto',
    'draggableCursor' => 'default',
]);

Map::add_marker([
    'position' => '37.429, -122.1519',
    'infowindow_content' => 'Hello World!',
    'icon' => 'https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=A|9999FF|000000',
]);

Map::add_marker([
    'position' => '37.409, -122.1319',
    'draggable' => true,
    'animation' => 'DROP',
]);

Map::add_marker([
    'position' => '37.449, -122.1419',
    'onclick' => 'alert("You clicked the marker!")',
]);

$map = Map::create_map();
```

### Polyline

```php
Map::initialize([
    'center' => '37.4419, -122.1419',
    'zoom' => 'auto',
]);

Map::add_polyline([
    'points' => [
        '37.429, -122.1319',
        '37.429, -122.1419',
        '37.4419, -122.1219',
    ],
]);

$map = Map::create_map();
```

### Polygon

```php
Map::initialize([
    'center' => '37.4419, -122.1419',
    'zoom' => 'auto',
]);

Map::add_polygon([
    'points' => [
        '37.425, -122.1321',
        '37.4422, -122.1622',
        '37.4412, -122.1322',
        '37.425, -122.1021',
    ],
    'strokeColor' => '#000099',
    'fillColor' => '#000099',
]);

$map = Map::create_map();
```

### Drawing Tools

```php
Map::initialize([
    'drawing' => true,
    'drawingDefaultMode' => 'circle',
    'drawingModes' => ['circle', 'rectangle', 'polygon'],
]);

$map = Map::create_map();
```

### Directions

```php
Map::initialize([
    'center' => '37.4419, -122.1419',
    'zoom' => 'auto',
    'directions' => true,
    'directionsStart' => 'Empire State Building',
    'directionsEnd' => 'Statue of Liberty',
    'directionsDivID' => 'directionsDiv',
]);

$map = Map::create_map();
```

When rendering, include a `<div id="directionsDiv"></div>` in your view
to display the turn-by-turn directions.

### Street View

```php
Map::initialize([
    'center' => '37.4419, -122.1419',
    'map_type' => 'STREET',
    'streetViewPovHeading' => 90,
]);

$map = Map::create_map();
```

### Marker Clustering

```php
Map::initialize([
    'center' => '37.409, -122.1319',
    'zoom' => '13',
    'cluster' => true,
    'clusterStyles' => [
        [
            'url' => 'https://raw.githubusercontent.com/googlemaps/js-marker-clusterer/gh-pages/images/m1.png',
            'width' => '53',
            'height' => '53',
        ],
    ],
]);

Map::add_marker(['position' => '37.409, -122.1319']);
Map::add_marker(['position' => '37.409, -122.1419']);
Map::add_marker(['position' => '37.409, -122.1219']);
Map::add_marker(['position' => '37.409, -122.1519']);

$map = Map::create_map();
```

### KML Layer

```php
Map::initialize([
    'zoom' => 'auto',
    'kmlLayerURL' => 'https://www.google.com/maps/d/kml?mid=your-kml-id',
]);

$map = Map::create_map();
```

### Circles

```php
Map::initialize([
    'center' => '37.4419, -122.1419',
    'zoom' => 14,
]);

Map::add_circle([
    'center' => '37.4419, -122.1419',
    'radius' => 500,
    'strokeColor' => '#FF0000',
    'fillColor' => '#FF0000',
    'fillOpacity' => 0.35,
]);

$map = Map::create_map();
```

### Rectangles

```php
Map::initialize([
    'center' => '37.4419, -122.1419',
    'zoom' => 14,
]);

Map::add_rectangle([
    'bounds' => [
        '37.435, -122.155',
        '37.449, -122.129',
    ],
    'strokeColor' => '#FF0000',
    'fillColor' => '#FF0000',
    'fillOpacity' => 0.35,
]);

$map = Map::create_map();
```

### Ground Overlay

```php
Map::initialize([
    'center' => '40.7128, -74.0060',
    'zoom' => 13,
]);

Map::add_ground_overlay([
    'url' => 'https://example.com/overlay-image.png',
    'bounds' => [
        '40.700, -74.020',
        '40.730, -73.990',
    ],
    'opacity' => 0.5,
]);

$map = Map::create_map();
```

## Rendering in Blade

Pass the map data to your view and render the JavaScript in your
`<head>` and the HTML in your `<body>`:

```blade
<!DOCTYPE html>
<html>
<head>
    {!! $map['js'] !!}
</head>
<body>
    {!! $map['html'] !!}
</body>
</html>
```

With a layout:

```blade
@section('scripts')
    {!! $map['js'] !!}
@endsection

@section('content')
    {!! $map['html'] !!}
    <div id="directionsDiv"></div>
@endsection
```

## Controller Example

For more complex setups, use a dedicated controller:

```php
namespace App\Http\Controllers;

use GeneaLabs\LaravelMaps\Facades\Map;

class MapController extends Controller
{
    public function markers(): \Illuminate\View\View
    {
        Map::initialize([
            'center' => '37.4419, -122.1419',
            'zoom' => 'auto',
        ]);

        Map::add_marker([
            'position' => '37.429, -122.1519',
            'infowindow_content' => 'Location A',
        ]);

        Map::add_marker([
            'position' => '37.449, -122.1419',
            'infowindow_content' => 'Location B',
        ]);

        return view('maps.show', ['map' => Map::create_map()]);
    }

    public function directions(): \Illuminate\View\View
    {
        Map::initialize([
            'center' => '37.4419, -122.1419',
            'zoom' => 'auto',
            'directions' => true,
            'directionsStart' => 'San Francisco, CA',
            'directionsEnd' => 'San Jose, CA',
            'directionsDivID' => 'directionsDiv',
        ]);

        return view('maps.directions', ['map' => Map::create_map()]);
    }
}
```

## API Reference

The `Map` facade (or `app('map')`) exposes the following methods:

| Method | Description |
|--------|-------------|
| `initialize(array $config)` | Configure map center, zoom, type, and behavior |
| `add_marker(array $params)` | Add a marker with position, info window, icon, etc. |
| `add_polyline(array $params)` | Draw a polyline from an array of points |
| `add_polygon(array $params)` | Draw a filled polygon from an array of points |
| `add_circle(array $params)` | Draw a circle with center and radius |
| `add_rectangle(array $params)` | Draw a rectangle from bounds |
| `add_ground_overlay(array $params)` | Overlay an image on the map |
| `create_map()` | Generate the map and return `['js' => ..., 'html' => ...]` |
| `get_lat_long_from_address(string $address)` | Geocode an address to lat/lng |

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
