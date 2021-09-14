# plg_system_venoboxghsvs for Joomla

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

# My personal build procedure (WSL 1, Debian, Win 10)
- - If new Venobox release out adapt your custom venobox-jquery-slim.js.

- Prepare/adapt `./package.json`.
- `cd /mnt/z/git-kram/plg_system_venoboxghsvs`

## node/npm updates/installation
- `npm run g-npm-update-check` or (faster) `ncu`
- `npm run g-ncu-override-json` (if needed) or (faster) `ncu -u`
- `npm install` (if needed)

## Build installable ZIP package
- `node build.js`
- New, installable ZIP is in `./dist` afterwards.
- All packed files for this ZIP can be seen in `./package`. **But only if you disable deletion of this folder at the end of `build.js`**.

### For Joomla update and changelog server
- Create new release with new tag.
- - See and copy and complete release description in `dist/release.txt`.
- Extracts(!) of the update and changelog XML for update and changelog servers are in `./dist` as well. Copy/paste and make necessary additions.
