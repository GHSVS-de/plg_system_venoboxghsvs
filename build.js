/*const {
  //copy,
  //exists,
  //mkdir,
  //readFile,
  unlink: unl,
  // writeFile,
} = require("fs-extra");*/

const Fs = require('fs-extra');
// const Path = require('path');

const util = require("util");
const rimRaf = util.promisify(require("rimraf"));
const {
	version,
	minimumPhp,
	maximumPhp,
	minimumJoomla,
	maximumJoomla,
	allowDowngrades,
} = require("./package.json");

// const Program = require('commander');
// const RootPath = process.cwd();

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

  let xml = await Fs.readFile("./package/venoboxghsvs.xml", { encoding: "utf8" });
  xml = xml.replace(/{{version}}/g, version);
	xml = xml.replace(/{{minimumPhp}}/g, minimumPhp);
	xml = xml.replace(/{{maximumPhp}}/g, maximumPhp);
	xml = xml.replace(/{{minimumJoomla}}/g, minimumJoomla);
	xml = xml.replace(/{{maximumJoomla}}/g, maximumJoomla);
	xml = xml.replace(/{{allowDowngrades}}/g, allowDowngrades);
	xml = xml.replace(/{{venoboxVersion}}/g, sourceInfos.version);
  Fs.writeFileSync("./package/venoboxghsvs.xml", xml, { encoding: "utf8" });

  // Package it
  const zip = new (require("adm-zip"))();
  zip.addLocalFolder("package", false);
  zip.writeZip(`dist/plg_system_venoboxghsvs-${version}_${sourceInfos.version}.zip`);

})();
