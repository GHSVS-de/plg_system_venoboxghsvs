<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
#use Joomla\CMS\User\UserFactoryInterface;
#use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use GHSVS\Plugin\System\VenoboxGhsvs\Extension\VenoboxGhsvs;
#use GHSVS\Plugin\System\OnUserGhsvs\Helper\OnUserGhsvsHelper;
use Joomla\CMS\HTML\Registry;

return new class () implements ServiceProviderInterface
{
	public function register(Container $container): void
	{
		$container->set(
			PluginInterface::class,
			function (Container $container)
			{
				$app = Factory::getApplication();
				$dispatcher = $container->get(DispatcherInterface::class);
				$plugin = new VenoboxGhsvs(
					$dispatcher,
					(array) PluginHelper::getPlugin('system', 'venoboxghsvs')
				);
				$plugin->setApplication($app);
				$plugin->setRegistry($container->get(Registry::class));
				#$plugin->setDatabase($container->get(DatabaseInterface::class));
				#$plugin->setUserFactory($container->get(UserFactoryInterface::class));

				return $plugin;
			}
		);
	}
};
