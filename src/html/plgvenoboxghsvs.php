<?php
/*
Aufruf via:
HTMLHelper::_('plgvenoboxghsvs.venobox', [optional options]);
*/

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

abstract class JHtmlPlgvenoboxghsvs
{
	protected static $loaded = array();

	// media-Ordner:
	protected static $basepath = 'plg_system_venoboxghsvs/venobox';

	public static function venobox($selector = null, $options = [])
	{
		JLoader::register('VenoboxGhsvsHelper',
			JPATH_PLUGINS . '/system/venoboxghsvs/Helper/VenoboxGhsvsHelper.php');

		// START B\C shit. $selector is deprecated.
		$argList = func_get_args();

		if (count($argList) > 0)
		{
			if (is_array($argList[0]))
			{
				$options = $argList[0];
				$selector = '';
			}
			else
			{
				$selector = $argList[0];
			}
		}

		if (!isset($options['selector']))
		{
			if (!($options['selector'] = $selector))
			{
				$options['selector'] = VenoboxGhsvsHelper::getPluginParams(
					['system', 'venoboxghsvs']
				)->get('selector', '') ?: '.venobox';
			}
		}
		// END B\C shit.

		$sig = md5($selector);

		if (isset(static::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		if (!isset(static::$loaded[__METHOD__]['core']))
		{
			$attribs = array();
			$min = JDEBUG ? '' : '.min';
			$version = JDEBUG ? time() : 'auto';

			HTMLHelper::_('jquery.framework');
			HTMLHelper::_('stylesheet',
				static::$basepath . '/' . 'venobox' . $min . '.css',
				array('version' => $version, 'relative' => true),
				$attribs
			);
			HTMLHelper::_('script',
				static::$basepath . '/' . 'venobox' . $min . '.js',
				array('version' => $version, 'relative' => true),
				$attribs
			);

			static::$loaded[__METHOD__]['core'] = 1;
		}

		$options = \array_merge(
			VenoboxGhsvsHelper::prepareDefaultOptions($sig),
			$options
		);
			$ready_or_load = $options['ready_or_load'] === 'ready'
				? 'jQuery(document).ready(' : 'jQuery(window).on("load",';
		$js = $ready_or_load . 'function(){jQuery("' . $options['selector'] . '").venobox('
				. json_encode($options) . ');});';

			Factory::getDocument()->addScriptDeclaration($js);
			static::$loaded[__METHOD__][$sig] = 1;
	}
}
