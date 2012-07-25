<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category    Ninjaboard
 * @copyright   Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://ninjaforge.com
 */

/**
 * Sluggable behavior untill we get around to switching alias to slug
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @package     Ninjaboard
 * @subpackage 	Behaviors
 */
class ComNinjaboardDatabaseBehaviorSluggable extends KDatabaseBehaviorSluggable
{
	/**
     * Get the methods that are available for mixin based
     *
     * This function conditionaly mixes the behavior. Only if the mixer
     * has a 'alias' property the behavior will be mixed in.
     *
     * @param object The mixer requesting the mixable methods.
     * @return array An array of methods
     */
    public function getMixableMethods(KObject $mixer = null)
    {
        $methods = array();
        
        if(isset($mixer->alias)) {
            $methods = parent::getMixableMethods($mixer);
        }

        return $methods;
    }

    /**
     * Create a sluggable filter
     *
     * @return void
     */
    protected function _createFilter()
    {
        $config = array();
        $config['separator'] = $this->_separator;

        if(!isset($this->_length)) {
            $config['length'] = $this->getTable()->getColumn('alias')->length;
        } else {
            $config['length'] = $this->_length;
        }
        
        //Create the filter
        $filter = $this->getService('koowa:filter.slug', $config);
        return $filter;
    }

    /**
     * Create the slug
     *
     * @return void
     */
    protected function _createSlug()
    {
        //Create the slug filter
        $filter = $this->_createFilter();
        
        if(empty($this->alias))
        {
            $slugs = array();
            foreach($this->_columns as $column) {
                $slugs[] = $filter->sanitize($this->$column);
            }

            $this->alias = implode($this->_separator, array_filter($slugs));
            
            //Canonicalize the slug
            $this->_canonicalizeSlug();
        }
        else
        {
            if(in_array('alias', $this->getModified())) 
            {
                $this->alias = $filter->sanitize($this->alias);
                
                //Canonicalize the slug
                $this->_canonicalizeSlug();
            }
        }
    }
    
    /**
     * Make sure the slug is unique
     * 
     * This function checks if the slug already exists and if so appends
     * a number to the slug to make it unique. The slug will get the form
     * of slug-x.
     *
     * @return void
     */
    protected function _canonicalizeSlug()
    {
        $table = $this->getTable();
        
        //If unique is not set, use the column metadata
        if(is_null($this->_unique)) { 
            $this->_unique = $table->getColumn('alias', true)->unique;
        }
    
        //If the slug needs to be unique and it already exist make it unqiue
        if($this->_unique && $table->count(array('alias' => $this->alias))) 
        {   
            $db    = $table->getDatabase();
            $query = $db->getQuery()
                        ->select('alias')
                        ->where('alias', 'LIKE', $this->alias.'-%');          
            
            $slugs = $table->select($query, KDatabase::FETCH_FIELD_LIST);
            
            $i = 1;
            while(in_array($this->alias.'-'.$i, $slugs)) {
                $i++;
            }
            
            $this->alias = $this->alias.'-'.$i;
        }
    }
}