# Lyre Content

Lyre Content is a [Lyre](https://packagist.org/packages/lyre/lyre) addon for simple Content Management. It comes with the following predefined models to help you easily manage your website content:

- Page - This defines the basic Page SEO content, including title and description
- Section - This is a direct link to your frontend's sections, it contains a one to many relationship with itself and the rest of the models in this section
- Button - Basic button with title and link
- Icon - An svg icon to go with your buttons, your texts, and your sections
- Text - For managing all text within your sections
- Data - Some sections require data from your application, data is defined here in a json, see the example below
- File - Lyre content comes with a whole file management system out of the box

## Installation

```bash
composer require lyre/content
```

### Publish Assets

```bash
php artisan vendor:publish --provider="Lyre\Content\Providers\LyreContentServiceProvider"
```

### Dependencies

Lyre Content depends on [Lyre](https://packagist.org/packages/lyre/lyre) and [Laravel Filament](https://filamentphp.com/). To complete installation, especially if your require the functionalities from Laravel Filament, follow these additional commands:

```bash
php artisan filament:install --panels
```

To create an admin user:

```bash
php artisan make:filament-user
```

#### Discover Content Filament Resources

To Discover Lyre Content Filament Resources on your Filament dashboard, add the LyreContentFilamentPlugin to your Filament panel like so:

```php
use Lyre\Content\Filament\Plugins\LyreContentFilamentPlugin;

$panel->plugins([
    new LyreContentFilamentPlugin(),
]);
```

# ISSUES

You need to change your minimum-stability level to `dev` on your composer.json like:

```json
"minimum-stability": "dev",
```

This is because Lyre Content depends on a fork of [FilamentShield](https://github.com/bezhanSalleh/filament-shield/pull/537) that has not yet been merged to [main](https://github.com/bezhanSalleh/filament-shield).
