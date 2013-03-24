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

