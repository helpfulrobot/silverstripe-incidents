<?php

/**
 * Class Incident
 */
class Incident extends Dataobject implements PermissionProvider {

	/**
	 * @var array
	 */
	private static $db = array(
		'Title' => 'Varchar',
		'Notes' => 'Text',
		'Category' => 'Varchar',
		'Status' => "Enum('Open, Closed', 'Open')",
		'StartTime' => 'SS_Datetime',
		'EndTime' => 'SS_Datetime',
		'ReportedBy' => 'Varchar',
		'Resolved' => 'Boolean',
		'InterimReportLink' => 'Text',
		'InterimReportSent' => 'Boolean',
		'FinalReportLink' => 'Text',
		'FinalReportSent' => 'Boolean',
	);

	/**
	 * @var array
	 */
	private static $summary_fields = array(
		'Status',
		'Title',
		'StartTime',
		'Category',
		'EndTime',
		'Assignee.Email',
		'InterimReportLink',
		'Resolved',
		'FinalReportLink'
	);

	/**
	 * @var string
	 */
	private static $default_sort = 'StartTime, LastEdited';

	/**
	 * @var array
	 */
	private static $has_one = array(
		"Assignee" => 'Member',
		'Clients' => "IncidentClient"
	);

	/**
	 * Use a casting object for a field. This is a map from
	 * field name to class name of the casting object.
	 * @var array
	 */
	private static $casting = array(
		"StartTime" => "SS_Datetime->FormatFromSettings",
		"EndTime" => "SS_Datetime->FormatFromSettings",
	);

	/**
	 * @return FieldList
	 */
	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$opsMember = array();
		foreach(Group::get() as $group) {
			if($group->Permissions()->filter("Code", "INCIDENTS.STAFF")->count()) {
				foreach($group->Members() as $member) {
					$opsMember[$member->ID] = $member->FirstName.' '.$member->Surname.' <'.$member->Email.'>';
				}
			}
		}

		$assignable = new DropdownField('AssigneeID', 'Assigned to', $opsMember);
		$fields->replaceField('AssigneeID', $assignable);

		$startTime = new DatetimeField('StartTime', 'Start time');
		$startTime->getDateField()->setConfig('showcalendar', 1);
		$fields->replaceField('StartTime', $startTime);

		$endTime = new DatetimeField('EndTime', 'End time');
		$endTime->getDateField()->setConfig('showcalendar', 1);
		$fields->replaceField('EndTime', $endTime);

		$interimReportLink = new TextField('InterimReportLink', 'Interim report link');
		$fields->replaceField('InterimReportLink', $interimReportLink);

		$interimSent = new CheckboxField('InterimReportSent', 'Interim report sent?');
		$fields->insertAfter($interimSent, 'InterimReportLink');

		$finalReportLink = new TextField('FinalReportLink', 'Final report link');
		$fields->replaceField('FinalReportLink', $finalReportLink);

		$interimSent = new CheckboxField('FinalReportSent', 'Final report sent?');
		$fields->insertAfter($interimSent, 'FinalReportLink');

		return $fields;
	}

	/**
	 * @param null $member
	 * @return bool|void
	 */
	public function canEdit($member = null) {
		if(!$member) {
			$member = Member::currentUser();
		}
		if(!$member instanceof Member && $member->ID) {
			return false;
		}
		return Permission::check('INCIDENTS.STAFF', 'any', $member);
	}

	/**
	 * @param null $member
	 * @return bool
	 */
	public function canView($member = null) {
		if(!$member) {
			$member = Member::currentUser();
		}
		if(!$member instanceof Member && $member->ID) {
			return false;
		}
		if($this->canEdit($member)) {
			return true;
		}
		//@todo check for member in $this->Clients
	}


	/**
	 * Return a map of permission codes to add to the dropdown shown in the Security section of the CMS.
	 *
	 * @return array
	 */
	public function providePermissions() {
		return array(
			'INCIDENTS.STAFF' => array(
				'name' => 'Add, Change and View all incidents',
				'category' => "Incidents",
				'help' => 'This permission should be for the groups that handles incidents, ie ops',
				'sort' => 0
			)
		);
	}

}