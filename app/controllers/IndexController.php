<?php

class IndexController extends \Phalcon\Mvc\Controller {

	public function indexAction() {
		
	}

	public function chordAction($params='') {
		// see routes
		$id = $this->dispatcher->getParam("id");
		if ($id) {
			$chords = Chords::findFirst($id);
		}
	}

}