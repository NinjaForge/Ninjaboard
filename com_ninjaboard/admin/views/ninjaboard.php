<?php
/**
 * @version $Id: ninjaboard.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Panel View
 *
 * @package Ninjaboard
 */
class ViewNinjaboard {

	/**
	 * show ninjaboard panel
	 */
	function showControlPanel( &$rows ) {
		
		// initialize variables	
		$document =& JFactory::getDocument();
		$document->addStyleSheet(NB_ADMINCSS_LIVE.DL.'icon.css');
			
		$pane   =& JPane::getInstance('sliders');
		?>
		<table class="adminform">			
			<tr>
				<td width="55%" valign="top">
					<div id="cpanel">
					<?php
					$link = 'index.php?option=com_ninjaboard&task=ninjaboard_forum_view';
					ViewNinjaboard::createIconButton($link, 'icon-48-board.png', JText::_('NB_BOARD'));
				
					$link = 'index.php?option=com_ninjaboard&task=ninjaboard_config_view';
					ViewNinjaboard::createIconButton($link, 'icon-48-config.png', JText::_('NB_CONFIG'));
						
					$link = 'index.php?option=com_ninjaboard&task=ninjaboard_timezone_view';
					ViewNinjaboard::createIconButton($link, 'icon-48-timezone.png', JText::_('NB_TIMEZONES'));
					
					$link = 'index.php?option=com_ninjaboard&task=ninjaboard_timeformat_view';
					ViewNinjaboard::createIconButton($link, 'icon-48-timeformat.png', JText::_('NB_TIMEFORMATS'));
													
					$link = 'index.php?option=com_ninjaboard&task=ninjaboard_design_view';
					ViewNinjaboard::createIconButton($link, 'icon-48-design.png', JText::_('NB_DESIGNS'));
																																													
					$link = 'index.php?option=com_ninjaboard&task=ninjaboard_user_view';
					ViewNinjaboard::createIconButton($link, 'icon-48-ninjaboarduser.png', JText::_('NB_USERS') );
								
					$link = 'index.php?option=com_ninjaboard&task=ninjaboard_group_view';
					ViewNinjaboard::createIconButton($link, 'icon-48-group.png', JText::_('NB_GROUPS') );
	
					$link = 'index.php?option=com_ninjaboard&task=ninjaboard_rank_view';
					ViewNinjaboard::createIconButton($link, 'icon-48-rank.png', JText::_('NB_RANKS') );

					$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefield_view';
					ViewNinjaboard::createIconButton($link, 'icon-48-profilefield.png', JText::_('NB_PROFILEFIELDS') );

					$link = 'index.php?option=com_ninjaboard&task=ninjaboard_terms_view';
					ViewNinjaboard::createIconButton($link, 'icon-48-terms.png', JText::_('NB_TERMS') );
					
					$link = 'index.php?option=com_ninjaboard&task=ninjaboard_usersync_view';
					ViewNinjaboard::createIconButton($link, 'icon-48-ninjaboardtools.png', JText::_('NB_TOOLS') );
																				
					$link = 'index.php?option=com_ninjaboard&task=ninjaboard_credits_view';
					ViewNinjaboard::createIconButton($link, 'icon-48-credits.png', JText::_('NB_CREDITS') );
					
					?>
				</div>
				</td>
				<td width="45%" valign="top">
				<?php
					$pane->startPane("content-pane");
					$pane->startPanel(JText::_('NB_QUICKOVERVIEW'), "detail-page" );
				?>
					<table class="adminlist" cellspacing="1">
					<thead>
						<tr>
							<th colspan="2" nowrap="nowrap" width="100%">
								<?php echo JText::_('NB_BOARDSTATISTIC'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
					<?php
					$k = 0;
					for ( $i=0, $n=count( $rows ); $i < $n; $i++ ) {
						$row 	=& $rows[$i];
						?>
						<tr>
							<td width="50%">
								<?php echo $row->description; ?>
							</td>																																																							
							<td width="50%">
								<?php echo $row->value; ?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}
					?>																
					</tbody>					
					</table>
				<?php	
					$pane->endPanel();
					$pane->endPane();
				?>				
				</td>			
			</tr>
		</table>
		<?php
	}

	/**
	 * create icon button
	 */  	
	function createIconButton($link, $image, $text) {
		$config =& JFactory::getConfig();
		
		$image = NB_ADMINIMAGES_LIVE.DL.'header'.DL.$image;
		?>
		<div style="float:left;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
					<img src="<?php echo $image; ?>" alt="<?php echo $text; ?>" align="top" border="0" />
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}
		
}
?>