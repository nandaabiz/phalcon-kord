<?php

// Create the router
$router = new \Phalcon\Mvc\Router();

$router->add('/:controller/([a-zA-Z\-]+)/:params', array(
	'controller' => 1,
	'action' => 2,
	'params' => 3
))->convert('action', function($action) {
	return lcfirst(Phalcon\Text::camelize($action));
});

$router->add(
    "/chord/{id}/:params",
    array(
        "controller" => "index",
        "action"     => "chord",
        "params"     => 2, // :params
    )
);

// njajal
$router->add(
    "/anu",
    array(
        "controller" => "admin",
        "action"     => "index",
    )
);
$router->handle();