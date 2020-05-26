<p align="center"><a href="https://github.com/Luca-Castelnuovo/Apps"><img src="https://rawcdn.githack.com/Luca-Castelnuovo/Apps/99fd8e1cb1f4f9992ebd7f8e2739332ab22401d2/public/assets/images/banner.png"></a></p>

<p align="center">
<a href="https://github.com/Luca-Castelnuovo/Apps/commits/master"><img src="https://img.shields.io/github/last-commit/Luca-Castelnuovo/Apps" alt="Latest Commit"></a>
<a href="https://github.com/Luca-Castelnuovo/Apps/issues"><img src="https://img.shields.io/github/issues/Luca-Castelnuovo/Apps" alt="Issues"></a>
<a href="LICENSE.md"><img src="https://img.shields.io/github/license/Luca-Castelnuovo/Apps" alt="License"></a>
</p>

# Apps

Centralized auth server for managing access to my apps

-   [Homepage](https://apps.lucacastelnuovo.nl)
-   [SDK](https://github.com/luca-castelnuovo/helpers-php#appsclient)

## Installation

For development

1. `git clone https://github.com/Luca-Castelnuovo/Apps.git`
2. `composer install`
3. Edit `.env`
4. `php cubequence app:key`
5. `php cubequence app:jwt`
6. `php cubequence db:migrate`
7. `php cubequence db:seed`
8. Start development server `php -S localhost:8080 -t public`

For deployment

1. `git clone https://github.com/Luca-Castelnuovo/Apps.git`
2. `composer install --optimize-autoloader --no-dev`
3. Edit `.env`
4. `php cubequence app:key`
5. `php cubequence app:jwt`
6. `php cubequence db:migrate`

## Security Vulnerabilities

Please review [our security policy](https://github.com/Luca-Castelnuovo/Apps/security/policy) on how to report security vulnerabilities.

## License

Copyright © 2020 [Luca Castelnuovo](https://github.com/Luca-Castelnuovo).<br />
This project is [MIT](LICENSE.md) licensed.
