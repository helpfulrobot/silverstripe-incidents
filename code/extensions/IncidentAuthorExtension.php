<?php

/**
 * Class IncidentAuthorExtension
 */
class IncidentAuthorExtension extends DataExtension {

	/**
	 * @var array
	 */
	private static $has_many = array(
		'Incidents' => 'Incident'
	);

}