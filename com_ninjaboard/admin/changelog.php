2008-12-15 beta release 0.7.7

backend
- added "new posts time" field to forum manager
  new_posts_time in DB
- latestpost_settings in table ninjaboard_config in DB
- fixed some corrupt translation strings
- added param "show terms" in config
- added feature signature. table #__ninjaboard_user
- profile fields can be now disabled from beeing
  edited in frontend in Profile Field Manager
- added tooltip infos in configuration and forum

frontend
- some XHTML 1.0 Transitional improvements
- fixed preview problem on edit topic
- added new posts functionality
- fixed problem registering new member 
- fixed pagination problem on posts
- added new box "Latest Topics/Posts" on board index
- fixed multiple incrementing of user posts on editing
  topic and post.
- fixed some corrupt translation strings
- update jQuery to 1.2.6
- fixed index.js not found
- added terms to board footer
- added feature signature
- registration is now dependent on Joomla user settings
- fixed search pagination
- fixed important security issue saving profile. many thanks
  to Rainer (mn82) for the report and a solid solution
- avatars can be now resized automatically 
- move topic feature
  
language
- \administrator\language\en-GB\en-GB.com_ninjaboard.ini
- \language\en-GB\en-GB.com_ninjaboard.ini
- \components\com_ninjaboard\designs\buttons\ninjaboard_black\en-GB\ninjaboard.ini
- \components\com_ninjaboard\designs\icons\ninjaboard\en-GB\ninjaboard.ini
- \components\com_ninjaboard\designs\templates\ninjaboard\language\en-GB\ninjaboard.ini

2008-10-26 beta release 0.7.6

backend
- added board settings params guest name 
- fixed database creation bugs
- changed field user_id to id_user in table #__ninjaboard_sessions

frontend
- added guest name functionality
- fixed no fuctionality of new topic and post reply with IE
- fixed redirection to board index having no permission to post
- fixed bug resetting login
- some XHTML 1.0 Transitional improvements
  
language
- \administrator\language\en-GB\en-GB.com_ninjaboard.ini
- \components\com_ninjaboard\designs\templates\ninjaboard\language\en-GB\ninjaboard.ini

2008-10-19 beta release 0.7.5

backend
- added params attachment settings in configuration 

frontend
- added user online status in posts
- added topic review on reply
- some template improvements
- added buttons submit, preview and cancel
- added search board box
- important security issue. spoofing view=resetlogin allows
  everybody to change every user password.
- main template redesign
  
language
- \administrator\language\en-GB\en-GB.com_ninjaboard.ini
- \components\com_ninjaboard\designs\templates\ninjaboard\language\en-GB\ninjaboard.ini
- \components\com_ninjaboard\designs\buttons\ninjaboard_black\en-GB\ninjaboard_black.ini
- \components\com_ninjaboard\designs\buttons\ninjaboard_red\en-GB\ninjaboard_red.ini
- \components\com_ninjaboard\designs\buttons\ninjaboard_yellow\en-GB\ninjaboard_yellow.ini

2008-09-14 beta release 0.7.4

backend
- redesigned Ninjaboard user table

frontend
- fixed editing topic not allowed for everybody
- core improvements
- implemented user posts view
- improved post view
- improved search view
- improved latest posts view
  
language
- \administrator\language\en-GB\en-GB.com_ninjaboard.ini
- \language\en-GB\en-GB.com_ninjaboard.ini
- \components\com_ninjaboard\designs\templates\ninjaboard\language\en-GB\ninjaboard.ini

2008-09-10 beta release 0.7.3

backend
- no changes

frontend
- fixed security problem. every site guest user has 
  been able posting into a zero forum.
  
language
- no changes

2008-09-07 beta release 0.7.2

backend
- no changes

frontend
- fixed quotation problem.
- fixed security problem. every user has been able
  to edit any post by changing the id of the post
  in the url.

2008-09-07 beta release 0.7.1
- fixed installation

2008-09-06 beta release 0.7.0

backend
- redesigned language files
- fixed some bugs
- redesigned icon-set management
- redesigned emoticon-set management

frontend
- some core improvments
- fixed some bugs
- some security fixes including SQL injection

2008-05-13 alpha release 0.5.9

backend
- some core improvments
- fixed some bugs
- redesigned icon-set management

frontend
- some core improvments
- fixed some bugs

2008-02-29 alpha release 0.5.8

backend
- some core improvments
- fixed some bugs
- redesigned button-set management

frontend
- some core improvments
- fixed some bugs
- added template customization

2008-01-30 alpha release 0.5.7

backend
- fixed some bugs

frontend
- fixed some bugs
- added user list view

2008-01-22 alpha release 0.5.6 (special release for Joomla! 1.5 Stable)

backend
- fixed some bugs

frontend
- fixed some bugs
- added latest posts view

2008-01-19 alpha release 0.5.5

backend
- fixed some bugs
- redesigned smiley management

frontend
- fixed some bugs
- added profile view

2008-01-02 alpha release 0.5.4

backend
- fixed some bugs
- added avatar management in user manager
- added terms of agreement management
- improved user synchronization

frontend
- fixed some bugs
- redesigned terms of agreement

2007-01-01 alpha release 0.5.3

backend
- added avatar settings in configuration manager
- added field "role" in user manager view
- added filter "profile field list" in profile
  field-list-values manager

frontend
- fixed some bugs
- solved url problems appearing on joomla rc4 using sef
- improved search
- added avatar system

2007-12-22 alpha release 0.5.2

backend
- fixed many mozilla firefox bugs (we all know that this browser do exactly this, what it should do)

frontend
- fixed many mozilla firefox bugs
- added new bulletin codes functionality
- added search

2007-12-18 alpha release 0.5.1

backend
- no changes

frontend
- fixed some bugs
- added login
- added request login
- added reset login

2007-12-13 alpha release 0.5.0

backend
- fixed some bugs

frontend
- fixed some bugs
- added board statistic
- added who's online

2007-11-21 pre-alpha release 0.3.2

backend
- fixed some bugs

frontend
- fixed some bugs
- added registration/profile edit

2007-11-13 pre-alpha release 0.3.1

backend
- fixed some bugs

frontend
- added fuctionality "edit" and "delete" post/topic
- fixed some bugs

2007-11-06 pre-alpha public relese 0.3.0
