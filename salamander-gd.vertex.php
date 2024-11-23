<?php

/***************************************************************************************************
*                                                            VERTEX ROUTINES
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
* Salamander-GD Core/Libraries (c) 2024 Neil Withnall (gammalogic).
* 
* Please see LICENSE for full copyright notice and licensing terms.
***************************************************************************************************/

namespace SalamanderGD;

class Vertex
{
	public $x;
	public $y;

	/**
	 * Constructor for the vertex
	 *
	 * @param float $x
	 * @param float $y
	 * @return void
	 */
	public function __construct($x, $y)
	{
		$this->x = $x;
		$this->y = $y;
	}
}
