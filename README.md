# plg_system_venoboxghsvs-v2plus aka plg_system_venoboxghsvs for Joomla

# This README is not up-to-date in detail!

## In advance
- Since version 2021.12.21 this Joomla plugin uses Venobox version 2.x+ which doesn't require JQuery anymore.
- Configuration settings in Venobox have changed. Some have been renamed. Some others were removed.
- - See possible options on https://veno.es/venobox
- I have removed all JQuery parts including the experimental JQuery-slim feature from the code of `plg_system_venoboxghsvs`.
- - If you used the JQuery-Slim feature: This means that the associated CSS should also be removed if you used or created any.
- You can update from earlier versions of my Joomla plugin (that used Venobox v1.x), but you should know that
- - my plugin does not catch outdated 1.x configuration parameters. They are going nowhere.
- - you don't get any automated update hint in Joomla backend for the step 1.x to 2.x. Simply install over it!
- If you are looking for the old README for plugin versions with Venobox versions 1.x: https://github.com/GHSVS-de/plg_system_venoboxghsvs/tree/venobox-v1
- - If you're searching for a version of this Joomla plugin that uses Venobox 1.x+: https://github.com/GHSVS-de/plg_system_venoboxghsvs/releases/tag/2021.09.14


------
# Old old old README (For the most part still correct, but not yet revised in detail.)

- Uses VenoBox library (https://github.com/nicolafranchini/VenoBox)
- Integrates a Venobox JHtml helper.
  - Loads standard CSS and JS.

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

-----------------------------------------------------

# My personal build procedure (WSL 1, Debian, Win 10)

**@since v???: Build procedure uses local repo fork of https://github.com/GHSVS-de/buildKramGhsvs**

- Prepare/adapt `./package.json`.
- `cd /mnt/z/git-kram/plg_system_venoboxghsvs`

## node/npm updates/installation
- `npm install` (if never done before)

### Update dependencies
- `npm run updateCheck` or (faster) `npm outdated`
- `npm run update` (if needed) or (faster) `npm update --save-dev`

## Build installable ZIP package
- `node build.js`
- New, installable ZIP is in `./dist` afterwards.
- All packed files for this ZIP can be seen in `./package`. **But only if you disable deletion of this folder at the end of `build.js`**.

### For Joomla update and changelog server
- Create new release with new tag.
- - See and copy and complete release description in `dist/release.txt`.
- Extracts(!) of the update and changelog XML for update and changelog servers are in `./dist` as well. Copy/paste and make necessary additions.
