# Joomla plugin plg_system_venoboxghsvs-v2plus aka plg_system_venoboxghsvs

- Uses VenoBox library (https://github.com/nicolafranchini/VenoBox) version 2+
- Integrates a Venobox JHtml/HTMLHelper helper.
  - Loads VB standard CSS and JS.

## In advance. For updaters using VB versions 1.
- You can update from earlier versions of my Joomla plugin (that used Venobox v1.x), but you should know that
  - my plugin does not catch outdated 1.x configuration parameters. They are going nowhere.
  - you don't get any automated update hint in Joomla backend for the step 1.x to 2.x. Simply install over it!
- Since version 2021.12.21 this Joomla plugin uses Venobox version 2.x+ which doesn't require JQuery anymore.
- Configuration settings in Venobox have changed. Some have been renamed. Some others were removed.
  - See possible options on https://veno.es/venobox
- I have removed all JQuery parts including the experimental JQuery-slim feature from the code of `plg_system_venoboxghsvs`.
  - If you used the JQuery-Slim feature: This means that the associated CSS should also be removed if you used or created any.
- If you are looking for the old README for plugin versions with Venobox versions 1.x: https://github.com/GHSVS-de/plg_system_venoboxghsvs/tree/venobox-v1
  - If you're searching for a version of this Joomla plugin that uses Venobox 1.x+: https://github.com/GHSVS-de/plg_system_venoboxghsvs/releases/tag/2021.09.14

### After updates of older versions than V2021.02.27
- Save the plugin once afterwards!!

## German description/Deutsche Beschreibung
https://ghsvs.de/programmierer-schnipsel/joomla/294-jhtml-htmlhelper-helper-fuer-venobox-in-joomla

## Usage:

- Create one or more `<a>` with `class=venobox` that shall open in a lightbox popup.
- As href attribute use the image path.

```
<a href="path/to/image.jpg" class="venobox">
	Click me to open a fantastic image in pop-up
</a>
```
- Add PHP snippet in your Joomla code, e.g. in template index.php.

```
Joomla\CMS\HTML\HTMLHelper::_('plgvenoboxghsvs.venobox');
```
or
```
Joomla\CMS\HTML\HTMLHelper::_('plgvenoboxghsvs.venobox');
```
### Configuration
- Set your favorite default options in plugin configuration.
- More possible venobox options can be found below headline "others" on https://veno.es/venobox.
- All options are optional.
- You can also use the `JHtml`/`HTMLHelper` call to pass over options.

#### Example for other selector than `.venobox`

```
Joomla\CMS\HTML\HTMLHelper::_('plgvenoboxghsvs.venobox'
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
Joomla\CMS\HTML\HTMLHelper::_('plgvenoboxghsvs.venobox'
	[
		'selector' => '.anotherCustomSelector',
		'titleattr' => 'data-title',
		'numeration' => true,
		'infinigall' => true,
		'share' => true
	]
);
```

```
<a href="path/to/image.jpg" class="anotherCustomSelector" data-title="Hello image 1"
	data-gall="gallery01">
	Click me to open a fantastic image in gallery pop-up
</a>
<a href="path/to/image2.jpg" class="anotherCustomSelector" data-title="Hello image 2"
	data-gall="gallery01">
	Click me to open a fantastic image 2 in gallery pop-up
</a>
```

-----------------------------------------------------

# My personal build procedure (WSL 1 or 2, Debian, Win 10)

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

### For Joomla update server
- Create new release with new tag.
  - See and copy and complete release description in `dist/release_no-changelog.txt`.
- Extracts(!) of the update XML for update servers are in `./dist` as well. Copy/paste and make necessary additions.
