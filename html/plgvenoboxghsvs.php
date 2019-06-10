<?php
/*
Aufruf via:
JHtml::_('plgvenoboxghsvs.venobox');
*/
?>
<?php
defined('JPATH_BASE') or die;
use Joomla\Registry\Registry;

abstract class JHtmlPlgVenoboxghsvs
{
	
	protected static $loaded = array();
	protected static $loadedPlugins = array();
	
	// media-Ordner:
	protected static $basepath = 'plg_system_venoboxghsvs/venobox';

	public static function venobox($selector = '.venobox', $options = array())
	{
		
		$options = (array) $options;

		$plgParams = self::getPluginParams(array('system', 'venoboxghsvs'));

		$options_default = array(
			'titleattr' => 'data-title',
			'titleBackground' => '#fff', 
			'titleColor' => '#000',
			'closeBackground' => '#fff',
			'closeColor' => '#f00',
			//'infinigall' => false,
			'arrowsColor' => "#000000",
			'venoboxVersion' => $plgParams->get('venoboxVersion', '1.8.3'),
			'developer_mode' => $plgParams->get('developer_mode', 0),
			'ready_or_load' => $plgParams->get('ready_or_load', 'ready')
		);
		$removeForVenobox = array(
			'developer_mode',
			'ready_or_load',
			'venoboxVersion'
		);

		$options = array_merge($options_default, $options);

		// Um nach Möglichkeit doppeltes Laden zu verhindern, wenn nur Reihenfolge anders.
		// Nein, weil es keinen Sinn macht, pro Selektor doppelt zu laden.
		// Deshalb auch $sig ohne $options.
		//ksort($options);
		
		$sig = md5($selector);

		if (empty(static::$loaded[__METHOD__ . '_core']))
		{
			$loadOptions = array(
				'framework' => false,
				'pathOnly' => false,
				'detectBrowser' => false,
				'relative' => true,
				'version' => $options['developer_mode'] ? uniqid() : 'auto'
			);

			JHtml::_('jquery.framework');

			$path = static::$basepath . '/' . $options['venoboxVersion'] . '/';

			JHtml::_('stylesheet', $path . 'venobox.css', $loadOptions);
			JHtml::_('script', $path . 'venobox.min.js', $loadOptions);
			
			static::$loaded[__METHOD__ . '_core'] = 1;
		}

		if (empty(static::$loaded[__METHOD__ . '_' . $sig]))
		{
		
			$js = ';/* START plg_system_venoboxghsvs */' . "\n";

			$ready_or_load = $options['ready_or_load'] === 'ready'
				? 'jQuery(document).ready' : 'jQuery(window).load';

			$options = array_diff_key($options, array_flip($removeForVenobox));

			$js .= $ready_or_load . '(function(){jQuery("' . $selector . '").venobox(' . json_encode($options) . ');});';

			JFactory::getDocument()->addScriptDeclaration($js);
			
			
			
			static::$loaded[__METHOD__ . '_' . $sig] = 1;
		}
		return;
	}
	
	/**
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
		
  	if(empty(self::$loadedPlugins[$key]) || !(self::$loadedPlugins[$key] instanceof Registry))
  	{
			$pluginP = JPluginHelper::getPlugin($plugin[0], $plugin[1]);

			if (!empty($pluginP->params))
			{
				self::$loadedPlugins[$key] = new Registry($pluginP->params);
				self::$loadedPlugins[$key]->set('isEnabled', 1);
				self::$loadedPlugins[$key]->set('isInstalled', 1);
			}
			// Falls deaktiviert, DB
			elseif (file_exists(JPATH_SITE . '/plugins/' . implode('/', $plugin) . '/' . $plugin[1] . '.php'))
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
				->select('params')
				->from('#__extensions')
				->where('type = ' . $db->q('plugin'))
				->where('element = ' . $db->q($plugin[1]))
				->where('folder = ' . $db->q($plugin[0]))
				;
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