<?php
require 'plugins/vendor/autoload.php';
require 'users.php';
require 'posts.php';

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
    if (!$request)
        return false;        

    if (!$request->getHeaderLine('Abouda-Token'))
        return false;
    
    $token = base64_decode($request->getHeaderLine('Abouda-Token'));        
    $token = explode(":", $token);

    if (count($token) != 2)
        return false;

    $token = array(
        Users::ID_KEY  => trim($token[0]),
        Users::TOKEN_KEY => trim($token[1]),
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
    return Users::authUser($response, $data);
});

/* Handle delete current user */
$app->delete('/user/me', function ($request, $response) {
    $token = parseToken($request);    
    return Users::deleteMe($response, $token);
});


/* Handle get post */
$app->get('/post/{id}', function ($request, $response, $args) {
    $token = parseToken($request);
    $post_id = $args['id'];
    return Posts::getPost($response, $token, $post_id);
});


/* Handle update current user */
$app->put('/user/me', function ($request, $response) {
    $token = parseToken($request);
    $data = parseJsonBody($request);
    return Users::updateMe($response, $token, $data);
});

/* Handle get current user */
$app->get('/user/me', function ($request, $response) {
    $token = parseToken($request);    
    return Users::getMe($response, $token);
});

/* Handle existing user */
$app->get('/user/:id', function ($request, $response, $args) { 
    $token = parseToken($request);        
    return Users::getUser($response, $token, $id);
});

$app->run();

?>