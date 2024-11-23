<?php

/***************************************************************************************************
*                                  CATMULL-ROM SPLINE DROP-IN LIBRARY FOR GD
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
* OVERVIEW
* 
* A drop-in library to draw Catmull-Rom splines using GD drawing primitives.
*
* Please see README.md for usage notes, issues, and PHP version compability.
*
* COPYRIGHT NOTICE
* 
* salamander-GD Core/Libraries (c) 2024 Neil Withnall (gammalogic).
* 
* Please see LICENSE for full copyright notice and licensing terms.
***************************************************************************************************/

namespace SalamanderGD;

class CatmullRomSpline
{
	private $CatmullRomSplineAlpha;
	private $CatmullRomSplineTension;
	private $CatmullRomSplineTensionRemainder;
	private $Points = [];
	private $ClosedSpline = true;
	private $BackgroundColor = null;
	private $StrokeColor = null;
	private $StrokeWidth;
	private $ControlPointColor = null;
	private $ControlPointRadius;
	private $InterpolationPointColor = null;
	private $InterpolationPointRadius;
	private $HasBackgroundColor = false;
	private $HasStrokeColor = false;
	private $ShowControlPoints = false;
	private $ShowInterpolationPoints = false;
	private $IsPHP8 = false;

	/**
	 * Constructor for the Catmull-Rom spline routines
	 *
	 * @param bool $closed_spline
	 * @return void
	 */
	public function __construct($closed_spline=true)
	{
		// Initialize defaults
		$this->CatmullRomSplineAlpha = (float) 1;
		$this->CatmullRomSplineTension = (float) 0;
		$this->CatmullRomSplineTensionRemainder = ((float) 1 - $this->CatmullRomSplineTension);
		$this->ClosedSpline = $closed_spline;
		$this->StrokeWidth = (int) 1;
		$this->ControlPointRadius = (int) 1;
		$this->InterpolationPointRadius = (int) 1;

		if (version_compare(phpversion(), '8', '>=')) {
			$this->IsPHP8 = true;
		}
	}

	/**
	 * Add a new control point to the spline
	 *
	 * @param int $x
	 * @param int $y
	 * @return void
	 */
	public function addPoint($x, $y)
	{
		$this->Points[] = new Vertex($x, $y);
	}

	/**
	 * Set the background color of the spline
	 *
	 * @param mixed $color
	 * @throws Exception
	 * @return void
	 */
	public function setBackgroundColor($color)
	{
		try {
			if (Helper::isHexColor($color)) {
				$this->BackgroundColor = $color;
			} elseif (Helper::isGdColorArray($color)) {
				$this->BackgroundColor = Helper::convertRGBToHexColor($color);
			} else {
				throw new \Exception('Sorry, an error occurred (invalid background color)');
			}

			$this->HasBackgroundColor = true;
		} catch (\Exception $e) {
			die($e->getMessage() . ' at line ' . $e->getLine());
		}
	}

	/**
	 * Set the stroke color of the spline
	 *
	 * @param mixed $color
	 * @throws Exception
	 * @return void
	 */
	public function setStrokeColor($color)
	{
		try {
			if (Helper::isHexColor($color)) {
				$this->StrokeColor = $color;
			} elseif (Helper::isGdColorArray($color)) {
				$this->StrokeColor = Helper::convertRGBToHexColor($color);
			} else {
				throw new \Exception('Sorry, an error occurred (invalid stroke color)');
			}

			$this->HasStrokeColor = true;
		} catch (\Exception $e) {
			die($e->getMessage() . ' at line ' . $e->getLine());
		}
	}

	/**
	 * Set the stroke width of the spline
	 *
	 * @param int $width
	 * @throws Exception
	 * @return void
	 */
	public function setStrokeWidth($width)
	{
		try {
			if (Helper::isInteger($width) && (int) $width > 0) {
				$this->StrokeWidth = (int) $width;
			} else {
				throw new \Exception();
			}
		} catch (\Exception $e) {
			$this->StrokeWidth = (int) 1;
		}
	}

