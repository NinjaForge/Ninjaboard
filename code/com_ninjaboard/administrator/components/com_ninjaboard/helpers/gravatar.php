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
     *    E-mail. This will be converted to md5($email)
     */
    public $email, $size;

    /**
     * Constructor
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config) 
    {
        parent::__construct($config);

        $this->email   = $config->email;
        $this->size    = $config->size;
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
            'email'   => '',
            'size'    => 80
        ));

        parent::_initialize($config);
    }

    /**
     *    The toString
     */
    public function __toString() {
        $url = self::GRAVATAR_URL .'?'.http_build_query(array(
            'gravatar_id' => md5(strtolower($this->email)),
            'size' => max(0, (int)$this->size)
        ));
die($url);
        return $url;    
    }
}