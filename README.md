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

**This package is the continuation of `GeneaLabs/Phpgmaps`. The move to the more appropriately-named package namespace is in preparation for a complete rewrite of the package, optimizing it for Laravel.**

# PhpGmaps
This repo aims to keep appitventures/phpgmaps alive, hopefully filling in temporarily until they make their repo
available again, or else continuing its maintenance going forward and keeping it working with future versions of
Laravel.

## Installation
Add the repo to composer.json under this new namespace:
```sh
composer require genealabs/laravel-maps
```

Add an environment variable with your Google Maps API Key in your `.env` file:
```
GOOGLE_MAPS_API_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

Lastly, add the following entry to your `\config\services.php` config file:
```php
    'google' => [
        'maps' => [
            'api-key' => env('GOOGLE_MAPS_API_KEY'),
        ],
    ],
```

### Example
The following code will prompt the user for access to their geolocation and then creates a map centered on their lat/lng

    Route::get('/', function(){
        $config = array();
        $config['center'] = 'auto';
        $config['onboundschanged'] = 'if (!centreGot) {
                var mapCentre = map.getCenter();
                marker_0.setOptions({
                    position: new google.maps.LatLng(mapCentre.lat(), mapCentre.lng())
                });
            }
            centreGot = true;';

        app('map')->initialize($config);

        // set up the marker ready for positioning
        // once we know the users location
        $marker = array();
        app('map')->add_marker($marker);

        $map = app('map')->create_map();
        echo "<html><head><script type="text/javascript">var centreGot = false;</script>".$map['js']."</head><body>".$map['html']."</body></html>";
    });

### MultiPolygon from GeoJSON
To render a [GeoJSON MultiPolygon](https://datatracker.ietf.org/doc/html/rfc7946#section-3.1.7), decode the JSON and call `add_polygon()` once per polygon in the collection:

```php
Route::get('/multipolygon', function () {
    $geojson = json_decode($geojsonString); // your GeoJSON MultiPolygon

    $config = ['center' => '-6.2, 106.8', 'zoom' => 10];
    app('map')->initialize($config);

    foreach ($geojson->coordinates as $polygon) {
        $points = [];

        // Each polygon may have an outer ring and optional holes — use the outer ring (index 0)
        foreach ($polygon[0] as $coord) {
            $points[] = $coord[1] . ', ' . $coord[0]; // GeoJSON is [lng, lat]; the library expects "lat, lng"
        }

        app('map')->add_polygon(['points' => $points]);
    }

    $map = app('map')->create_map();

    return '<html><head>' . $map['js'] . '</head><body>' . $map['html'] . '</body></html>';
});
```

> **Note:** GeoJSON coordinates use `[longitude, latitude]` order, but this library expects `"latitude, longitude"` strings. The example above swaps them accordingly.

### More Examples
BIOINSTALL has a great website showing how to do all the things with the class. No reason to reinvent the wheel, so [here](http://biostall.com/demos/google-maps-v3-api-codeigniter-library/) it is. The only thing to note is that `$this->googlemaps` is now either the facade `Map::` or the app variable `app('map')`.
