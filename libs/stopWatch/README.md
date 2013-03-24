#Stopwatch-DebugBar (cc)#
Pavel Železný (2bfree), 2012 ([www.pavelzelezny.cz](http://www.pavelzelezny.cz))

## Requirements ##

[Nette Framework 2.0.3](http://nette.org) or higher. (PHP 5.3 edition)

## Documentation ##
Improved version of great but old [Stopwatch Debug Panel](https://github.com/pekelnik/nextensions) what I used before.

## Examples ##
First load Stopwatch into the DebugPanel by insert following code into config.neon
```neon
common:
	services:
		stopwatch:
			class: stopwatch
			arguments:
				- @application

	nette:
		debugger:
			strictMode: true
			bar:
				- @stopwatch
```

After that you can simply make new measurement by following code
```php
Stopwatch::start('Optional name of measurement');
// do something...
Stopwatch::stop('Optional name of measurement');
``` 