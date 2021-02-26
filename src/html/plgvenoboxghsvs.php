<?php
/*
Aufruf via:
HTMLHelper::_('plgvenoboxghsvs.venobox');
*/

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

class JHtmlPlgvenoboxghsvs
{
	protected static $loaded = array();
	protected static $loadedPlugins = array();

	// media-Ordner:
	protected static $basepath = 'plg_system_venoboxghsvs/venobox';

	public static function venobox($selector = '.venobox', $options = array())
	{
		if (!($selector = trim($selector)) || !is_array($options))
		{
			return;
		}

		$plgParams = self::getPluginParams(array('system', 'venoboxghsvs'));

		$options_default = array(
			'titleattr' => 'data-title',
			'titleBackground' => '#fff',
			'titleColor' => '#000',
			'closeBackground' => '#fff',
			'closeColor' => '#f00',
			//'infinigall' => false,
			'arrowsColor' => "#000000",
			'ready_or_load' => $plgParams->get('ready_or_load', 'ready')
		);

		// Don't use in JS configuration.
		$removeForVenobox = array(
			'ready_or_load',
		);

		if (empty(static::$loaded[__METHOD__]['core']))
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

		$sig = md5($selector);

		if (empty(static::$loaded[__METHOD__][$sig]))
		{
			$options = array_merge($options_default, $options);
			$ready_or_load = $options['ready_or_load'] === 'ready'
				? 'jQuery(document).ready(' : 'jQuery(window).on("load",';
			$options = array_diff_key($options, array_flip($removeForVenobox));

			$js = $ready_or_load
				. 'function(){jQuery("' . $selector . '").venobox('
				. json_encode($options) . ');});';

			Factory::getDocument()->addScriptDeclaration($js);
			static::$loaded[__METHOD__][$sig] = 1;
		}
		return;
	}

	/*
	 * Lädt auch Params aus nicht aktivierten Plugins.
	 * Fügt den Params isEnabled- und isInstalled-Parameter hinzu.
	*/
	protected static function getPluginParams($plugin = null)
	{
		if (!is_array($plugin) || count($plugin) != 2)
		{
			return false;
		}

		$key = implode('', $plugin);

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
			elseif (
				file_exists(JPATH_SITE . '/plugins/' . implode('/', $plugin) . '/'
					. $plugin[1] . '.php')
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
}
