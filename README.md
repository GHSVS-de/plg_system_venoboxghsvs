# plg_system_venoboxghsvs
Use at your own risk! Work in progress.

- Load VenoBox library (https://github.com/nicolafranchini/VenoBox)
- Integrate Venobox JHtml helper.
- - Loads standard CSS and JS.

Last tests:
- V2019.06.11
- Joomla 4.0.0-dev
- Joomla 3.9.7-dev
- PHP7.3
- Only installation, updates and script loading tested.

Usage:

- Create one or more image with CSS class=venobox that shall open in a lightbox popup.

- Add PHP to your Joomla, e.g. in template.

```
use Joomla\CMS\HTML\HTMLHelper;
HTMLHelper::_('plgvenoboxghsvs.venobox', $selector = '.venobox', $options = array());
```
or
```
JHtml::_('plgvenoboxghsvs.venobox', $selector = '.venobox', $options = array());
```

Optional options

```
$options_default = array(
	'titleattr' => 'data-title',
	'titleBackground' => '#fff',
	'titleColor' => '#000',
	'closeBackground' => '#fff',
	'closeColor' => '#f00',
	//'infinigall' => false,
	'arrowsColor' => "#000000",
	'developer_mode' => $plgParams->get('developer_mode', 0), // Make uniqid() version 
	'ready_or_load' => $plgParams->get('ready_or_load', 'ready') // JQuery default:ready or 'load'
);
```
See others at http://veno.es/venobox/
