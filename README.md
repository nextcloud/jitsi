# Jitsi integration for Nextcloud

## Features

- 🎥 Easy online conferences in Nextcloud utilising Jitsi
- 🔗 Sharable conference room links
- 🔎 Shows conference rooms in the global search
- ✅ System test before joining a conference

## Changelog

[See CHANGELOG.md](./CHANGELOG.md)

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
