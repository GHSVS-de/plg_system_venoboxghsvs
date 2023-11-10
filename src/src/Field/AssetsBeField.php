<?php
namespace GHSVS\Plugin\System\VenoboxGhsvs\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;

class AssetsBeField extends FormField
{
	protected $type = 'AssetsBe';

	// Path inside /media/.
	protected $basePath = 'plg_system_venoboxghsvs';

	protected function getInput()
	{
		// Only explicit "false" will be respected.
		$loadjs     = isset($this->element['loadjs'])
			? (string) $this->element['loadjs'] : true;
		$loadcss    = isset($this->element['loadcss'])
			? (string) $this->element['loadcss'] : true;
		$loadJQuery = isset($this->element['loadJQuery'])
			? (string) $this->element['loadJQuery'] : true;

		$file = $this->basePath . '/backend';

		if ($loadcss !== 'false')
		{
			HTMLHelper::_('stylesheet',
				$file . '.css',
				array(
					'relative' => true,
					'version' => 'auto',
				)
			);
		}

		if ($loadJQuery !== 'false')
		{
			HTMLHelper::_('jquery.framework');
		}

		if ($loadjs !== 'false')
		{
			HTMLHelper::_('script',
				$file . '.js',
				array(
					'relative' => true,
					'version' => 'auto',
				)
			);
		}
		return '';
	}

	protected function getLabel()
	{
		return '';
	}
}
