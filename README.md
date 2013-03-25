LswAutomaticUpdateBundle (not ready)
========================

Symfony2 bundle that allows you to upgrade the application from the web interface

security.yml:

    automatic_update:
        pattern: ^/update/
        security: false

routing_dev.yml:

    automatic_update:
        resource: "@LswAutomaticUpdateBundle/Resources/config/routing/update.yml"

### License

This bundle is under the MIT license.

The "circular arrows" icon in the web debug toolbar is part of the Picas icon set (official website: http://www.picasicons.com).
The icon is licensed and may only be used to identifying the LswAutomaticUpdateBundle in the Symfony2 web debug toolbar.
All ownership and copyright of this icon remain the property of Rok Benedik.
