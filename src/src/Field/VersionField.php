<?php
namespace GHSVS\Plugin\System\VenoboxGhsvs\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Exception;

class VersionField extends FormField
{
	protected $type = 'Version';

	protected function getInput()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
		->select($db->qn('manifest_cache'))->from($db->qn('#__extensions'))
		->where($db->qn('extension_id') . '='
		. (int) Factory::getApplication()->input->get('extension_id'))
		;
		$db->setQuery($query);

		try
		{
			$manifest = $db->loadResult();
		}
		catch (Exception $e)
		{
			return '';
		}
		$manifest = @json_decode($manifest);
		$version = isset($manifest->version) ? $manifest->version : Text::_('JLIB_UNKNOWN');
		#$date = isset($manifest->date) ? $manifest->date : Text::_('JLIB_UNKNOWN');

		return $version;
	}
}
