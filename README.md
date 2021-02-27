# plg_system_venoboxghsvs

- Load VenoBox library (https://github.com/nicolafranchini/VenoBox)
- Integrate Venobox JHtml helper.
- - Loads standard CSS and JS.

Last tests:
- Joomla 4.0.0-dev
- Joomla 3.9.20
- PHP7.4
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

## Build
- `cd /mnt/z/git-kram/plg_system_venoboxghsvs/`
- Adapt file `package.json` (version etc.).
- Base/source files are in folder `/src/`.
- Check for updates: `ncu`.
- `npm install`
- `node build.js`

- External libraries like `venobox` are copied to `/src/` now (old files overriden!).
- Files are copied to folder `/package/` afterwards and manifest XML file adapted automatically = Base for ZIP file.
- New ZIP in folder `/dist/` afterwards.
- Only tested with WSL 1/Debian on WIndows 10.

### New release/update for Joomla
- Create new GitHub release/tag.
- You can add extension ZIP file to "Assets" list via Drag&Drop. See "Attach binaries by dropping them here or selecting them.".
