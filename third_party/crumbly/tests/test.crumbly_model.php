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
		$this->_package_name 	= 'example_package';
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


	public function test__delete_all_glossary_terms__success()
	{
		$where = array('site_id' => $this->_site_id);
		$this->_ee->db->expectOnce('delete', array('crumbly_glossary', $where));
	
		$this->assertIdentical(TRUE, $this->_subject->delete_all_glossary_terms());
	}


	public function test__get_all_crumbly_glossary_terms__success()
	{
		$this->_ee->db->expectOnce('select', array('glossary_definition, glossary_term, glossary_term_id'));
		$this->_ee->db->expectOnce('get_where', array('crumbly_glossary', array('site_id' => $this->_site_id)));

		$db_result	= $this->_get_mock('db_query');
		$db_rows	= array(
			array(
				'glossary_definition'	=> 'Definition A',
				'glossary_term'			=> 'term_a',
				'glossary_term_id'		=> 10
			),
			array(
				'glossary_definition'	=> 'Definition B',
				'glossary_term'			=> 'term_b',
				'glossary_term_id'		=> 20
			)
		);

		$this->_ee->db->setReturnReference('get_where', $db_result);
		$db_result->setReturnValue('result_array', $db_rows);

		foreach ($db_rows AS $db_row)
		{
			$return[] = new Crumbly_glossary_term($db_row);
		}
	
		$result = $this->_subject->get_all_crumbly_glossary_terms();
		$this->assertIdentical(count($result), count($return));

		for ($count = 0, $length = count($result); $count < $length; $count++)
		{
			$this->assertIdentical($result[$count], $return[$count]);
		}
	}


	public function test__get_all_crumbly_templates__success()
	{
		$this->_ee->db->expectOnce('select', array('label, template_id'));
		$this->_ee->db->expectOnce('get_where', array('crumbly_templates', array('site_id' => $this->_site_id)));
	
		$db_templates	= $this->_get_mock('db_query');
		$db_rows		= array(
			array(
				'label'			=> 'Template A',
				'template_id'	=> '10'
			),
			array(
				'label'			=> 'Template B',
				'template_id'	=> '20'
			),
			array(
				'label'			=> 'Template C',
				'template_id'	=> '30'
			)
		);

		$this->_ee->db->setReturnReference('get_where', $db_templates);
		$db_templates->setReturnValue('result_array', $db_rows);

		foreach ($db_rows AS $db_row)
		{
			$expected_result[] = new Crumbly_template($db_row);
		}

		$actual_result = $this->_subject->get_all_crumbly_templates();
		$this->assertIdentical(count($actual_result), count($expected_result));

		for ($count = 0, $length = count($actual_result); $count < $length; $count++)
		{
			$this->assertIdentical($actual_result[$count], $expected_result[$count]);
		}
	}


	public function test__get_all_crumbly_templates__no_templates()
	{
		$db_templates = $this->_get_mock('db_query');

		$this->_ee->db->setReturnReference('get_where', $db_templates);
		$db_templates->setReturnValue('result_array', array());
	
		$this->assertIdentical(array(), $this->_subject->get_all_crumbly_templates());
	}


	public function test__get_all_crumbly_template_groups__success()
	{
		$this->_ee->db->expectOnce('select', array('group_id, label'));
		$this->_ee->db->expectOnce('get_where', array('crumbly_template_groups', array('site_id' => $this->_site_id)));
	
		$db_groups	= $this->_get_mock('db_query');
		$db_rows	= array(
			array(
				'group_id'	=> '10',
				'label'		=> 'Template A'
			),
			array(
				'group_id'	=> '20',
				'label'		=> 'Template B'
			),
			array(
				'group_id'	=> '30',
				'label'		=> 'Template C'
			)
		);

		$this->_ee->db->setReturnReference('get_where', $db_groups);
		$db_groups->setReturnValue('result_array', $db_rows);

		foreach ($db_rows AS $db_row)
		{
			$expected_result[] = new Crumbly_template_group($db_row);
		}

		$actual_result = $this->_subject->get_all_crumbly_template_groups();
		$this->assertIdentical(count($actual_result), count($expected_result));

		for ($count = 0, $length = count($actual_result); $count < $length; $count++)
		{
			$this->assertIdentical($actual_result[$count], $expected_result[$count]);
		}
	}


	public function test__get_all_crumbly_template_groups__no_groups()
	{
		$db_groups = $this->_get_mock('db_query');

		$this->_ee->db->setReturnReference('get_where', $db_groups);
		$db_groups->setReturnValue('result_array', array());
	
		$this->assertIdentical(array(), $this->_subject->get_all_crumbly_template_groups());
	}


	public function test__get_all_templates__success()
	{
		$this->_ee->db->expectOnce('select', array('group_id, template_id, template_name'));
		$this->_ee->db->expectOnce('get_where', array('templates', array('site_id' => $this->_site_id, 'template_type' => 'webpage')));

		$db_result = $this->_get_mock('db_query');
		$db_rows = array(
			array(
				'group_id'		=> '10',
				'template_id'	=> '15',
				'template_name'	=> 'template_a'
			),
			array(
				'group_id'		=> '10',
				'template_id'	=> '25',
				'template_name'	=> 'template_b'
			)
		);

		$this->_ee->db->setReturnReference('get_where', $db_result);
		$db_result->setReturnValue('result_array', $db_rows);

		foreach ($db_rows AS $db_row)
		{
			$expected_result[] = new EI_template($db_row);
		}

		$actual_result = $this->_subject->get_all_templates();
		$this->assertIdentical(count($actual_result), count($expected_result));

		for ($count = 0, $length = count($actual_result); $count < $length; $count++)
		{
			$this->assertIdentical($actual_result[$count], $expected_result[$count]);
		}
	}


	public function test__get_all_templates__no_templates()
	{
		$db_result	= $this->_get_mock('db_query');
		$db_rows	= array();

		$this->_ee->db->setReturnReference('get_where', $db_result);
		$db_result->setReturnValue('result_array', $db_rows);

		$this->assertIdentical(array(), $this->_subject->get_all_templates());
	}


	public function test__get_all_template_groups__success()
	{
		$db_result	= $this->_get_mock('db_query');
		$db_rows	= array(
			array(
				'group_id'		=> '10',
				'group_name'	=> 'group_a'
			),
			array(
				'group_id'		=> '20',
				'group_name'	=> 'group_b'
			),
			array(
				'group_id'		=> '30',
				'group_name'	=> 'group_c'
			)
		);

		$this->_ee->db->expectOnce('select', array('group_id, group_name'));
		$this->_ee->db->expectOnce('get_where', array('template_groups', array('site_id' => $this->_site_id)));

		$this->_ee->db->setReturnReference('get_where', $db_result);
		$db_result->setReturnValue('result_array', $db_rows);

		foreach ($db_rows AS $db_row)
		{
			$expected_result[] = new EI_template_group($db_row);
		}

		$actual_result = $this->_subject->get_all_template_groups();
		$this->assertIdentical(count($actual_result), count($expected_result));

		for ($count = 0, $length = count($actual_result); $count < $length; $count++)
		{
			$this->assertIdentical($actual_result[$count], $expected_result[$count]);
		}
	}


	public function test__get_all_template_groups__no_templates()
	{
		$db_result	= $this->_get_mock('db_query');
		$db_rows	= array();

		$this->_ee->db->setReturnReference('get_where', $db_result);
		$db_result->setReturnValue('result_array', $db_rows);

		$this->assertIdentical(array(), $this->_subject->get_all_template_groups());
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


	public function test__get_package_theme_url__end_slash_exists()
	{
		// Dummy values.
		$config_theme_url	= 'http://example.com/themes/';
		$return_theme_url	= 'http://example.com/themes/third_party/' .$this->_package_name .'/';
		
		// Expectations and return values.
		$this->_ee->config->expectOnce('item', array('theme_folder_url'));
		$this->_ee->config->setReturnValue('item', $config_theme_url, array('theme_folder_url'));

		// Run the tests.
		$this->assertIdentical($return_theme_url, $this->_subject->get_package_theme_url());
	}
		

	public function test__get_package_theme_url__no_end_slash_exists()
	{
		// Dummy values.
		$config_theme_url	= 'http://example.com/themes';
		$return_theme_url	= 'http://example.com/themes/third_party/' .$this->_package_name .'/';
		
		// Expectations and return values.
		$this->_ee->config->expectOnce('item', array('theme_folder_url'));
		$this->_ee->config->setReturnValue('item', $config_theme_url, array('theme_folder_url'));

		// Run the tests.
		$this->assertIdentical($return_theme_url, $this->_subject->get_package_theme_url());
	}
		

	public function test__get_site_id__success()
	{
		// Expectations.
		$this->_ee->config->expectOnce('item', array('site_id'));
		
		// Tests.
		$this->assertIdentical(intval($this->_site_id), $this->_subject->get_site_id());
	}
	
	
	public function test__get_templates_by_template_group__success()
	{
		$group_id = 10;

		$this->_ee->db->expectOnce('select', array('group_id, template_id, template_name'));
		$this->_ee->db->expectOnce('get_where', array(
			'templates',
			array('group_id' => $group_id, 'template_type' => 'webpage')
		));

		$db_result = $this->_get_mock('db_query');
		$db_rows = array(
			array(
				'group_id'		=> $group_id,
				'template_id'	=> '15',
				'template_name'	=> 'template_a'
			),
			array(
				'group_id'		=> $group_id,
				'template_id'	=> '25',
				'template_name'	=> 'template_b'
			)
		);

		$this->_ee->db->setReturnReference('get_where', $db_result);
		$db_result->setReturnValue('result_array', $db_rows);

		foreach ($db_rows AS $db_row)
		{
			$expected_result[] = new EI_template($db_row);
		}

		$actual_result = $this->_subject->get_templates_by_template_group($group_id);
		$this->assertIdentical(count($actual_result), count($expected_result));

		for ($count = 0, $length = count($actual_result); $count < $length; $count++)
		{
			$this->assertIdentical($actual_result[$count], $expected_result[$count]);
		}
	}


	public function test__get_templates_by_template_group__no_templates()
	{
		$group_id	= 10;
		$db_result	= $this->_get_mock('db_query');

		$this->_ee->db->setReturnReference('get_where', $db_result);
		$db_result->setReturnValue('result_array', array());
	
		$this->assertIdentical(array(), $this->_subject->get_templates_by_template_group($group_id));
	}


	public function test__get_templates_by_template_group__invalid_group_id()
	{
		$group_id = FALSE;

		$this->_ee->db->expectNever('select');
		$this->_ee->db->expectNever('get_where');
		$this->assertIdentical(FALSE, $this->_subject->get_templates_by_template_group($group_id));
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


	public function test__install_module_glossary_table__success()
	{
		$fields = array(
			'glossary_term_id' => array(
				'auto_increment'	=> TRUE,
				'constraint'		=> 10,
				'type'				=> 'INT',
				'unsigned'			=> TRUE
			),
			'site_id' => array(
				'constraint'		=> 5,
				'type'				=> 'INT',
				'unsigned'			=> TRUE
			),
			'glossary_definition' => array(
				'constraint'		=> 255,
				'type'				=> 'VARCHAR'
			),
			'glossary_term' => array(
				'constraint'		=> 255,
				'type'				=> 'VARCHAR'
			)
		);

		$this->_ee->dbforge->expectOnce('add_field', array($fields));
		$this->_ee->dbforge->expectCallCount('add_key', 2);
		$this->_ee->dbforge->expectAt(0, 'add_key', array('glossary_term_id', TRUE));
		$this->_ee->dbforge->expectAt(1, 'add_key', array('site_id'));
		$this->_ee->dbforge->expectOnce('create_table', array('crumbly_glossary', TRUE));
	
		$this->_subject->install_module_glossary_table();
	}


	public function test__install_module_register__success()
	{
		// Dummy values.
		$query_data = array(
			'has_cp_backend'		=> 'y',
			'has_publish_fields'	=> 'n',
			'module_name'			=> ucfirst($this->_package_name),
			'module_version'		=> $this->_package_version
		);
		
		// Expectations.
		$this->_ee->db->expectOnce('insert', array('modules', $query_data));
		
		// Tests.
		$this->_subject->install_module_register();
	}
	
		
	public function test__install_module_templates_table__success()
	{
		$fields = array(
			'template_id' => array(
				'constraint'		=> 10,
				'type'				=> 'INT',
				'unsigned'			=> TRUE
			),
			'site_id' => array(
				'constraint'		=> 5,
				'type'				=> 'INT',
				'unsigned'			=> TRUE
			),
			'label' => array(
				'constraint'		=> 255,
				'type'				=> 'VARCHAR'
			)
		);

		$this->_ee->dbforge->expectOnce('add_field', array($fields));
		$this->_ee->dbforge->expectCallCount('add_key', 2);
		$this->_ee->dbforge->expectAt(0, 'add_key', array('site_id'));
		$this->_ee->dbforge->expectAt(1, 'add_key', array('template_id', TRUE));
		$this->_ee->dbforge->expectOnce('create_table', array('crumbly_templates', TRUE));
	
		$this->_subject->install_module_templates_table();
	}


	public function test__install_module_template_groups_table__success()
	{
		$fields = array(
			'group_id' => array(
				'constraint'		=> 10,
				'type'				=> 'INT',
				'unsigned'			=> TRUE
			),
			'site_id' => array(
				'constraint'		=> 5,
				'type'				=> 'INT',
				'unsigned'			=> TRUE
			),
			'label' => array(
				'constraint'		=> 255,
				'type'				=> 'VARCHAR'
			)
		);

		$this->_ee->dbforge->expectOnce('add_field', array($fields));
		$this->_ee->dbforge->expectCallCount('add_key', 2);
		$this->_ee->dbforge->expectAt(0, 'add_key', array('site_id'));
		$this->_ee->dbforge->expectAt(1, 'add_key', array('group_id', TRUE));
		$this->_ee->dbforge->expectOnce('create_table', array('crumbly_template_groups', TRUE));
	
		$this->_subject->install_module_template_groups_table();
	}


	public function test__save_crumbly_glossary_term__create_success()
	{
		$definition		= 'Definition';
		$term			= 'term';
		$glossary_term	= new Crumbly_glossary_term(array(
			'glossary_definition'	=> $definition,
			'glossary_term'			=> $term
		));

		$insert_data = array(
			'glossary_definition'	=> $definition,
			'glossary_term'			=> $term,
			'site_id'				=> $this->_site_id
		);

		$this->_ee->db->expectOnce('insert', array('crumbly_glossary', $insert_data));
		$this->_ee->db->expectNever('update');
	
		$this->assertEqual(TRUE, $this->_subject->save_crumbly_glossary_term($glossary_term));
	}


	public function test__save_crumbly_glossary_term__update_success()
	{
		$definition		= 'Definition';
		$term			= 'term';
		$term_id		= 10;
		$glossary_term	= new Crumbly_glossary_term(array(
			'glossary_definition'	=> $definition,
			'glossary_term'			=> $term,
			'glossary_term_id'		=> $term_id
		));

		$insert_data = array(
			'glossary_definition'	=> $definition,
			'glossary_term'			=> $term,
			'site_id'				=> $this->_site_id
		);

		$where = array('glossary_term_id' => $term_id);

		$this->_ee->db->expectOnce('update', array('crumbly_glossary', $insert_data, $where));
		$this->_ee->db->expectNever('insert');

		$this->assertEqual(TRUE, $this->_subject->save_crumbly_glossary_term($glossary_term));
	}


	public function test__save_crumbly_glossary_term__missing_glossary_definition()
	{
		$glossary_term = new Crumbly_glossary_term(array('glossary_term' => 'term'));

		$this->_ee->db->expectNever('insert');
		$this->_ee->db->expectNever('update');
	
		$this->assertEqual(FALSE, $this->_subject->save_crumbly_glossary_term($glossary_term));
	}


	public function test__save_crumbly_glossary_term__missing_glossary_term()
	{
		$glossary_term = new Crumbly_glossary_term(array('glossary_definition' => 'Definition'));

		$this->_ee->db->expectNever('insert');
		$this->_ee->db->expectNever('update');
	
		$this->assertEqual(FALSE, $this->_subject->save_crumbly_glossary_term($glossary_term));
	}


	public function test__save_crumbly_template__success()
	{
		$template = new Crumbly_template(array(
			'label'			=> 'Example template',
			'template_id'	=> 20
		));

		$insert_data = array(
			'label'			=> $template->get_label(),
			'template_id'	=> $template->get_template_id(),
			'site_id'		=> $this->_site_id
		);

		$delete_criteria = array(
			'site_id'		=> $this->_site_id,
			'template_id'	=> $template->get_template_id()
		);

		$this->_ee->db->expectOnce('delete', array('crumbly_templates', $delete_criteria));
		$this->_ee->db->expectOnce('insert', array('crumbly_templates', $insert_data));
	
		$this->assertEqual(TRUE, $this->_subject->save_crumbly_template($template));
	}


	public function test__save_crumbly_template__missing_label()
	{
		$template = new Crumbly_template(array('template_id' => 20));
	
		$this->_ee->db->expectNever('delete');
		$this->_ee->db->expectNever('insert');

		$this->assertIdentical(FALSE, $this->_subject->save_crumbly_template($template));
	}


	public function test__save_crumbly_template__missing_template_id()
	{
		$template = new Crumbly_template(array('label' => 'Example label'));
	
		$this->_ee->db->expectNever('delete');
		$this->_ee->db->expectNever('insert');

		$this->assertIdentical(FALSE, $this->_subject->save_crumbly_template($template));
	}


	public function test__save_crumbly_template_group__success()
	{
		$group = new Crumbly_template_group(array(
			'group_id'	=> 10,
			'label'		=> 'Example group'
		));

		$insert_data = array(
			'group_id'	=> $group->get_group_id(),
			'label'		=> $group->get_label(),
			'site_id'	=> $this->_site_id
		);

		$delete_criteria = array(
			'group_id'	=> $group->get_group_id(),
			'site_id'	=> $this->_site_id
		);

		$this->_ee->db->expectOnce('delete', array('crumbly_template_groups', $delete_criteria));
		$this->_ee->db->expectOnce('insert', array('crumbly_template_groups', $insert_data));
	
		$this->assertIdentical(TRUE, $this->_subject->save_crumbly_template_group($group));
	}


	public function test__save_crumbly_template_group__missing_group_id()
	{
		$group = new Crumbly_template_group(array('label' => 'Example group'));

		$this->_ee->db->expectNever('delete');
		$this->_ee->db->expectNever('insert');
	
		$this->assertIdentical(FALSE, $this->_subject->save_crumbly_template_group($group));
	}


	public function test__save_crumbly_template_group__missing_label()
	{
		$group = new Crumbly_template_group(array('group_id' => 10));

		$this->_ee->db->expectNever('delete');
		$this->_ee->db->expectNever('insert');
	
		$this->assertIdentical(FALSE, $this->_subject->save_crumbly_template_group($group));
	}


	public function test__uninstall_module__success()
	{
		$db_module_result 			= $this->_get_mock('db_query');
		$db_module_row 				= new StdClass();
		$db_module_row->module_id	= '10';
		$module_name				= ucfirst($this->_package_name);
		
		$this->_ee->db->expectOnce('select', array('module_id'));
		$this->_ee->db->expectOnce('get_where', array('modules', array('module_name' => $module_name), 1));
		$this->_ee->db->setReturnReference('get_where', $db_module_result);
		$db_module_result->setReturnValue('num_rows', 1);
		$db_module_result->setReturnValue('row', $db_module_row);
		
		$this->_ee->db->expectCallCount('delete', 2);
		$this->_ee->db->expectAt(0, 'delete', array('module_member_groups', array('module_id' => $db_module_row->module_id)));
		$this->_ee->db->expectAt(1, 'delete', array('modules', array('module_name' => $module_name)));
				
		$this->_ee->dbforge->expectCallCount('drop_table', 3);
		$this->_ee->dbforge->expectAt(0, 'drop_table', array('crumbly_glossary'));
		$this->_ee->dbforge->expectAt(1, 'drop_table', array('crumbly_templates'));
		$this->_ee->dbforge->expectAt(2, 'drop_table', array('crumbly_template_groups'));

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
