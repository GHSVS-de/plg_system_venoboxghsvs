# plg_system_venoboxghsvs

- Load VenoBox library (https://github.com/nicolafranchini/VenoBox)
- Integrate Venobox JHtml helper.
- - Loads standard CSS and JS.

Last tests:
- V2019.06.11
- Joomla 4.0.0-dev
- Joomla 3.9.9-dev
- PHP7.3
- Only installation, updates and script loading tested.

Venobox js requires:
- jQuery >= 1.7.0

Usage:

- Create one or more image with CSS class=venobox that shall open in a lightbox popup.

- Add PHP to your Joomla, e.g. in template.

```
Joomla\CMS\HTML\HTMLHelper::_('plgvenoboxghsvs.venobox', $selector = '.venobox', $options = array());
```
or
```
JHtml::_('plgvenoboxghsvs.venobox', $selector = '.venobox', $options = array());
```

Optional options (See others at http://veno.es/venobox/).

```
$options_default = array(
	'titleattr' => 'data-title',
	'titleBackground' => '#fff',
	'titleColor' => '#000',
	'closeBackground' => '#fff',
	'closeColor' => '#f00',
	//'infinigall' => false,
	'arrowsColor' => "#000000",
	// Shall JQuery load on 'ready' or on 'load'. Default: 'ready'.
	'ready_or_load' => $plgParams->get('ready_or_load', 'ready')
); 
```

I you don't want to activate the plugin include the HTMLHelper class yourself before you use above mentioned calls

```
Joomla\CMS\HTML\HTMLHelper::addIncludePath(JPATH_SITE . '/plugins/system/venoboxghsvs/html');
```