	/**
	 * Set the color and radius of the control points for display purposes
	 *
	 * @param mixed $color
	 * @param int $radius
	 * @throws Exception
	 * @return void
	 */
	public function showControlPoints($color, $radius)
	{
		try {
			if (Helper::isHexColor($color)) {
				$this->ControlPointColor = $color;
			} elseif (Helper::isGdColorArray($color)) {
				$this->ControlPointColor = Helper::convertRGBToHexColor($color);
			} else {
				throw new \Exception('Sorry, an error occurred (invalid control points color)');
			}

			$this->ShowControlPoints = true;
		} catch (\Exception $e) {
			die($e->getMessage());
		}

		try {
			if (Helper::isInteger($radius) && (int) $radius > 0) {
				$this->ControlPointRadius = (int) $radius;
			} else {
				throw new \Exception();
			}
		} catch (\Exception $e) {
			$this->ControlPointRadius = (int) 1;
		}
	}

	/**
	 * Set the color and radius of the calculated interpolation points for display purposes
	 *
	 * @param mixed $color
	 * @param int $radius
	 * @throws Exception
	 * @return void
	 */
	public function showInterpolationPoints($color, $radius)
	{
		try {
			if (Helper::isHexColor($color)) {
				$this->InterpolationPointColor = $color;
			} elseif (Helper::isGdColorArray($color)) {
				$this->InterpolationPointColor = Helper::convertRGBToHexColor($color);
			} else {
				throw new \Exception('Sorry, an error occurred (invalid interpolation points color)');
			}

			$this->ShowInterpolationPoints = true;
		} catch (\Exception $e) {
			die($e->getMessage());
		}

		try {
			if (Helper::isInteger($radius) && (int) $radius > 0) {
				$this->InterpolationPointRadius = (int) $radius;
			} else {
				throw new \Exception();
			}
		} catch (\Exception $e) {
			$this->InterpolationPointRadius = (int) 1;
		}
	}

	/**
	 * Set the spline's alpha (α) value; this controls how closely the curve tracks the straight
	 * line shape/polygon drawn by the control points
	 *
	 * 0   = uniform spline (straighter, but can lead to self-intersections or "knots")
	 * 0.5 = centripetal spline
	 * 1   = chordal spline (smoother, but can lead to exaggerated curves that do not track)
	 *
	 * @param float $alpha
	 * @throws Exception
	 * @return void
	 */
	public function setSplineAlpha($alpha)
	{
		try {
			if (Helper::isFloat($alpha)) {
				$this->CatmullRomSplineAlpha = (float) $alpha;
			} else {
				throw new \Exception();
			}
		} catch (\Exception $e) {
			$this->CatmullRomSplineAlpha = (float) 1;
		}
	}

	/**
	 * Set the spline's tension value
	 *
	 * 0 = minimum tension (curves drawn to fullest extent)
	 * 1 = maximum tension (curves drawn as straight lines)
	 *
	 * @param float $tension
	 * @throws Exception
	 * @return void
	 */
	public function setSplineTension($tension)
	{
		try {
			if (Helper::isFloat($tension)) {
				$this->CatmullRomSplineTension = (float) $tension;
			} else {
				throw new \Exception();
			}
		} catch (\Exception $e) {
			$this->CatmullRomSplineTension = (float) 0;
		}

		$this->CatmullRomSplineTensionRemainder = ((float) 1 - $this->CatmullRomSplineTension);
	}

	/**
	 * Rotate the control points by X degrees around a user-specified origin
	 *
	 * @param int $x
	 * @param int $y
	 * @param float $angle
	 * @return void
	 */
	public function rotate($x, $y, $angle)
	{
		$origin = new Vertex($x, $y);

		$radians = deg2rad($angle);
		$sin = sin($radians);
		$cos = cos($radians);

		$points = $this->Points;

		$this->Points = [];

		foreach ($points as $point) {
			$offset_x = $point->x - $origin->x;
			$offset_y = $point->y - $origin->y;

			$rx = $origin->x + $cos * $offset_x - $sin * $offset_y;
			$ry = $origin->y + $sin * $offset_x + $cos * $offset_y;

			$this->addPoint($rx, $ry);
		}
	}

