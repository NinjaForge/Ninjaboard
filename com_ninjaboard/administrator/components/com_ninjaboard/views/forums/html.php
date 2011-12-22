<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewForumsHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		//creating the toolbar
		$this->_createToolbar()
			->append('spacer')
			->append(KFactory::get('admin::com.ninja.toolbar.button.enable'))
			->append(KFactory::get('admin::com.ninja.toolbar.button.disable'));

		
		$this->_document->addScriptDeclaration('
			window.addEvent(\'domready\', function(){
				$$(\'select.auto-redirect\').each(function(e){
					e.addEvent(\'change\', function(){
						var name = this.getProperty(\'name\');
						if ($$(\'div.pagination\')[0].hasClass(\'ajax\')) {
							window.location = \'#\' + name + \'=\' + this.value;
							//window.location = window.location.href.replace(window.location.hash, \'#\' + name + \'=\' + this.value);
						} else {
							var location = this.getProperty(\'rel\');
							window.location = location.replace(\'{value}\', this.value);
							//window.location = this.getProperty(\'rel\').replace(name + \'=0\', name + \'=\' + this.value);
						}
					});
				});
			});
		');

		return parent::display();
	}
}