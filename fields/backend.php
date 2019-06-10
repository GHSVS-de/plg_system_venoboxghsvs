<?php
defined('JPATH_PLATFORM') or die;

class plgsystemvenoboxghsvsFormFieldbackend extends JFormField
{
	protected $type = 'backend';
	protected function getInput()
	{
		JFactory::getDocument()->addStylesheet(JUri::root() . 'media/plg_system_venoboxghsvs/css/backend.css');
		//return $js;
	}
}
