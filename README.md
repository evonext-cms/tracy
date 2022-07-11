## Integration [Nette Tracy](https://github.com/nette/tracy.git) for EvoNext CMS

Better Exception Handler

[![Total Downloads](https://poser.pugx.org/evonext/tracy/d/total.svg)](https://packagist.org/packages/evonext/tracy)
[![Latest Stable Version](https://poser.pugx.org/evonext/tracy/v/stable.svg)](https://packagist.org/packages/evonext/tracy)
[![Latest Unstable Version](https://poser.pugx.org/evonext/tracy/v/unstable.svg)](https://packagist.org/packages/evonext/tracy)
[![License](https://poser.pugx.org/evonext/tracy/license.svg)](https://packagist.org/packages/evonext/tracy)
[![Monthly Downloads](https://poser.pugx.org/evonext/tracy/d/monthly)](https://packagist.org/packages/evonext/tracy)
[![Daily Downloads](https://poser.pugx.org/evonext/tracy/d/daily)](https://packagist.org/packages/evonext/tracy)

![Laravel Tracy](docs/screenshots/tracy.png)

## Features

- Visualization of errors and exceptions
- Debugger Bar (ajax support @v1.5.6)
- Exception stack trace contains values of all method arguments.

## Installation

To get the latest version of Laravel Exceptions, simply require the project using [Composer](https://getcomposer.org):

```bash
composer require evonext/tracy --dev
```

Instead, you may of course manually update your `require` block and run `composer update` if you so choose:

```json
{
    "require-dev": {
        "evonext/tracy": "^1.0"
    }
}
```

Include the service provider within `config/app.php`. The service provider is needed for the generator artisan command.

```php
'providers' => [
    ...
    EvoNext\Tracy\TracyServiceProvider::class,
    ...
];
```

If you see Route `tracy.bar` not defined, please run `artisan route:clear` once

```bash
artisan route:clear
```

## Config

Basic settings can be changed using environment variables:

```dotenv
TRACY_ENABLED=true        # true | false | 'manager' | 'web'
TRACY_SHOW_BAR=true       # true | false
TRACY_EXCEPTION=true      # true | false
TRACY_MGR_TOP_FRAME=false # true | false
```

### Publish config

If you need to change other settings, publish the configuration:

```bash
php artisan vendor:publish --provider="EvoNext\Tracy\TracyServiceProvider"
```

The `/config` directory will contain the file `tracy.php`, which you can change as you see fit.

```php
return [

    /* Activate tracy
    |--------------------------------------------------------------------------
    | Available values:
    | true      – Enable for any context
    | false     – Disable for any context
    | 'manager' – Enable only for manager context (admin area)
    | 'web'     – Enable only for web context (public area)
    |-------------------------------------------------------------------------- */

    'enabled' => env('TRACY_ENABLED', env('APP_DEBUG') === true),

    /* Show bar
    |-------------------------------------------------------------------------- */

    'showBar' => env('TRACY_SHOW_BAR', env('APP_ENV') !== 'production'),

    /* Show exceptions
    |-------------------------------------------------------------------------- */

    'showException' => env('TRACY_EXCEPTION', true),

    /* The URL prefix for the manager dashboard
    |-------------------------------------------------------------------------- */

    'managerPrefix' => 'admin',

    /* The URL prefix for a frame top level the manager dashboard
    |-------------------------------------------------------------------------- */

    'managerTopRoute' => 'main',

    /* If true tracy shown bar in a frame top level
    | instead pages frames in the manager context
    |-------------------------------------------------------------------------- */

    'enabledInTopFrame' => env('TRACY_MGR_TOP_FRAME', false),

    'route'         => [
        'prefix' => 'tracy',
        'as'     => 'tracy.',
    ],
    'accepts'       => [
        'text/html',
    ],
    'appendTo'      => 'body',
    'editor'        => 'editor://%action/?file=%file&line=%line&search=%search&replace=%replace',
    'maxDepth'      => 4,
    'maxLength'     => 1000,
    'scream'        => true,
    'showLocation'  => true,
    'strictMode'    => true,
    'editorMapping' => [],
    'panels'        => [
        'routing'        => true,
        'database'       => true,
        'view'           => true,
        'event'          => false,
        'session'        => true,
        'request'        => true,
        'auth'           => true,
        'html-validator' => false,
    ],
];
```

### Editor Link

See [https://tracy.nette.org/en/open-files-in-ide](https://tracy.nette.org/en/open-files-in-ide)

## Debugger Bar

*Images clickable*

<table>
<tr>
<td><code>@bdump</code></td>
<td>Ajax</td>
<td>SysInfo</td>
<td>Route</td>
</tr>
<tr>
<td><a href="docs/screenshots/bdump.png"><img src="docs/screenshots/bdump.png" width="100" height="100"></a></td>
<td><a href="docs/screenshots/ajax.png"><img src="docs/screenshots/ajax.png" width="100" height="100"></a></td>
<td><a href="docs/screenshots/systeminfo.png"><img src="docs/screenshots/systeminfo.png" width="100" height="100"></a></td>
<td><a href="docs/screenshots/route.png"><img src="docs/screenshots/route.png" width="100" height="100"></a></td>
</tr>
<tr>
<td>View</td>
<td>Session</td>
<td>Request</td>
<td>Login</td>
</tr>
<tr>
<td><a href="docs/screenshots/view.png"><img src="docs/screenshots/view.png" width="100" height="100"></a></td>
<td><a href="docs/screenshots/session.png"><img src="docs/screenshots/session.png" width="100" height="100"></a></td>
<td><a href="docs/screenshots/request.png"><img src="docs/screenshots/request.png" width="100" height="100"></a></td>
<td><a href="docs/screenshots/login.png"><img src="docs/screenshots/login.png" width="100" height="100"></a></td>
</tr>
</table>

#### Custom Auth

```
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Recca0120\LaravelTracy\BarManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(BarManager $barManager)
    {
        $barManager->get('auth')->setUserResolver(function() {
            return [
                'id' => 'xxx',
                'username' => 'xxx',
                ...
            ];
        });
    }
}
```

## Thanks

- [Laravel PHP Framework](https://github.com/laravel/laravel)
- [nette/tracy](https://github.com/nette/tracy)
- [recca0120/laravel-tracy](https://github.com/recca0120/laravel-tracy)
