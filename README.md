# Jitsi integration for Nextcloud (unofficial)

## Translation workflow

```
wget https://github.com/nextcloud/docker-ci/raw/master/translations/translationtool/translationtool.phar
php translationtool.phar create-pot-files
```

Translate

```
php translationtool.phar convert-po-files
```
