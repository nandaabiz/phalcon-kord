<?php 

class ControllerBase extends \Phalcon\Mvc\Controller {

	public function initialize() {

	}

	public function echoProfilerDB() {
		$profiles = $this->di->get('profiler')->getProfiles();
		foreach ($profiles as $profile) {
			echo "SQL Statement: ", $profile->getSQLStatement(), "\n";
			echo "Start Time: ", $profile->getInitialTime(), "\n";
			echo "Final Time: ", $profile->getFinalTime(), "\n";
			echo "Total Elapsed Time: ", $profile->getTotalElapsedSeconds(), "\n";
		}
	}

}

/* End of file ControllerBase.php */
/* Location: ./controllers/ControllerBase.php */