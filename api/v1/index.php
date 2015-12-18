<?php
require 'plugins/slim/Slim.php';
require 'users.php';

\Slim\Slim::registerAutoloader();

$debug_mode = true;

$app = new \Slim\Slim();
$app->config('debug', $debug_mode);

function parseJsonBody($app) {
    return json_decode($app->request->getBody(), true);
}

function putJsonBody($body, $app) {
    $response = $app->response();
    $response['Content-Type'] = 'application/json';
    $response->status($body['error'] ? 400: 200);
    $response->body(json_encode($body));
}

function putError($body, $code, $app) {
    return putJsonBody(array(
        'error' => true,
        'error_code' => $code,
        'msg'   => $body
    ), $app);    
}

/* Handle new user */
$app->post('/user/new', function () use ($app) {    
    $data = parseJsonBody($app);
    return Users::newUser($app, $data);
});


/* Handle get current user */
$app->get('/user/me', function () use ($app) {
    return Users::getMe($app);
});

/* Handle delete current user */
$app->delete('/user/me', function () use ($app) {
    return Users::deleteMe($app);
});

/* Handle update current user */
$app->put('/user/me', function () use ($app) {
    $data = parseJsonBody($app);
    return Users::updateMe($app, $data);
});


/* Handle existing user */
$app->get('/user/:id', function ($id) use ($app) {        
    return Users::getUser($app, $id);
});



$app->run();

?>