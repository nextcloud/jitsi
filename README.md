# Jitsi integration for Nextcloud

## Features

- 🎥 Easy online conferences in Nextcloud utilising Jitsi
- 🔗 Sharable conference room links
- 🔎 Shows conference rooms in the global search
- ✅ System test before joining a conference

## Changelog

[See CHANGELOG.md](./CHANGELOG.md)

## Nextcloud compatibility

The app declares compatibility with Nextcloud 25 through 35, including 34 and 35.
Use a PHP version supported by your Nextcloud release: Nextcloud 34 supports
PHP 8.2–8.5, and Nextcloud 35 supports PHP 8.3–8.5
([upstream PHP matrix](https://github.com/nextcloud/server/wiki/Releases-and-PHP-versions)).

The compatibility workflow runs on pushes, pull requests and weekly. It builds
the frontend, tests both the legacy XML and current JSON settings responses,
and checks class loading, room serialization and conference response policies
against Nextcloud 25, 31, 34, 35 and the development branch (`master`).
These API smoke checks do not replace a browser test of an installed server.

Run the checks locally with:

```sh
npm ci
npm test
npm run build
composer install --no-dev
php tests/compatibility.php /path/to/nextcloud-source
```

For each future major release, inspect the
[developer release notes](https://docs.nextcloud.com/server/latest/developer_manual/release_notes/index.html),
add its stable branch and supported PHP versions to the workflow, then test an
installed instance: admin settings loading/saving, room creation/deletion,
global search, authenticated and guest conference links, JWT authentication,
and camera/microphone/speaker checks. Raise `max-version` in `appinfo/info.xml`
only after those checks pass. The development-branch job gives early warning;
it does not guarantee compatibility with unreleased versions. Nextcloud
[requires an explicit maximum version](https://docs.nextcloud.com/server/stable/developer_manual/app_development/info.html#dependencies-nextcloud).

## Setup

⚠ It is highly recommended to set up a dedicated Jitsi instance.
Further instructions can be found in the [Jitsi setup doc](https://jitsi.github.io/handbook/docs/devops-guide/devops-guide-start). 

🔒 In addition to that the Jitsi instance should be secured via JSON Web Token.
Information about this can be found in the [Jitsi authentication doc](https://jitsi.github.io/handbook/docs/devops-guide/devops-guide-docker#authentication).

Nextcloud setup and configuration:

- Install the Nextcloud Jitsi app
- Go to *Settings* → *Jitsi* and enter your server URL (and JWT secret)
- Start conferencing 🍻

## Issues

Report issues and feature requests [here](https://github.com/nextcloud/jitsi).


## Translations

```
wget https://github.com/nextcloud/docker-ci/raw/master/translations/translationtool/translationtool.phar
chmod u+x translationtool.phar
./translationtool.phar create-pot-files
./translationtool.phar convert-po-files
```

## Licence

See [LICENCE](./LICENCE)
