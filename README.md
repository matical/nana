** still a WIP **
# nana
[![Build Status](https://img.shields.io/travis/matical/nana.svg?style=flat-square)](https://travis-ci.org/matical/nana)
[![Coverage](https://img.shields.io/coveralls/github/matical/nana/master.svg?style=flat-square)](https://coveralls.io/github/matical/nana?branch=master)
[![StyleCI](https://styleci.io/repos/103241043/shield?branch=master)](https://styleci.io/repos/103241043)

Yet another guzzle wrapper.

Documentation is still in the [works](https://github.com/matical/nana/wiki). Checkout the [tests](https://github.com/matical/nana/tree/master/Tests) for a rough idea.

```php
$response = Nana::get('https://httpbin.org/get');

$response->status() // 200
$response->json()->url // "https://httpbin.org/get"

$response->body() // Raw JSON string
$response->getHeaderLine('Date') // All calls are forwarded to the underlying PSR-7/Guzzle Response instance
```
