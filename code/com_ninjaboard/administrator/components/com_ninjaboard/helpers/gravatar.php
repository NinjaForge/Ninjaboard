<?php defined( 'KOOWA' ) or die( 'Restricted access' );

/**
*  Class Gravatar
*
* From Gravatar Help:
*        "A gravatar is a dynamic image resource that is requested from our server. The request
*        URL is presented here, broken into its segments."
* Source:
*    http://site.gravatar.com/site/implement
*
* Usage:
* <code>
*        $email = "youremail@yourhost.com";
*        $default = "http://www.yourhost.com/default_image.jpg";	// Optional
*        $gravatar = new Gravatar($email, $default);
*        $gravatar->size = 80;
*        $gravatar->rating = "G";
*        $gravatar->border = "FF0000";
*
*        echo $gravatar; // Or echo $gravatar->toHTML();
* </code>
*
*	Class Page: http://www.phpclasses.org/browse/package/4227.html
*
* @author Lucas Araújo <araujo.lucas@gmail.com>
* @version 1.0
* @package Gravatar
*/
class ComNinjaboardHelperGravatar extends KObject
{
    /**
     *    Gravatar's url
     */
    const GRAVATAR_URL = "http://www.gravatar.com/avatar.php";

    /**
     *    Ratings available
     */
    private $GRAVATAR_RATING = array("G", "PG", "R", "X");
    
    /**
     *    Query string. key/value
     */
    protected $properties = array(
        "gravatar_id"	=> NULL,
        "default"		=> 404,
        "size"			=> 80,        // The default value
        "rating"		=> NULL,
        "border"		=> NULL,
    );

    /**
     *    E-mail. This will be converted to md5($email)
     */
    protected $email = "";

    /**
     *    
     */
    public function __construct($email=NULL, $default=NULL) {
        $this->setEmail($email);
        $this->setDefault($default);
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'table' => 'ninja:database.table.'.$this->getIdentifier()->name,
        ));

        parent::_initialize($config);
    }

    /**
     *    
     */
    public function setEmail($email) {
        if (KService::get('koowa:filter.email')->validate($email)) {
            $this->email = $email;
            $this->properties['gravatar_id'] = md5(strtolower($this->email));
            return true;
        }
        return false;
    }

    /**
     *    
     */
    public function setDefault($default) {
        $this->properties['default'] = $default;
    }

    /**
     *    
     */
    public function setRating($rating) {
        if (in_array($rating, $this->GRAVATAR_RATING)) {
            $this->properties['rating'] = $rating;
            return true;
        }
        return false;
    }

    /**
     *    
     */
    public function setSize($size) {
        $size = (int) $size;
        if ($size <= 0)
            $size = NULL;        // Use the default size
        $this->properties['size'] = $size;
    }

    /**
     *    Object property overloading
     */
    public function __get($var) { return @$this->properties[$var]; }

    /**
     *    Object property overloading
     */
    public function __set($var, $value) {
        switch($var) {
            case "email":    return $this->setEmail($value);
            case "rating":    return $this->setRating($value);
            case "default":    return $this->setDefault($value);
            case "size":    return $this->setSize($value);
            // Cannot set gravatar_id
            case "gravatar_id": return;
        }
        return @$this->properties[$var] = $value;
    }

    /**
     *    Object property overloading
     */
    public function __isset($var) { return isset($this->properties[$var]); }

    /**
     *    Object property overloading
     */
    public function __unset($var) { return @$this->properties[$var] == NULL; }

    /**
     *    The toString
     */
    public function __toString() {
        $url = self::GRAVATAR_URL ."?";
        $first = true;
        foreach($this->properties as $key => $value) {
            if (isset($value)) {
                if (!$first)
                    $url .= "&";
                $url .= $key."=".urlencode($value);
                $first = false;
            }
        }
        die($url);
        return $url;    
    }
}