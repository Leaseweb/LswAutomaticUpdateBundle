LswAutomaticUpdateBundle
========================

Symfony2 bundle that enables automatic updates of the application from the Web Debug Toolbar.

## Installation

Installation is broken down in 4 easy steps.

### Step 1: Download LswAutomaticUpdateBundle using composer

Add LswAutomaticUpdateBundle in your composer.json:

```js
{
    "require": {
        "leaseweb/automatic-update-bundle": "1.0.*@dev"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update leaseweb/automatic-update-bundle
```

Composer will install the bundle to your project's `vendor/leaseweb` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    ...

    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        ...
        $bundles[] = new Lsw\AutomaticUpdateBundle\LswAutomaticUpdateBundle();
    }

}
```

### Step 3: Add routes to support automatic update

Add the following lines to ```app/config/routing_dev.yml```:

    automatic_update:
        resource: "@LswAutomaticUpdateBundle/Resources/config/routing/update.yml"


### Step 4: Allow access to the automatic update actions

Add the following lines to ```app/config/security.yml```:

    automatic_update:
        pattern: ^/update/
        security: false


## License

This bundle is under the MIT license.

The "circular arrows" icon in the web debug toolbar is part of the Picas icon set (official website: http://www.picasicons.com).
The icon is licensed and may only be used to identifying the LswAutomaticUpdateBundle in the Symfony2 web debug toolbar.
All ownership and copyright of this icon remain the property of Rok Benedik.
