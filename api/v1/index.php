<?php
require 'plugins/vendor/autoload.php';
require 'database.php';
require 'users.php';
require 'users_info.php';
require 'posts.php';
require 'friends.php';

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
    return Users::create($response, $data);
});

/* Handle authenticate user */
$app->post('/user/me', function ($request, $response) {    
    $data = parseJsonBody($request);    
    return Users::auth($response, $data);
});

/* Handle delete current user */
$app->delete('/user/me', function ($request, $response) {
    $token = parseToken($request);    
    return Users::delete($response, $token);
});



/* Handle get my info */
$app->get('/user/{id:[0-9]+}/info', function ($request, $response, $args) {
    $token = parseToken($request);
    $friend_id = $args['id'];
    return UsersInfo::get($response, $token, $friend_id);
});

/* Handle get user info */
$app->get('/user/me/info', function ($request, $response) {
    $token = parseToken($request);    
    return UsersInfo::get($response, $token, null);
});

/* Handle update my info */
$app->put('/user/me/info', function ($request, $response) {
    $token = parseToken($request);
    $data = parseJsonBody($request);
    return UsersInfo::update($response, $token, $data);
});






/* Handle get my blocked friends */
$app->get('/user/me/friends/blocked', function ($request, $response) {
    $token = parseToken($request);    
    return Friends::blocked($response, $token);
});

/* Handle block friend */
$app->post('/user/me/friends/block', function ($request, $response) {
    $token = parseToken($request);
    $data = parseJsonBody($request);    
    return Friends::block($response, $token, $data);
});

/* Handle unblock friend */
$app->delete('/user/me/friends/blocked/{id:[0-9]+}', function ($request, $response) {
    $token = parseToken($request);
    $friend_id = $args['id'];
    return Friends::unblock($response, $token, $friend_id);
});


/* Handle get my waiting friends */
$app->get('/user/me/friends/waiting', function ($request, $response) {
    $token = parseToken($request);    
    return Friends::waiting($response, $token);
});

/* Handle get my requested friends */
$app->get('/user/me/friends/requested', function ($request, $response) {
    $token = parseToken($request);    
    return Friends::requested($response, $token);
});

/* Handle add friend */
$app->post('/user/me/friends/request', function ($request, $response) {
    $token = parseToken($request); 
    $data = parseJsonBody($request);   
    return Friends::request($response, $token, $data);
});

/* Handle get my accepted friends */
$app->get('/user/me/friends/accepted', function ($request, $response) {
    $token = parseToken($request);    
    return Friends::getAccepted($response, $token, null);
});


/* Handle get user accepted friends */
$app->get('/user/{id:[0-9]+}/friends', function ($request, $response, $args) {
    $token = parseToken($request);
    $friend_id = $args['id'];
    return Friends::getAccepted($response, $token, $friend_id);
});


/* Handle get post */
$app->get('/post/{id}', function ($request, $response, $args) {
    $token = parseToken($request);
    $post_id = $args['id'];
    return Posts::getPost($response, $token, $post_id);
});

$app->run();

?>