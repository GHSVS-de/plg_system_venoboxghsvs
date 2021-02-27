# plg_system_venoboxghsvs

- Uses VenoBox library (https://github.com/nicolafranchini/VenoBox)
- Integrates a Venobox JHtml helper.
- - Loads standard CSS and JS.

Venobox js requires:
- jQuery >= 1.7.0

## After updates of older versions than V2021.02.27
- Save the plugin once afterwards!!

## Usage:

- Create one or more `<a>` with `class=venobox` that shall open in a lightbox popup.
- As href attribute use the image path.

```
<a href="path/to/image.jpg" class="venobox">
	Click me to open a fantastic image in pop-up
</a>
```
- Add PHP snippet in your Joomla code, e.g. in template.

```
Joomla\CMS\HTML\HTMLHelper::_('plgvenoboxghsvs.venobox');
```
or
```
JHtml::_('plgvenoboxghsvs.venobox');
```
### Configuration
- Set your favorite default options in plugin configuration.
- More possible venobox options can be found below headline "others" on https://veno.es/venobox.
- All options are optional.
- You can also use the `JHtml` call to pass over options.

#### Example for other selector than `.venobox`

```
JHtml::_('plgvenoboxghsvs.venobox'
	[
		'selector' => '.myCustomSelector'
	]
);
```

```
<a href="path/to/image.jpg" class="myCustomSelector">
	Click me to open a fantastic image in pop-up
</a>
```
#### Example for other selector than `.venobox` plus individual configuration


```
JHtml::_('plgvenoboxghsvs.venobox'
	[
		'selector' => '.anotherCustomSelector',
		'titleattr' => 'data-title',
		'titleBackground' => '#fff',
		'titleColor' => '#000',
		'closeBackground' => '#fff',
		'closeColor' => '#f00',
		'arrowsColor' => "#000000",
		// Shall JQuery load on 'ready' or on 'load'. Default: 'ready'.
		'ready_or_load' => $plgParams->get('ready_or_load', 'ready')
	]
);
```

```
<a href="path/to/image.jpg" class="anotherCustomSelector">
	Click me to open a fantastic image in pop-up
</a>
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
