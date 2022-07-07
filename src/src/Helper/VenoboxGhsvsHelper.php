<?php

namespace Joomla\Plugin\System\VenoboxGhsvs\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

abstract class VenoboxGhsvsHelper
{
	protected static $loadedPlugins = [];
	protected static $loaded = [];
	protected static $basepath = 'plg_system_venoboxghsvs/venobox';

	/**
	 * Lädt auch Params aus nicht aktivierten Plugins.
   * Fügt den Params isEnabled- und isInstalled-Parameter hinzu.
	*/
	public static function getPluginParams(array $plugin)
	{
		if (count($plugin) != 2)
		{
			return false;
		}

		$key = implode(':', $plugin);

  	if(
			empty(self::$loadedPlugins[$key])
			|| !(self::$loadedPlugins[$key] instanceof Registry)
		){
			$pluginP = PluginHelper::getPlugin($plugin[0], $plugin[1]);

			if (!empty($pluginP->params))
			{
				self::$loadedPlugins[$key] = new Registry($pluginP->params);
				self::$loadedPlugins[$key]->set('isEnabled', 1);
				self::$loadedPlugins[$key]->set('isInstalled', 1);
			}
			// Falls deaktiviert, DB
			elseif (\is_file(
				JPATH_PLUGINS . '/' . implode('/', $plugin) . '/' . $plugin[1]
					. '.php')
			){
				$db = Factory::getDbo();
				$query = $db->getQuery(true)
					->select('params')
					->from('#__extensions')
					->where('type = ' . $db->q('plugin'))
					->where('element = ' . $db->q($plugin[1]))
					->where('folder = ' . $db->q($plugin[0]));
				$db->setQuery($query);
				$params = $db->loadResult();

				if (!empty($params))
				{
					self::$loadedPlugins[$key] = new Registry($params);
					self::$loadedPlugins[$key]->set('isEnabled', 0);
					self::$loadedPlugins[$key]->set('isInstalled', 1);
				}
			}
			// Falls erfolglos
			else
			{
				self::$loadedPlugins[$key] = new Registry();
				self::$loadedPlugins[$key]->set('isEnabled', -1);
				self::$loadedPlugins[$key]->set('isInstalled', 0);
			}
		}
		return self::$loadedPlugins[$key];
	}

	public static function prepareDefaultOptions(string $sig) : Array
	{
		if (!isset(static::$loaded[__METHOD__][$sig]))
		{
			$plgParams = self::getPluginParams(array('system', 'venoboxghsvs'));
			$options_default = [];
			$configs = $plgParams->get('configs');

			if (is_object($configs) && count(get_object_vars($configs)))
			{
				foreach ($configs as $config)
				{
					/* Aufpassen! $config wird referenziert in foreach. Deshalb weiters
					nicht direkt hier verändern! Das führt zu Missverständnissen bei
					Bool-Versus-String-Werten. */

					if ($config->active !== 1)
					{
						continue;
					}

					if (
						($config->parameter = trim($config->parameter))
						&& ($config->value = trim($config->value))
					){
						if (strtolower($config->value === 'false'))
						{
							$options_default[$config->parameter] = false;
						}
						elseif (strtolower($config->value === 'true'))
						{
							$options_default[$config->parameter] = true;
						}
						else
						{
							$options_default[$config->parameter] = $config->value;
						}
					}
				}
			}

			static::$loaded[__METHOD__][$sig] = $options_default;
		}

		return static::$loaded[__METHOD__][$sig];
	}

	/*
	Da ich im Ordner /Html einen Split zwischen Joomla 3 und 4 Datei gemacht habe.
	*/
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

		$js = 'document.addEventListener("DOMContentLoaded", (event) => {'
			. 'new VenoBox(' . json_encode($options) . ')});';

		Factory::getDocument()->addScriptDeclaration($js);
		static::$loaded[__METHOD__][$sig] = 1;
	}
}
