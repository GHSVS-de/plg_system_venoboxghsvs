<?php
/**
2015-08-30: GHSVS
*/
defined('JPATH_BASE') or die;
use Joomla\CMS\HTML\HTMLHelper;

// Bisschen deplaziert. Brauch ich für eigene Blog-View-Einstellungen.
JFactory::getLanguage()->load('mod_j51news', JPATH_SITE . '/modules/mod_j51news');

class PlgSystemvenoboxGhsvs extends JPlugin
{
	function __construct(&$subject, $config = array())
	{
		HTMLHelper::addIncludePath(__DIR__ . '/html');
	}
}