	/**
	 * Draw the spline
	 *
	 * @param gd $image
	 * @throws Exception
	 * @return void
	 */
	public function draw($image)
	{
		try {
			if ($this->IsPHP8) {
				if (!is_a($image, 'GdImage')) {
					throw new \Exception('Sorry, an error occurred (invalid GD object)');
				}
			} else {
				if (get_resource_type($image) !== 'gd') {
					throw new \Exception('Sorry, an error occurred (invalid GD object)');
				}
			}
			if (count($this->Points) < 3) {
				throw new \Exception('Sorry, an error occurred (a minimum of 3 points must be specified for each spline)');
			}
		} catch (\Exception $e) {
			die($e->getMessage());
		}

		$points = $this->calculateInterpolationPoints();

		if ($this->ClosedSpline) {
			// Connect the start and end points of the spline
			$points[] = $points[0];
			$points[] = $points[1];
		}

		if ($this->HasBackgroundColor) {
			$rgb = Helper::convertHexColorToRGB($this->BackgroundColor);
			$background_color = imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']);
		}
		if ($this->HasStrokeColor) {
			$rgb = Helper::convertHexColorToRGB($this->StrokeColor);
			$stroke_color = imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']);
		}

		array_splice($points, 0, 0, $this->Points[0]->y);
		array_splice($points, 0, 0, $this->Points[0]->x);

		if ($this->HasBackgroundColor) {
			if ($this->IsPHP8) {
				imagefilledpolygon(
					$image,
					$points,
					$background_color
				);
			} else {
				imagefilledpolygon(
					$image,
					$points,
					(count($points) / 2),
					$background_color
				);
			}
		}

		if ($this->HasStrokeColor && $this->StrokeWidth > 1) {
			$spline = $points;
			$inner_spline = [];
			$outer_spline = [];
			$spline_draw_segments = [];

			$spline_total_points = count($spline);
			$offset = ($this->StrokeWidth / 2);

			for ($i = 0; $i < $spline_total_points; $i += 2) {
				if (array_key_exists($i + 2, $spline) && array_key_exists($i + 3, $spline)) {
					$dx = $spline[$i + 2] - $spline[$i];
					$dy = $spline[$i + 3] - $spline[$i + 1];
					$dxy_sqrt = pow($dx * $dx + $dy * $dy, 0.5);

					// Inner offset
					$scale = $offset / $dxy_sqrt;
					$ox = -$dy * $scale;
					$oy = $dx * $scale;

					$inner_spline[] = $ox + $spline[$i + 0];
					$inner_spline[] = $oy + $spline[$i + 1];
					$inner_spline[] = $ox + $spline[$i + 2];
					$inner_spline[] = $oy + $spline[$i + 3];

					// Outer offset
					$scale = -$offset / $dxy_sqrt;
					$ox = -$dy * $scale;
					$oy = $dx * $scale;

					$outer_spline[] = $ox + $spline[$i + 0];
					$outer_spline[] = $oy + $spline[$i + 1];
					$outer_spline[] = $ox + $spline[$i + 2];
					$outer_spline[] = $oy + $spline[$i + 3];
				}
			}

			$inner_spline_total_points = count($inner_spline);

			for ($i = 0; $i < $inner_spline_total_points; $i += 2) {
				if (array_key_exists($i + 2, $inner_spline) && array_key_exists($i + 3, $inner_spline)) {
					$spline_draw_segments[] = [
						$inner_spline[$i + 0],
						$inner_spline[$i + 1],
						$outer_spline[$i + 0],
						$outer_spline[$i + 1],
						$outer_spline[$i + 2],
						$outer_spline[$i + 3],
						$inner_spline[$i + 2],
						$inner_spline[$i + 3],
					];
				}
			}

			$spline_draw_segments_total = count($spline_draw_segments);

			// Connect the start and end draw segments together with a new draw segment
			if ($this->ClosedSpline) {
				$spline_draw_segment_connector = [
					$spline_draw_segments[0][0],
					$spline_draw_segments[0][1],
					$spline_draw_segments[0][2],
					$spline_draw_segments[0][3],
					$spline_draw_segments[$spline_draw_segments_total - 1][4],
					$spline_draw_segments[$spline_draw_segments_total - 1][5],
					$spline_draw_segments[$spline_draw_segments_total - 1][6],
					$spline_draw_segments[$spline_draw_segments_total - 1][7],
				];
				$spline_draw_segments[] = $spline_draw_segment_connector;
			}

			$spline_draw_segments_total = count($spline_draw_segments);

			for ($i = 0; $i < $spline_draw_segments_total; $i += 1) {
				if ($this->IsPHP8) {
					imagefilledpolygon(
						$image,
						$spline_draw_segments[$i],
						$stroke_color
					);
				} else {
					imagefilledpolygon(
						$image,
						$spline_draw_segments[$i],
						(count($spline_draw_segments[$i]) / 2),
						$stroke_color
					);
				}
			}
		} elseif ($this->HasStrokeColor && $this->StrokeWidth === 1) {
			imagesetthickness($image, $this->StrokeWidth);

			for ($i = 0; $i < count($points); $i += 2) {
				if (array_key_exists($i + 2, $points) && array_key_exists($i + 3, $points)) {
					imageline(
						$image,
						(int) $points[$i + 0],
						(int) $points[$i + 1],
						(int) $points[$i + 2],
						(int) $points[$i + 3],
						$stroke_color
					);
				}
			}
		}

		if ($this->ShowControlPoints) {
			$rgb = Helper::convertHexColorToRGB($this->ControlPointColor);
			$control_point_color = imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']);

			$points_total = count($this->Points);

			for ($i = 0; $i < $points_total; $i++) {
				imagefilledellipse(
					$image,
					(int) $this->Points[$i]->x,
					(int) $this->Points[$i]->y,
					$this->ControlPointRadius,
					$this->ControlPointRadius,
					$control_point_color
				);
			}
		}

		if ($this->ShowInterpolationPoints) {
			$rgb = Helper::convertHexColorToRGB($this->InterpolationPointColor);
			$interpolation_point_color = imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']);

			$points_total = count($points);

			for ($i = 0; $i < $points_total; $i += 2) {
				imagefilledellipse(
					$image,
					(int) $points[$i],
					(int) $points[$i + 1],
					$this->InterpolationPointRadius,
					$this->InterpolationPointRadius,
					$interpolation_point_color
				);
			}
		}
	}

