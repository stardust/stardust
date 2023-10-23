# Stardust

Stardust is what the application of your dreams is made of.

Stardust is the result of my personal journey through the learning and understanding of how Symfony works at its core. It all started from
the [official documentation](http://symfony.com/doc/current/create_framework/index.html) and now aims to be what I think it's the most 
minimalistic and lean version of the Symfony Framework.
              
## Requirements
It's recommended that you use [Composer](https://getcomposer.org/) to use Stardust.

## Getting started with Stardust
To start a new project based on Stardust run this command from the directory you want to install your new Stardust Micro Framework application
```sh
composer create-project stardust/stardust [my-app-name]
```
Replace [my-app-name] with the desired directory name for your new application. 
This will install Stardust and all the required dependencies. Stardust requires PHP 5.6.0 or newer.

You will also want to:

* Point your virtual host document root to your new application's public/ directory.
* Or run the following command inside the just created directory to run test your app
  ```sh
  php -S localhost:[port] -t web web/rewrite.php
  ```
That's it! Now go build something cool.

## Usage
// TODO

## Tests
// TODO

## Contributing
Please see [CONTRIBUTING](https://github.com/debo/stardust/blob/master/CONTRIBUTING.md) for details.

## Learn More
// TODO

## Security
If you discover security related issues, please email me@debo.io instead of using the issue tracker.

## Author Information
Stardust was created in 2016 by [Marco "debo" De Bortoli & Contributors](https://github.com/debo/stardust/contributors).

Credits go to [Matthias Noback](https://github.com/matthiasnoback) for the help provided with some design decisions, [Loïc Faugeron](https://github.com/gnugat) for the contribution of the "Controller As A Service" decorator and to the [The Symfony project & community](https://github.com/symfony) for all the hard work constantly put in the evolution and support of the components used in this project.

An important mention also go to [Konstantin Kudryashov](https://github.com/everzet/) for [Behat](https://github.com/behat/behat), [Ciaran McNulty](https://github.com/ciaranmcnulty) for [PhpSpec](https://github.com/behat/behat) and [Sebastian Bergmann](https://github.com/sebastianbergmann/) for [PHPUnit](https://github.com/sebastianbergmann/phpunit/), tools that enable developers to deliver quality software, and to [Chris Hartjes](https://github.com/chartjes) for his [Phpmd](https://github.com/phpmd/phpmd) rulesets.          

## License
The Stardust Micro Framework is licensed under the Apache 2.0 license. See [License File](https://github.com/debo/stardust/blob/master/LICENSE.md) for more information.
