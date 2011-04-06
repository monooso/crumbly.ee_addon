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
		$segments = array();
		$segments[1]	= 'about';
		$segments[2]	= 'team';
		$segments[3]	= 'leonard';

		$this->_ee->uri->expectOnce('segment_array');
		$this->_ee->uri->setReturnValue('segment_array', $segments);

		// No root breadcrumb (tested separately).
		$this->_ee->functions->expectNever('fetch_site_index');
		$this->_ee->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', '*'));

		// Template tag parser.
		$tagdata = 'Tagdata';
		$this->_ee->TMPL->expectOnce('__get', array('tagdata'));
		$this->_ee->TMPL->setReturnValue('__get', $tagdata, array('tagdata'));
		
		$site_url = 'http://example.com/';
		$this->_ee->functions->expectCallCount('create_url', 3);

		// Template group URL.
		$template_group = new Crumbly_template_group(array('group_id' => 10, 'label' => 'About Us'));
		$this->_model->expectOnce('get_crumbly_template_group_from_segment', array($segments[1]));
		$this->_model->setReturnValue('get_crumbly_template_group_from_segment', $template_group);

		$this->_ee->functions->expectAt(0, 'create_url', array('about'));
		$this->_ee->functions->setReturnValueAt(0, 'create_url', $site_url .'about/');

		// Template URL.
		$template = new Crumbly_template(array('template_id' => 10, 'label' => 'Meet the Team'));
		$this->_model->expectOnce('get_crumbly_template_from_segments', array($segments[1], $segments[2]));
		$this->_model->setReturnValue('get_crumbly_template_from_segments', $template);

		$this->_ee->functions->expectAt(1, 'create_url', array('about/team'));
		$this->_ee->functions->setReturnValueAt(1, 'create_url', $site_url .'about/team/');

		// Channel entry URL title.
		$this->_model->expectOnce('get_channel_entry_title_from_segment', array('leonard'));
		$this->_model->setReturnValue('get_channel_entry_title_from_segment', 'Leonard Rossiter');

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
				'breadcrumb_title'		=> 'Meet the Team',
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


	public function test__breadcrumbs__template_group_not_found()
	{
		$site_url				= 'http://example.com/';
		$template_group			= 'about';
		$template_group_title	= 'About';

		// Retrieve the segments.
		$segments = array();
		$segments[1] = $template_group;

		$this->_ee->uri->setReturnValue('segment_array', $segments);

		// Retrieve the tag parameters (no root breadcrumb).
		$this->_ee->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', 'yes'));

		// Template group URL.
		$this->_model->expectOnce('get_crumbly_template_group_from_segment', array($template_group));
		$this->_model->setReturnValue('get_crumbly_template_group_from_segment', FALSE);

		$this->_model->expectOnce('humanize', array($template_group));
		$this->_model->setReturnValue('humanize', $template_group_title, array($template_group));

		$this->_ee->functions->expectOnce('create_url', array($template_group));
		$this->_ee->functions->setReturnValue('create_url', $site_url .$template_group .'/');

		// Template tag parser.
		$tagdata = 'Tagdata';
		$this->_ee->TMPL->setReturnValue('__get', $tagdata, array('tagdata'));
		
		$breadcrumbs = array(
			array(
				'breadcrumb_segment'	=> $template_group,
				'breadcrumb_title'		=> $template_group_title,
				'breadcrumb_url'		=> $site_url .$template_group .'/'
			)
		);

		$parsed_tagdata = 'Parsed tagdata';
		$this->_ee->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
		$this->_ee->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

		// Tests.
		$this->_subject->breadcrumbs();
	}


	public function test__breadcrumbs__template_group_not_found_url_includes_template()
	{
		// Dummy values.
		$template_group			= 'about';
		$template_group_title	= 'About';
		$template				= 'team';
		$template_title			= 'Team';

		// Retrieve the segments.
		$segments = array();
		$segments[1]	= $template_group;
		$segments[2]	= $template;

		$this->_ee->uri->setReturnValue('segment_array', $segments);

		// Retrieve the package settings.
		$settings = array(
			'glossary' => array(),
			'template_groups' => array()
		);

		$this->_model->setReturnValue('get_package_settings', $settings);

		// Retrieve the tag parameters (no root breadcrumb).
		$this->_ee->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', 'yes'));

		$site_url = 'http://example.com/';
		$this->_ee->functions->expectCallCount('create_url', 2);
		$this->_model->expectCallCount('humanize', 2);

		// Template group URL.
		$this->_model->expectOnce('get_crumbly_template_group_from_segment', array($template_group));
		$this->_model->setReturnValue('get_crumbly_template_group_from_segment', FALSE);

		$this->_model->setReturnValue('humanize', $template_group_title, array($template_group));
		$this->_ee->functions->setReturnValueAt(0, 'create_url', $site_url .$template_group .'/');
		
		// Template URL.
		$this->_model->setReturnValue('get_crumbly_template_from_segments', FALSE);
		$this->_ee->functions->setReturnValueAt(1, 'create_url', $site_url .$template_group .'/' .$template .'/');
		$this->_model->setReturnValue('humanize', $template_title, array($template));

		// Template tag parser.
		$tagdata = 'Tagdata';
		$this->_ee->TMPL->setReturnValue('__get', $tagdata, array('tagdata'));
		
		$breadcrumbs = array(
			array(
				'breadcrumb_segment'	=> $template_group,
				'breadcrumb_title'		=> $template_group_title,
				'breadcrumb_url'		=> $site_url .$template_group .'/'
			),
			array(
				'breadcrumb_segment'	=> $template,
				'breadcrumb_title'		=> $template_title,
				'breadcrumb_url'		=> $site_url .$template_group .'/' .$template .'/'
			)
		);

		$parsed_tagdata = 'Parsed tagdata';
		$this->_ee->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
		$this->_ee->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

		// Tests.
		$this->_subject->breadcrumbs();
	}


	public function test__breadcrumbs__template_not_found()
	{
		// Retrieve the segments.
		$segments		= array();
		$segments[1]	= 'about';
		$segments[2]	= 'team';

		$this->_ee->uri->expectOnce('segment_array');
		$this->_ee->uri->setReturnValue('segment_array', $segments);

		// No root breadcrumb (tested separately).
		$this->_ee->functions->expectNever('fetch_site_index');
		$this->_ee->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', '*'));

		// Template tag parser.
		$tagdata = 'Tagdata';
		$this->_ee->TMPL->expectOnce('__get', array('tagdata'));
		$this->_ee->TMPL->setReturnValue('__get', $tagdata, array('tagdata'));
		
		$site_url = 'http://example.com/';
		$this->_ee->functions->expectCallCount('create_url', 2);

		// Template group URL.
		$template_group = new Crumbly_template_group(array('group_id' => 10, 'label' => 'About Us'));
		$this->_model->expectOnce('get_crumbly_template_group_from_segment', array($segments[1]));
		$this->_model->setReturnValue('get_crumbly_template_group_from_segment', $template_group);

		$this->_ee->functions->expectAt(0, 'create_url', array('about'));
		$this->_ee->functions->setReturnValueAt(0, 'create_url', $site_url .'about/');

		// Template URL.
		$this->_model->expectOnce('get_crumbly_template_from_segments', array($segments[1], $segments[2]));
		$this->_model->setReturnValue('get_crumbly_template_from_segments', FALSE);

		$this->_ee->functions->expectAt(1, 'create_url', array('about/team'));
		$this->_ee->functions->setReturnValueAt(1, 'create_url', $site_url .'about/team/');

		$this->_model->expectOnce('humanize', array('team'));
		$this->_model->setReturnValue('humanize', 'Team');

		$breadcrumbs = array(
			array(
				'breadcrumb_segment'	=> 'about',
				'breadcrumb_title'		=> 'About Us',
				'breadcrumb_url'		=> $site_url .'about/'
			),
			array(
				'breadcrumb_segment'	=> 'team',
				'breadcrumb_title'		=> 'Team',
				'breadcrumb_url'		=> $site_url .'about/team/'
			)
		);

		$parsed_tagdata = 'Parsed tagdata';

		$this->_ee->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
		$this->_ee->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

		// Tests.
		$this->_subject->breadcrumbs();
	}


	public function test__breadcrumbs__include_default_root()
	{
		// Retrieve the segments (no segments).
		$this->_ee->uri->setReturnValue('segment_array', array());

		// Retrieve the tag parameters.
		$root_label = 'Home';
		$root_url	= 'http://example.com/';

		$this->_ee->lang->expectOnce('line', array('default_root_label'));
		$this->_ee->lang->setReturnValue('line', $root_label);

		$this->_ee->functions->expectOnce('fetch_site_index');
		$this->_ee->functions->setReturnValue('fetch_site_index', $root_url);

		$this->_ee->TMPL->setReturnValue('fetch_param', 'yes', array('root_breadcrumb:include', 'yes'));
		$this->_ee->TMPL->setReturnValue('fetch_param', $root_label, array('root_breadcrumb:label', $root_label));
		$this->_ee->TMPL->setReturnValue('fetch_param', $root_url, array('root_breadcrumb:url', $root_url));

		// Template tag parser.
		$tagdata = 'Tagdata';
		$this->_ee->TMPL->setReturnValue('__get', $tagdata, array('tagdata'));
		
		$breadcrumbs = array(
			array(
				'breadcrumb_segment'	=> '',
				'breadcrumb_title'		=> $root_label,
				'breadcrumb_url'		=> $root_url
			)
		);

		$parsed_tagdata = 'Parsed tagdata';
		$this->_ee->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
		$this->_ee->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

		// Tests.
		$this->_subject->breadcrumbs();

	}


	public function test__breadcrumbs__include_custom_root()
	{
		// Retrieve the segments (no segments).
		$this->_ee->uri->setReturnValue('segment_array', array());

		// Retrieve the tag parameters.
		$default_root_label		= 'Home';
		$default_root_url		= 'http://example.com/';

		$custom_root_label		= 'Casa';
		$custom_root_url		= 'http://example.es/';

		$this->_ee->lang->expectOnce('line', array('default_root_label'));
		$this->_ee->lang->setReturnValue('line', $default_root_label);

		$this->_ee->functions->expectOnce('fetch_site_index');
		$this->_ee->functions->setReturnValue('fetch_site_index', $default_root_url);

		$this->_ee->TMPL->setReturnValue('fetch_param', 'yes', array('root_breadcrumb:include', 'yes'));
		$this->_ee->TMPL->setReturnValue('fetch_param', $custom_root_label, array('root_breadcrumb:label', $default_root_label));
		$this->_ee->TMPL->setReturnValue('fetch_param', $custom_root_url, array('root_breadcrumb:url', $default_root_url));

		// Template tag parser.
		$tagdata = 'Tagdata';
		$this->_ee->TMPL->setReturnValue('__get', $tagdata, array('tagdata'));
		
		$breadcrumbs = array(
			array(
				'breadcrumb_segment'	=> '',
				'breadcrumb_title'		=> $custom_root_label,
				'breadcrumb_url'		=> $custom_root_url
			)
		);

		$parsed_tagdata = 'Parsed tagdata';
		$this->_ee->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
		$this->_ee->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

		// Tests.
		$this->_subject->breadcrumbs();
	}


	public function test__breadcrumbs__custom_url_pattern_ignore_trailing()
	{
		// Retrieve the segments.
		$segments		= array();
		$segments[1]	= 'destinations';
		$segments[2]	= 'details';
		$segments[3]	= 'moscow';
		$segments[4]	= 'hotels';
		$segments[5]	= 'hilton-moscow';
		$segments[6]	= 'facilities';
		$segments[7]	= 'irrelevant';
		$segments[8]	= 'trailing-segment-to-ignore';

		$this->_ee->uri->setReturnValue('segment_array', $segments);

		// Retrieve the tag parameters.
		$include_root	= 'no';
		$url_pattern	= 'template_group/template/entry/glossary/entry/glossary/ignore';
		$ignore_trailing = 'yes';

		$this->_ee->TMPL->setReturnValue('fetch_param', $include_root, array('root_breadcrumb:include', '*'));
		$this->_ee->TMPL->setReturnValue('fetch_param', $url_pattern, array('custom_url:pattern'));
		$this->_ee->TMPL->setReturnValue('fetch_param', $ignore_trailing, array('custom_url:ignore_trailing_segments', 'yes'));

		// URL builder.
		$site_url = 'http://example.com/';

		$this->_ee->functions->setReturnValue('create_url', $site_url .'destinations/', array('destinations'));
		$this->_ee->functions->setReturnValue('create_url', $site_url .'destinations/details/', array('destinations/details'));
		$this->_ee->functions->setReturnValue('create_url', $site_url .'destinations/details/moscow/', array('destinations/details/moscow'));
		$this->_ee->functions->setReturnValue('create_url', $site_url .'destinations/details/moscow/hotels/', array('destinations/details/moscow/hotels'));
		$this->_ee->functions->setReturnValue('create_url', $site_url .'destinations/details/moscow/hotels/hilton-moscow/', array('destinations/details/moscow/hotels/hilton-moscow'));
		$this->_ee->functions->setReturnValue('create_url', $site_url .'destinations/details/moscow/hotels/hilton-moscow/facilities/', array('destinations/details/moscow/hotels/hilton-moscow/facilities'));

		// Template group URL.
		$template_group = new Crumbly_template_group(array('group_id' => 10, 'label' => 'Our Destinations'));
		$this->_model->expectOnce('get_crumbly_template_group_from_segment', array($segments[1]));
		$this->_model->setReturnValue('get_crumbly_template_group_from_segment', $template_group);

		// Template URL.
		$template = new Crumbly_template(array('template_id' => 10, 'label' => 'Destination Details'));
		$this->_model->expectOnce('get_crumbly_template_from_segments', array($segments[1], $segments[2]));
		$this->_model->setReturnValue('get_crumbly_template_from_segments', $template);

		// Channel entry titles.
		$this->_model->setReturnValue('get_channel_entry_title_from_segment', 'Moscow', array('moscow'));
		$this->_model->setReturnValue('get_channel_entry_title_from_segment', 'The Moscow Hilton', array('hilton-moscow'));

		// Humanising the 'facilities' string.
		$this->_model->setReturnValue('humanize', 'Exclusive Hotels', array('hotels'));
		$this->_model->setReturnValue('humanize', 'Facilities', array('facilities'));

		// Tagdata.
		$tagdata = 'tagdata';
		$this->_ee->TMPL->setReturnValue('__get', $tagdata, array('tagdata'));

		// Expected breadcrumbs.
		$breadcrumbs = array(
			array(
				'breadcrumb_segment'	=> 'destinations',
				'breadcrumb_title'		=> 'Our Destinations',
				'breadcrumb_url'		=> $site_url .'destinations/'
			),
			array(
				'breadcrumb_segment'	=> 'details',
				'breadcrumb_title'		=> 'Destination Details',
				'breadcrumb_url'		=> $site_url .'destinations/details/'
			),
			array(
				'breadcrumb_segment'	=> 'moscow',
				'breadcrumb_title'		=> 'Moscow',
				'breadcrumb_url'		=> $site_url .'destinations/details/moscow/'
			),
			array(
				'breadcrumb_segment'	=> 'hotels',
				'breadcrumb_title'		=> 'Exclusive Hotels',
				'breadcrumb_url'		=> $site_url .'destinations/details/moscow/hotels/'
			),
			array(
				'breadcrumb_segment'	=> 'hilton-moscow',
				'breadcrumb_title'		=> 'The Moscow Hilton',
				'breadcrumb_url'		=> $site_url .'destinations/details/moscow/hotels/hilton-moscow/'
			),
			array(
				'breadcrumb_segment'	=> 'facilities',
				'breadcrumb_title'		=> 'Facilities',
				'breadcrumb_url'		=> $site_url .'destinations/details/moscow/hotels/hilton-moscow/facilities/'
			)
		);

		// Parsed tagdata.
		$parsed_tagdata = 'parsed_tagdata';
		$this->_ee->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
		$this->_ee->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

		// Run the tests.
		$this->_subject->breadcrumbs();
	}

	
	public function test__breadcrumbs__custom_url_include_trailing()
	{
		// Retrieve the segments.
		$segments		= array();
		$segments[1]	= 'destinations';
		$segments[2]	= 'moscow';
		$segments[3]	= 'trailing-segment';

		$this->_ee->uri->setReturnValue('segment_array', $segments);

		// Retrieve the tag parameters.
		$include_root	= 'no';
		$url_pattern	= 'template_group/entry';
		$ignore_trailing = 'no';

		$this->_ee->TMPL->setReturnValue('fetch_param', $include_root, array('root_breadcrumb:include', '*'));
		$this->_ee->TMPL->setReturnValue('fetch_param', $url_pattern, array('custom_url:pattern'));
		$this->_ee->TMPL->setReturnValue('fetch_param', $ignore_trailing, array('custom_url:ignore_trailing_segments', 'yes'));

		// URL builder.
		$site_url = 'http://example.com/';

		$this->_ee->functions->setReturnValue('create_url', $site_url .'destinations/', array('destinations'));
		$this->_ee->functions->setReturnValue('create_url', $site_url .'destinations/moscow/', array('destinations/moscow'));
		$this->_ee->functions->setReturnValue('create_url', $site_url .'destinations/moscow/trailing-segment/', array('destinations/moscow/trailing-segment'));

		// Template group URL.
		$template_group = new Crumbly_template_group(array('group_id' => 10, 'label' => 'Our Destinations'));
		$this->_model->expectOnce('get_crumbly_template_group_from_segment', array($segments[1]));
		$this->_model->setReturnValue('get_crumbly_template_group_from_segment', $template_group);

		// Template URL.
		$this->_model->expectNever('get_crumbly_template_from_segments');

		// Channel entry titles.
		$this->_model->setReturnValue('get_channel_entry_title_from_segment', 'Moscow', array('moscow'));

		// Humanising the 'facilities' string.
		$this->_model->setReturnValue('humanize', 'Trailing Segment', array('trailing-segment'));

		// Tagdata.
		$tagdata = 'tagdata';
		$this->_ee->TMPL->setReturnValue('__get', $tagdata, array('tagdata'));

		// Expected breadcrumbs.
		$breadcrumbs = array(
			array(
				'breadcrumb_segment'	=> 'destinations',
				'breadcrumb_title'		=> 'Our Destinations',
				'breadcrumb_url'		=> $site_url .'destinations/'
			),
			array(
				'breadcrumb_segment'	=> 'moscow',
				'breadcrumb_title'		=> 'Moscow',
				'breadcrumb_url'		=> $site_url .'destinations/moscow/'
			),
			array(
				'breadcrumb_segment'	=> 'trailing-segment',
				'breadcrumb_title'		=> 'Trailing Segment',
				'breadcrumb_url'		=> $site_url .'destinations/moscow/trailing-segment/'
			)
		);

		// Parsed tagdata.
		$parsed_tagdata = 'parsed_tagdata';
		$this->_ee->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
		$this->_ee->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

		// Run the tests.
		$this->_subject->breadcrumbs();
	}

}

/* End of file		: test.mod_crumbly.php */
/* File location	: third_party/crumbly/tests/test.mod_crumbly.php */
