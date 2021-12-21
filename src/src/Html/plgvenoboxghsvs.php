<?php
/*
Aufruf via:
HTMLHelper::_('plgvenoboxghsvs.venobox', [optional options]);
*/

defined('JPATH_BASE') or die;

if (version_compare(JVERSION, '4', 'lt'))
{
  JLoader::registerNamespace(
    'Joomla\Plugin\System\VenoboxGhsvs',
    dirname(__DIR__),
    false, false, 'psr4'
  );
}

use Joomla\Plugin\System\VenoboxGhsvs\Helper\VenoboxGhsvsHelper;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

abstract class JHtmlPlgvenoboxghsvs
{
	protected static $loaded = [];

	// media-Ordner:
	protected static $basepath = 'plg_system_venoboxghsvs/venobox';

	public static function venobox($selector = null, $options = [])
	{
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
			$min = JDEBUG ? '' : '.min';
			$version = JDEBUG ? time() : 'auto';

			HTMLHelper::_('stylesheet',
				static::$basepath . '/' . 'venobox' . $min . '.css',
				['version' => $version, 'relative' => true]
			);

			// e.g. compiled from SCSS
			$customCSSPath = 'templates/'
				. Factory::getApplication()->getTemplate()
				. '/css/venobox' . $min . '.css';

			HTMLHelper::_('stylesheet',
				$customCSSPath,
				['version' => $version]
			);
			HTMLHelper::_('script',
				static::$basepath . '/' . 'venobox' . $min . '.js',
				['version' => $version, 'relative' => true]
			);

			static::$loaded[__METHOD__]['core'] = 1;
		}

		$options = \array_merge(
			VenoboxGhsvsHelper::prepareDefaultOptions($sig),
			$options
		);

		$js = 'document.addEventListener("DOMContentLoaded", (event) => {';
		$js .= 'new VenoBox(' . json_encode($options) . ')';
		$js .= '});';

		Factory::getDocument()->addScriptDeclaration($js);
		static::$loaded[__METHOD__][$sig] = 1;
	}
}
