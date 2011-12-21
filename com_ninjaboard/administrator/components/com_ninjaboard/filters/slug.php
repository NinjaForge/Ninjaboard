<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: slug.php 1942 2011-05-24 23:22:13Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard UTF8 friendly slug filter
 *
 * The code for dealing with greek chars are donated by JoomlaWorks, originating from K2.
 *
 * @link   http://getk2.org
 * @link   http://cubiq.org/the-perfect-php-clean-url-generator
 * @TODO   implement optional support for http://no.php.net/manual/en/class.transliterator.php
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardFilterSlug extends KFilterAbstract
{
	/**
	 * Validate a value
	 *
	 * @param	scalar	Value to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		return $value === $this->_sanitize($value);
	}
	
	/**
	 * Sanitize a value
	 *
	 * @param	mixed	Value to be sanitized
	 * @return	string
	 */
	protected function _sanitize($str)
	{
	    //@TODO investigate if this really helps or not
	    //setlocale(LC_ALL, 'en_US.UTF8');

        //Greek is a special case
        $search = array("α","β","γ","δ","ε","ζ","η","θ","ι","κ","λ","μ","ν","ξ","ο","π","ρ","σ","τ","υ","φ","χ","ψ","ω","Α","Β","Γ","Δ","Ε","Ζ","Η","Θ","Ι","Κ","Λ","Μ","Ξ","Ο","Π","Ρ","Σ","Τ","Υ","Φ","Χ","Ψ","Ω","ά","έ","ή","ί","ό","ύ","ώ","Ά","Έ","Ή","Ί","Ό","Ύ","Ώ","ϊ","ϋ","ς");
        $replace = array("a","b","g","d","e","z","h","th","i","k","l","m","n","x","o","p","r","s","t","y","f","ch","ps","w","A","B","G","D","E","Z","H","Th","I","K","L","M","X","O","P","R","S","T","Y","F","Ch","Ps","W","a","e","h","i","o","y","w","A","E","H","I","O","Y","W","i","y","s");
        $str = str_replace($search, $replace, $str);

        $value = iconv('UTF-8', 'ASCII//IGNORE//TRANSLIT', $str);
		$value = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $value);
		$value = strtolower(trim($value, '-'));
		$value = preg_replace("/[\/_| -]+/", '-', $value);

		return $value;
	}	
}