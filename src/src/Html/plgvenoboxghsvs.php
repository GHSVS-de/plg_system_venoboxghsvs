<?php
/*
Aufruf via:
HTMLHelper::_('plgvenoboxghsvs.venobox', [optional options]);
- Diese Datei ist die Joomla 3 Version.
*/

\defined('_JEXEC') or die;

if (version_compare(JVERSION, '4', 'lt'))
{
  JLoader::registerNamespace(
    'Joomla\Plugin\System\VenoboxGhsvs',
    dirname(__DIR__),
    false, false, 'psr4'
  );
}

use Joomla\Plugin\System\VenoboxGhsvs\Helper\VenoboxGhsvsHelper;

abstract class JHtmlPlgvenoboxghsvs
{
	public static function venobox($selector = null, $options = [])
	{
		VenoboxGhsvsHelper::venobox($selector, $options);
	}
}
