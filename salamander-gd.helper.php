<?php

/***************************************************************************************************
*                                                            HELPER ROUTINES
*            _                                 _                 ____ ____  
*  ___  __ _| | __ _ _ __ ___   __ _ _ __   __| | ___ _ __      / ___|  _ \ 
* / __|/ _` | |/ _` | '_ ` _ \ / _` | '_ \ / _` |/ _ \ '__|____| |  _| | | |
* \__ \ (_| | | (_| | | | | | | (_| | | | | (_| |  __/ | |_____| |_| | |_| |
* |___/\__,_|_|\__,_|_| |_| |_|\__,_|_| |_|\__,_|\___|_|        \____|____/ 
*                                                                           
* @author Neil Withnall
* @version 0.1
* @licence MIT
* @system-requirements PHP 5.4+ with GD extension
*
* COPYRIGHT NOTICE
* 
* salamander-GD Core/Libraries (c) 2024 Neil Withnall (gammalogic).
* 
* Please see LICENSE for full copyright notice and licensing terms.
***************************************************************************************************/

namespace SalamanderGD;

class Helper
{
	/**
	 * Calculate the distance between two vertices
	 *
	 * @param Vertex $v1
	 * @param Vertex $v2
	 * @return float
	 */
	public static function calculateVerticesDistance($v1, $v2)
	{
		$a_x = $v1->x - $v2->x;
		$a_y = $v1->y - $v2->y;

		return round(sqrt(($a_x * $a_x) + ($a_y * $a_y)), 8);
	}

	/**
	 * Convert hex color value to RGB color values array
	 *
	 * @param string $hex_color
	 * @return array{'r': int, 'g': int, 'b': int}
	 */
	public static function convertHexColorToRGB($hex_color)
	{
		$hex_color = str_replace('#', '', $hex_color);
		$r = (int) hexdec(substr($hex_color, 0, 2));
		$g = (int) hexdec(substr($hex_color, 2, 2));
		$b = (int) hexdec(substr($hex_color, 4, 2));

		return [
			'r' => $r,
			'g' => $g,
			'b' => $b,
		];
	}

	/**
	 * Convert RGB color values array to hex color value
	 *
	 * @param array $rgb
	 * @return string
	 */
	public static function convertRGBToHexColor($rgb)
	{
		return '#' . dechex($rgb[0]) . dechex($rgb[1]) . dechex($rgb[2]);
	}

	/**
	 * Check if supplied value is a valid integer
	 *
	 * @param mixed $value
	 * @return bool
	 */
	public static function isInteger($value)
	{
		return preg_match('/^\-{0,1}([0-9]+)$/', $value); // 0, 1, 45, -45, etc.
	}

	/**
	 * Check if supplied value is a valid floating point number
	 *
	 * @param mixed $value
	 * @return bool
	 */
	public static function isFloat($value)
	{
		if ($value === 0 || $value === '0') {
			return true; // 0
		} else {
			return filter_var($value, FILTER_VALIDATE_FLOAT, array(
				'options' => array('min_range' => -1, 'max_range' => 1)
			)); // -1, -0.9, -0.1, 0, 0.1, 0.9, 1, etc.
		}
	}

	/**
	 * Check if supplied value is a valid hex color
	 *
	 * @param mixed $value
	 * @throws Exception
	 * @return bool
	 */
	public static function isHexColor($value)
	{
		try {
			if (is_array($value)) {
				throw new \Exception();
			}

			return preg_match('/^#[0-9A-F]{6}$/i', $value); // #FF0000 or #ff0000
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Check if supplied value is a valid RGB color values array
	 *
	 * @param mixed $value
	 * @throws Exception
	 * @return bool
	 */
	public static function isGdColorArray($value)
	{
		try {
			if (!is_array($value) || count($value) !== 3) {
				throw new \Exception();
			}

			for ($i = 0; $i < 3; $i++) {
				if (!preg_match('/^[0-9]{1,3}$/', $value[$i])) {
					throw new \Exception();
				}
				if ((int) $value < 0 || (int) $value > 255) {
					throw new \Exception();
				}
			}

			return true;
		} catch (\Exception $e) {
			return false;
		}

	}
}
