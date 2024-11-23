<?php

/***************************************************************************************************
*                                  CATMULL-ROM SPLINE DROP-IN LIBRARY FOR GD
*            _                                 _                 ____ ____  
*  ___  __ _| | __ _ _ __ ___   __ _ _ __   __| | ___ _ __      / ___|  _ \ 
* / __|/ _` | |/ _` | '_ ` _ \ / _` | '_ \ / _` |/ _ \ '__|____| |  _| | | |
* \__ \ (_| | | (_| | | | | | | (_| | | | | (_| |  __/ | |_____| |_| | |_| |
* |___/\__,_|_|\__,_|_| |_| |_|\__,_|_| |_|\__,_|\___|_|        \____|____/ 
*                                                                           
* Example Image Script
* 
* USAGE
* 
* Run this script in a browser like this:
* 
* 127.0.0.1/example-catmullrom-spline.php
*
* Please see README.md for usage notes, issues, and PHP version compability.
***************************************************************************************************/

ini_set('display_errors', 1);
error_reporting(-1);

require_once('salamander-gd.helper.php');
require_once('salamander-gd.vertex.php');
require_once('salamander-gd.catmullrom.php');

define('GD_WIDTH', (int) 700);
define('GD_HEIGHT', (int) 500);

// Initialize image object and draw background
$image = imagecreatetruecolor(GD_WIDTH, GD_HEIGHT);
if (function_exists('imageantialias')) {
	imageantialias($image, true);
}
$black = imagecolorallocate($image, 68, 68, 68);
$grey = imagecolorallocate($image, 200, 200, 200);
$white = imagecolorallocate($image, 255, 255, 255);

imagefilledrectangle(
	$image,
	0,
	0,
	GD_WIDTH - 1,
	GD_HEIGHT - 1,
	$black
);

// Draw a shape
$spline = new \SalamanderGD\CatmullRomSpline();
$spline->addPoint(50, 50);
$spline->addPoint(300, 50);
$spline->addPoint(150, 50);
$spline->addPoint(300, 300);
$spline->addPoint(50, 300);
$spline->setBackgroundColor('#FFFF00');
$spline->setStrokeColor('#FF0000');
$spline->setStrokeWidth(3);
$spline->showControlPoints('#00FFFF', 10);
$spline->draw($image);
unset($spline);

// Draw a line graph with two sets of random data
imageline($image, 50, (GD_HEIGHT - 50), (GD_WIDTH - 250), (GD_HEIGHT - 50), $white);
imageline($image, 50, (GD_HEIGHT - 150), 50, (GD_HEIGHT - 50), $white);
for ($i = 75; $i <= 450; $i += 25) {
	imageline($image, $i, (GD_HEIGHT - 150), $i, (GD_HEIGHT - 50), $grey);
}

$spline = new \SalamanderGD\CatmullRomSpline($closed_spline=false);
for ($i = 50; $i <= 450; $i += 25) {
	$spline->addPoint($i, 450 - rand(0, 100));
}
$spline->setStrokeColor([255, 0, 0]);
$spline->setStrokeWidth(2);
$spline->draw($image);
unset($spline);

$spline = new \SalamanderGD\CatmullRomSpline($closed_spline=false);
for ($i = 50; $i <= 450; $i += 10) {
	$spline->addPoint($i, 450 - rand(0, 100));
}
$spline->setStrokeColor('#00FFFF');
$spline->setStrokeWidth(1);
$spline->showControlPoints('#00FFFF', 10);
$spline->setSplineAlpha(1);
$spline->setSplineTension(1);
$spline->draw($image);
unset($spline);

// Draw a spirograph-style pattern
for ($i = 0; $i < 24; $i++) {
	$spline = new \SalamanderGD\CatmullRomSpline();
	$spline->addPoint(375, 150);
	$spline->addPoint(370, 70);
	$spline->addPoint(370, 70);
	$spline->addPoint(380, 70);
	$spline->setStrokeColor('#F6AA33');
	$spline->setStrokeWidth(1);
	$spline->rotate(375, 150, $i * 15);
	$spline->draw($image);
	unset($spline);
}

// Draw a random squiggle
$spline = new \SalamanderGD\CatmullRomSpline();
for ($i = 1; $i <= 50; $i++) {
	$spline->addPoint(rand(350, 500 - 50), rand(275, 500 - 175));
}
$spline->setStrokeColor('#00FF00');
$spline->setStrokeWidth(1);
$spline->draw($image);
unset($spline);

// Draw a series of open and closed splines with different tensions
for ($i = 0; $i <= 4; $i++) {
	$y_offset = 20 + ($i * 100);

	switch ($i) {
		case 0:
			$tension = (float) 1;
			break;
		case 1:
			$tension = (float) 0.5;
			break;
		case 2:
			$tension = (float) 0;
			break;
		case 3:
			$tension = (float) -0.5;
			break;
		case 4:
			$tension = (float) -1;
			break;
		default:break;
	}

	// Open spline
	$spline = new \SalamanderGD\CatmullRomSpline(false);
	$spline->addPoint(500, $y_offset + 50);
	$spline->addPoint(500, $y_offset);
	$spline->addPoint(550, $y_offset);
	$spline->addPoint(550, $y_offset + 50);
	if ($i === 4) {
		$spline->setBackgroundColor('#FF0000');
	}
	$spline->setStrokeColor('#FFFF00');
	$spline->setStrokeWidth(1);
	if ($i === 2) {
		$spline->showControlPoints('#00FFFF', 5);
	}
	if ($i === 3) {
		$spline->showInterpolationPoints('#FF00FF', 5);
	}
	$spline->setSplineTension($tension);
	$spline->draw($image);
	unset($spline);

	// Closed spline
	$spline = new \SalamanderGD\CatmullRomSpline();
	$spline->addPoint(600, $y_offset + 50);
	$spline->addPoint(600, $y_offset);
	$spline->addPoint(650, $y_offset);
	$spline->addPoint(650, $y_offset + 50);
	if ($i === 4) {
		$spline->setBackgroundColor('#FF0000');
	}
	$spline->setStrokeColor('#FFFF00');
	$spline->setStrokeWidth(1);
	if ($i === 2) {
		$spline->showControlPoints('#00FFFF', 5);
	}
	if ($i === 3) {
		$spline->showInterpolationPoints('#FF00FF', 5);
	}
	$spline->setSplineTension($tension);
	$spline->draw($image);
	unset($spline);
}

header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
