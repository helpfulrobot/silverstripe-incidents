<?php

/**
 * Class IncidentClient
 */
class IncidentClient extends Dataobject {

	/**
	 * @var array
	 */
	private static $db = array(
		'Title' => 'Varchar'
	);

	/**
	 * @var array
	 */
	private static $many_many = array(
		'Groups' => "Group"
	);

	/**
	 * @param $member
	 * @return true
	 */
	public function MemberInGroup($member) {

	}


}