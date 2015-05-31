<?php 
use Phalcon\Mvc\Model;
/**
* Model for chords table
*/
class Chords extends Model {
	public $id;
	public $artist;
	public $title;
	public $base;
	public $is_marked;
	public $desc;
	public $content;
	public $source;
	public $hit;
	public $rating_click;
	public $rating_value;
	public $last_updated;
	public $user_update;
}