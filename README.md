# laravel-wopi host demo/example app

This app is designed to test [laravel-wopi](https://github.com/nagi1/laravel-wopi) package.
And to demonstrate how to implement your own `Document Manager`.

See: [app/Services/DBDocumentManager.php](app/Services/DBDocumentManager.php)

## Docs

[https://nagi1.github.io/laravel-wopi/docs](https://nagi1.github.io/laravel-wopi/docs)

## installation

1- Clone this repo

```bash
git clone https://github.com/nagi1/wopi-host-example
```

2- Copy .env file you'll **not need** database connection 🔥

```bash
cp .env.example .env
```

```bash
composer install
```

3- fill up `APP_URL` and `WOPI_CLIENT_URL` values.

4- Follow [Setup code](#setup-code) step to setup local `code` image using docker.

5- Use convenience command to get ready for testing

**Tip:** Use this command after every testing session.

```bash
php artisan prepare-for-test
```

## Database

You don't need to setup any databases! [@calebporzio](https://github.com/calebporzio)'s [Sushi](https://github.com/calebporzio/sushi).

Eloquent's missing "array" driver.

## Setup code

Optionally you can skip this step entirely if you already have your own WOPI client installed.

1- Open `docker-compose.yml` and edit `"domain=wopiapp\\.test"` environment to your wopi app url.

2- edit `"wopiapp.test:host-gateway"` to your wopi app url

3- run `docker-compose up`

### Advance docker setup

You can easily add code as a service to your current docker-compose file and connect it to your app/host network.

## Testing for Discovery and proof validator

Use [laravel-wopi](https://github.com/nagi1/laravel-wopi#-tested)'s test suite.

## Test using Wopi Validator

**Please note that the host here is on wopiapp.test if you have changed it to something else change it here too!**

```sh
docker run -it --rm --add-host wopiapp.test:host-gateway \
tylerbutler/wopi-validator -- -w http://wopiapp.test/wopi/files/1 -t MyToken -l 0
```

## Test using your 👀

### Word document (Docx)

Visit `http://{your-app-url-here}/2`

### Powerpoint (pptx)

Visit `http://{your-app-url-here}/3`

### Excel (xlsx)

Visit `http://{your-app-url-here}/4`

## Enjoy and please share your feedback with me

[ahmedflnagi@gmail.com](mailto:ahmedflnagi@gmail.com)
