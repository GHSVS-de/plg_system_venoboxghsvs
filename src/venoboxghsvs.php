<?php
defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\CMSPlugin;

class PlgSystemvenoboxGhsvs extends CMSPlugin
{
	protected $app;

	function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config = array());

		if (!$this->app->isClient('administrator'))
		{
			if (version_compare(JVERSION, '4', 'lt'))
			{
				HTMLHelper::addIncludePath(__DIR__ . '/src/Html');
			}
			else
			{
				HTMLHelper::getServiceRegistry()->register(
					'plgvenoboxghsvs',
					'Joomla\Plugin\System\VenoboxGhsvs\Html\VenoboxGhsvs',
					true
				);
			}
		}
	}
}
