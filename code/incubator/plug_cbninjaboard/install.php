<?php 

function plug_ninjaboard_install()

{

    JFile::copy(JPATH_ROOT.DS."components".DS."com_comprofiler".DS."plugin".DS."user".DS."plug_cbninjaboard".DS."language".DS."en-GB.plg_cbninjaboard.ini", JPATH_ROOT.DS."language".DS."en-GB".DS."en-GB.plg_cbninjaboard.ini");
	JFile::copy(JPATH_ROOT.DS."components".DS."com_comprofiler".DS."plugin".DS."user".DS."plug_cbninjaboard".DS."language".DS."de-DE.plg_cbninjaboard.ini", JPATH_ROOT.DS."language".DS."de-DE".DS."de-DE.plg_cbninjaboard.ini");

}

?>