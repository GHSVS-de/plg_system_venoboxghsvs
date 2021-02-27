<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;

abstract class VenoboxGhsvsHelper
{
	protected static $loadedPlugins = [];
	protected static $loaded = [];

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

			if (!isset($options_default['ready_or_load']))
			{
				$options_default['ready_or_load'] = 'ready';
			}

			static::$loaded[__METHOD__][$sig] = $options_default;
		}

		return static::$loaded[__METHOD__][$sig];
	}
}
