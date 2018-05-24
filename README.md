<p align="center"><img src="https://raw.githubusercontent.com/matical/nana/master/nana.png"></p>
<p align="center">
    <a href="https://travis-ci.org/matical/nana"><img src="https://img.shields.io/travis/matical/nana.svg?style=flat-square" alt="Build Status" title="Build Status"></a>
    <a href="https://coveralls.io/github/matical/nana?branch=master"><img src="https://img.shields.io/coveralls/github/matical/nana/master.svg?style=flat-square" alt="Test Coverage" title="Test Coverage"></a>
    <a href="https://github.styleci.io/repos/134165946"><img src="https://github.styleci.io/repos/134165946/shield?branch=master" alt="Style CI Status" title="Style CI Status"></a>
</p>

Yet another guzzle wrapper.

Documentation is still in the [works](https://github.com/matical/nana/wiki). Checkout the [tests](https://github.com/matical/nana/tree/master/Tests) for a rough idea.

```php
$response = Nana::get('https://httpbin.org/get');

$response->status() // 200
$response->json()->url // "https://httpbin.org/get"

$response->body() // Raw JSON string
$response->getHeaderLine('Date') // All calls are forwarded to the underlying PSR-7/Guzzle Response instance
```
