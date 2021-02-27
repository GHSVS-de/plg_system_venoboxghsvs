const Fs = require('fs-extra');
const util = require("util");
const rimRaf = util.promisify(require("rimraf"));
const chalk = require('chalk');

const Manifest = "./package/venoboxghsvs.xml";

const {
	author,
	creationDate,
	copyright,
	filename,
	name,
	version,
	licenseLong,
	minimumPhp,
	maximumPhp,
	minimumJoomla,
	maximumJoomla,
	allowDowngrades,
} = require("./package.json");

(async function exec()
{
	// Remove old folders.
  await rimRaf("./dist");
  await rimRaf("./package");

	const source = `${__dirname}/node_modules/venobox`;
	const target = `${__dirname}/src/media`;
	await rimRaf(`${target}/_venobox-version`);
  await Fs.copy(
		`${source}/venobox/venobox.js`,
		`${target}/js/venobox/venobox.js`
	);
  await Fs.copy(
		`${source}/venobox/venobox.min.js`,
		`${target}/js/venobox/venobox.min.js`
	);
  await Fs.copy(
		`${source}/venobox/venobox.css`,
		`${target}/css/venobox/venobox.css`
	);
  await Fs.copy(
		`${source}/venobox/venobox.min.css`,
		`${target}/css/venobox/venobox.min.css`
	);
  await Fs.copy(
		`${source}/LICENSE`,
		`${target}/LICENSE_venobox.txt`
	);
	await Fs.mkdir(`${target}/_venobox-version`);
	const sourceInfos = JSON.parse(Fs.readFileSync(`${source}/package.json`).toString());
	Fs.writeFileSync(
		`${target}/_venobox-version/version.txt`, sourceInfos.version, { encoding: "utf8" }
	);

	// Copy and create new work dir.
  await Fs.copy("./src", "./package");
	await Fs.mkdir("./dist");

  let xml = await Fs.readFile(Manifest, { encoding: "utf8" });
	xml = xml.replace(/{{name}}/g, name);
	xml = xml.replace(/{{nameUpper}}/g, name.toUpperCase());
	xml = xml.replace(/{{authorName}}/g, author.name);
	xml = xml.replace(/{{creationDate}}/g, creationDate);
	xml = xml.replace(/{{copyright}}/g, copyright);
	xml = xml.replace(/{{licenseLong}}/g, licenseLong);
	xml = xml.replace(/{{authorUrl}}/g, author.url);
	xml = xml.replace(/{{version}}/g, version);
	xml = xml.replace(/{{minimumPhp}}/g, minimumPhp);
	xml = xml.replace(/{{maximumPhp}}/g, maximumPhp);
	xml = xml.replace(/{{minimumJoomla}}/g, minimumJoomla);
	xml = xml.replace(/{{maximumJoomla}}/g, maximumJoomla);
	xml = xml.replace(/{{allowDowngrades}}/g, allowDowngrades);
	xml = xml.replace(/{{filename}}/g, filename);
	xml = xml.replace(/{{venoboxVersion}}/g, sourceInfos.version);

  await Fs.writeFile(Manifest, xml, { encoding: "utf8" }).then(
		answer => console.log(`Replaced entries in ${Manifest}.`)
	);

  // Package it
  const zip = new (require("adm-zip"))();
	const zipFilePath = `dist/${name}-${version}_${sourceInfos.version}.zip`;
  zip.addLocalFolder("package", false);
  zip.writeZip(`${zipFilePath}`);
	console.log(chalk.green(`${zipFilePath} written.`));
})();
