<?php
/**
 * @version $Id: user.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard User View
 *
 * @package Ninjaboard
 */
class ViewUser {

	/**
	 * show users
	 */
	function showUsers(&$rows, &$pageNav, &$lists) {
		global $mainframe;
		
		// initialize variables
		$user	=& JFactory::getUser();
		
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
				<ul id="submenu">
					<li>
						<?php $class = ($task == 'ninjaboard_user_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_user_view"><?php echo JText::_('NB_USERS'); ?></a>
					</li>				
					<li>
						<?php $class = ($task == 'ninjaboard_group_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_group_view"><?php echo JText::_('NB_GROUPS'); ?></a>
					</li>
					<li>
						<?php $class = ($task == 'ninjaboard_rank_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_rank_view"><?php echo JText::_('NB_RANKS'); ?></a>
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
		<table>
			<tr>
				<td width="100%">
					<?php echo JText::_('NB_FILTER'); ?>:
					<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_('NB_GO'); ?></button>
					<button onclick="getElementById('search').value='';this.form.submit();"><?php echo JText::_('NB_RESET'); ?></button>
				</td>
				<td nowrap="nowrap">
					<?php echo $lists['type'];?>
					<?php echo $lists['logged'];?>
				</td>
			</tr>
		</table>

		<table class="adminlist" cellpadding="1">
			<thead>
				<tr>
					<th width="2%" class="title">
						<?php echo JText::_('NUM'); ?>
					</th>
					<th width="3%" class="title">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
					</th>
					<th class="title">
						<?php echo JHTML::_('grid.sort', 'NB_NAME', 'a.name', @$lists['order_Dir'], @$lists['order'], 'ninjaboard_user_view'); ?>
					</th>
					<th width="15%" class="title" >
						<?php echo JHTML::_('grid.sort', 'NB_USERNAME', 'a.username', @$lists['order_Dir'], @$lists['order'], 'ninjaboard_user_view'); ?>
					</th>
					<th width="5%" class="title" nowrap="nowrap">
						<?php echo JText::_('NB_LOGGEDIN'); ?>
					</th>
					<th width="5%" class="title" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort', 'NB_ENABLED', 'a.block', @$lists['order_Dir'], @$lists['order'], 'ninjaboard_user_view'); ?>
					</th>
					<th width="15%" class="title">
						<?php echo JHTML::_('grid.sort', 'NB_GROUP', 'a.groupname', @$lists['order_Dir'], @$lists['order'], 'ninjaboard_user_view'); ?>
					</th>
					<th width="15%" class="title">
						<?php echo JText::_('NB_ROLE'); ?>
					</th>
					<th width="15%" class="title">
						<?php echo JHTML::_('grid.sort', 'NB_EMAIL', 'a.email', @$lists['order_Dir'], @$lists['order'], 'ninjaboard_user_view'); ?>
					</th>
					<th width="10%" class="title">
						<?php echo JHTML::_('grid.sort', 'NB_LASTVISIT', 'a.lastvisitDate', @$lists['order_Dir'], @$lists['order'], 'ninjaboard_user_view'); ?>
					</th>
					<th width="1%" class="title" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort', 'NB_ID', 'a.id', @$lists['order_Dir'], @$lists['order'], 'ninjaboard_user_view'); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<td colspan="11">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row 	=& $rows[$i];

				$img 	= $row->block ? 'publish_x.png' : 'tick.png';
				$task 	= $row->block ? 'ninjaboard_user_unblock' : 'ninjaboard_user_block';
				$alt 	= $row->block ? JText::_('NB_ENABLED') : JText::_('NB_BLOCKED');
				
				$link 	= 'index.php?option=com_ninjaboard&task=ninjaboard_user_edit&cid[]='. $row->id. '&hidemainmenu=1';
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $i+1+$pageNav->limitstart;?>
					</td>
					<td>
						<?php echo JHTML::_('grid.id', $i, $row->id); ?>
					</td>
					<td>
						<?php
							if (JTable::isCheckedOut($user->get ('id'), $row->id)) {
								echo $row->name;
							} else {
								?>
								<a href="<?php echo JRoute::_($link); ?>">
									<?php echo htmlspecialchars($row->name, ENT_QUOTES); ?>
								</a>
								<?php
							}
						?>
					</td>
					<td>
						<?php echo $row->username; ?>
					</td>
					<td align="center">
						<?php echo $row->loggedin ? '<img src="images/tick.png" width="16" height="16" border="0" alt="" />': '<img src="components/com_ninjaboard/images/menu/spacer.png" width="16" height="16" border="0" alt="" />'; ?>
					</td>
					<td align="center">
						<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
							<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /></a>
					</td>
					<td>
						<?php echo JText::_($row->groupname); ?>
					</td>
					<td>
						<?php echo $lists['roles'][$row->role]; ?>
					</td>
					<td>
						<a href="mailto:<?php echo $row->email; ?>">
							<?php echo $row->email; ?></a>
					</td>
					<td nowrap="nowrap">
						<?php echo ($row->lastvisitDate == '0000-00-00 00:00:00') ?  'No visit since registration' : NinjaboardHelper::Date($row->lastvisitDate); ?>
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
		<input type="hidden" name="task" value="ninjaboard_user_view" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="" />
		</form>
		<?php

	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));


	}
	
	/**
	 * edit user
	 */
	function editUser(&$user, &$row, &$lists) {
		global $mainframe, $my;

		 // initialize variables
		$document =& JFactory::getDocument();
		$document->addStyleSheet(NB_ADMINCSS_LIVE.DL.'icon.css');		 
		$acl = & JFactory::getACL();

		$canBlockUser 	= $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'user properties', 'block_user');
		$canEmailEvents = $acl->acl_check('workflow', 'email_events', 'users', $acl->get_group_name($user->get('gid'), 'ARO'));
		
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
				if (pressbutton == 'ninjaboard_user_cancel') {
					submitform(pressbutton);
					return;
				}
				var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");
	
				// do field validation
				if (trim(form.name.value) == "") {
					alert("<?php echo JText::sprintf('NB_MSGFIELDREQUIRED', JText::_('NB_NAME'), JText::_('NB_USER')); ?>");
				} else if (form.username.value == "") {
					alert("<?php echo JText::sprintf('NB_MSGFIELDREQUIRED', JText::_('NB_USERNAME'), JText::_('NB_USER')); ?>");
				} else if (r.exec(form.username.value) || form.username.value.length < 3) {
					alert("<?php echo JText::_('WARNLOGININVALID', true); ?>");
				} else if (trim(form.email.value) == "") {
					alert("<?php echo JText::sprintf('NB_MSGFIELDREQUIRED', JText::_('NB_EMAILADRESS'), JText::_('NB_USER')); ?>");
				} else {
					submitform(pressbutton);
				}
			}
		</script>
		<form action="index.php" method="post" name="adminForm" id="adminForm" autocomplete="off" enctype="multipart/form-data">
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_USERDETAILSJOOMLA'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="name">
									<?php echo JText::_('NB_NAME'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="name" id="name" class="inputbox" size="40" value="<?php echo $user->get('name'); ?>" maxlength="50" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="username">
									<?php echo JText::_('NB_USERNAME'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="username" id="username" class="inputbox" size="40" value="<?php echo $user->get('username'); ?>" maxlength="25"  autocomplete="off" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="email">
									<?php echo JText::_('NB_EMAIL'); ?>
								</label>
							</td>
							<td>
								<input class="inputbox" type="text" name="email" id="email" size="40" value="<?php echo $user->get('email'); ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="password">
									<?php echo JText::_('NB_NEWPASSWORD'); ?>
								</label>
							</td>
							<td>
								<?php if(!$user->get('password')) : ?>
									<input class="inputbox disabled" type="password" name="password" id="password" size="40" value="" disabled="disabled" />
								<?php else : ?>
									<input class="inputbox" type="password" name="password" id="password" size="40" value=""/>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="password2">
									<?php echo JText::_('NB_VERIFYPASSWORD'); ?>
								</label>
							</td>
							<td>
								<?php if(!$user->get('password')) : ?>
									<input class="inputbox disabled" type="password" name="password2" id="password2" size="40" value="" disabled="disabled" />
								<?php else : ?>
									<input class="inputbox" type="password" name="password2" id="password2" size="40" value=""/>
								<?php endif; ?>
							</td>
						</tr>
						<?php
						if($user->get('id')) {
							?>
							<tr>
								<td valign="top" class="key">
									<?php echo JText::_('NB_GROUP'); ?>
								</td>
								<td>
									<?php echo $user->get('usertype'); ?>
								</td>
							</tr>
							<?php
						}
						if ($canBlockUser) {
							?>
							<tr>
								<td class="key">
									<?php echo JText::_('NB_BLOCKUSER'); ?>
								</td>
								<td>
									<?php echo $lists['block']; ?>
								</td>
							</tr>
							<?php
						}
						if ($canEmailEvents) {
							?>
							<tr>
								<td class="key">
									<?php echo JText::_('NB_RECEIVESYSTEMEMAILS'); ?>
								</td>
								<td>
									<?php echo $lists['sendEmail']; ?>
								</td>
							</tr>
							<?php
						}
						if($user->get('id')) {
							?>
							<tr>
								<td class="key">
									<?php echo JText::_('NB_REGISTERDATE'); ?>
								</td>
								<td>
									<?php echo $user->get('registerDate'); ?>
								</td>
							</tr>
							<tr>
								<td class="key">
									<?php echo JText::_('NB_LASTVISITDATE'); ?>
								</td>
								<td>
									<?php echo $user->get('lastvisitDate'); ?>
								</td>
							</tr>
							<?php
						}
						?>
					</table>
				</fieldset>			
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_PARAMETERS'); ?>
					</legend>
					<table class="admintable">
						<tr>
							<td>
								<?php
									$file 	= NB_ADMINPARAMS.DS.'users_params.xml';
									$params = new JParameter($user->get('params'), $file);
									echo $params->render('params');
								?>
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_AVATAR'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="avatarfile">
									<?php echo JText::_('NB_UPLOADAVATARFILE'); ?>
								</label>
							</td>
							<td>
								<input type="file" name="avatarfile" id="avatarfile" size="40" value="" maxlength="50" class="inputbox" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="avatarimage">
									<?php echo JText::_('NB_CURRENTIMAGE'); ?>
								</label>
							</td>
							<td>
								<?php if ($user->get('ninjaboardAvatar')->avatarFile != '') { ?>
									<img src="<?php echo $user->get('ninjaboardAvatar')->avatarFile; ?>" alt="<?php echo $user->get('name'); ?>" border="1" id="avatarimage" />
								<?php } else { ?>
									<?php echo JText::_('NB_NOAVATARIMAGE'); ?>
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="deleteavatar">
									<?php echo JText::_('NB_DELETEAVATARIMAGE'); ?>
								</label>
							</td>
							<td>
								<input type="checkbox" name="deleteavatar" value="1" />
							</td>
						</tr>					
					</table>
				</fieldset>			
			</div>
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_USERDETAILSNB'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="role">
									<?php echo JText::_('NB_USERROLE'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['roles']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="system_emails">
									<?php echo JText::_('NB_RECEIVESYSTEMEMAILS'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['systememails']; ?>
							</td>
						</tr>					
					</table>
				</fieldset>			
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_PREFERENCES'); ?>
					</legend>
						<fieldset class="adminform">
							<legend>
								<?php echo JText::_('NB_GENERALSETTINGS'); ?>
							</legend>
							<table class="admintable" cellspacing="1">
								<tr>
									<td class="key">
										<?php echo JText::_('NB_SHOWEMAIL'); ?>
									</td>
									<td>
										<?php echo $lists['show_email']; ?>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('NB_SHOWONLINESTATE'); ?>
									</td>
									<td>
										<?php echo $lists['show_online_state']; ?>
									</td>
								</tr>							
							</table>
						</fieldset>
						<fieldset class="adminform">
							<legend>
								<?php echo JText::_('NB_POSTSETTINGS'); ?>
							</legend>					
							<table class="admintable" cellspacing="1">					
								<?php /*<tr>
									<td class="key">
										<php echo JText::_('NB_ENABLEBBCODE'); >
									</td>
									<td>
										<php echo $lists['enablebbcode']; >
									</td>
								</tr>
								<tr>
									<td class="key">
										<php echo JText::_('NB_ENABLEEMOTICONS'); >
									</td>
									<td>
										<php echo $lists['enableemoticons']; >
									</td>
								</tr> */?>
								<tr>
									<td class="key">
										<?php echo JText::_('NB_NOTIFYONREPLY'); ?>
									</td>
									<td>
										<?php echo $lists['notify_on_reply']; ?>
									</td>
								</tr>							
							</table>
					</fieldset>
					<fieldset class="adminform">
						<legend>
							<?php echo JText::_('NB_TIMESETTINGS'); ?>
						</legend>										
						<table class="admintable" cellspacing="1">
							<tr>
								<td class="key">
									<label for="time_zone">
										<?php echo JText::_('NB_TIMEZONE'); ?>
									</label>
								</td>
								<td>
									<?php echo $lists['timezones']; ?>			
								</td>
							</tr>
							<tr>
								<td class="key">
									<label for="time_format">
										<?php echo JText::_('NB_TIMEFORMAT'); ?>
									</label>
								</td>
								<td>
									<?php echo $lists['timeformats']; ?>
								</td>
							</tr>																									
						</table>
					</fieldset>
				</fieldset>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_SIGNATURE'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="signature">
									<?php echo JText::_('NB_SIGNATURE'); ?>
								</label>
							</td>
							<td>
								<textarea name="signature" id="signature" rows="5" cols="40" class="inputbox"><?php echo $user->get('signature'); ?></textarea>
							</td>
						</tr>					
					</table>
				</fieldset>
			</div>			
			<div class="clr"></div>
			<div class="col100">
				<fieldset class="adminform">
					<legend><?php echo JText::_('NB_EXTENDEDROLES'); ?></legend>
					<div class="col width-50">
						<fieldset class="adminform">
							<legend>
								<?php echo JText::_('NB_ADMINISTRATION'); ?>
							</legend>				
								<table class="admintable">						
									<tr>
										<td class="key">
											<label for="administratedforums">
												<?php echo JText::_('NB_ADMINISTRATOR'); ?>
											</label>
										</td>
										<td>
											<?php echo $lists['administratedforums']; ?>
										</td>
									</tr>								
								</table>
						</fieldset>
						<fieldset class="adminform">
							<legend>
								<?php echo JText::_('NB_PRIVATEACCESS'); ?>
							</legend>				
								<table class="admintable">						
									<tr>
										<td class="key">
											<label for="privateforums">
												<?php echo JText::_('NB_PRIVATEMEMBER'); ?>
											</label>
										</td>
										<td>
											<?php echo $lists['privateforums']; ?>
										</td>
									</tr>								
								</table>
						</fieldset>					
					</div>											
					<div class="col width-50">
						<fieldset class="adminform">
							<legend><?php echo JText::_('NB_MODERATION'); ?></legend>				
								<table class="admintable">						
									<tr>
										<td class="key">
											<label for="moderatedforums">
												<?php echo JText::_('NB_MODERATOR'); ?>
											</label>
										</td>
										<td>
											<?php echo $lists['moderatedforums']; ?>
										</td>
									</tr>								
								</table>
						</fieldset>				
						<fieldset class="adminform">
							<legend>
								<?php echo JText::_('NB_GROUPS'); ?>
							</legend>				
								<table class="admintable">						
									<tr>
										<td class="key">
											<label for="groups">
												<?php echo JText::_('NB_GROUPMEMBER'); ?>
											</label>
										</td>
										<td>
											<?php echo $lists['groups']; ?>
										</td>
									</tr>								
								</table>
						</fieldset>					
					</div>
				</fieldset>
			</div>
					
			<div class="col100">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_PROFILE'); ?>
					</legend>
					
						<?php ViewUser::_getProfileFieldSets($lists['fieldsets'], $lists['fields']); ?>
				</fieldset>
			</div>						
			<input type="hidden" name="id" value="<?php echo $user->get('id'); ?>" />
			<input type="hidden" name="option" value="com_ninjaboard" />
			<input type="hidden" name="task" value="" />
			<?php
			if (!$canEmailEvents) {
				?>
				<input type="hidden" name="sendEmail" value="0" />
				<?php
			}
			?>
		</form>
	<?php

	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));


	}
	
	function _getProfileFieldSets($fieldsets, $fields) {
	
		for ($i=0, $n=count($fieldsets); $i < $n; $i++) {
			$fieldset =& $fieldsets[$i];
		
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
			<fieldset class="adminform">
				<legend>
					<?php echo JText::_($fieldset->name); ?>
				</legend>
				<table class="admintable">
					<?php
					for ($j=0, $m=count($fields); $j < $m; $j++) {
						$field 	=& $fields[$j];
						if ($fieldset->id == $field->id_profile_field_set) {
					?>						
						<tr>
							<td class="key">
								<label for="<?php echo $field->name; ?>">
									<?php echo JText::_($field->title); ?>
								</label>
							</td>
							<td>
								<?php echo $field->element; ?>
							</td>
						</tr>
					<?php
						}
					}
					?>								
				</table>		
			</fieldset>																																					
		<?php

	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));
		}		
	}
}
?>
