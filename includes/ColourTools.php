<?php

/*----------------------------------------------------------------------
	Colour Tools
	============
	Colour Tools is a class that contains various functions for
	manipulating colours in PHP. It includes basic conversion functions,
	a gradient function, an invert function and a set of functions for
	comparing two colours for brightness and colour contrast, according
	to the W3C recommendations.
	
	Usage:
		echo $this->compare('ffffff', '000000');
			
	Version 0.5
	Copyright Jack Sleight - www.reallyshiny.com
	This script is licensed under the:
		Creative Commons Attribution-ShareAlike 2.5 License
----------------------------------------------------------------------*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class ColourTools
{
	/*--------------------------------------------------
		Convert Hex code string to RGB array
	--------------------------------------------------*/

	function hexToRgb($hex)
	{
		$hex = preg_replace('/^#/', '', $hex);
		return array(
			'r' => hexdec(substr($hex, 0, 2)),
			'g' => hexdec(substr($hex, 2, 2)),
			'b' => hexdec(substr($hex, 4, 2))
		);
	}

	/*--------------------------------------------------
		Convert RGB array to Hex code string
	--------------------------------------------------*/

	function rgbToHex($rgb)
	{
		return sprintf('%02x', $rgb['r']).sprintf('%02x', $rgb['g']).sprintf('%02x', $rgb['b']);
	}

	/*--------------------------------------------------
		Create a gradient from one colour to another
	--------------------------------------------------*/

	function gradient($col1, $col2, $steps)
	{
		$col1 = $this->hexToRgb($col1);
		$col2 = $this->hexToRgb($col2);
				
		if($steps == 1) $steps ++;
		
		$step = array(
			'r' => ($col1['r'] - $col2['r']) / ($steps - 1),
			'g' => ($col1['g'] - $col2['g']) / ($steps - 1),
			'b' => ($col1['b'] - $col2['b']) / ($steps - 1)
		);
		$gradient = array();
		
		for($i = 0; $i <= $steps; $i++) {
			$colour = array(
				'r' => round($col1['r'] - ($step['r'] * $i)),
				'g' => round($col1['g'] - ($step['g'] * $i)),
				'b' => round($col1['b'] - ($step['b'] * $i))
			);
			$gradient[] = $this->rgbToHex($colour);
		}
		
		return $gradient;
	}

	/*--------------------------------------------------
		Invert a colour
	--------------------------------------------------*/

	function invert($col1)
	{
		$col1 = $this->hexToRgb($col1);
		$col2 = array(
			'r' => 256 - $col1['r'],
			'g' => 256 - $col1['g'],
			'b' => 256 - $col1['b']
		);
		return $this->rgbToHex($col2);
	}

	/*--------------------------------------------------
		Checks if two colours adhere to the W3C
		recommendations for brightness and colour 
		contrast
	--------------------------------------------------*/

	function compare($col1, $col2)
	{
		$brightnessDifference = $this->brightnessDifference($col1, $col2);
		$colourDifference = $this->colourDifference($col1, $col2);
		return (($brightnessDifference > 125) && ($colourDifference > 500)) ? true : false;
	}
	
	/*--------------------------------------------------
		Get the brightness value of a colour
	--------------------------------------------------*/

	function brightness($col)
	{
		$col = $this->hexToRgb($col);
		return (($col['r'] * 299) + ($col['g'] * 587) + ($col['b'] * 114)) / 1000;
	}
	
	/*--------------------------------------------------
		Get the brightness difference of two colours
	--------------------------------------------------*/

	function brightnessDifference($col1, $col2)
	{
		return abs($this->brightness($col1) - $this->brightness($col2));
	}
	
	/*--------------------------------------------------
		Get the colour difference of two colours
	--------------------------------------------------*/

	function colourDifference($col1, $col2)
	{
		$col1 = $this->hexToRgb($col1);
		$col2 = $this->hexToRgb($col2);
		return
			(max($col1['r'], $col2['r']) - min($col1['r'], $col2['r'])) +
			(max($col1['g'], $col2['g']) - min($col1['g'], $col2['g'])) +
			(max($col1['b'], $col2['b']) - min($col1['b'], $col2['b']));
	}
	
	function check_hex($colour)
	{

		$colour = strtolower(str_replace('#', '', $colour));
		// \d is all decimal digits (0-9)
		//$matches = array();
		$t = preg_match_all("([\da-f])", $colour, $matches);
		$length = strlen($colour);
			
		// Have to match the preg_match_all with the length to prevent incorrect results
		// else 3 matches in a 6 character code would make something like 123x98 valid
		if(($t == 3 and $length == 3) or ($t == 6 and $length == 6))
		{
			return true; 
		}
		else
		{ 
			return false; 
		}

	}
}

?>