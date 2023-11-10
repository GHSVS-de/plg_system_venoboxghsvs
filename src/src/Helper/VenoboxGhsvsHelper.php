<?php

namespace GHSVS\Plugin\System\VenoboxGhsvs\Helper;

\defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;

class VenoboxGhsvsHelper
{
	protected static $loaded = [];
	protected static $plgParams;

	protected static $wa = null;

	protected function init()
	{
		self::$plgParams = $this->getPluginParams();
	}

	/*
	Ich habe nicht die leiseste Idee, warum ich diesen Kram verwenden muss(!), um
	an die Plugin-params dran zu kommen. im Plugin selbst ist $this->params leer,
	an der Stelle, wo ich das gerne abfragen würde.
	Irgendeinen Grund wirds schon geben ;-)
	A: Ich denke mal, weil der Einstiegspunkt der JHtml-Helfer ist, der ja auch
	ohne aktives Plugin verwendet werden kann. Irgend so was...
	A2: Ich glaube A: ist hinfällig mit Joomla 4.3+/Joomla 5????
	*/
	public function getPluginParams($plugin = ['system', 'venoboxghsvs'])
	{
		if (empty(self::$plgParams) || !(self::$plgParams instanceof Registry))
		{
			$model = Factory::getApplication()->bootComponent('plugins')
				->getMVCFactory()->createModel('Plugin', 'Administrator', ['ignore_request' => true]);
			$pluginObject = $model->getItem(['folder' => $plugin[0], 'element' => $plugin[1]]);

			if (!\is_object($pluginObject) || empty($pluginObject->params))
			{
				self::$plgParams = new Registry();
				self::$plgParams->set('isEnabled', -1);
				self::$plgParams->set('isInstalled', 0);
			}
			elseif (!($pluginObject->params instanceof Registry))
				{
				self::$plgParams = new Registry($pluginObject->params);
				self::$plgParams->set('isEnabled', ($pluginObject->enabled ? 1 : 0));
					self::$plgParams->set('isInstalled', 1);
				}
			}
		return self::$plgParams;
	}

	public function prepareDefaultOptions(string $sig) : Array
	{
		if (!isset(static::$loaded[__METHOD__][$sig]))
		{
			$options_default = [];
			$configs = self::$plgParams->get('configs');

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

	public function venobox($selector = null, $options = [])
	{
		$this->getWa();

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

		if (!isset($options['selector']))
		{
			if (!($options['selector'] = $selector))
			{
				$selector = trim(self::$plgParams->get('selector', ''));
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
			self::$wa->usePreset('plg_system_venoboxghsvs.all');
			static::$loaded[__METHOD__]['core'] = 1;
		}

		$options = \array_merge(
			$this->prepareDefaultOptions($sig),
			$options
		);

		$js = 'document.addEventListener("DOMContentLoaded", (event) => {'
			. 'new VenoBox(' . json_encode($options) . ')});';

		self::$wa->addInline(
			'script',
			$js,
			['name' => 'plg_system_venoboxghsvs.' . 'selector-' . str_replace(' ', '', $sig)]
		);

		static::$loaded[__METHOD__][$sig] = 1;
	}

	public function getWa()
	{
		$this->init();

		if (empty(self::$wa))
		{
			self::$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
			self::$wa->getRegistry()->addExtensionRegistryFile('plg_system_venoboxghsvs');
		}

		return self::$wa;
	}
}