	/**
	 * Calculate the position of the interpolation points needed to draw the spline
	 *
	 * @throws Exception
	 * @return array $points
	 */
	private function calculateInterpolationPoints()
	{
		$ipv = $this->Points;

		for ($i = count($ipv) - 1; $i > 0; $i--) {
			if (Helper::calculateVerticesDistance($ipv[$i], $ipv[$i - 1]) < 1) {
				array_splice($ipv, $i, 1);
			}
		}

		if ($this->ClosedSpline && Helper::calculateVerticesDistance($ipv[0], $ipv[count($ipv) - 1]) < 1) {
			array_splice($ipv, count($ipv) - 1, 1);
		}

		$ipv_total = count($ipv);

		try {
			if ($ipv_total < 2) {
				throw new \Exception('Sorry, an error occurred (distance between control points is too small)');
			}
		} catch (\Exception $e) {
			die($e->getMessage());
		}

		if ($this->ClosedSpline) {
			$ipv[] = $ipv[0];
			$ipv[] = $ipv[1];
			array_splice($ipv, 0, 0, [$ipv[$ipv_total - 1]]);
		} else {
			$first = [new Vertex(
				2 * $ipv[0]->x - $ipv[1]->x,
				2 * $ipv[0]->y - $ipv[1]->y
			)];
			$last = new Vertex(
				2 * $ipv[$ipv_total - 1]->x - $ipv[$ipv_total - 2]->x,
				2 * $ipv[$ipv_total - 1]->y - $ipv[$ipv_total - 2]->y
			);

			array_splice($ipv, 0, 0, $first);
			$ipv[] = $last;
		}

		$points = [];

		for ($i = 1; $i < count($ipv) - 2; $i++) {
			$p0 = $ipv[$i - 1];
			$p1 = $ipv[$i];
			$p2 = $ipv[$i + 1];
			$p3 = $ipv[$i + 2];

			$p0_p1_distance = Helper::calculateVerticesDistance($p0, $p1);
			$p1_p2_distance = Helper::calculateVerticesDistance($p1, $p2);
			$p2_p3_distance = Helper::calculateVerticesDistance($p2, $p3);

			$t01 = pow($p0_p1_distance, $this->CatmullRomSplineAlpha);
			$t12 = pow($p1_p2_distance, $this->CatmullRomSplineAlpha);
			$t23 = pow($p2_p3_distance, $this->CatmullRomSplineAlpha);

			$t0 = (float) 0;
			$t1 = $t0 + $t01;
			$t2 = $t1 + $t12;
			$t3 = $t2 + $t23;

			$t0_m_t1 = $t0 - $t1;
			$t0_m_t2 = $t0 - $t2;
			$t1_m_t2 = $t1 - $t2;
			$t1_m_t3 = $t1 - $t3;
			$t2_m_t1 = $t2 - $t1;
			$t2_m_t3 = $t2 - $t3;
			$tension_remainder_t2_m_t1 = $this->CatmullRomSplineTensionRemainder * $t2_m_t1;

			$m1x = $tension_remainder_t2_m_t1 * (
				($p0->x - $p1->x) / $t0_m_t1 - ($p0->x - $p2->x) / $t0_m_t2 + ($p1->x - $p2->x) / $t1_m_t2
			);
			$m1y = $tension_remainder_t2_m_t1 * (
				($p0->y - $p1->y) / $t0_m_t1 - ($p0->y - $p2->y) / $t0_m_t2 + ($p1->y - $p2->y) / $t1_m_t2
			);
			$m2x = $tension_remainder_t2_m_t1 * (
				($p1->x - $p2->x) / $t1_m_t2 - ($p1->x - $p3->x) / $t1_m_t3 + ($p2->x - $p3->x) / $t2_m_t3
			);
			$m2y = $tension_remainder_t2_m_t1 * (
				($p1->y - $p2->y) / $t1_m_t2 - ($p1->y - $p3->y) / $t1_m_t3 + ($p2->y - $p3->y) / $t2_m_t3
			);

			$ax = 2 * $p1->x - 2 * $p2->x + $m1x + $m2x;
			$ay = 2 * $p1->y - 2 * $p2->y + $m1y + $m2y;
			$bx = -3 * $p1->x + 3 * $p2->x - 2 * $m1x - $m2x;
			$by = -3 * $p1->y + 3 * $p2->y - 2 * $m1y - $m2y;
			$cx = $m1x;
			$cy = $m1y;
			$dx = $p1->x;
			$dy = $p1->y;

			$amount = max(10, ceil($p0_p1_distance / 10));

			for ($j = 1; $j <= $amount; $j++) {
				$t = ($j / $amount);

				$t_sqrd = $t * $t;
				$t_cube = $t_sqrd * $t;

				$px = $ax * $t_cube + $bx * $t_sqrd + $cx * $t + $dx;
				$py = $ay * $t_cube + $by * $t_sqrd + $cy * $t + $dy;

				$points[] = $px;
				$points[] = $py;
			}
		}

		return $points;
	}
}
