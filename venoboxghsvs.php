<?php
defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\CMSPlugin;

class PlgSystemvenoboxGhsvs extends CMSPlugin
{
	function __construct(&$subject, $config = array())
	{
		HTMLHelper::addIncludePath(__DIR__ . '/html');
	}
}
