<?php
/**
 * @version $Id: forum.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Forum View
 *
 * @package Ninjaboard
 */
class ViewForum {

	/**
	 * show forums
	 */
	function showForums(&$rows, $pageNav, &$lists) {
		global $mainframe;
		
		// initialize variables
		$user		=& JFactory::getUser();
		$ninjaboardAuth 	=& NinjaboardAuth::getInstance();
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet(NB_ADMINCSS_LIVE.DL.'icon.css');

		$task	= JRequest::getVar('task');
		
	//include our helper file to create the footer and buttons
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'HTML_ninjahelper_admin.php');
			
	//Include our stylesheet for our Ninja Component Styling
		$document =& JFactory::getDocument();
		$cssFile = JURI::base(true).'/components/com_ninjaboard/css/HTML_ninjahelper_admin.css';
		$document->addStyleSheet($cssFile, 'text/css', null, array());
	
	//add ie6 css if needed
		$ua = $_SERVER['HTTP_USER_AGENT'];
		if (strstr($ua, "MSIE")&& strstr($ua, "6")) {
			  
			$cssFile = JURI::base(true).'/components/com_ninjaboard/css/HTML_ninjahelper_admin_ie6.css';
			$document->addStyleSheet($cssFile, 'text/css', null, array());
			  
		}
		
		?>
		<div id="submenu-box2">
			<div class="t">
				<div class="t">
					<div class="t">
					</div>
				</div>
			</div>
			<div class="m">
				<ul id="submenu2">
					<li>
						<a class="active" href="index.php?option=com_ninjaboard&task=ninjaboard_forum_view"><?php echo JText::_('NB_FORUMS'); ?></a>
					</li>
					<li>
						<a href="index.php?option=com_ninjaboard&task=display&controller=category"><?php echo JText::_('NB_CATEGORIES'); ?></a>
					</li>
				</ul>
				<div class="clr"></div>
			</div>
			<div class="b">
				<div class="b">
					<div class="b">
					</div>
				</div>
			</div>
		</div>
		<form action="index.php?option=com_ninjaboard" method="post" name="adminForm">
			<table class="adminlist" cellspacing="1">
			<thead>
				<tr>
					<th nowrap="nowrap" width="5">
						<?php echo JText::_('Num'); ?>
					</th>
					<th nowrap="nowrap" width="5">
						<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" />
					</th>
					<th nowrap="nowrap" width="40%">
						<?php echo JHTML::_('grid.sort', 'NB_FORUM', 'f.name', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>
					<th nowrap="nowrap" width="20%">
						<?php echo JHTML::_('grid.sort', 'NB_CATEGORY', 'c.name', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_STATE', 'f.state', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>										
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_POSTS', 'f.posts', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_TOPICS', 'f.topics', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>					
					<th nowrap="nowrap" width="8%">
						<?php echo JHTML::_('grid.sort', 'NB_ORDER', 'c.ordering, f.ordering', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
						<?php echo JHTML::_('grid.order', $rows, 'filesave.png', 'ninjaboard_forum_saveorder' ); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_VIEW', 'f.auth_view', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_READ', 'f.auth_read', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_POST', 'f.auth_post', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_REPLY', 'f.auth_reply', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_EDIT', 'f.auth_edit', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_DELETE', 'f.auth_delete', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_REPORT', 'f.auth_reportpost', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_STICKY', 'f.auth_sticky', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_LOCK', 'f.auth_lock', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_ANNOUNCE', 'f.auth_announce', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_VOTE', 'f.auth_vote', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_POLL', 'f.auth_pollcreate', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_ATTACHMENTS', 'f.auth_attachments', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>																																			
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_ID', 'f.id', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_forum_view'); ?>
					</th>			
				</tr>
			</thead>
			<tfoot>
				<td colspan="22">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tfoot>			
			<tbody>
			<?php
			$k = 0;
			for ( $i=0, $n=count( $rows ); $i < $n; $i++ ) {
				$row 	=& $rows[$i];
				
				$img_published = $row->state ? 'tick.png' : 'publish_x.png';
				$task_published = $row->state ? 'ninjaboard_forum_unpublish' : 'ninjaboard_forum_publish';
				$alt_published = $row->state ? JText::_('NB_PUBLISHED') :  JText::_('NB_UNPUBLISHED');				
				
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_forum_edit&hidemainmenu=1&cid[]='. $row->id;
				
				$checked = JHTML::_( 'grid.checkedout', $row, $i );																																				
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $pageNav->getRowOffset( $i ); ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
						<?php
							if (JTable::isCheckedOut($user->get('id'), $row->checked_out)) {
								echo $row->name;
							} else {
								?>
								<a href="<?php echo JRoute::_( $link ); ?>">
									<?php echo htmlspecialchars($row->name, ENT_QUOTES); ?>
								</a>
								<?php
							}
						?>				
					</td>
					<td>
						<?php echo $row->category; ?>				
					</td>
					<td align="center">
						<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task_published;?>')">
						<img src="images/<?php echo $img_published;?>" width="16" height="16" border="0" alt="<?php echo $alt_published; ?>" />
						</a>
					</td>										
					<td align="center">
						<?php echo isset($row->posts) ? $row->posts : '-'; ?>
					</td>
					<td align="center">
						<?php echo isset($row->topics) ? $row->topics : '-'; ?>
					</td>	
					<td class="order">
						<span><?php echo $pageNav->orderUpIcon( $i, ($row->id_cat == @$rows[$i-1]->id_cat), 'ninjaboard_forum_orderup', 'NB_ORDERUP', true); ?></span>
						<span><?php echo $pageNav->orderDownIcon( $i, $n, ($row->id_cat == @$rows[$i+1]->id_cat), 'ninjaboard_forum_orderdown', 'NB_ORDERDOWN', true); ?></span>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
					</td>
					<td align="center">
						<?php echo $ninjaboardAuth->getAuthText($row->auth_view); ?>
					</td>
					<td align="center">
						<?php echo $ninjaboardAuth->getAuthText($row->auth_read); ?>
					</td>
					<td align="center">
						<?php echo $ninjaboardAuth->getAuthText($row->auth_post); ?>
					</td>
					<td align="center">
						<?php echo $ninjaboardAuth->getAuthText($row->auth_reply); ?>
					</td>
					<td align="center">
						<?php echo $ninjaboardAuth->getAuthText($row->auth_edit); ?>
					</td>
					<td align="center">
						<?php echo $ninjaboardAuth->getAuthText($row->auth_delete); ?>
					</td>
					<td align="center">
						<?php echo $ninjaboardAuth->getAuthText($row->auth_reportpost); ?>
					</td>
					<td align="center">
						<?php echo $ninjaboardAuth->getAuthText($row->auth_sticky); ?>
					</td>
					<td align="center">
						<?php echo $ninjaboardAuth->getAuthText($row->auth_lock); ?>
					</td>
					<td align="center">
						<?php echo $ninjaboardAuth->getAuthText($row->auth_announce); ?>
					</td>
					<td align="center">
						<?php echo $ninjaboardAuth->getAuthText($row->auth_vote); ?>
					</td>
					<td align="center">
						<?php echo $ninjaboardAuth->getAuthText($row->auth_pollcreate); ?>
					</td>
					<td align="center">
						<?php echo $ninjaboardAuth->getAuthText($row->auth_attachments); ?>
					</td>																																																							
					<td>
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>
			<input type="hidden" name="option" value="com_ninjaboard" />
			<input type="hidden" name="task" value="ninjaboard_forum_view" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $lists['filter_order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="" />
		</form>
		<?php

	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));


	}
	
	/**
	 * edit forum
	 */
	function editForum(&$row, &$lists) {
		global $mainframe;
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet(NB_ADMINCSS_LIVE.DL.'icon.css');		
		
	//include our helper file to create the footer and buttons
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'HTML_ninjahelper_admin.php');
			
	//Include our stylesheet for our Ninja Component Styling
		$document =& JFactory::getDocument();
		$cssFile = JURI::base(true).'/components/com_ninjaboard/css/HTML_ninjahelper_admin.css';
		$document->addStyleSheet($cssFile, 'text/css', null, array());
	
	//add ie6 css if needed
		$ua = $_SERVER['HTTP_USER_AGENT'];
		if (strstr($ua, "MSIE")&& strstr($ua, "6")) {
			  
			$cssFile = JURI::base(true).'/components/com_ninjaboard/css/HTML_ninjahelper_admin_ie6.css';
			$document->addStyleSheet($cssFile, 'text/css', null, array());
			  
		}

		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'ninjaboard_forum_cancel') {
				submitform(pressbutton);
				return;
			}
			var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");

