<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: slug.php 2221 2011-07-15 21:40:15Z stian $
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
        $value = str_replace($search, $replace, $str);
        
        //Now do a general transliteration
        $table = array(
            'a' => 'àáâãäåăąÀÁÂÃÄÅĂĄ',
            'c' => 'ćčçĆČÇ',
            'd' => 'ďđĎÐ',
            'e' => 'èéêëěęÈÉÊËĚĘ',
            'g' => 'ğĞ',
            'i' => 'ìíîïÌÍÎÏ',
            'l' => 'ĺľłĹĽŁ',
            'n' => 'ñňńÑŇŃ',
            'o' => 'òóôõöøőÒÓÔÕÖØ',
            'r' => 'řŕŘŔ',
            's' => 'ššşŠŞŚ',
            't' => 'ťţŤŢ',
            'ue' => 'üÜ',
            'u' => 'ùúûůµÙÚÛŮ',
            'y' => 'ÿýŸÝ',
            'z' => 'žźżŽŹŻ',
            'th' => 'þÞ',
            'dh' => 'ðÐ',
            'ss' => 'ß',
            'oe' => 'œŒ',
            'ae' => 'æÆ'
        );
        foreach($table as $safe => $symbols)
        {
            $value = preg_replace('/['.$symbols.']/ui', $safe, $value);
        }
        
        /**
         * Use iconv as it supports a very wide range of symbols, but we can't run it first as it'll strip out ignored
         * symbols and if you remove the IGNORE option it'll throw errors and give you a blank result
         */
        $value = iconv('UTF-8', 'ASCII//IGNORE//TRANSLIT', $value);
        
		$value = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $value);
		$value = preg_replace("/[\/_| -]+/", '-', $value);
		$value = strtolower(trim($value, '-'));

		return $value;
	}	
}