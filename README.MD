## Run This To Terminal
composer config repositories.vn vcs https://github.com/New-Vision-2050/base.git
## Composer Require
composer require vn/base-package:dev-master
## Run Vendor
php artisan vendor:publish --tag="base-package" --force
