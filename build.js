#!/usr/bin/env node
const path = require('path');

/* Configure START */
const pathBuildKram = path.resolve("../buildKramGhsvs");
const updateXml = `${pathBuildKram}/build/update_no-changelog.xml`;
//const changelogXml = `${pathBuildKram}/build/changelog.xml`;
const releaseTxt = `${pathBuildKram}/build/release_no-changelog.txt`;
/* Configure END */

const replaceXml = require(`${pathBuildKram}/build/replaceXml.js`);
const helper = require(`${pathBuildKram}/build/helper.js`);

const pc = require(`${pathBuildKram}/node_modules/picocolors`);
// const fse = require(`${pathBuildKram}/node_modules/fs-extra`);

let replaceXmlOptions = {
	"xmlFile": "",
	"zipFilename": "",
	"checksum": "",
	"dirname": __dirname,
	"jsonString": "",
	"versionSub": ""
};
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
const target = `${__dirname}/package/media`;
let versionSub = '';

(async function exec()
{
	let cleanOuts = [
		`./package`,
		`./dist`,
		`${target}/_venobox-version`
	];
	await helper.cleanOut(cleanOuts);

	await helper.mkdir('./package');
	await helper.mkdir('./dist');

	versionSub = await helper.findVersionSubSimple (
		path.join(source, `package.json`),
		'Venobox');
	console.log(pc.magenta(pc.bold(`versionSub identified as: "${versionSub}"`)));

	replaceXmlOptions.versionSub = versionSub;

	from = `./media`;
	to = target;
	await helper.copy(from, to)

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

	await helper.gzip([target]);

	from = path.resolve('package', 'media', 'joomla.asset.json');
	replaceXmlOptions.xmlFile = from;
	await replaceXml.main(replaceXmlOptions);
	await helper.copy(from, `./dist/joomla.asset.json`);


	const zipFilename = `${name}-${version}_${versionSub}.zip`;

	replaceXmlOptions.xmlFile = Manifest;
	replaceXmlOptions.zipFilename = zipFilename;

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
	for (const file of [updateXml, releaseTxt])
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
