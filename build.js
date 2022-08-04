#!/usr/bin/env node
const path = require('path');

/* Configure START */
const pathBuildKram = path.resolve("../buildKramGhsvs");
const updateXml = `${pathBuildKram}/build/update.xml`;
const changelogXml = `${pathBuildKram}/build/changelog.xml`;
const releaseTxt = `${pathBuildKram}/build/release.txt`;
/* Configure END */

const replaceXml = require(`${pathBuildKram}/build/replaceXml.js`);
const helper = require(`${pathBuildKram}/build/helper.js`);

const pc = require(`${pathBuildKram}/node_modules/picocolors`);
// const fse = require(`${pathBuildKram}/node_modules/fs-extra`);

let replaceXmlOptions = {};
let zipOptions = {};
let from = "";
let to = "";

const {
	filename,
	name,
	version,
} = require("./package.json");

const manifestFileName = `${filename}.xml`;
const Manifest = `${__dirname}/package/${manifestFileName}`;
const source = `${__dirname}/node_modules/venobox`;
const target = `./media`;
let versionSub = '';

(async function exec()
{
	let cleanOuts = [
		`./package`,
		`./dist`,
		`${target}/_venobox-version`
	];
	await helper.cleanOut(cleanOuts);

	versionSub = await helper.findVersionSubSimple (
		path.join(source, `package.json`),
		'Venobox');
	console.log(pc.magenta(pc.bold(`versionSub identified as: "${versionSub}"`)));

	from = `${source}/dist/venobox.js`
	to = `${target}/js/venobox/venobox.js`
	await helper.copy(from, to);

	from = `${source}/dist/venobox.min.js`
	to = `${target}/js/venobox/venobox.min.js`
	await helper.copy(from, to);

	from = `${from}.map`
	to = `${to}.map`
	await helper.copy(from, to);

	from = `${source}/dist/venobox.css`
	to = `${target}/css/venobox/venobox.css`
	await helper.copy(from, to);

	from = `${source}/dist/venobox.min.css`
	to = `${target}/css/venobox/venobox.min.css`
	await helper.copy(from, to);

	from = `${from}.map`
	to = `${to}.map`
	await helper.copy(from, to);

	from = `${source}/LICENSE`
	to = `${target}/LICENSE_venobox.txt`
	await helper.copy(from, to);

	from = `./src`;
	to = `./package`;
	await helper.copy(from, to);

	from = "./media";
	to = "./package/media";
	await helper.copy(from, to);

	await helper.gzip([to]);

	await helper.mkdir('./dist');

	const zipFilename = `${name}-${version}_${versionSub}.zip`;

	replaceXmlOptions = {
		"xmlFile": Manifest,
		"zipFilename": zipFilename,
		"checksum": "",
		"dirname": __dirname
	};

	await replaceXml.main(replaceXmlOptions);

	from = Manifest;
	to = `./dist/${manifestFileName}`;
	await helper.copy(from, to);

	// ## Create zip file and detect checksum then.
	const zipFilePath = path.resolve(`./dist/${zipFilename}`);

	zipOptions = {
		"source": path.resolve("package"),
		"target": zipFilePath
	};
	await helper.zip(zipOptions)

	replaceXmlOptions.checksum = await helper._getChecksum(zipFilePath);

	// Bei diesen werden zuerst Vorlagen nach dist/ kopiert und dort erst "replaced".
	for (const file of [updateXml, changelogXml, releaseTxt])
	{
		from = file;
		to = `./dist/${path.win32.basename(file)}`;
		await helper.copy(from, to)

		replaceXmlOptions.xmlFile = path.resolve(to);
		await replaceXml.main(replaceXmlOptions);
	}

	cleanOuts = [
		`./package`,
	];
	await helper.cleanOut(cleanOuts).then(
		answer => console.log(pc.cyan(pc.bold(pc.bgRed(
			`Finished. Good bye!`))))
	);
})();
