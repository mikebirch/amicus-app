# Anticus website skeleton

[![Total Downloads](https://img.shields.io/packagist/dt/mikebirch/anticus-app.svg?style=flat-square)](https://packagist.org/packages/mikebirch/anticus-app)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%207-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)

A skeleton for building simple websites with the [Anticus PHP framework](https://github.com/mikebirch/anticus)

This skeleton includes a blog and pages. Pages can be organised into a <a href="https://www.nngroup.com/articles/flat-vs-deep-hierarchy/">flat hierachy</a> and the URLs can reflect this.
For example, if you have an about section on the website, the “About” page could have child pages like “Team” and “Story”.
The corresponding urls for these pages would be:
- /about
- /about/team
- /about/story

This structure will result in navigation menus being generated for child and sibling pages. 

Breadcrumbs are also generated from the URL.

Of course, if these menus do not suit, they can be removed from the twig template files.

If your website needs a CMS, Anticus can be used with the headless CMS [Directus](https://directus.io/) which connects to your database and provides an intuitive admin app for managing its content. 

## Installation

```bash
composer create-project --prefer-dist mikebirch/anticus-app
```

To install into a directory e.g. `/name-of-app/`:

```bash
composer create-project --prefer-dist mikebirch/anticus-app name-of-app
```
Create a config file. Rename `Config/config_example.php` to `Config/config.php`

For local development create `Config/config_local.php` and overwrite `$config`

For example:
```<?php
$config['environment'] = 'dev';
$config['database'] = [
    'type' => 'mysql',
    'name' => 'dbname',
    'host' => 'mysql',
    'username' => 'dbuser',
    'password' => '123',
    'charset' => 'utf8mb4'
];
```