			// do field validation
			if (trim(form.name.value) == "") {
				alert("<?php echo JText::sprintf('NB_MSGFIELDREQUIRED', JText::_('NB_NAME'), JText::_('NB_FORUM')); ?>");
			} else {
				submitform( pressbutton );
			}
		}		
		</script>
		<form action="index.php" method="post" name="adminForm">
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_FORUMDETAILS'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="name" class="hasTip" title="<?php echo JText::_('NB_NAME') .'::'. JText::_('NB_FORUMNAMEDESC'); ?>">
									<?php echo JText::_('NB_NAME'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="name" id="name" class="inputbox" size="50" value="<?php echo $row->name; ?>" maxlength="150" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="description" class="hasTip" title="<?php echo JText::_('NB_DESCRIPTION') .'::'. JText::_('NB_FORUMDESCRIPTIONDESC'); ?>">
									<?php echo JText::_('NB_DESCRIPTION'); ?>
								</label>						
							</td>
							<td>
								<textarea name="description" id="description" rows="5" cols="50" class="inputbox"><?php echo $row->description; ?></textarea>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="id_cat" class="hasTip" title="<?php echo JText::_('NB_CATEGORY') .'::'. JText::_('NB_FORUMCATEGORYDESC'); ?>">
									<?php echo JText::_('NB_CATEGORY'); ?>
								</label>						
							</td>
							<td>
								<?php echo $lists['categories']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label class="hasTip" title="<?php echo JText::_('NB_ENABLED') .'::'. JText::_('NB_FORUMENABLEDDESC'); ?>">
									<?php echo JText::_('NB_ENABLED'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['state']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label class="hasTip" title="<?php echo JText::_('NB_LOCKED') .'::'. JText::_('NB_FORUMLOCKEDDESC'); ?>">
									<?php echo JText::_('NB_LOCKED'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['locked']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="new_posts_time" class="hasTip" title="<?php echo JText::_('NB_NEWPOSTSTIME') .'::'. JText::_('NB_FORUMNEWPOSTSTIMEDESC'); ?>">
									<?php echo JText::_('NB_NEWPOSTSTIME'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="new_posts_time" id="new_posts_time" class="inputbox" size="20" value="<?php echo $row->new_posts_time; ?>" maxlength="5" />
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_FORUMINFORMATION'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="posts" class="hasTip" title="<?php echo JText::_('NB_POSTS') .'::'. JText::_('NB_FORUMPOSTSDESC'); ?>">
									<?php echo JText::_('NB_POSTS'); ?>
								</label>
							</td>
							<td>
								<?php echo $row->posts; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="topics" class="hasTip" title="<?php echo JText::_('NB_TOPICS') .'::'. JText::_('NB_FORUMTOPICSDESC'); ?>">
									<?php echo JText::_('NB_TOPICS'); ?>
								</label>
							</td>
							<td>
								<?php echo $row->topics; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="last_post_href" class="hasTip" title="<?php echo JText::_('NB_LASTPOST') .'::'. JText::_('NB_FORUMLASTPOSTDESC'); ?>">
									<?php echo JText::_('NB_LASTPOST'); ?>
								</label>
							</td>
							<td>
								<?php echo $row->last_post_href; ?>
							</td>
						</tr>														
					</table>
				</fieldset>						
			</div>
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_FORUMPERMISSIONS'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="auth_view" class="hasTip" title="<?php echo JText::_('NB_VIEW') .'::'. JText::_('NB_FORUMVIEWDESC'); ?>">
									<?php echo JText::_('NB_VIEW'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['auth_view']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="auth_read" class="hasTip" title="<?php echo JText::_('NB_READ') .'::'. JText::_('NB_FORUMREADDESC'); ?>">
									<?php echo JText::_('NB_READ'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['auth_read']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="auth_post" class="hasTip" title="<?php echo JText::_('NB_POST') .'::'. JText::_('NB_FORUMPOSTDESC'); ?>">
									<?php echo JText::_('NB_POST'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['auth_post']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="auth_reply" class="hasTip" title="<?php echo JText::_('NB_REPLY') .'::'. JText::_('NB_FORUMREPLYDESC'); ?>">
									<?php echo JText::_('NB_REPLY'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['auth_reply']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="auth_edit" class="hasTip" title="<?php echo JText::_('NB_EDIT') .'::'. JText::_('NB_FORUMEDITDESC'); ?>">
									<?php echo JText::_('NB_EDIT'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['auth_edit']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="auth_delete" class="hasTip" title="<?php echo JText::_('NB_DELETE') .'::'. JText::_('NB_FORUMDELETEDESC'); ?>">
									<?php echo JText::_('NB_DELETE'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['auth_delete']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="auth_reportpost" class="hasTip" title="<?php echo JText::_('NB_REPORTPOST') .'::'. JText::_('NB_FORUMREPORTPOSTDESC'); ?>">
									<?php echo JText::_('NB_REPORTPOST'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['auth_reportpost']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="auth_sticky" class="hasTip" title="<?php echo JText::_('NB_STICKY') .'::'. JText::_('NB_FORUMSTICKYDESC'); ?>">
									<?php echo JText::_('NB_STICKY'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['auth_sticky']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="auth_lock" class="hasTip" title="<?php echo JText::_('NB_LOCK') .'::'. JText::_('NB_FORUMLOCKDESC'); ?>">
									<?php echo JText::_('NB_LOCK'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['auth_lock']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="auth_announce" class="hasTip" title="<?php echo JText::_('NB_ANNOUNCE') .'::'. JText::_('NB_FORUMANNOUNCEDESC'); ?>">
									<?php echo JText::_('NB_ANNOUNCE'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['auth_announce']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="auth_vote" class="hasTip" title="<?php echo JText::_('NB_VOTE') .'::'. JText::_('NB_FORUMVOTEDESC'); ?>">
									<?php echo JText::_('NB_VOTE'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['auth_vote']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="auth_pollcreate" class="hasTip" title="<?php echo JText::_('NB_POLL') .'::'. JText::_('NB_FORUMPOLLDESC'); ?>">
									<?php echo JText::_('NB_POLL'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['auth_pollcreate']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="auth_attachments" class="hasTip" title="<?php echo JText::_('NB_ATTACHMENTS') .'::'. JText::_('NB_FORUMATTACHMENTSDESC'); ?>">
									<?php echo JText::_('NB_ATTACHMENTS'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['auth_attachments']; ?>			
							</td>
						</tr>															
					</table>				
				</fieldset>
			</div>			
			<div class="clr"></div>	
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="com_ninjaboard" />
			<input type="hidden" name="task" value="" />
		</form>
	<?php

	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));


	}
	
}
?>
