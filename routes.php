<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Simply tell Laravel the HTTP verbs and URIs it should respond to. It is a
| breeze to setup your applications using Laravel's RESTful routing, and it
| is perfectly suited for building both large applications and simple APIs.
| Enjoy the fresh air and simplicity of the framework.
|
| Let's respond to a simple GET request to http://example.com/hello:
|
|		Route::get('hello', function()
|		{
|			return 'Hello World!';
|		});
|
| You can even respond to more than one URI:
|
|		Route::post('hello, world', function()
|		{
|			return 'Hello World!';
|		});
|
| It's easy to allow URI wildcards using (:num) or (:any):
|
|		Route::put('hello/(:any)', function($name)
|		{
|			return "Welcome, $name.";
|		});
|
*/


// sequential router
	$uri = explode('/', URI::current());
	array_push($uri, null);
	$folder = path('app') . 'routes/';

	while (count($uri)) {
		array_pop($uri);
		$file = $folder . implode('/', $uri) . '.php';
		if (file_exists($file)) {
			require_once ($file);
			break;
		}
		$index = $folder . implode('/', $uri) . (count($uri) ? '/' : '') . 'index.php';
		if (file_exists($index)) {
			require_once ($index);
			break;
		}
	};


/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
|
| To centralize and simplify 404 handling, Laravel uses an awesome event
| system to retrieve the response. Feel free to modify this function to
| your tastes and the needs of your application.
|
| Similarly, we use an event to handle the display of 500 level errors
| within the application. These errors are fired when there is an
| uncaught exception thrown in the application.
|
*/

Event::listen('404', function()
{
	return array(
		'error' => '404 - Not Found'
	);
});

Event::listen('500', function()
{
	return array(
		'error' => '500 - Internal Server Error'
	);
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in "before" and "after" filters are called before and
| after every request to your application, and you may even create other
| filters that can be attached to individual routes.
|
| Let's walk through an example...
|
| First, define a filter:
|
|		Filter::register('filter', function()
|		{
|			return 'Filtered!';
|		});
|
| Next, attach the filter to a route:
|
|		Router::register('GET /', array('before' => 'filter', function()
|		{
|			return 'Hello World!';
|		}));
|
*/

Route::filter('cache', function($response = NULL) {
	$cname = 'response:' . Str::slug(URI::full());
	if (!$response) {
		return Cache::get($cname);
	}
	else {
		if ($response->status == 200) {
			$ctime = floor(pow(current(sys_getloadavg()) + 1, 5)); # cache for between 1 and 32 minutes
			Cache::put($cname, $response, $ctime);
		}
	}
});

Route::filter('before', function()
{
	// Do stuff before every request to your application...
});

Route::filter('after', function($response)
{
	// Encode objects/arrays/booleans to JSON
	if (is_array($response->content) || is_object($response->content) || is_bool($response->content)) {
		if ((is_array($response->content) && isset($response->content['error'])) || (is_object($response->content) && isset($response->content->error))) {
			$response->status = 400;
		}
		$response->content = json_encode($response->content);
		$response->header('Content-Type', 'application/json; charset=UTF-8');
		$response->header('Content-length', strlen($response->content));
	}

	return $response;
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::to('login');
});
