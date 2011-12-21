<?php defined('_JEXEC') or die('Restricted access');

// TODO: uwalter - We urgently need another way to get the category name in topics view!

?>

		<div class="nbSeparator nbClr"></div>
		<div class="nbCategoryHeader">
			<?php
			// The loadbar property is set in the calling template view.
			switch ($this->loadbar) {
				case 'latest':
					echo '<span class="slidingBar-trigger"></span><span class="nbLatest">', $this->latestItemsHeader, '</span>';
					break;
				case 'topics':
					if ($this->buttonNewTopic->href != '') {
						echo
							'<a class="nbHeadButton nb-buttons buttonNewTopic" href="', $this->buttonNewTopic->href, '">',
								'<span class="buttonNewTopic">', $this->buttonNewTopic->title,
							'</span></a>';
					}
					echo '<span class="nbForumHeader">', $this->_models['forum']->_forum->name, '</span>';
					break;
				case 'posts':
					if ($this->buttonNewPost->href != '') {
						echo
							'<a class="nbHeadButton nb-buttons buttonPostReply" href="', $this->buttonNewPost->href, '">',
								'<span class="buttonPostReply">', $this->buttonNewPost->title,
							'</span></a>';
					}
					echo '<span class="nbForumHeader">', $this->_models['topic']->_forum->name, '</span>';
					break;
				case 'information':   echo '<span class="nbForumHeader">', JText::_('NB_INFORMATION'),'</span>';   break;
				case 'edittopic':     echo '<span class="nbForumHeader">', JText::_('NB_EDITTOPIC'),'</span>';     break;
				case 'preview':       echo '<span class="nbForumHeader">', JText::_('NB_PREVIEW'), '</span>';      break;
				case 'editpost':      echo '<span class="nbForumHeader">', JText::_('NB_EDITPOST'),'</span>';      break;
				case 'topicreview':   echo '<span class="nbForumHeader">', JText::_('NB_TOPICREVIEW'),'</span>';   break;
				case 'userposts':     echo '<span class="nbForumHeader">', JText::_('NB_USERPOSTS'),'</span>';     break;
				case 'reportpost':    echo '<span class="nbForumHeader">', JText::_('NB_REPORTPOST'),'</span>';    break;
				case 'search':        echo '<span class="nbForumHeader">', JText::_('NB_SEARCH'),'</span>';        break;
				case 'searchresults': echo '<span class="nbForumHeader">', JText::_('NB_SEARCHRESULTS'),'</span>'; break;
				case 'editprofile':   echo '<span class="nbForumHeader">', JText::_('NB_EDITPROFILE'),'</span>';   break;
				case 'viewprofile':   echo '<span class="nbForumHeader">', JText::_('NB_VIEWPROFILE'),'</span>';   break;
				case 'requestlogin':  echo '<span class="nbForumHeader">', JText::_('NB_REQUESTLOGIN'),'</span>';  break;
				case 'login':         echo '<span class="nbForumHeader">', JText::_('NB_LOGIN'),'</span>';         break;
				case 'terms':         echo '<span class="nbForumHeader">', $this->terms->terms,'</span>';          break;
				case 'registration':  echo '<span class="nbForumHeader">', JText::_('NB_REGISTRATION'),'</span>';  break;
				default:
					echo
						'<span class="slidingBar-trigger"></span>',
						'<a class="nbCatLink" href="', !empty($this->category->categoryLink) ? $this->category->categoryLink : '#', '">', $this->category->name, '</a>';
			}
			?>

		</div>
		<div class="nbBarShadow"></div>
