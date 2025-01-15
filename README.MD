## Run This To Terminal
composer config repositories.m-tech-stack vcs https://github.com/mabou7agar/base.git
## Composer Require
composer require m-tech-stack/base-package:dev-master
## Run Vendor
php artisan vendor:publish --tag="base-package" --force
