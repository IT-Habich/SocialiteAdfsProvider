# Laravel Socialite Adfs Provider

Basic ADFS OAuth2 Provider for [Laravel Socialite](https://laravel.com/docs/8.x/socialite)


## Installation

```bash
composer require codeadminde/socialite-adfs-provider
```

## Configuration

Add the following configuration to the `config/services.php` file:

```php
'Adfs' => [
  'client_id' => env('ADFS_CLIENT_ID'), // Sample: 78d90125-a243-416a-b8b5-c2c7574e9e85 
  'client_secret' => env('ADFS_CLIENT_SECRET'), // Sample: IGlnE3M5XFoWCPb/lXBUtSA2X5z3M6lbMSax13UH8HU=
  'redirect' => env('ADFS_REDIRECT'), // Sample: https://your-application.example.com/sso/callback
  'adfs_server' => env('ADFS_SERVER'),   // Sample: https://my-idp.example.com
],
```

## Usage

Define the redirect and callback routes for Socialite interaction. Sample:

```php
Route::get('/sso/redirect', function () {
    return Socialite::driver('Adfs')->redirect();
});

Route::get('/sso/callback', function () {
    $user = Socialite::driver('Adfs')->user();

    // Available User fields 
    // $user->nickname 
    // $user->name 
    // $user->email

    // Your custom logic ...

});
```

For further information about this, go to the offical documentation of [Laravel Socialite](https://laravel.com/docs/8.x/socialite#routing).

## Provided values

This provider returns the following fields:

* `email`
* `name` (optional)
* `nickname` (optional)

Your ADFS server must return at least the `email` attribute in order to use this provider. If desired (and recommended),
you should configure your ADFS server to provide a `nickname` (=> usually the sAMAccountName) and `name` (=> usually the displayName) attribute as well.

## Feedback / Support / Security

Please reach out to me at gh-security@it-habich.de for feedback or if you'll need support.

If you find security-related issues, please do not use the issue tracker instead, contact me via email.

## License

The contents of this repository is released under the [MIT license](LICENSE).
