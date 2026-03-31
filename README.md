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

### Custom Overlay Popup
You can create a custom overlay popup that appears automatically (without requiring a click or hover) by using the `onload` configuration option together with the Google Maps JavaScript API [custom popup example](https://developers.google.com/maps/documentation/javascript/examples/overlay-popup).

The approach has two parts:
1. Define a custom `Popup` overlay class in a separate `<script>` tag.
2. Use the `onload` option to instantiate the popup once the map finishes loading.

```php
Route::get('/popup', function () {
    $lat = 37.4419;
    $lng = -122.1419;

    $config = [
        'center' => "{$lat}, {$lng}",
        'zoom' => 13,
        'onload' => "
            var position = new google.maps.LatLng({$lat}, {$lng});
            var popup = new Popup(position, document.getElementById('popup-content'));
            popup.setMap(map);
        ",
    ];

    app('map')->initialize($config);

    $map = app('map')->create_map();

    $popupScript = <<<'JS'
    <script>
    class Popup extends google.maps.OverlayView {
        constructor(position, content) {
            super();
            this.position = position;
            content.classList.add("popup-bubble");

            var container = document.createElement("div");
            container.classList.add("popup-container");
            container.appendChild(content);

            this.anchor = document.createElement("div");
            this.anchor.classList.add("popup-anchor");
            this.anchor.appendChild(container);

            this.stopEventPropagation();
        }

        onAdd() {
            this.getPanes().floatPane.appendChild(this.anchor);
        }

        onRemove() {
            if (this.anchor.parentElement) {
                this.anchor.parentElement.removeChild(this.anchor);
            }
        }

        draw() {
            var divPosition = this.getProjection().fromLatLngToDivPixel(this.position);
            var display = Math.abs(divPosition.x) < 4000 && Math.abs(divPosition.y) < 4000
                ? "block"
                : "none";

            if (display === "block") {
                this.anchor.style.left = divPosition.x + "px";
                this.anchor.style.top = divPosition.y + "px";
            }

            if (this.anchor.style.display !== display) {
                this.anchor.style.display = display;
            }
        }

        stopEventPropagation() {
            var anchor = this.anchor;
            anchor.style.cursor = "auto";

            ["click", "dblclick", "contextmenu", "wheel", "mousedown",
             "mouseup", "mouseover", "mouseout", "touchstart", "touchend",
             "touchmove"].forEach(function (event) {
                anchor.addEventListener(event, function (e) {
                    e.stopPropagation();
                });
            });
        }
    }
    </script>
    JS;

    $popupStyles = <<<'CSS'
    <style>
    .popup-container {
        cursor: auto;
        position: absolute;
        width: 200px;
        transform: translate(-50%, -100%);
    }
    .popup-bubble {
        background-color: white;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        font-family: sans-serif;
        font-size: 14px;
        overflow-y: auto;
        max-height: 200px;
    }
    .popup-anchor {
        position: absolute;
        width: 100%;
    }
    </style>
    CSS;

    return "<html><head>"
        . $popupStyles
        . $map['js']
        . $popupScript
        . "</head><body>"
        . '<div id="popup-content" style="display:none;">Hello from a custom popup!</div>'
        . $map['html']
        . "</body></html>";
});
```

The `onload` JavaScript runs after the map initializes, so the `map` variable is available. The `Popup` class extends `google.maps.OverlayView` and positions custom HTML at a given lat/lng coordinate. The popup appears immediately without any user interaction.

You can also use the `infowindow_content` marker option for simpler popups that open on click:

```php
$marker = [
    'position' => '37.4419, -122.1419',
    'infowindow_content' => '<strong>Hello!</strong><br>This popup opens on click.',
];
app('map')->add_marker($marker);
```

To auto-open a standard info window without a click, use the `onload` option:

```php
$config = [
    'center' => '37.4419, -122.1419',
    'zoom' => 13,
    'onload' => 'google.maps.event.trigger(marker_0, "click");',
];
```

### More Examples
BIOINSTALL has a great website showing how to do all the things with the class. No reason to reinvent the wheel, so [here](http://biostall.com/demos/google-maps-v3-api-codeigniter-library/) it is. The only thing to note is that `$this->googlemaps` is now either the facade `Map::` or the app variable `app('map')`.
