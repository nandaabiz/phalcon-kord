<?php

class BulkController extends ControllerBase {
	public $regex_chord = "/([CDEFGAB](#|b)?)((M|m|Maj|min|aug|dim|sus|add)?(6|7|9|11|13|-5|\+5)?)(\s|-|\/|\(|\.)+/";
	public $keys = array('C','C#','D','D#','E','F','F#','G','G#','A','Bb','B');
	public $base_interval = array(0,5,7);
	public $markedtag = '{~}';
	public $chordtag = '{~cl}';

	public function indexAction() {
		
	}

	public function markingAction($chord_id=0) {
		set_time_limit(0);
		// keep on going even if user pulls the plug*
		while(ob_get_level())ob_end_clean(); // remove output buffers
		ob_implicit_flush(true);
		if ($chord_id) {
			$chord = Chords::findFirst($chord_id);
			$this->singleMarking($chord);
		} else {
			$chords = Chords::find(array(
				"is_marked = 0",
				"order" => "artist,title"
				));
			if ($chords) {
				foreach ($chords as $chord) {
					$this->singleMarking($chord);
				}
			}
		}
		echo '[FINISHED] All is well :p';
	}

	protected function singleMarking($chord) {
		try {
			$contentArray = explode("\n", $chord->content);
			$found = false;
			foreach ($contentArray as &$line) {
				$line = str_replace($this->markedtag, '', $line);
				$line = str_replace($this->chordtag, '', $line);
				if (preg_match($this->regex_chord, $line)) {
					$line .= ' '.$this->chordtag;
					$found = true;
				}
			}
			if ($found) {
				array_unshift($contentArray, $this->markedtag);
				$chord->content = implode("\n", $contentArray);
				$chord->is_marked = 1;
				$chord->save();
				echo $chord->artist.' - '. $chord->title.' ('.$chord->id.')'.' [MARKED]<br>'."\n";
			}
		} catch (Exception $e) {
			echo $chord->artist.' - '. $chord->title.' ('.$chord->id.')'.' [UNMARKED]<br>'."\n";
		}
	}

	public function taggingAction($chord_id=0, $to_id=0) {
		set_time_limit(0);
		// keep on going even if user pulls the plug*
		while(ob_get_level())ob_end_clean(); // remove output buffers
		ob_implicit_flush(true);
		if ($chord_id) {
			$chord = Chords::findFirst($chord_id);
			$this->singleTagging($chord);
		} else {
			$chords = Chords::find(array(
				// "base = ''",
				"order" => "artist,title"
				));
			if ($chords) {
				foreach ($chords as $chord) {
					$this->singleTagging($chord);
				}
			}
		}
		echo 'Done :p';
	}

	protected function singleTagging($chord) {
		try {
			$temp_chords = array();
			preg_match_all($this->regex_chord, $chord->content, $temp_chords, PREG_PATTERN_ORDER);
			$temp_chords = $temp_chords[0];
			$temp_chords = array_unique(array_map(array($this,"trimChord"), $temp_chords));
			$base_key = '';
			foreach ($temp_chords as $key) {
				if (!in_array($key, $this->keys)) {
					continue;
				}
				$base_keys = $this->getBaseKey($key);
				if (array_intersect($base_keys, $temp_chords) == $base_keys) {
					$base_key = $key;
					break;
				}
			}
			if (!$base_key) {
				preg_match("/([CDEFGAB](#|b)?)/", $temp_chords[0],$temp_base_key);
				$temp_base_key = $temp_base_key[0];
				$base_key = $this->keys[array_search($temp_base_key, $this->keys)];
			}
			if ($base_key) {
				$chord->base = $base_key;
				$chord->save();
				echo $chord->artist.' - '. $chord->title.' ('.$chord->id.')'.' in '.$base_key.' [DONE]<br>'."\n";
			}
		} catch (Exception $e) {
			echo $chord->artist.' - '. $chord->title.' ('.$chord->id.')'.' [FAILED]<br>'."\n";
		}
	}

	public function getBaseKey($base='C') {
		$base = ucfirst($base);
		$start_interval_pos = array_search($base, $this->keys);
		$keys_length = count($this->keys);
		$base_keys = array();
		foreach ($this->base_interval as $interval) {
			$cur_pos = $interval + $start_interval_pos;
			if ($cur_pos >= $keys_length) {
				$cur_pos -= $keys_length;
			}
			$base_keys[] = $this->keys[$cur_pos];
		}
		return $base_keys;
	}

	public function trimChord($string) {
		$string = preg_replace("/(\/|\(|\.)/", '', $string);
		$string = rtrim($string,'-');
		return trim($string);
	}

}