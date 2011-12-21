<?php  defined('_JEXEC') or die('Restricted access'); 

	$messageQueue	=& NinjaboardMessageQueue::getInstance();
	$messages		=  $messageQueue->getMessages(); 

	for ($i = 0, $k = count($messages); $i < $k; $i++) {

		$message =& $messages[$i];

		echo <<<EOF
	<div id="nbMessage">$message->message</div>
EOF;
	}
?>
