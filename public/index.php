<?php
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Db\Profiler as DbProfiler;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
try {
	$config = require __DIR__."/../app/config/config.php";

	//Register an autoloader
	$loader = new \Phalcon\Loader();
	$loader->registerDirs(array_values((array)$config->dir))->register();
	
	//Create a DI
	$di = new Phalcon\DI\FactoryDefault();

	$di->set('profiler', function(){
		return new DbProfiler();
	}, true);

	//Setup the database service
	$di->set('db', function() use ($config, $di) {
		$eventsManager = new EventsManager();
		$profiler      = $di->getProfiler();
		//Listen all the database events
		$eventsManager->attach('db', function($event, $connection) use ($profiler) {
			if ($event->getType() == 'beforeQuery') {
				$profiler->startProfile($connection->getSQLStatement());
			}
			if ($event->getType() == 'afterQuery') {
				$profiler->stopProfile();
			}
		});
		$connection = new DbAdapter((array)$config->database);
		$connection->setEventsManager($eventsManager);
		return $connection;
	});
	//Setup the view component
	$di->set('view', function() use ($config){
		$view = new \Phalcon\Mvc\View();
		$view->setViewsDir($config->dir->viewsDir);
		return $view;
	});

	//Setup a base URI so that all generated URIs include the "tutorial" folder
	$di->set('url', function(){
		$url = new \Phalcon\Mvc\Url();
		$url->setBaseUri('/kord/');
		return $url;
	});

	// setup custom router
	$di->set('router', function(){
		require __DIR__.'/../app/config/routes.php';
		return $router;
	});

	//Handle the request
	$application = new \Phalcon\Mvc\Application($di);

	echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
	 echo "PhalconException: ", $e->getMessage();
}