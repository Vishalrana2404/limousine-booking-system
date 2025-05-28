# Project Name

Limousine Booking System

## Overview

Briefly introduce your project, its purpose, and any key features.

## Prerequisites
-   sudo apt-get install imagemagick
-   sudo apt-get install ghostscript
-   sudo apt-get install php8.3-imagick

-   [Node.js](https://nodejs.org/) v21.7.3
-   [npm](https://www.npmjs.com/) v10.5.0
-   [Composer](https://getcomposer.org/) v2.7.4
-   [PHP](https://www.php.net/) v8.3.6
-   MySQL Database

## Installation

1. Clone the repository:
    ```bash
    git clone https://gitlab.zapbuild.com/zapbuild/limousine-bookings.git
    ```
2. Go to project Directory
    ```bash
    cd limousine-bookings
    ```

3. Checkout to development branch
    ```bash
    git checkout dev
    ```

4. Install PHP dependencies:
   ```bash
   composer update
   ```

5. Install Node.js dependencies:
   ```bash
   npm install
   ```

6. Migrate the database schema:
   ```bash
   php artisan migrate
   ```

7. Seed the database:
   ```bash
   php artisan db:seed
   ```

## Usage
1. Compile assets:
   ```bash
   npm run dev
   ```

2. Start the PHP development server:
   ```bash
   php artisan serve
   ```

## Additional Notes

## Contributors

## License
````
