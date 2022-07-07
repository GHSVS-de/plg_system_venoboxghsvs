<?php
/*
Aufruf via:
HTMLHelper::_('plgvenoboxghsvs.venobox', [optional options]);
- Diese Datei ist die Joomla 4 Version.
*/

namespace Joomla\Plugin\System\VenoboxGhsvs\Html;

\defined('_JEXEC') or die;

use Joomla\Plugin\System\VenoboxGhsvs\Helper\VenoboxGhsvsHelper;

abstract class VenoboxGhsvs
{
	public static function venobox($selector = null, $options = [])
	{
		VenoboxGhsvsHelper::venobox($selector, $options);
	}
}
