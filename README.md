Custom EasyAdmin Demo Application
==========================

This project is a custom [EasyAdmin][1] based on the official [EasyAdmin Demo application][7] that showcases some custom features of EasyAdmin, a popular admin generator for [Symfony][2] applications.

It's a fork of the [EasyAdmin Demo application][7]. This allows to test some advanced integration of EasyAdmin with Symfony in some more complex scenarios than the base [Symfony Demo Application][3]

Documentation
------------

All the documentation bout the objectives and update of the project can be found here : [docs](doc/objectives.md)
Requirements
------------

  * PHP 8.0 or higher;
  * PDO-SQLite PHP extension enabled
  * Composer installed
  * **pnpm** for the assets build
  * and the [usual Symfony application requirements][2].

Installation
------------

clone this project
```bash
$ git clone git@github.com:yalit/custom-easy-admin.git
```

Run these commands in the folder you cloned the repo into:

1. Install all dependencies (composer and js)
```bash
$ make install
```

2. build the assets
```bash
$ make build-dev
```

3. Create the database and generate the fixtures
```bash
$ make db-init-dev
$ make bd-fixtures
```

Usage
-----

If you have [installed Symfony CLI][5], run this command:

```bash
$ symfony serve
```

Then access the application in your browser at the given URL (<https://localhost:8000> by default).

If you don't have the Symfony binary installed, run `php -S localhost:8000 -t public/`
to use the built-in PHP web server or [configure a web server][6] like Nginx or
Apache to run the application.

Test
----

To run the tests, follow the 2 following steps

```bash
$ make tests-prepare
$ make test
```

[1]: https://github.com/EasyCorp/EasyAdminBundle/
[2]: https://symfony.com
[3]: https://github.com/symfony/demo
[4]: https://getcomposer.org/
[5]: https://symfony.com/download
[6]: https://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html
[7]: https://github.com/EasyCorp/easyadmin-demo
