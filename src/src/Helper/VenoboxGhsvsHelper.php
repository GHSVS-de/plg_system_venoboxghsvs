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

	protected static $plgParams;

	protected static $isJ3 = true;

	protected static $wa = null;

	protected static function init()
	{
		self::$isJ3 = version_compare(JVERSION, '4', 'lt');
		self::$plgParams = self::getPluginParams();
	}

	/*
	Ich habe nicht die leiseste Idee, warum ich diesen Kram verwenden muss(!), um
	an die Plugin-params dran zu kommen. im Plugin selbst ist $this->params leer,
	an der Stelle, wo ich das gerne abfragen würde.
	Irgendeinen Grund wirds schon geben ;-)
	A: Ich denke mal, weil der Einstiegspunkt der JHtml-Helfer ist, der ja auch
	ohne aktives Plugin verwendet werden kann. Irgend so was...
	*/
	public static function getPluginParams($plugin = ['system', 'venoboxghsvs'])
	{
		if (empty(self::$plgParams) || !(self::$plgParams instanceof Registry))
		{
			self::$plgParams = PluginHelper::getPlugin($plugin[0], $plugin[1]);

			if (!empty(self::$plgParams->params))
			{
				self::$plgParams = new Registry(self::$plgParams->params);
				self::$plgParams->set('isEnabled', 1);
				self::$plgParams->set('isInstalled', 1);
			}
			elseif (\is_file(JPATH_PLUGINS . '/' . implode('/', $plugin) . '/' . $plugin[1] . '.php'))
			{
				$db = Factory::getDbo();
				$query = $db->getQuery(true)
					->select('params')
					->from('#__extensions')
					->where('type = ' . $db->q('plugin'))
					->where('element = ' . $db->q($plugin[1]))
					->where('folder = ' . $db->q($plugin[0]));
				$db->setQuery($query);
				self::$plgParams = $db->loadResult();

				if (!empty(self::$plgParams->params))
				{
					self::$plgParams = new Registry(self::$plgParams->params);
					self::$plgParams->set('isEnabled', 0);
					self::$plgParams->set('isInstalled', 1);
				}
			}

			if (empty(self::$plgParams))
			{
				self::$plgParams = new Registry();
				self::$plgParams->set('isEnabled', -1);
				self::$plgParams->set('isInstalled', 0);
			}
		}

		return self::$plgParams;
	}

	public static function prepareDefaultOptions(string $sig) : Array
	{
		if (!isset(static::$loaded[__METHOD__][$sig]))
		{
			$plgParams = self::getPluginParams();
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

		$pluginParams = self::getPluginParams();

		if (!isset($options['selector']))
		{
			if (!($options['selector'] = $selector))
			{
				$selector = trim($pluginParams->get('selector', ''));
				$options['selector'] = $selector ?: '.venobox';
			}
		}
		// END B\C shit.

		$sig = $options['selector'];

		if (isset(static::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		$wa = self::getWa();

		if (!isset(static::$loaded[__METHOD__]['core']))
		{
			if ($wa)
			{
				$wa->usePreset('plg_system_venoboxghsvs.all');
			}
			else
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
			}
			static::$loaded[__METHOD__]['core'] = 1;
		}

		$options = \array_merge(
			self::prepareDefaultOptions($sig),
			$options
		);

		$js = 'document.addEventListener("DOMContentLoaded", (event) => {'
			. 'new VenoBox(' . json_encode($options) . ')});';

		if ($wa)
		{
			$wa->addInline(
				'script',
				$js,
				['name' => 'plg_system_venoboxghsvs.' . 'selector-' . str_replace(' ', '', $sig)]
			);
		}
		else
		{
			Factory::getDocument()->addScriptDeclaration($js);
		}

		static::$loaded[__METHOD__][$sig] = 1;
	}

	public static function getWa()
	{
		self::init();

		if (self::$isJ3 === false && empty(self::$wa))
		{
			self::$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
			self::$wa->getRegistry()->addExtensionRegistryFile('plg_system_venoboxghsvs');
		}

		return self::$wa;
	}
}
