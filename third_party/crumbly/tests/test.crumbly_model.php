<?php

/**
 * Crumbly model tests.
 *
 * @author			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright		Experience Internet
 * @package			Crumbly
 */

require_once PATH_THIRD .'crumbly/models/crumbly_model' .EXT;

class Test_crumbly_model extends Testee_unit_test_case {
	
	/* --------------------------------------------------------------
	 * PRIVATE PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * Package name.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_package_name;
	
	/**
	 * Package version.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_package_version;
	
	/**
	 * Site ID.
	 *
	 * @access	private
	 * @var		int
	 */
	private $_site_id;
	
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
		
		// Dummy package name and version.
		$this->_package_name 	= 'Example_package';
		$this->_package_version	= '1.0.0';
		
		// Dummy site ID value.
		$this->_site_id = 10;
		$this->_ee->config->setReturnValue('item', $this->_site_id, array('site_id'));
		
		// The test subject.
		$this->_subject = new Crumbly_model($this->_package_name, $this->_package_version);
	}


	public function test__get_channel_entry_title_from_segment__url_title_success()
	{
		// Shortcuts.
		$db = $this->_ee->db;

		// Dummy values.
		$url_title	= 'white_stripes';
		$title		= 'The White Stripes';

		$query_result		= $this->_get_mock('db_query');
		$query_row			= new StdClass();
		$query_row->title	= $title;

		// Expectations.
		$db->expectOnce('select', array('title'));
		$db->expectOnce('get_where', array('channel_titles', array('url_title' => $url_title), 1));

		$query_result->expectOnce('num_rows');
		$query_result->expectOnce('row');

		// Return values.
		$db->setReturnReference('get_where', $query_result);
		$query_result->setReturnValue('num_rows', 1);
		$query_result->setReturnValue('row', $query_row);

		// Run the tests.
		$this->assertIdentical($title, $this->_subject->get_channel_entry_title_from_segment($url_title));
	}


	public function test__get_channel_entry_title_from_segment__entry_id_success()
	{
		// Shortcuts.
		$db = $this->_ee->db;

		// Dummy values.
		$entry_id	= '10';
		$title		= 'The White Stripes';

		$query_result		= $this->_get_mock('db_query');
		$query_row			= new StdClass();
		$query_row->title	= $title;

		// Expectations.
		$db->expectOnce('select', array('title'));
		$db->expectOnce('get_where', array('channel_titles', array('entry_id' => $entry_id), 1));

		$query_result->expectOnce('num_rows');
		$query_result->expectOnce('row');

		// Return values.
		$db->setReturnReference('get_where', $query_result);
		$query_result->setReturnValue('num_rows', 1);
		$query_result->setReturnValue('row', $query_row);

		// Run the tests.
		$this->assertIdentical($title, $this->_subject->get_channel_entry_title_from_segment($entry_id));
	}


	public function test__get_channel_entry_title_from_segment__no_segment()
	{
		// Shortcuts.
		$db = $this->_ee->db;

		// Dummy values.
		$segment = '';;

		// Expectations.
		$db->expectNever('select');
		$db->expectNever('get_where');

		// Run the tests.
		$this->assertIdentical(FALSE, $this->_subject->get_channel_entry_title_from_segment($segment));

	}


	public function test__get_channel_entry_title_from_segment__segment_not_found()
	{
		// Shortcuts.
		$db = $this->_ee->db;

		// Dummy values.
		$url_title		= 'white_stripes';
		$query_result	= $this->_get_mock('db_query');

		// Expectations.
		$db->expectOnce('select', array('title'));
		$db->expectOnce('get_where', array('channel_titles', array('url_title' => $url_title), 1));

		$query_result->expectOnce('num_rows');
		$query_result->expectNever('row');

		// Return values.
		$db->setReturnReference('get_where', $query_result);
		$query_result->setReturnValue('num_rows', 0);

		// Run the tests.
		$this->assertIdentical(FALSE, $this->_subject->get_channel_entry_title_from_segment($url_title));
	}
	
	
	public function test__constructor__package_name_and_version()
	{
		// Dummy values.
		$package_name 		= 'Example_package';
		$package_version	= '1.0.0';

		// Tests.
		$subject = new Crumbly_model($package_name, $package_version);
		$this->assertIdentical($package_name, $subject->get_package_name());
		$this->assertIdentical($package_version, $subject->get_package_version());
	}


	public function test__get_package_settings__success()
	{
		// Shortcuts.
		$config = $this->_ee->config;

		// Dummy values.
		$settings = array(
			'glossary' => array('wig' => 'Merkin'),
			'template_groups' => array(
				'about' => array(
					'title' => 'About Us',
					'templates' => array(
						'founder'	=> 'Our Founder',
						'history'	=> 'Our History',
						'team'		=> 'Meet the Team'
					)
				),
				'blog' => array(
					'title' => 'Blog',
					'templates' => array(
						'archive' => 'Archived Posts'
					)
				),
				'contact' => array(
					'title' => 'Contact Us',
					'templates' => array(
						'error' => 'OMG! Teh Epic Internet Failerz! LOLZ!',
						'thank-you' => 'Thanks You!',
					)
				)
			)
		);

		// Expectations.
		$config->expectOnce('item', array('crumbly_settings'));

		// Return values.
		$config->setReturnValue('item', $settings);

		// Run the tests.
		$this->assertIdentical($settings, $this->_subject->get_package_settings());

		// Settings should be cached by the model, so the expectation counts should still pass.
		$this->assertIdentical($settings, $this->_subject->get_package_settings());
	}


	public function test__get_site_id__success()
	{
		// Expectations.
		$this->_ee->config->expectOnce('item', array('site_id'));
		
		// Tests.
		$this->assertIdentical(intval($this->_site_id), $this->_subject->get_site_id());
	}
	
	
	public function test__humanize__no_glossary_underscore_success()
	{
		// Dummy values.
		$machine	= 'about_us';
		$human		= 'About Us';

		// Retrieve the word separator.
		$this->_ee->config->expectOnce('item', array('word_separator'));
		$this->_ee->config->setReturnValue('item', 'underscore', array('word_separator'));

		// Run the tests.
		$this->assertIdentical($human, $this->_subject->humanize($machine, FALSE));
	}


	public function test__humanize__no_glossary_dash_success()
	{
		// Dummy values.
		$machine	= 'about-us';
		$human		= 'About Us';

		// Retrieve the word separator.
		$this->_ee->config->expectOnce('item', array('word_separator'));
		$this->_ee->config->setReturnValue('item', 'dash', array('word_separator'));

		// Run the tests.
		$this->assertIdentical($human, $this->_subject->humanize($machine, FALSE));
	}


	public function test__humanize__no_glossary_undefined_separator_dash()
	{
		// Dummy values.
		$machine	= 'about-us';
		$human		= 'About Us';

		// Retrieve the word separator.
		$this->_ee->config->expectOnce('item', array('word_separator'));
		$this->_ee->config->setReturnValue('item', FALSE, array('word_separator'));

		// Run the tests.
		$this->assertIdentical($human, $this->_subject->humanize($machine, FALSE));
	}


	public function test__humanize__glossary_success()
	{
		// Shortcuts.
		$config = $this->_ee->config;

		// Dummy values.
		$settings	= array('glossary' => array('room' => 'Zimmer'));
		$machine	= 'room';
		$human		= 'Zimmer';

		// Return values (used in `get_package_settings`).
		$config->setReturnValue('item', $settings);

		// Run the tests.
		$this->assertIdentical($human, $this->_subject->humanize($machine));
	}


	public function test__humanize__no_machine_string()
	{
		// Dummy values.
		$machine	= '';
		$human		= '';

		// Expectations.
		$this->_ee->config->expectNever('item');

		// Run the tests.
		$this->assertIdentical($human, $this->_subject->humanize($machine));
	}


	public function test__install_module_register__success()
	{
		// Dummy values.
		$query_data = array(
			'has_cp_backend'		=> 'y',
			'has_publish_fields'	=> 'n',
			'module_name'			=> $this->_package_name,
			'module_version'		=> $this->_package_version
		);
		
		// Expectations.
		$this->_ee->db->expectOnce('insert', array('modules', $query_data));
		
		// Tests.
		$this->_subject->install_module_register();
	}
	
		
	public function test__uninstall_module__success()
	{
		// Dummy values.
		$db_module_result 			= $this->_get_mock('db_query');
		$db_module_row 				= new StdClass();
		$db_module_row->module_id	= '10';
		
		// Expectations.
		$this->_ee->db->expectOnce('select', array('module_id'));
		$this->_ee->db->expectOnce('get_where', array('modules', array('module_name' => $this->_package_name), 1));
		
		$this->_ee->db->expectCallCount('delete', 2);
		$this->_ee->db->expectAt(0, 'delete', array('module_member_groups', array('module_id' => $db_module_row->module_id)));
		$this->_ee->db->expectAt(1, 'delete', array('modules', array('module_name' => $this->_package_name)));
				
		// Return values.
		$this->_ee->db->setReturnReference('get_where', $db_module_result);
		$db_module_result->setReturnValue('num_rows', 1);
		$db_module_result->setReturnValue('row', $db_module_row);
		
		// Tests.
		$this->assertIdentical(TRUE, $this->_subject->uninstall_module());
	}
	
	
	public function test__uninstall_module__module_not_found()
	{
		// Dummy values.
		$db_module_result = $this->_get_mock('db_query');
		
		// Expectations.
		$this->_ee->db->expectOnce('select');
		$this->_ee->db->expectOnce('get_where');
		$this->_ee->db->expectNever('delete');
		
		// Return values.
		$this->_ee->db->setReturnReference('get_where', $db_module_result);
		$db_module_result->setReturnValue('num_rows', 0);
		
		// Tests.
		$this->assertIdentical(FALSE, $this->_subject->uninstall_module());
	}
	
	
	public function test__update_module__no_update_required()
	{
		// Dummy values.
		$installed_version = $this->_package_version;

		// Tests.
		$this->assertIdentical(FALSE, $this->_subject->update_module($installed_version));
	}
	
	
	
	public function test__update_module__update_required()
	{
		// Dummy values.
		$installed_version = '0.9.0';

		// Tests.
		$this->assertIdentical(TRUE, $this->_subject->update_module($installed_version));
	}
	
	
	public function test__update_module__no_installed_version()
	{
		// Dummy values.
		$installed_version = '';

		// Tests.
		$this->assertIdentical(TRUE, $this->_subject->update_module($installed_version));
	}
	
}


/* End of file		: test.crumbly_model.php */
/* File location	: third_party/crumbly/tests/test.crumbly_model.php */
