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

	public function test__breadcrumbs__template_group_template_url_title()
	{
		// Retrieve the segments.
		$segments = array('about', 'team', 'leonard');

		$this->_ee->uri->expectOnce('segment_array');
		$this->_ee->uri->setReturnValue('segment_array', $segments);

		// Retrieve the package settings.
		$settings = array(
			'glossary' => array(),
			'template_groups' => array(
				'about' => array(
					'title' => 'About Us',
					'templates' => array(
						'company'	=> 'Our Company',
						'team'		=> 'Our Team'
					)
				),
				'blog' => array(
					'title' => 'Blog',
					'templates' => array()
				),
				'contact' => array(
					'title' => 'Contact Us',
					'templates' => array()
				)
			)
		);

		$this->_model->expectOnce('get_package_settings');
		$this->_model->setReturnValue('get_package_settings', $settings);

		// No root breadcrumb (tested separately).
		$this->_ee->functions->expectNever('fetch_site_index');
		$this->_ee->TMPL->expectOnce('fetch_param');
		$this->_ee->TMPL->setReturnValue('fetch_param', 'no', array('include_root', '*'));

		// Template tag parser.
		$tagdata = 'Tagdata';
		$this->_ee->TMPL->expectOnce('__get', array('tagdata'));
		$this->_ee->TMPL->setReturnValue('__get', $tagdata, array('tagdata'));
		
		$site_url = 'http://example.com/';
		$this->_ee->functions->expectCallCount('create_url', 3);

		// Template group URL.
		$this->_ee->functions->expectAt(0, 'create_url', array('about'));
		$this->_ee->functions->setReturnValueAt(0, 'create_url', $site_url .'about/');

		// Template URL.
		$this->_ee->functions->expectAt(1, 'create_url', array('about/team'));
		$this->_ee->functions->setReturnValueAt(1, 'create_url', $site_url .'about/team/');

		// Channel entry URL title.
		$this->_model->expectOnce('get_channel_entry_title_from_url_title', array('leonard'));
		$this->_model->setReturnValue('get_channel_entry_title_from_url_title', 'Leonard Rossiter');

		$this->_ee->functions->expectAt(2, 'create_url', array('about/team/leonard'));
		$this->_ee->functions->setReturnValueAt(2, 'create_url', $site_url .'about/team/leonard/');

		$breadcrumbs = array(
			array(
				'breadcrumb_segment'	=> 'about',
				'breadcrumb_title'		=> 'About Us',
				'breadcrumb_url'		=> $site_url .'about/'
			),
			array(
				'breadcrumb_segment'	=> 'team',
				'breadcrumb_title'		=> 'Our Team',
				'breadcrumb_url'		=> $site_url .'about/team/'
			),
			array(
				'breadcrumb_segment'	=> 'leonard',
				'breadcrumb_title'		=> 'Leonard Rossiter',
				'breadcrumb_url'		=> $site_url .'about/team/leonard/'
			)
		);

		$parsed_tagdata = 'Parsed tagdata';

		$this->_ee->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
		$this->_ee->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

		// Tests.
		$this->_subject->breadcrumbs();
	}


	public function test__breadcrumbs__template_group_not_in_settings()
	{
		// Dummy values.
		$template_group			= 'about';
		$template_group_title	= 'About';

		// Retrieve the segments.
		$segments = array($template_group);
		$this->_ee->uri->setReturnValue('segment_array', $segments);

		// Retrieve the package settings.
		$settings = array(
			'glossary' => array(),
			'template_groups' => array()
		);

		$this->_model->setReturnValue('get_package_settings', $settings);

		// Retrieve the tag parameters (no root breadcrumb).
		$this->_ee->TMPL->setReturnValue('fetch_param', 'no', array('include_root', 'yes'));

		// Determine the template group.
		$this->_model->expectOnce('humanize', array($template_group));
		$this->_model->setReturnValue('humanize', $template_group_title, array($template_group));

		// Template tag parser.
		$tagdata = 'Tagdata';
		$this->_ee->TMPL->setReturnValue('__get', $tagdata, array('tagdata'));
		
		$breadcrumbs = array(
			array('breadcrumb_title' => $template_group_title, 'url_segment' => $template_group)
		);

		$parsed_tagdata = 'Parsed tagdata';
		$this->_ee->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

		// Tests.
		$this->_subject->breadcrumbs();
	}


	public function test__breadcrumbs__include_default_root()
	{
		// Retrieve the segments (no segments).
		$this->_ee->uri->setReturnValue('segment_array', array());

		// Retrieve the package settings.
		$settings = array('glossary' => array(), 'template_groups' => array());
		$this->_model->setReturnValue('get_package_settings', $settings);

		// Retrieve the tag parameters.
		$root_label = 'Home';
		$root_url	= 'http://example.com/';

		$this->_ee->lang->expectOnce('line', array('default_root_label'));
		$this->_ee->lang->setReturnValue('line', $root_label);

		$this->_ee->functions->expectOnce('fetch_site_index');
		$this->_ee->functions->setReturnValue('fetch_site_index', $root_url);

		$this->_ee->TMPL->expectCallCount('fetch_param', 3);
		$this->_ee->TMPL->setReturnValue('fetch_param', 'yes', array('include_root', 'yes'));
		$this->_ee->TMPL->setReturnValue('fetch_param', FALSE, array('root_label', $root_label));
		$this->_ee->TMPL->setReturnValue('fetch_param', FALSE, array('root_url', $root_url));

		// Template tag parser.
		$tagdata = 'Tagdata';
		$this->_ee->TMPL->setReturnValue('__get', $tagdata, array('tagdata'));
		
		$breadcrumbs = array(
			array('breadcrumb_title' => $root_label, 'url_segment' => '')
		);

		$parsed_tagdata = 'Parsed tagdata';
		$this->_ee->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

		// Tests.
		$this->_subject->breadcrumbs();

	}


}

/* End of file		: test.mod_crumbly.php */
/* File location	: third_party/crumbly/tests/test.mod_crumbly.php */
