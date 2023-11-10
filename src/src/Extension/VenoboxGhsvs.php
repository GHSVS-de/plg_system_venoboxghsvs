<?php
namespace GHSVS\Plugin\System\VenoboxGhsvs\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use GHSVS\Plugin\System\VenoboxGhsvs\Service\HTML\VenoboxGhsvs as VenoboxGhsvsJHtml;

final class VenoboxGhsvs extends CMSPlugin
{
	use HTMLRegistryAwareTrait;

	public function onAfterInitialise()
	{
		if (!$this->getApplication()->isClient('administrator'))
		{
			/* HTMLHelper::getServiceRegistry()->register(
				'plgvenoboxghsvs',
				'GHSVS\Plugin\System\VenoboxGhsvs\Html\VenoboxGhsvs',
				true
			); */
			$VenoboxGhsvsJHtml = new VenoboxGhsvsJHtml();
			$this->getRegistry()->register('plgvenoboxghsvs', $VenoboxGhsvsJHtml);
		}
	}
}
