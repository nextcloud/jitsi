# Jitsi integration for Nextcloud

forked from https://github.com/nextcloud/jitsi
fork on github: https://github.com/fairkom/nextcloud-fairmeeting-integration
fork on gitlab: 
opimized for: https://www.fairkom.eu/en/fairmeeting
faq: https://git.fairkom.net/hosting/fairmeeting/-/wikis/home

## Dev Instance:

- use nextcloud developer docker: https://github.com/juliushaertl/nextcloud-docker-dev
- in repo folde make `composer install`, `npm install` and `npm run build`
- I used v28 because the nextcloud jitsi app is only available for stable28 (https://juliushaertl.github.io/nextcloud-docker-dev/), thats why look `pwd` in repo folder and change `~/path/to/appid` and `appid`, look also if other instances with different versions are still running

```
docker run --rm -p 8080:80 -e SERVER_BRANCH=v28.0.6 \
  -v $(pwd):/var/www/html/apps-extra/fairmeeting \
  ghcr.io/juliushaertl/nextcloud-dev-php80:latest
```

- than go to http://localhost:8080/index.php/settings/apps, login with u: admin pw: admin, and activate the 'fairmeeting Integration App'
-

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

- Install the Nextcloud fairmeeting app
- Go to _Settings_ → _fairmeeting_ and enter your server URL (and JWT secret)
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
