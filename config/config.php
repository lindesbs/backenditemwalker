<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

$GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('backenditemwalker', 'parseBackendTemplate');

if (TL_MODE == 'BE')
{
	$GLOBALS['TL_CSS'][] = 'system/modules/backenditemwalker/html/backenditemwalker.css'; 
}
