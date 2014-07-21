<?php


/**
 * Class IncidentHolderPage
 */
class IncidentHolderPage extends Page {

	/**
	 * @param null $member
	 * @return bool
	 */
	public function canView($member = null) {
		if(!$member) {
			$member = Member::currentUser();
		}
		if($member && !$member instanceof Member) {
			return false;
		}
		if($this->canEdit($member)) {
			return true;
		}

		if($member && Permission::checkMember($member, "INCIDENTS.STAFF")) {
			return true;
		}
		//@todo check for member in $this->Clients
	}

	/**
	 * @param null $params
	 * @return FieldList
	 */
	public function getFrontendFields($params = NULL) {
		$fields = new FieldList();

		$config = new FrontEndGridFieldConfig_RecordEditor(20);
		$gf = new FrontEndGridField('Incidents', 'Incidents', Incident::get(), $config);

		$fields->add($gf);
		return $fields;
	}
}

/**
 * Class IncidentHolderPage_Controller
 */
class IncidentHolderPage_Controller extends Page_Controller {

	/**
	 *
	 * @var array
	 */
	private static $allowed_actions = array(
		'IncidentForm'
	);

	/**
	 *
	 * @return Form
	 */
	public function Form() {
		$fields = $this->failover->getFrontendFields();
		$actions = new FieldList();
		return Form::create($this, 'IncidentForm', $fields, $actions);
	}

	/**
	 * @param SS_HTTPRequest $request
	 * @return Form
	 */
	public function IncidentForm(SS_HTTPRequest $request) {
		return $this->customise(array(
			'Form' => $this->Form()->handleRequest($request, new DataModel()))
		)->renderWith(array('Page'));

	}
}