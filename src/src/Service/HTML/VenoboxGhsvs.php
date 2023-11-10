<?php
/*
Aufruf via:
HTMLHelper::_('plgvenoboxghsvs.venobox', [optional options]);
- Diese Datei ist die Joomla 4 Version.
*/

namespace GHSVS\Plugin\System\VenoboxGhsvs\Service\HTML;

\defined('_JEXEC') or die;

use GHSVS\Plugin\System\VenoboxGhsvs\Helper\VenoboxGhsvsHelper;

class VenoboxGhsvs
{
	public function venobox($selector = null, $options = [])
	{
		$helper = new VenoboxGhsvsHelper();
		$helper->venobox($selector, $options);
	}
}
