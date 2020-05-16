<h1 align="center">Welcome to Apps Manager 👋</h1>
<p>
  <a href="https://github.com/Luca-Castelnuovo/Apps/blob/master/LICENSE" target="_blank">
    <img alt="License: MIT" src="https://img.shields.io/badge/License-MIT-yellow.svg" />
  </a>
</p>

> Centralized auth server for managing access to my apps

### 🏠 [Homepage](https://apps.lucacastelnuovo.nl)

### 💾 [SDK](https://github.com/luca-castelnuovo/helpers-php#appsclient)

## Install

1. Install Package

```sh
git clone https://github.com/Luca-Castelnuovo/Apps.git
composer install
```

2. Set DB credentials in `.env`

3. Run `composer migrate`

4. Run `composer jwt`

5. Create Gihub OAuth application and place keys in `.env`  
   _the callback url should be https://your.app/auth/github/callback_

6. Create Google OAuth application and place keys in `.env`  
   _the callback url should be https://your.app/auth/google/callback_

7. Create hCaptcha or ReCaptcha keys and place in `.env`  
   _if you want to use recaptcha edit /bootstrap/config.php_

8. Create Gumroad app and access_token and place in `.env`

8. Create new template from `/views/partials/email_template.twig` on mailjs.lucacastelnuovo.nl  
   _create accesstoken for https://your.app_

## 📝 License

Copyright © 2020 [Luca Castelnuovo](https://github.com/Luca-Castelnuovo).<br />
This project is [MIT](https://github.com/Luca-Castelnuovo/Apps/blob/master/LICENSE) licensed.
 
