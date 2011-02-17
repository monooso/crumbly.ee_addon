<?php

/**
 * Crumbly module tests.
 *
 * @author			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright		Experience Internet
 * @package			Crumbly
 */

require_once PATH_THIRD .'crumbly/mod.crumbly' .EXT;
require_once PATH_THIRD .'crumbly/tests/mocks/mock.crumbly_model' .EXT;

class Test_crumbly extends Testee_unit_test_case {
	
	/* --------------------------------------------------------------
	 * PRIVATE PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * Model.
	 *
	 * @access	private
	 * @var		object
	 */
	private $_model;
	
	/**
	 * The test subject.
	 *
	 * @access	private
	 * @var		object
	 */
	private $_subject;
	
	
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */
	public function setUp()
	{
		parent::setUp();
		
		// Generate the mock model.
		Mock::generate('Mock_crumbly_model', get_class($this) .'_mock_model');
		$this->_model = $this->_get_mock('model');
		$this->_ee->crumbly_model =& $this->_model;
		
		// The test subject.
		$this->_subject = new Crumbly();
	}
	

	/* --------------------------------------------------------------
	 * TEST METHODS
	 * ------------------------------------------------------------ */

	public function test__breadcrumbs__template_group()
	{
		// Retrieve the segments.
		$segments = array('about', 'team', 'leonard');

		$this->_ee->uri->expectOnce('segment_array');
		$this->_ee->uri->setReturnValue('segment_array', $segments);

		// Retrieve the template groups.
		$settings = array(
			'template_groups' => array(
				'about'		=> 'About',
				'blog'		=> 'Blog',
				'contact'	=> 'Contact Us'
			),
			'templates' => array(
				'company'	=> 'Our Company',
				'team'		=> 'Our Team'
			)
		);

		$this->_model->expectOnce('get_settings');
		$this->_model->setReturnValue('get_settings', $settings);

		// Retrieve the tag parameters.
		$root_label = 'Home';
		$root_url	= 'http://example.com/';

		$this->_ee->lang->expectOnce('line', array('default_root_label'));
		$this->_ee->lang->setReturnValue('line', $root_label);

		$this->_ee->functions->expectOnce('fetch_site_index');
		$this->_ee->functions->setReturnValue('fetch_site_index', $root_url);

		$this->_ee->TMPL->expectCallCount('fetch_param', 3);
		$this->_ee->TMPL->setReturnValue('fetch_param', 'yes', array('include_root', 'yes'));
		$this->_ee->TMPL->setReturnValue('fetch_param', $root_label, array('root_label', $root_label));
		$this->_ee->TMPL->setReturnValue('fetch_param', $root_url, array('root_url', $root_url));

		// Template tag parser.
		$tagdata = 'Tagdata';
		$this->_ee->TMPL->expectOnce('__get', array('tagdata'));
		$this->_ee->TMPL->setReturnValue('__get', $tagdata, array('tagdata'));
		
		// Channel entry URL title.
		$this->_model->expectOnce('get_channel_entry_title_from_url_title', array('leonard'));
		$this->_model->setReturnValue('get_channel_entry_title_from_url_title', 'Leonard Rossiter');

		$breadcrumbs = array(
			array('breadcrumb_title' => $root_label, 'url_segment' => ''),
			array('breadcrumb_title' => 'About', 'url_segment' => 'about'),
			array('breadcrumb_title' => 'Our Team', 'url_segment' => 'team'),
			array('breadcrumb_title' => 'Leonard Rossiter', 'url_segment' => 'leonard')
		);

		$parsed_tagdata = 'Parsed tagdata';

		$this->_ee->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
		$this->_ee->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

		// Tests.
		$this->_subject->breadcrumbs();
	}

}


/* End of file		: test.mod_crumbly.php */
/* File location	: third_party/crumbly/tests/test.mod_crumbly.php */
