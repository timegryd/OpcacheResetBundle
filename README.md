# OpcacheResetBundle

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e903c805-7570-4e12-9c67-ca4a04ca4391/mini.png)](https://insight.sensiolabs.com/projects/e903c805-7570-4e12-9c67-ca4a04ca4391)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/timegryd/OpcacheResetBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/timegryd/OpcacheResetBundle/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/timegryd/OpcacheResetBundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/timegryd/OpcacheResetBundle/build-status/master)

Command line interface to reset opcache

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require timegryd/opcache-reset-bundle "1.0.*"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Timegryd\OpcacheResetBundle\TimegrydOpcacheResetBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Configuration
---------------------

Set configuration:

``` yaml
# app/config/config.yml
timegryd_opcache_reset:
    host: http://timegryd.io
    dir: "%kernel.root_dir%/../web"
```

Step 4: Use it !
----------------

```console
$ php bin/console opcache:reset
```

You can also override configuration :

```console
$ php bin/console opcache:reset timegryd.io /web-dir
```

Thanks
------

Rework of OpcacheBundle by sixdayz https://github.com/sixdayz/OpcacheBundle


