const fse = require('fs-extra');
const chalk = require('chalk');
const path = require("path");
const replaceXml = require('./build/replaceXml.js');
const helper = require('./build/helper.js');

const {
	name,
	filename,
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

	versionSub = await helper.findVersionSub (
		path.join(source, `package.json`),
		'Venobox');

	console.log(chalk.magentaBright(`versionSub identified as: "${versionSub}"`));

	await fse.copy(`${source}/venobox/venobox.js`,
		`${target}/js/venobox/venobox.js`
	).then(
		answer => console.log(chalk.yellowBright(
			`Copied unminified "venobox.js" into "${target}".`))
	);

	await fse.copy(`${source}/venobox/venobox.min.js`,
		`${target}/js/venobox/venobox.min.js`
	).then(
		answer => console.log(chalk.yellowBright(
			`Copied minified "venobox.min.js" into "${target}".`))
	);

	await fse.copy(`${source}/venobox/venobox.css`,
		`${target}/css/venobox/venobox.css`
	).then(
		answer => console.log(chalk.yellowBright(
			`Copied unminified "venobox.css" into "${target}".`))
	);

	await fse.copy(`${source}/venobox/venobox.min.css`,
		`${target}/css/venobox/venobox.min.css`
	).then(
		answer => console.log(chalk.yellowBright(
			`Copied minified "venobox.min.js" into "${target}".`))
	);

	await fse.copy(`${source}/LICENSE`,
		`${target}/LICENSE_venobox.txt`
	).then(
		answer => console.log(chalk.yellowBright(
			`Copied Venobox "LICENSE" to "${target}/LICENSE_venobox.txt".`))
	);

	await fse.copy("./src", "./package"
	).then(
		answer => console.log(chalk.yellowBright(`Copied "./src" to "./package".`))
	);

	await fse.copy("./media", "./package/media"
	).then(
		answer => console.log(chalk.yellowBright(`Copied "./media" to "./package".`))
	);

	if (!(await fse.exists("./dist")))
	{
		await fse.mkdir("./dist"
		).then(
			answer => console.log(chalk.yellowBright(`Created "./dist".`))
		);
  }

	const zipFilename = `${name}-${version}_${versionSub}.zip`;

	await replaceXml.main(Manifest, zipFilename);
	await fse.copy(`${Manifest}`, `./dist/${manifestFileName}`).then(
		answer => console.log(chalk.yellowBright(
			`Copied "${manifestFileName}" to "./dist".`))
	);

	// Create zip file and detect checksum then.
	const zipFilePath = `./dist/${zipFilename}`;

	const zip = new (require('adm-zip'))();
	zip.addLocalFolder("package", false);
	await zip.writeZip(`${zipFilePath}`);
	console.log(chalk.cyanBright(chalk.bgRed(
		`./dist/${zipFilename} written.`)));

	const Digest = 'sha256'; //sha384, sha512
	const checksum = await helper.getChecksum(zipFilePath, Digest)
  .then(
		hash => {
			const tag = `<${Digest}>${hash}</${Digest}>`;
			console.log(chalk.greenBright(`Checksum tag is: ${tag}`));
			return tag;
		}
	)
	.catch(error => {
		console.log(error);
		console.log(chalk.redBright(`Error while checksum creation. I won't set one!`));
		return '';
	});

	let xmlFile = 'update.xml';
	await fse.copy(`./${xmlFile}`, `./dist/${xmlFile}`).then(
		answer => console.log(chalk.yellowBright(
			`Copied "${xmlFile}" to ./dist.`))
	);
	await replaceXml.main(`${__dirname}/dist/${xmlFile}`, zipFilename, checksum);

	xmlFile = 'changelog.xml';
	await fse.copy(`./${xmlFile}`, `./dist/${xmlFile}`).then(
		answer => console.log(chalk.yellowBright(
			`Copied "${xmlFile}" to ./dist.`))
	);
	await replaceXml.main(`${__dirname}/dist/${xmlFile}`, zipFilename, checksum);

	xmlFile = 'release.txt';
	await fse.copy(`./${xmlFile}`, `./dist/${xmlFile}`).then(
		answer => console.log(chalk.yellowBright(
			`Copied "${xmlFile}" to ./dist.`))
	);
	await replaceXml.main(`${__dirname}/dist/${xmlFile}`, zipFilename, checksum);

	cleanOuts = [
		`./package`
	];
	await helper.cleanOut(cleanOuts).then(
		answer => console.log(chalk.cyanBright(chalk.bgRed(
			`Finished. Good bye!`)))
	);

})();
