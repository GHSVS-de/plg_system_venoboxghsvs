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

		// B\C.
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

		$pluginParams = VenoboxGhsvsHelper::getPluginParams(
			['system', 'venoboxghsvs']);

		if (!isset($options['selector']))
		{
			if (!($options['selector'] = $selector))
			{
				$selector = $pluginParams->get('selector', '');
				$selector = trim($selector);
				$options['selector'] = $selector ?: '.venobox';
			}
		}
		// END B\C shit.

		$sig = $options['selector'];

		if (isset(static::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		if (!isset(static::$loaded[__METHOD__]['core']))
		{
			$attribs = array();
			$postFix = $pluginParams->get('jquerySlim', '');
			$min = JDEBUG ? '' : '.min';
			$version = JDEBUG ? time() : 'auto';
			HTMLHelper::_('jquery.framework');

			HTMLHelper::_('stylesheet',
				static::$basepath . '/' . 'venobox' . $min . '.css',
				array('version' => $version, 'relative' => true),
				$attribs
			);

			// e.g. compiled from SCSS
			$customCSSPath = 'templates/'
				. Factory::getApplication()->getTemplate()
				. '/css/venobox' . $min . '.css';
			HTMLHelper::_('stylesheet',
				$customCSSPath,
				array('version' => $version),
				$attribs
			);
			HTMLHelper::_('script',
				static::$basepath . '/' . 'venobox' . $postFix . $min . '.js',
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
