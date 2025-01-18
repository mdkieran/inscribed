# Inscribed

Share your Laravel constants with JavaScript.

## About

This lightweight Laravel package makes it easy to share your application's constants, config values, and other static data with your frontend in an organized and efficient way. The data is "inscribed" onto the window object, where it lives for the lifecycle of the page.

By embedding static data directly into the initial page load, this package eliminates the need for repetitive API calls, reducing overhead and improving performance. For efficiency, caching is used in production, while in development, it generates inline JavaScript for easy editing.

Optionally, there's a JavaScript helper function that you can publish to simplify retrieving the inscribed data on the JavaScript side.

## Features

- Share static data from Laravel to your frontend.
- Group data to keep it organized.
- Generates inline JavaScript when in development mode for easy editing.
- Caching is used in production to reduce both client and server load.
- An optional JavaScript helper is available to easily access inscribed data.
- Works seamlessly with Inertia.js and probably many other frontend toolkits.

## Installation

### [1/5] Composer

```bash
composer require mdkieran/inscribed
```

### [2/5] Publish an example class

The following command will publish `App\Inscribed\ExampleInscribed.php` to your project:

```bash
php artisan vendor:publish --tag=inscribed-php
```

### [3/5] Register the example class

Open up a Service Provider class and add the following:

```php
public function register(): void
{
    $this->app->singleton('inscribed.fqns', function () {
        return [
            \App\Inscribed\ExampleInscribed::class,

            // You can add more here...
        ];
    });
}
```

### [4/5] Include the blade directive

Include the blade directive in your layout file.

```html
<!doctype html>
<html>
    <head>
        ...
        @inscribed
    </head>
    <body>
        ...
    </body>
</html>
```

Once you've included the blade directive, you should be able to type `window.Inscribed` into your browser's console and see the data from `ExampleInscribed`. That's pretty much it, you're all set.

### [5/5] Publish the JavaScript helper (Optional)

The following command will publish `resources/js/inscribed.js` to your project, which can help you to easily retrieve the "inscribed" data. You may integrate it into your project as you wish:

```bash
php artisan vendor:publish --tag=inscribed-js
```

## Caching

To prepare your app for production or the local environment you can run:

```bash
php artisan inscribed:cache
php artisan inscribed:clear
```

Or if you prefer, there's integration with Laravel's optimize commands so you could also run:

```bash
php artisan optimize
php artisan optimize:clear
```

### How does caching work?

Caching works by taking each "Inscribed" class that you have in your project and making a JavaScript file for it that holds all of the static data that the class returned. Once done, we then make the file `bootstrap/cache/inscribed.php` which stores the list of JavaScript files that we just made.

The blade directive looks for `bootstrap/cache/inscribed.php`, if it finds it then it fetches the list of JavaScript files (from within it) and prints out script tags for each one. If it doesn't find `bootstrap/cache/inscribed.php` then it instantiates each of your inscribed classes to produce inline JavaScript that holds your static data.
