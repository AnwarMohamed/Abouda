<?php
require 'plugins/vendor/autoload.php';
require 'users.php';

$debug_mode = false;

$container = new \Slim\Container();
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        $error = json_encode(array(
            'error' => true,
            'error_code' => 404,
            'msg' => 'page not found'
        ));

        return $container['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json')
            ->write($error);
    };
};

$container['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        $error = json_encode(array(
            'error' => true,
            'error_code' => 500,
            'msg' => 'something went wrong!'
        ));

        return $container['response']
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write($error);
    };
};

$container['notAllowedHandler'] = function ($container) {
    return function ($request, $response, $methods) use ($container) {
        $error = json_encode(array(
            'error' => true,
            'error_code' => 405,
            'msg' => 'HTTP request method not allowed'
        ));

        return $container['response']
            ->withStatus(405)            
            ->withHeader('Content-Type', 'application/json')
            ->write($error);
    };
};

$app = new \Slim\App($container);
$app->config('debug', $debug_mode);

function parseToken($request) {
    if (!$request->headers)
        return false;

    if (!isset($request->headers['Abouda-Token']))
        return false;

    $token = base64_decode($request->headers['Abouda-Token']);
    $token = explode(":", $token);

    if (count($token) != 2)
        return false;

    $token = array(
        Users::ID_KEY  => $token[0],
        Users::TOKEN_KEY => $token[1],
        Users::REMOTE_ADDR_KEY => $_SERVER['REMOTE_ADDR']
    );

    return $token;
}

function parseJsonBody($request) {
    return json_decode($request->getBody(), true);
}

function putJsonBody($body, $status, $response) { 
    return $response
        ->withStatus($status)
        ->withHeader('Content-Type', 'application/json')    
        ->write(json_encode($body));
}

function putError($body, $code, $response) {
    return putJsonBody(array(
        'error' => true,
        'error_code' => $code,
        'msg'   => $body
    ), 400, $response);    
}


/* Handle new user */
$app->post('/user/new', function ($request, $response) {
    $data = parseJsonBody($request);        
    return Users::newUser($response, $data);
});

/* Handle authenticate user */
$app->post('/user/', function ($request, $response) {    
    $data = parseJsonBody($request);    
    return Users::authenticate($response, $data);
});

/* Handle delete current user */
$app->delete('/user/me', function ($request, $response) {
    $token = parseToken($request);
    return Users::deleteMe($response, $token);
});

/* Handle get current user */
$app->get('/user/me', function ($request, $response) {
    return Users::getMe($response);
});



/* Handle update current user */
$app->put('/user/me', function ($request, $response) {
    if (!$app->request) {
        putError(
            'invalid post request', 
            ERROR_REQUEST_INVALID, $app);
    }

    $data = parseJsonBody($app);
    return Users::updateMe($app, $data);
});


/* Handle existing user */
$app->get('/user/:id', function ($request, $response, $args) {        
    return Users::getUser($app, $id);
});

$app->run();

?>