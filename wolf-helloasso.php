<?php

/*
Plugin Name: Wolf HelloAsso
Plugin URI: http://wordpress.org/plugins/wolf-helloasso/
Description: This is not just a plugin, it symbolizes the hope and enthusiasm of an entire generation summed up in two words sung most famously by Louis Armstrong: Hello, Dolly. When activated you will randomly see a lyric from <cite>Hello, Dolly</cite> in the upper right of your admin screen on every page.
Author: Phabi1
Version: 0.0.1
Author URI: http://www.rollerlesloups.fr/
Requires plugins: wolf
*/

require __DIR__ . '/vendor/autoload.php';

$plugin = new \Wolf\HelloAsso\Plugin();
$plugin->run();