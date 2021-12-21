#!/usr/bin/env node

'use strict'

const fse = require('fs-extra');
const pc = require('picocolors');
const path = require('path');

const {
	author,
	update,
	changelog,
	releaseTxt,
	copyright,
	creationDate,
	description,
	name,
	nameReal,
	filename,
	version,
	versionCompare,
	licenseLong,
	minimumPhp,
	maximumPhp,
	minimumJoomla,
	maximumJoomla,
	allowDowngrades,
	bugs
} = require("./../package.json");

module.exports.main = async (xmlFile, zipFilename, checksum, thisPackages) =>
{
  try
  {
		let targetPlatforms = update.targetplatform;

		let checksumEntity = '';
		if (checksum)
		{
			checksumEntity = checksum.replace(/</g, '&lt;');
			checksumEntity = checksumEntity.replace(/>/g, '&gt;');
		}

		// B\C
		if (! Array.isArray(targetPlatforms))
		{
			targetPlatforms = [targetPlatforms];
		}

		let requires = [];

		if (releaseTxt.requires && releaseTxt.requires.length)
		{
			requires = releaseTxt.requires;
		}

		// Force 1 loop.
		if (path.basename(xmlFile) !== 'update.xml')
		{
			targetPlatforms = [targetPlatforms.join(", ")];
		}

		// console.log(targetPlatforms);

		let fileContent = '';

		for (const targetplatform of targetPlatforms)
		{
			let xml = await fse.readFile(xmlFile, { encoding: "utf8" });
			xml = xml.replace(/{{allowDowngrades}}/g, allowDowngrades);
			xml = xml.replace(/{{authorName}}/g, author.name);
			xml = xml.replace(/{{authorUrl}}/g, author.url);
			xml = xml.replace(/{{bugs}}/g, bugs);
			xml = xml.replace(/{{checksum}}/g, checksum);
			xml = xml.replace(/{{checksumEntity}}/g, checksumEntity);
			xml = xml.replace(/{{client}}/g, update.client);
			xml = xml.replace(/{{copyright}}/g, copyright);
			xml = xml.replace(/{{creationDate}}/g, creationDate);
			xml = xml.replace(/{{description}}/g, description);
			xml = xml.replace(/{{docsDE}}/g, changelog.docsDE);
			xml = xml.replace(/{{docsEN}}/g, changelog.docsEN);
			xml = xml.replace(/{{element}}/g, filename);
			xml = xml.replace(/{{filename}}/g, filename);
			xml = xml.replace(/{{folder}}/g, update.folder);
			xml = xml.replace(/{{infosDE}}/g, changelog.infosDE);
			xml = xml.replace(/{{infosEN}}/g, changelog.infosEN);
			xml = xml.replace(/{{lastTests}}/g, changelog.lastTests.join('<br>'));
			xml = xml.replace(/{{licenseLong}}/g, licenseLong);
			xml = xml.replace(/{{maintainer}}/g, author.name);
			xml = xml.replace(/{{maintainerurl}}/g, author.url);
			xml = xml.replace(/{{maximumJoomla}}/g, maximumJoomla);
			xml = xml.replace(/{{maximumPhp}}/g, maximumPhp);
			xml = xml.replace(/{{minimumJoomla}}/g, minimumJoomla);
			xml = xml.replace(/{{minimumPhp}}/g, minimumPhp);
			xml = xml.replace(/{{name}}/g, name);
			xml = xml.replace(/{{nameReal}}/g, nameReal);
			xml = xml.replace(/{{nameUpper}}/g, name.toUpperCase());
			xml = xml.replace(/{{php_minimum}}/g, minimumPhp);
			xml = xml.replace(/{{projecturl}}/g, changelog.projecturl);
			xml = xml.replace(/{{releaseTxt.title}}/g, releaseTxt.title);
			xml = xml.replace(/{{requires}}/g, requires.join("<br>"));
			xml = xml.replace(/{{tag}}/g, update.tag);
			xml = xml.replace(/{{targetplatform}}/g, targetplatform);
			xml = xml.replace(/{{type}}/g, update.type);
			xml = xml.replace(/{{version}}/g, version);
			xml = xml.replace(/{{versionCompare}}/g, versionCompare);
			xml = xml.replace(/{{zipFilename}}/g, zipFilename);

			if (thisPackages && thisPackages.length)
			{
				xml = xml.replace(/{{thisPackages}}/g, thisPackages.join("\n"));
			}

			fileContent = fileContent + xml;
		}

		await fse.writeFile(xmlFile, fileContent, { encoding: "utf8" }
		).then(
		answer => console.log(pc.green(pc.bold(
			`Replaced entries in "${xmlFile}".`)))
		);

    // return true;
  } catch (error) {
    console.error(error)
    process.exit(1)
  }
}
