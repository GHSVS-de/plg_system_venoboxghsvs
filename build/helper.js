#!/usr/bin/env node

'use strict'

const fse = require('fs-extra');
const util = require("util");
const rimRaf = util.promisify(require("rimraf"));
const chalk = require('chalk');
const crypto = require('crypto');

module.exports.cleanOut = async (cleanOuts) =>
{
	for (const file of cleanOuts)
	{
		await rimRaf(file).then(
			answer => console.log(chalk.redBright(`rimRafed "${file}".`))
		).catch(error => console.error('Error ' + error));
	}
}

// Digest sha256, sha384 or sha512.
module.exports.getChecksum = async (path, Digest) =>
{
	if (!Digest)
	{
		Digest = 'sha256';
	}

  return new Promise(function (resolve, reject)
	{
    const hash = crypto.createHash(Digest);
    const input = fse.createReadStream(path);

    input.on('error', reject);
    input.on('data', function (chunk)
		{
      hash.update(chunk);
    });

    input.on('close', function ()
		{
      resolve(hash.digest('hex'));
    });
  });
}

module.exports.findVersionSub = async (packagesFile, packageName) =>
{
	console.log(chalk.magentaBright(
	`Search versionSub of package "${packageName}" in "${packagesFile}".`));

	return require(packagesFile).version;
}
