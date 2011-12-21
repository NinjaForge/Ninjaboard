<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 2493 2011-11-10 22:25:40Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewHtml extends NinjaViewDefault
{
	public function display()
	{
		// Display the toolbar
		/*$toolbar = $this->_createToolbar();
		$path = $this->getService($this->getModel())->getIdentifier()->path;

		if(KInflector::isPlural($this->getService($this->getModel())->getIdentifier()->name) && $this->getName() != 'dashboard')
		{
			$this->_mixinMenubar();
		}

		if ($this->getName() == 'dashboard')
		{
			$toolbar->reset();
			$this->_document->setBuffer(false, 'modules', 'submenu');
		}
		else
		{
			$toolbar->append('spacer');
		}

		$toolbar->append($this->getService('ninja:toolbar.button.about'));
		*/


		//@TODO finish this
		//$this->lang();

		return	'<div class="nf template-'.JFactory::getApplication()->getTemplate().' -koowa-box-flex -koowa-box-scroll">'.
				parent::display().
				'</div>';

		//Add tooltips?
		//if(KInflector::isPlural($this->getName()) && ($this->getService($this->getModel())->getTotal() > 1)) KTemplate::loadHelper('ninja:helper.behavior.tooltip', 'th.hasHint', array('showOnce' => true, 'showOnLoad' => true, 'fixed' => true));
	}
	
	/**
	 * MooTools.lang localization for Form.Validator.js and Date.js
	 * 
	 * Will likely be moved to Napi once stable
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return void
	 */
	protected function lang()
	{
		$lang = JFactory::getLanguage()->getTag();
		
		$translate = create_function('$text', 'return ucfirst(JText::_($text));');
		$months    = json_encode(array_map($translate, array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')));
		$days      = json_encode(array_map($translate, array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')));
		
		$dateParts = explode('-', str_replace(array('%Y','%M','%D'), array('year', 'month', 'date'), JText::_('%Y-%M-%D')));
		$dateOrder = json_encode($dateParts);
		$shortDate = JText::_('DATE_FORMAT_LC4');
		
		$translate = create_function('$text', 'return JText::_($text);');
		$parts = array_map($translate, array(
			'lessThanMinuteAgo' => 'less than a minute ago',
			'minuteAgo' => 'about a minute ago',
			'minutesAgo' => '{delta} minutes ago',
			'hourAgo' => 'about an hour ago',
			'hoursAgo' => 'about {delta} hours ago',
			'dayAgo' => '1 day ago',
			'daysAgo' => '{delta} days ago',
			'weekAgo' => '1 week ago',
			'weeksAgo' => '{delta} weeks ago',
			'monthAgo' => '1 month ago',
			'monthsAgo' => '{delta} months ago',
			'yearAgo' => '1 year ago',
			'yearsAgo' => '{delta} years ago',
			'lessThanMinuteUntil' => 'less than a minute from now',
			'minuteUntil' => 'about a minute from now',
			'minutesUntil' => '{delta} minutes from now',
			'hourUntil' => 'about an hour from now',
			'hoursUntil' => 'about {delta} hours from now',
			'dayUntil' => '1 day from now',
			'daysUntil' => '{delta} days from now',
			'weekUntil' => '1 week from now',
			'weeksUntil' => '{delta} weeks from now',
			'monthUntil' => '1 month from now',
			'monthsUntil' => '{delta} months from now',
			'yearUntil' => '1 year from now',
			'yearsUntil' => '{delta} years from now'
		));
		echo KHelperArray::toString($parts, ':', ',"');
		die('<pre>'.var_export($parts, true).'</pre>');
		
		$this->_document->addScriptDeclaration("
			MooTools.lang.set('$lang', 'Date', {
			
				months: $months,
				days: $days,
				//culture's date order: MM/DD/YYYY
				dateOrder: $dateOrder,
				shortDate: '$shortDate',			
				$parts
			});
			
			MooTools.lang.set('$lang', 'Form.Validator', {
			
				required:'This field is required.',
				minLength:'Please enter at least {minLength} characters (you entered {length} characters).',
				maxLength:'Please enter no more than {maxLength} characters (you entered {length} characters).',
				integer:'Please enter an integer in this field. Numbers with decimals (e.g. 1.25) are not permitted.',
				numeric:'Please enter only numeric values in this field (i.e. \"1\" or \"1.1\" or \"-1\" or \"-1.1\").',
				digits:'Please use numbers and punctuation only in this field (for example, a phone number with dashes or dots is permitted).',
				alpha:'Please use letters only (a-z) with in this field. No spaces or other characters are allowed.',
				alphanum:'Please use only letters (a-z) or numbers (0-9) only in this field. No spaces or other characters are allowed.',
				dateSuchAs:'Please enter a valid date such as {date}',
				dateInFormatMDY:'Please enter a valid date such as MM/DD/YYYY (i.e. \"12/31/1999\")',
				email:'Please enter a valid email address. For example \"fred@domain.com\".',
				url:'Please enter a valid URL such as http://www.google.com.',
				currencyDollar:'Please enter a valid $ amount. For example $100.00 .',
				oneRequired:'Please enter something for at least one of these inputs.',
				errorPrefix: 'Error: ',
				warningPrefix: 'Warning: ',
			
				//Form.Validator.Extras
			
				noSpace: 'There can be no spaces in this input.',
				reqChkByNode: 'No items are selected.',
				requiredChk: 'This field is required.',
				reqChkByName: 'Please select a {label}.',
				match: 'This field needs to match the {matchName} field',
				startDate: 'the start date',
				endDate: 'the end date',
				currendDate: 'the current date',
				afterDate: 'The date should be the same or after {label}.',
				beforeDate: 'The date should be the same or before {label}.',
				startMonth: 'Please select a start month',
				sameMonth: 'These two dates must be in the same month - you must change one or the other.',
				creditcard: 'The credit card number entered is invalid. Please check the number and try again. {length} digits entered.'
			
			});
			MooTools.lang.setLanguage('$lang');
		");
	}
}