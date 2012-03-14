<?php

/**
 * Crumbly module tests.
 *
 * @author          Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright       Experience Internet
 * @package         Crumbly
 */

require_once PATH_THIRD .'crumbly/mod.crumbly.php';
require_once PATH_THIRD .'crumbly/classes/EI_category.php';
require_once PATH_THIRD .'crumbly/models/crumbly_model.php';

class Test_crumbly extends Testee_unit_test_case {
    
  private $_model;
  private $_subject;
  private $_site_id;
  
  
  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */
  
  /**
   * Constructor.
   *
   * @access  public
   * @return  void
   */
  public function setUp()
  {
    parent::setUp();
    
    // Generate the mock model.
    Mock::generate('Crumbly_model', get_class($this) .'_mock_model');
    $this->_model             = $this->_get_mock('model');
    $this->EE->crumbly_model  = $this->_model;

    $this->_site_id = 10;
    $this->EE->crumbly_model->setReturnValue('get_site_id', $this->_site_id);
    
    $this->_subject = new Crumbly();
  }
  

  /* --------------------------------------------------------------
   * TEST METHODS
   * ------------------------------------------------------------ */

  public function test__breadcrumbs__template_group_template_url_title()
  {
    // Retrieve the segments.
    $segments = array();
    $segments[1]    = 'about';
    $segments[2]    = 'team';
    $segments[3]    = 'leonard';

    $this->EE->uri->expectOnce('segment_array');
    $this->EE->uri->setReturnValue('segment_array', $segments);

    // No root breadcrumb (tested separately).
    $this->EE->functions->expectNever('fetch_site_index');
    $this->EE->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', '*'));

    // Template tag parser.
    $tagdata = 'Tagdata';
    $this->EE->TMPL->tagdata = $tagdata;
    
    $site_url = 'http://example.com/';
    $this->EE->functions->expectCallCount('create_url', 3);

    // Template group URL.
    $template_group = new Crumbly_template_group(array('group_id' => 10, 'label' => 'About Us'));
    $this->_model->expectOnce('get_crumbly_template_group_from_segment', array($segments[1]));
    $this->_model->setReturnValue('get_crumbly_template_group_from_segment', $template_group);

    $this->EE->functions->expectAt(0, 'create_url', array('about'));
    $this->EE->functions->setReturnValueAt(0, 'create_url', $site_url .'about/');

    // Template URL.
    $template = new Crumbly_template(array('template_id' => 10, 'label' => 'Meet the Team'));
    $this->_model->expectOnce('get_crumbly_template_from_segments', array($segments[1], $segments[2]));
    $this->_model->setReturnValue('get_crumbly_template_from_segments', $template);

    $this->EE->functions->expectAt(1, 'create_url', array('about/team'));
    $this->EE->functions->setReturnValueAt(1, 'create_url', $site_url .'about/team/');

    // Channel entry URL title.
    $this->_model->expectOnce('get_channel_entry_title_from_segment', array('leonard'));
    $this->_model->setReturnValue('get_channel_entry_title_from_segment', 'Leonard Rossiter');

    $this->EE->functions->expectAt(2, 'create_url', array('about/team/leonard'));
    $this->EE->functions->setReturnValueAt(2, 'create_url', $site_url .'about/team/leonard/');

    $breadcrumbs = array(
      array(
        'breadcrumb_segment'    => 'about',
        'breadcrumb_title'      => 'About Us',
        'breadcrumb_url'        => $site_url .'about/'
      ),
      array(
        'breadcrumb_segment'    => 'team',
        'breadcrumb_title'      => 'Meet the Team',
        'breadcrumb_url'        => $site_url .'about/team/'
      ),
      array(
        'breadcrumb_segment'    => 'leonard',
        'breadcrumb_title'      => 'Leonard Rossiter',
        'breadcrumb_url'        => $site_url .'about/team/leonard/'
      )
    );

    $parsed_tagdata = 'Parsed tagdata';

    $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
    $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

    // Tests.
    $this->_subject->breadcrumbs();
  }


  public function test__breadcrumbs__template_group_template_url_title_reversed()
  {
    // Retrieve the segments.
    $segments = array();
    $segments[1]    = 'about';
    $segments[2]    = 'team';
    $segments[3]    = 'leonard';

    $this->EE->uri->expectOnce('segment_array');
    $this->EE->uri->setReturnValue('segment_array', $segments);

    // Reverse.
    $this->EE->TMPL->setReturnValue('fetch_param', 'yes',
      array('breadcrumbs:reverse'));

    // No root breadcrumb (tested separately).
    $this->EE->functions->expectNever('fetch_site_index');
    $this->EE->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', '*'));

    // Template tag parser.
    $tagdata = 'Tagdata';
    $this->EE->TMPL->tagdata = $tagdata;
    
    $site_url = 'http://example.com/';
    $this->EE->functions->expectCallCount('create_url', 3);

    // Template group URL.
    $template_group = new Crumbly_template_group(array('group_id' => 10, 'label' => 'About Us'));
    $this->_model->expectOnce('get_crumbly_template_group_from_segment', array($segments[1]));
    $this->_model->setReturnValue('get_crumbly_template_group_from_segment', $template_group);

    $this->EE->functions->expectAt(0, 'create_url', array('about'));
    $this->EE->functions->setReturnValueAt(0, 'create_url', $site_url .'about/');

    // Template URL.
    $template = new Crumbly_template(array('template_id' => 10, 'label' => 'Meet the Team'));
    $this->_model->expectOnce('get_crumbly_template_from_segments', array($segments[1], $segments[2]));
    $this->_model->setReturnValue('get_crumbly_template_from_segments', $template);

    $this->EE->functions->expectAt(1, 'create_url', array('about/team'));
    $this->EE->functions->setReturnValueAt(1, 'create_url', $site_url .'about/team/');

    // Channel entry URL title.
    $this->_model->expectOnce('get_channel_entry_title_from_segment', array('leonard'));
    $this->_model->setReturnValue('get_channel_entry_title_from_segment', 'Leonard Rossiter');

    $this->EE->functions->expectAt(2, 'create_url', array('about/team/leonard'));
    $this->EE->functions->setReturnValueAt(2, 'create_url', $site_url .'about/team/leonard/');

    $breadcrumbs = array(
      array(
        'breadcrumb_segment'    => 'leonard',
        'breadcrumb_title'      => 'Leonard Rossiter',
        'breadcrumb_url'        => $site_url .'about/team/leonard/'
      ),
      array(
        'breadcrumb_segment'    => 'team',
        'breadcrumb_title'      => 'Meet the Team',
        'breadcrumb_url'        => $site_url .'about/team/'
      ),
      array(
        'breadcrumb_segment'    => 'about',
        'breadcrumb_title'      => 'About Us',
        'breadcrumb_url'        => $site_url .'about/'
      )
    );

    $parsed_tagdata = 'Parsed tagdata';

    $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
    $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

    // Tests.
    $this->_subject->breadcrumbs();
  }


  public function test__breadcrumbs__template_group_not_found()
  {
    $site_url               = 'http://example.com/';
    $template_group         = 'about';
    $template_group_title   = 'About';

    // Retrieve the segments.
    $segments = array();
    $segments[1] = $template_group;

    $this->EE->uri->setReturnValue('segment_array', $segments);

    // Retrieve the tag parameters (no root breadcrumb).
    $this->EE->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', 'yes'));

    // Template group URL.
    $this->_model->expectOnce('get_crumbly_template_group_from_segment', array($template_group));
    $this->_model->setReturnValue('get_crumbly_template_group_from_segment', FALSE);

    $this->_model->expectOnce('humanize', array($template_group));
    $this->_model->setReturnValue('humanize', $template_group_title, array($template_group));

    $this->EE->functions->expectOnce('create_url', array($template_group));
    $this->EE->functions->setReturnValue('create_url', $site_url .$template_group .'/');

    // Template tag parser.
    $tagdata = 'Tagdata';
    $this->EE->TMPL->tagdata = $tagdata;
    
    $breadcrumbs = array(
      array(
        'breadcrumb_segment'    => $template_group,
        'breadcrumb_title'      => $template_group_title,
        'breadcrumb_url'        => $site_url .$template_group .'/'
      )
    );

    $parsed_tagdata = 'Parsed tagdata';
    $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
    $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

    // Tests.
    $this->_subject->breadcrumbs();
  }


  public function test__breadcrumbs__template_group_not_found_url_includes_template()
  {
    // Dummy values.
    $template_group         = 'about';
    $template_group_title   = 'About';
    $template               = 'team';
    $template_title         = 'Team';

    // Retrieve the segments.
    $segments = array();
    $segments[1]    = $template_group;
    $segments[2]    = $template;

    $this->EE->uri->setReturnValue('segment_array', $segments);

    // Retrieve the tag parameters (no root breadcrumb).
    $this->EE->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', 'yes'));

    $site_url = 'http://example.com/';
    $this->EE->functions->expectCallCount('create_url', 2);
    $this->_model->expectCallCount('humanize', 2);

    // Template group URL.
    $this->_model->expectOnce('get_crumbly_template_group_from_segment', array($template_group));
    $this->_model->setReturnValue('get_crumbly_template_group_from_segment', FALSE);

    $this->_model->setReturnValue('humanize', $template_group_title, array($template_group));
    $this->EE->functions->setReturnValueAt(0, 'create_url', $site_url .$template_group .'/');
    
    // Template URL.
    $this->_model->setReturnValue('get_crumbly_template_from_segments', FALSE);
    $this->EE->functions->setReturnValueAt(1, 'create_url', $site_url .$template_group .'/' .$template .'/');
    $this->_model->setReturnValue('humanize', $template_title, array($template));

    // Template tag parser.
    $tagdata = 'Tagdata';
    $this->EE->TMPL->tagdata = $tagdata;
    
    $breadcrumbs = array(
      array(
        'breadcrumb_segment'    => $template_group,
        'breadcrumb_title'      => $template_group_title,
        'breadcrumb_url'        => $site_url .$template_group .'/'
      ),
      array(
        'breadcrumb_segment'    => $template,
        'breadcrumb_title'      => $template_title,
        'breadcrumb_url'        => $site_url .$template_group .'/' .$template .'/'
      )
    );

    $parsed_tagdata = 'Parsed tagdata';
    $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
    $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

    // Tests.
    $this->_subject->breadcrumbs();
  }


  public function test__breadcrumbs__template_not_found()
  {
    // Retrieve the segments.
    $segments       = array();
    $segments[1]    = 'about';
    $segments[2]    = 'team';

    $this->EE->uri->expectOnce('segment_array');
    $this->EE->uri->setReturnValue('segment_array', $segments);

    // No root breadcrumb (tested separately).
    $this->EE->functions->expectNever('fetch_site_index');
    $this->EE->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', '*'));

    // Template tag parser.
    $tagdata = 'Tagdata';
    $this->EE->TMPL->tagdata = $tagdata;
    
    $site_url = 'http://example.com/';
    $this->EE->functions->expectCallCount('create_url', 2);

    // Template group URL.
    $template_group = new Crumbly_template_group(array('group_id' => 10, 'label' => 'About Us'));
    $this->_model->expectOnce('get_crumbly_template_group_from_segment', array($segments[1]));
    $this->_model->setReturnValue('get_crumbly_template_group_from_segment', $template_group);

    $this->EE->functions->expectAt(0, 'create_url', array('about'));
    $this->EE->functions->setReturnValueAt(0, 'create_url', $site_url .'about/');

    // Template URL.
    $this->_model->expectOnce('get_crumbly_template_from_segments', array($segments[1], $segments[2]));
    $this->_model->setReturnValue('get_crumbly_template_from_segments', FALSE);

    $this->EE->functions->expectAt(1, 'create_url', array('about/team'));
    $this->EE->functions->setReturnValueAt(1, 'create_url', $site_url .'about/team/');

    $this->_model->expectOnce('humanize', array('team'));
    $this->_model->setReturnValue('humanize', 'Team');

    $breadcrumbs = array(
      array(
        'breadcrumb_segment'    => 'about',
        'breadcrumb_title'      => 'About Us',
        'breadcrumb_url'        => $site_url .'about/'
      ),
      array(
        'breadcrumb_segment'    => 'team',
        'breadcrumb_title'      => 'Team',
        'breadcrumb_url'        => $site_url .'about/team/'
      )
    );

    $parsed_tagdata = 'Parsed tagdata';

    $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
    $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

    // Tests.
    $this->_subject->breadcrumbs();
  }


  public function test__breadcrumbs__template_group_category()
  {
    // Retrieve the segments.
    $segments = array();
    $segments[1]    = 'products';
    $segments[2]    = 'C12';

    $this->EE->uri->expectOnce('segment_array');
    $this->EE->uri->setReturnValue('segment_array', $segments);

    // No root breadcrumb (tested separately).
    $this->EE->functions->expectNever('fetch_site_index');
    $this->EE->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', '*'));

    // Template tag parser.
    $tagdata = 'Tagdata';
    $this->EE->TMPL->tagdata = $tagdata;
    
    $site_url = 'http://example.com/';
    $this->EE->functions->expectCallCount('create_url', 2);

    // Template group URL.
    $template_group = new Crumbly_template_group(array('group_id' => 10, 'label' => 'Products'));
    $this->_model->expectOnce('get_crumbly_template_group_from_segment', array($segments[1]));
    $this->_model->setReturnValue('get_crumbly_template_group_from_segment', $template_group);

    $this->EE->functions->expectAt(0, 'create_url', array($segments[1]));
    $this->EE->functions->setReturnValueAt(0, 'create_url', $site_url .$segments[1] .'/');

    // Category.
    $category = new EI_category(array('cat_id' => 12, 'cat_url_title' => 'chairs', 'cat_name' => 'Chairs'));
    $this->_model->expectOnce('get_category_from_segment', array($segments[2]));
    $this->_model->setReturnValue('get_category_from_segment', $category);

    $this->EE->functions->expectAt(1, 'create_url', array($segments[1] .'/' .$segments[2]));
    $this->EE->functions->setReturnValueAt(1, 'create_url', $site_url .$segments[1] .'/' .$segments[2] .'/');

    $breadcrumbs = array(
      array(
        'breadcrumb_segment'    => $segments[1],
        'breadcrumb_title'      => 'Products',
        'breadcrumb_url'        => $site_url .$segments[1] .'/'
      ),
      array(
        'breadcrumb_segment'    => $segments[2],
        'breadcrumb_title'      => 'Chairs',
        'breadcrumb_url'        => $site_url .$segments[1] .'/' .$segments[2] .'/'
      )
    );

    $parsed_tagdata = 'Parsed tagdata';

    $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
    $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

    // Tests.
    $this->_subject->breadcrumbs();
  }


  public function test__breadcrumbs__template_group_category_no_crumbly_category()
  {
    // Retrieve the segments.
    $segments = array();
    $segments[1]    = 'products';
    $segments[2]    = 'C12';

    $this->EE->uri->expectOnce('segment_array');
    $this->EE->uri->setReturnValue('segment_array', $segments);

    // No root breadcrumb (tested separately).
    $this->EE->functions->expectNever('fetch_site_index');
    $this->EE->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', '*'));

    // Template tag parser.
    $tagdata = 'Tagdata';
    $this->EE->TMPL->tagdata = $tagdata;
    
    $site_url = 'http://example.com/';
    $this->EE->functions->expectCallCount('create_url', 2);

    // Template group URL.
    $template_group = new Crumbly_template_group(array('group_id' => 10, 'label' => 'Products'));
    $this->_model->expectOnce('get_crumbly_template_group_from_segment', array($segments[1]));
    $this->_model->setReturnValue('get_crumbly_template_group_from_segment', $template_group);

    $this->EE->functions->expectAt(0, 'create_url', array($segments[1]));
    $this->EE->functions->setReturnValueAt(0, 'create_url', $site_url .$segments[1] .'/');

    // Category.
    $category = new EI_category(array(
      'cat_id'        => 12,
      'cat_name'      => 'Single Serving Seating',
      'cat_url_title' => 'chairs'
    ));
    
    $this->_model->expectOnce('get_category_from_segment', array($segments[2]));
    $this->_model->setReturnValue('get_category_from_segment', $category);

    $this->EE->functions->expectAt(1, 'create_url', array($segments[1] .'/' .$segments[2]));
    $this->EE->functions->setReturnValueAt(1, 'create_url', $site_url .$segments[1] .'/' .$segments[2] .'/');

    $breadcrumbs = array(
      array(
        'breadcrumb_segment'    => $segments[1],
        'breadcrumb_title'      => 'Products',
        'breadcrumb_url'        => $site_url .$segments[1] .'/'
      ),
      array(
        'breadcrumb_segment'    => $segments[2],
        'breadcrumb_title'      => 'Single Serving Seating',
        'breadcrumb_url'        => $site_url .$segments[1] .'/' .$segments[2] .'/'
      )
    );

    $parsed_tagdata = 'Parsed tagdata';

    $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
    $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

    // Tests.
    $this->_subject->breadcrumbs();
  }


  public function test__breadcrumbs__template_group_category_unknown_category()
  {
    // Retrieve the segments.
    $segments = array();
    $segments[1]    = 'products';
    $segments[2]    = 'C12';

    $this->EE->uri->expectOnce('segment_array');
    $this->EE->uri->setReturnValue('segment_array', $segments);

    // No root breadcrumb (tested separately).
    $this->EE->functions->expectNever('fetch_site_index');
    $this->EE->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', '*'));

    // Template tag parser.
    $tagdata = 'Tagdata';
    $this->EE->TMPL->tagdata = $tagdata;
    
    $site_url = 'http://example.com/';
    $this->EE->functions->expectCallCount('create_url', 2);

    // Template group URL.
    $template_group = new Crumbly_template_group(array('group_id' => 10, 'label' => 'Products'));
    $this->_model->expectOnce('get_crumbly_template_group_from_segment', array($segments[1]));
    $this->_model->setReturnValue('get_crumbly_template_group_from_segment', $template_group);

    $this->EE->functions->expectAt(0, 'create_url', array($segments[1]));
    $this->EE->functions->setReturnValueAt(0, 'create_url', $site_url .$segments[1] .'/');

    // Category.
    $this->_model->expectOnce('get_category_from_segment', array($segments[2]));
    $this->_model->setReturnValue('get_category_from_segment', FALSE);

    $this->_model->expectOnce('humanize', array($segments[2], FALSE));
    $this->_model->setReturnValue('humanize', 'C12');

    $this->EE->functions->expectAt(1, 'create_url', array($segments[1] .'/' .$segments[2]));
    $this->EE->functions->setReturnValueAt(1, 'create_url', $site_url .$segments[1] .'/' .$segments[2] .'/');

    $breadcrumbs = array(
      array(
        'breadcrumb_segment'    => $segments[1],
        'breadcrumb_title'      => 'Products',
        'breadcrumb_url'        => $site_url .$segments[1] .'/'
      ),
      array(
        'breadcrumb_segment'    => $segments[2],
        'breadcrumb_title'      => 'C12',
        'breadcrumb_url'        => $site_url .$segments[1] .'/' .$segments[2] .'/'
      )
    );

    $parsed_tagdata = 'Parsed tagdata';

    $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
    $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

    // Tests.
    $this->_subject->breadcrumbs();
  }


  public function test__breadcrumbs__template_group_category_trigger_category()
  {
    // Use category names.
    $this->EE->config->setReturnValue('item', 'y', array('use_category_name'));
    $this->EE->config->setReturnValue('item', 'seating', array('reserved_category_word'));

    // Retrieve the segments.
    $segments = array();
    $segments[1]    = 'products';
    $segments[2]    = 'seating';
    $segments[3]    = 'chairs';

    $this->EE->uri->expectOnce('segment_array');
    $this->EE->uri->setReturnValue('segment_array', $segments);

    // No root breadcrumb (tested separately).
    $this->EE->functions->expectNever('fetch_site_index');
    $this->EE->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', '*'));

    // Template tag parser.
    $tagdata = 'Tagdata';
    $this->EE->TMPL->tagdata = $tagdata;
    
    $site_url = 'http://example.com/';
    $this->EE->functions->expectCallCount('create_url', 3);

    // Template group URL.
    $template_group = new Crumbly_template_group(array('group_id' => 10, 'label' => 'Our Products'));
    $this->_model->expectOnce('get_crumbly_template_group_from_segment', array($segments[1]));
    $this->_model->setReturnValue('get_crumbly_template_group_from_segment', $template_group);

    $this->EE->functions->expectAt(0, 'create_url', array($segments[1]));
    $this->EE->functions->setReturnValueAt(0, 'create_url', $site_url .$segments[1] .'/');

    // Category trigger.
    $this->_model->expectOnce('humanize', array($segments[2]));
    $this->_model->setReturnValue('humanize', 'Seating');

    $this->EE->functions->expectAt(1, 'create_url', array($segments[1] .'/' .$segments[2]));
    $this->EE->functions->setReturnValueAt(1, 'create_url', $site_url .$segments[1] .'/' .$segments[2] .'/');

    // Category.
    $category = new EI_category(array('cat_id' => 10, 'cat_url_title' => 'Chairs', 'cat_name' => 'Single Serving Seating'));
    $this->_model->expectOnce('get_category_from_segment', array($segments[3]));
    $this->_model->setReturnValue('get_category_from_segment', $category);

    $this->EE->functions->expectAt(2, 'create_url', array($segments[1] .'/' .$segments[2] .'/' .$segments[3]));
    $this->EE->functions->setReturnValueAt(2, 'create_url', $site_url .$segments[1] .'/' .$segments[2] .'/' .$segments[3] .'/');

    $breadcrumbs = array(
      array(
        'breadcrumb_segment'    => $segments[1],
        'breadcrumb_title'      => 'Our Products',
        'breadcrumb_url'        => $site_url .$segments[1] .'/'
      ),
      array(
        'breadcrumb_segment'    => $segments[2],
        'breadcrumb_title'      => 'Seating',
        'breadcrumb_url'        => $site_url .$segments[1] .'/' .$segments[2] .'/'
      ),
      array(
        'breadcrumb_segment'    => $segments[3],
        'breadcrumb_title'      => 'Single Serving Seating',
        'breadcrumb_url'        => $site_url .$segments[1] .'/' .$segments[2] .'/' .$segments[3] .'/'
      )
    );

    $parsed_tagdata = 'Parsed tagdata';

    $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
    $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

    // Tests.
    $this->_subject->breadcrumbs();
  }


  public function test__breadcrumbs__template_group_category_trigger_category_no_crumbly_category()
  {
    // Use category names.
    $this->EE->config->setReturnValue('item', 'y', array('use_category_name'));
    $this->EE->config->setReturnValue('item', 'seating', array('reserved_category_word'));

    // Retrieve the segments.
    $segments = array();
    $segments[1]    = 'products';
    $segments[2]    = 'seating';
    $segments[3]    = 'chairs';

    $this->EE->uri->expectOnce('segment_array');
    $this->EE->uri->setReturnValue('segment_array', $segments);

    // No root breadcrumb (tested separately).
    $this->EE->functions->expectNever('fetch_site_index');
    $this->EE->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', '*'));

    // Template tag parser.
    $tagdata = 'Tagdata';
    $this->EE->TMPL->tagdata = $tagdata;
    
    $site_url = 'http://example.com/';
    $this->EE->functions->expectCallCount('create_url', 3);

    // Template group URL.
    $template_group = new Crumbly_template_group(array('group_id' => 10, 'label' => 'Our Products'));
    $this->_model->expectOnce('get_crumbly_template_group_from_segment', array($segments[1]));
    $this->_model->setReturnValue('get_crumbly_template_group_from_segment', $template_group);

    $this->EE->functions->expectAt(0, 'create_url', array($segments[1]));
    $this->EE->functions->setReturnValueAt(0, 'create_url', $site_url .$segments[1] .'/');

    // Category trigger.
    $this->_model->expectOnce('humanize', array($segments[2]));
    $this->_model->setReturnValue('humanize', 'Seating');

    $this->EE->functions->expectAt(1, 'create_url', array($segments[1] .'/' .$segments[2]));
    $this->EE->functions->setReturnValueAt(1, 'create_url', $site_url .$segments[1] .'/' .$segments[2] .'/');

    // Category.
    $category = new EI_category(array(
      'cat_id'        => '10',
      'cat_name'      => 'Comfy Chairs',
      'cat_url_title' => $segments[3]
    ));

    $this->_model->expectOnce('get_category_from_segment', array($segments[3]));
    $this->_model->setReturnValue('get_category_from_segment', $category);

    $this->EE->functions->expectAt(2, 'create_url', array($segments[1] .'/' .$segments[2] .'/' .$segments[3]));
    $this->EE->functions->setReturnValueAt(2, 'create_url', $site_url .$segments[1] .'/' .$segments[2] .'/' .$segments[3] .'/');

    $breadcrumbs = array(
      array(
        'breadcrumb_segment'    => $segments[1],
        'breadcrumb_title'      => 'Our Products',
        'breadcrumb_url'        => $site_url .$segments[1] .'/'
      ),
      array(
        'breadcrumb_segment'    => $segments[2],
        'breadcrumb_title'      => 'Seating',
        'breadcrumb_url'        => $site_url .$segments[1] .'/' .$segments[2] .'/'
      ),
      array(
        'breadcrumb_segment'    => $segments[3],
        'breadcrumb_title'      => 'Comfy Chairs',
        'breadcrumb_url'        => $site_url .$segments[1] .'/' .$segments[2] .'/' .$segments[3] .'/'
      )
    );

    $parsed_tagdata = 'Parsed tagdata';

    $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
    $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

    // Tests.
    $this->_subject->breadcrumbs();
  }


  public function test__breadcrumbs__template_group_category_trigger_category_unknown_category()
  {
      // Use category names.
      $this->EE->config->setReturnValue('item', 'y', array('use_category_name'));
      $this->EE->config->setReturnValue('item', 'seating', array('reserved_category_word'));

      // Retrieve the segments.
      $segments = array();
      $segments[1]    = 'products';
      $segments[2]    = 'seating';
      $segments[3]    = 'chairs';

      $this->EE->uri->expectOnce('segment_array');
      $this->EE->uri->setReturnValue('segment_array', $segments);

      // No root breadcrumb (tested separately).
      $this->EE->functions->expectNever('fetch_site_index');
      $this->EE->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', '*'));

      // Template tag parser.
      $tagdata = 'Tagdata';
      $this->EE->TMPL->tagdata = $tagdata;
      
      $site_url = 'http://example.com/';
      $this->EE->functions->expectCallCount('create_url', 3);
      $this->_model->expectCallCount('humanize', 2);

      // Template group URL.
      $template_group = new Crumbly_template_group(array('group_id' => 10, 'label' => 'Our Products'));
      $this->_model->expectOnce('get_crumbly_template_group_from_segment', array($segments[1]));
      $this->_model->setReturnValue('get_crumbly_template_group_from_segment', $template_group);

      $this->EE->functions->expectAt(0, 'create_url', array($segments[1]));
      $this->EE->functions->setReturnValueAt(0, 'create_url', $site_url .$segments[1] .'/');

      // Category trigger.
      $this->_model->expectAt(0, 'humanize', array($segments[2]));
      $this->_model->setReturnValueAt(0, 'humanize', 'Seating');

      $this->EE->functions->expectAt(1, 'create_url', array($segments[1] .'/' .$segments[2]));
      $this->EE->functions->setReturnValueAt(1, 'create_url', $site_url .$segments[1] .'/' .$segments[2] .'/');

      // Category.
      $this->_model->expectOnce('get_category_from_segment', array($segments[3]));
      $this->_model->setReturnValue('get_category_from_segment', FALSE);

      $this->_model->expectAt(1, 'humanize', array($segments[3], FALSE));
      $this->_model->setReturnValueAt(1, 'humanize', 'Chairs');

      $this->EE->functions->expectAt(2, 'create_url', array($segments[1] .'/' .$segments[2] .'/' .$segments[3]));
      $this->EE->functions->setReturnValueAt(2, 'create_url', $site_url .$segments[1] .'/' .$segments[2] .'/' .$segments[3] .'/');

      $breadcrumbs = array(
          array(
              'breadcrumb_segment'    => $segments[1],
              'breadcrumb_title'      => 'Our Products',
              'breadcrumb_url'        => $site_url .$segments[1] .'/'
          ),
          array(
              'breadcrumb_segment'    => $segments[2],
              'breadcrumb_title'      => 'Seating',
              'breadcrumb_url'        => $site_url .$segments[1] .'/' .$segments[2] .'/'
          ),
          array(
              'breadcrumb_segment'    => $segments[3],
              'breadcrumb_title'      => 'Chairs',
              'breadcrumb_url'        => $site_url .$segments[1] .'/' .$segments[2] .'/' .$segments[3] .'/'
          )
      );

      $parsed_tagdata = 'Parsed tagdata';

      $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
      $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

      // Tests.
      $this->_subject->breadcrumbs();
  }


  public function test__breadcrumbs__template_group_template_category()
  {
      // Retrieve the segments.
      $segments = array();
      $segments[1]    = 'shop';
      $segments[2]    = 'furniture';
      $segments[3]    = 'C12';

      $this->EE->uri->expectOnce('segment_array');
      $this->EE->uri->setReturnValue('segment_array', $segments);

      // No root breadcrumb (tested separately).
      $this->EE->functions->expectNever('fetch_site_index');
      $this->EE->TMPL->setReturnValue('fetch_param', 'no', array('root_breadcrumb:include', '*'));

      // Template tag parser.
      $tagdata = 'Tagdata';
      $this->EE->TMPL->tagdata = $tagdata;
      
      $site_url = 'http://example.com/';
      $this->EE->functions->expectCallCount('create_url', 3);

      // Template group URL.
      $template_group = new Crumbly_template_group(array('group_id' => 10, 'label' => 'Shop'));
      $this->_model->expectOnce('get_crumbly_template_group_from_segment', array($segments[1]));
      $this->_model->setReturnValue('get_crumbly_template_group_from_segment', $template_group);

      $this->EE->functions->expectAt(0, 'create_url', array($segments[1]));
      $this->EE->functions->setReturnValueAt(0, 'create_url', $site_url .$segments[1] .'/');

      // Template URL.
      $template = new Crumbly_template(array('template_id' => 20, 'label' => 'Furniture'));
      $this->_model->expectOnce('get_crumbly_template_from_segments', array($segments[1], $segments[2]));
      $this->_model->setReturnValue('get_crumbly_template_from_segments', $template);

      $this->EE->functions->expectAt(1, 'create_url', array($segments[1] .'/' .$segments[2]));
      $this->EE->functions->setReturnValueAt(1, 'create_url', $site_url .$segments[1] .'/' .$segments[2] .'/');

      // Category.
      $category = new EI_category(array('cat_id' => 12, 'cat_url_title' => 'seating', 'cat_name' => 'Seating'));
      $this->_model->expectOnce('get_category_from_segment', array($segments[3]));
      $this->_model->setReturnValue('get_category_from_segment', $category);

      $this->EE->functions->expectAt(2, 'create_url', array($segments[1] .'/' .$segments[2] .'/' .$segments[3]));
      $this->EE->functions->setReturnValueAt(2, 'create_url', $site_url .$segments[1] .'/' .$segments[2] .'/' .$segments[3] .'/');

      $breadcrumbs = array(
          array(
              'breadcrumb_segment'    => $segments[1],
              'breadcrumb_title'      => 'Shop',
              'breadcrumb_url'        => $site_url .$segments[1] .'/'
          ),
          array(
              'breadcrumb_segment'    => $segments[2],
              'breadcrumb_title'      => 'Furniture',
              'breadcrumb_url'        => $site_url .$segments[1] .'/' .$segments[2] .'/'
          ),
          array(
              'breadcrumb_segment'    => $segments[3],
              'breadcrumb_title'      => 'Seating',
              'breadcrumb_url'        => $site_url .$segments[1] .'/' .$segments[2] .'/' .$segments[3] .'/'
          )
      );

      $parsed_tagdata = 'Parsed tagdata';

      $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
      $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

      // Tests.
      $this->_subject->breadcrumbs();
  }


  public function test__breadcrumbs__include_default_root()
  {
      // Retrieve the segments (no segments).
      $this->EE->uri->setReturnValue('segment_array', array());

      // Retrieve the tag parameters.
      $root_label = 'Home';
      $root_url   = 'http://example.com/';

      $this->EE->lang->expectOnce('line', array('default_root_label'));
      $this->EE->lang->setReturnValue('line', $root_label);

      $this->EE->functions->expectOnce('fetch_site_index');
      $this->EE->functions->setReturnValue('fetch_site_index', $root_url);

      $this->EE->TMPL->setReturnValue('fetch_param', 'yes', array('root_breadcrumb:include', 'yes'));
      $this->EE->TMPL->setReturnValue('fetch_param', $root_label, array('root_breadcrumb:label', $root_label));
      $this->EE->TMPL->setReturnValue('fetch_param', $root_url, array('root_breadcrumb:url', $root_url));

      // Template tag parser.
      $tagdata = 'Tagdata';
      $this->EE->TMPL->tagdata = $tagdata;
      
      $breadcrumbs = array(
          array(
              'breadcrumb_segment'    => '',
              'breadcrumb_title'      => $root_label,
              'breadcrumb_url'        => $root_url
          )
      );

      $parsed_tagdata = 'Parsed tagdata';
      $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
      $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

      // Tests.
      $this->_subject->breadcrumbs();

  }


  public function test__breadcrumbs__include_custom_root()
  {
      // Retrieve the segments (no segments).
      $this->EE->uri->setReturnValue('segment_array', array());

      // Retrieve the tag parameters.
      $default_root_label     = 'Home';
      $default_root_url       = 'http://example.com/';

      $custom_root_label      = 'Casa';
      $custom_root_url        = 'http://example.es/';

      $this->EE->lang->expectOnce('line', array('default_root_label'));
      $this->EE->lang->setReturnValue('line', $default_root_label);

      $this->EE->functions->expectOnce('fetch_site_index');
      $this->EE->functions->setReturnValue('fetch_site_index', $default_root_url);

      $this->EE->TMPL->setReturnValue('fetch_param', 'yes', array('root_breadcrumb:include', 'yes'));
      $this->EE->TMPL->setReturnValue('fetch_param', $custom_root_label, array('root_breadcrumb:label', $default_root_label));
      $this->EE->TMPL->setReturnValue('fetch_param', $custom_root_url, array('root_breadcrumb:url', $default_root_url));

      // Template tag parser.
      $tagdata = 'Tagdata';
      $this->EE->TMPL->tagdata = $tagdata;
      
      $breadcrumbs = array(
          array(
              'breadcrumb_segment'    => '',
              'breadcrumb_title'      => $custom_root_label,
              'breadcrumb_url'        => $custom_root_url
          )
      );

      $parsed_tagdata = 'Parsed tagdata';
      $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
      $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

      // Tests.
      $this->_subject->breadcrumbs();
  }


  public function test__breadcrumbs__custom_url_pattern_ignore_trailing()
  {
      // Retrieve the segments.
      $segments       = array();
      $segments[1]    = 'destinations';
      $segments[2]    = 'details';
      $segments[3]    = 'moscow';
      $segments[4]    = 'hotels';
      $segments[5]    = 'hilton-moscow';
      $segments[6]    = 'facilities';
      $segments[7]    = 'irrelevant';
      $segments[8]    = 'trailing-segment-to-ignore';

      $this->EE->uri->setReturnValue('segment_array', $segments);

      // Retrieve the tag parameters.
      $include_root   = 'no';
      $url_pattern    = 'template_group/template/entry/glossary/entry/glossary/ignore';
      $ignore_trailing = 'yes';

      $this->EE->TMPL->setReturnValue('fetch_param', $include_root, array('root_breadcrumb:include', '*'));
      $this->EE->TMPL->setReturnValue('fetch_param', $url_pattern, array('custom_url:pattern'));
      $this->EE->TMPL->setReturnValue('fetch_param', $ignore_trailing, array('custom_url:ignore_trailing_segments', 'yes'));

      // URL builder.
      $site_url = 'http://example.com/';

      $this->EE->functions->setReturnValue('create_url', $site_url .'destinations/', array('destinations'));
      $this->EE->functions->setReturnValue('create_url', $site_url .'destinations/details/', array('destinations/details'));
      $this->EE->functions->setReturnValue('create_url', $site_url .'destinations/details/moscow/', array('destinations/details/moscow'));
      $this->EE->functions->setReturnValue('create_url', $site_url .'destinations/details/moscow/hotels/', array('destinations/details/moscow/hotels'));
      $this->EE->functions->setReturnValue('create_url', $site_url .'destinations/details/moscow/hotels/hilton-moscow/', array('destinations/details/moscow/hotels/hilton-moscow'));
      $this->EE->functions->setReturnValue('create_url', $site_url .'destinations/details/moscow/hotels/hilton-moscow/facilities/', array('destinations/details/moscow/hotels/hilton-moscow/facilities'));

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
      $this->EE->TMPL->tagdata = $tagdata;

      // Expected breadcrumbs.
      $breadcrumbs = array(
          array(
              'breadcrumb_segment'    => 'destinations',
              'breadcrumb_title'      => 'Our Destinations',
              'breadcrumb_url'        => $site_url .'destinations/'
          ),
          array(
              'breadcrumb_segment'    => 'details',
              'breadcrumb_title'      => 'Destination Details',
              'breadcrumb_url'        => $site_url .'destinations/details/'
          ),
          array(
              'breadcrumb_segment'    => 'moscow',
              'breadcrumb_title'      => 'Moscow',
              'breadcrumb_url'        => $site_url .'destinations/details/moscow/'
          ),
          array(
              'breadcrumb_segment'    => 'hotels',
              'breadcrumb_title'      => 'Exclusive Hotels',
              'breadcrumb_url'        => $site_url .'destinations/details/moscow/hotels/'
          ),
          array(
              'breadcrumb_segment'    => 'hilton-moscow',
              'breadcrumb_title'      => 'The Moscow Hilton',
              'breadcrumb_url'        => $site_url .'destinations/details/moscow/hotels/hilton-moscow/'
          ),
          array(
              'breadcrumb_segment'    => 'facilities',
              'breadcrumb_title'      => 'Facilities',
              'breadcrumb_url'        => $site_url .'destinations/details/moscow/hotels/hilton-moscow/facilities/'
          )
      );

      // Parsed tagdata.
      $parsed_tagdata = 'parsed_tagdata';
      $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
      $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

      // Run the tests.
      $this->_subject->breadcrumbs();
  }

  
  public function test__breadcrumbs__custom_url_include_trailing()
  {
      // Retrieve the segments.
      $segments       = array();
      $segments[1]    = 'destinations';
      $segments[2]    = 'moscow';
      $segments[3]    = 'trailing-segment';

      $this->EE->uri->setReturnValue('segment_array', $segments);

      // Retrieve the tag parameters.
      $include_root   = 'no';
      $url_pattern    = 'template_group/entry';
      $ignore_trailing = 'no';

      $this->EE->TMPL->setReturnValue('fetch_param', $include_root, array('root_breadcrumb:include', '*'));
      $this->EE->TMPL->setReturnValue('fetch_param', $url_pattern, array('custom_url:pattern'));
      $this->EE->TMPL->setReturnValue('fetch_param', $ignore_trailing, array('custom_url:ignore_trailing_segments', 'yes'));

      // URL builder.
      $site_url = 'http://example.com/';

      $this->EE->functions->setReturnValue('create_url', $site_url .'destinations/', array('destinations'));
      $this->EE->functions->setReturnValue('create_url', $site_url .'destinations/moscow/', array('destinations/moscow'));
      $this->EE->functions->setReturnValue('create_url', $site_url .'destinations/moscow/trailing-segment/', array('destinations/moscow/trailing-segment'));

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
      $this->EE->TMPL->tagdata = $tagdata;

      // Expected breadcrumbs.
      $breadcrumbs = array(
          array(
              'breadcrumb_segment'    => 'destinations',
              'breadcrumb_title'      => 'Our Destinations',
              'breadcrumb_url'        => $site_url .'destinations/'
          ),
          array(
              'breadcrumb_segment'    => 'moscow',
              'breadcrumb_title'      => 'Moscow',
          'breadcrumb_url'        => $site_url .'destinations/moscow/'
          ),
          array(
              'breadcrumb_segment'    => 'trailing-segment',
              'breadcrumb_title'      => 'Trailing Segment',
              'breadcrumb_url'        => $site_url .'destinations/moscow/trailing-segment/'
          )
      );

      // Parsed tagdata.
      $parsed_tagdata = 'parsed_tagdata';
      $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
      $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);

      // Run the tests.
      $this->_subject->breadcrumbs();
  }


  public function test__breadcrumbs__pages_uri_success()
  {
      // URL segments.
      $segments       = array();
      $segments[1]    = 'about';
      $segments[2]    = 'people';
      $segments[3]    = 'john-doe';

      $this->EE->uri->setReturnValue('segment_array', $segments);

      // Create URL expectations.
      $site_url = 'http://example.com/';

      $this->EE->functions->expectCallCount('create_url', 3);

      $this->EE->functions->returns('create_url',
        $site_url .$segments[1] .'/',
        array($segments[1]));

      $this->EE->functions->returns('create_url',
        $site_url .$segments[1] .'/' .$segments[2] .'/',
        array($segments[1] .'/' .$segments[2]));

      $this->EE->functions->returns('create_url',
        $site_url .$segments[1] .'/' .$segments[2] .'/' .$segments[3] .'/',
        array($segments[1] .'/' .$segments[2] .'/' .$segments[3]));

      // Retrieve the Pages information.
      $pages = array();
      $pages[$this->_site_id] = array(
          'url'       => '/' .implode('/', $segments) .'/',
          'uris'      => array(
              10 => '/' .$segments[1] .'/' .$segments[2],
              20 => '/' .$segments[1] .'/' .$segments[2] .'/' .$segments[3] .'/'
          ),
          'templates' => array('10' => 100, '20' => 200)
      );
      
      $this->EE->config->setReturnValue('item', $pages, array('site_pages'));

      // Breadcrumb titles.
      $segment_2_title    = 'Our People';
      $segment_3_title    = 'Mr. Jonathan Doe';

      $this->_model->expectCallCount('get_channel_entry_title_from_segment', 2);

      // 'people' segment title.
      $this->_model->expectAt(0, 'get_channel_entry_title_from_segment', array(10));
      $this->_model->setReturnValueAt(0, 'get_channel_entry_title_from_segment', $segment_2_title);

      // 'john-doe' segment title.
      $this->_model->expectAt(1, 'get_channel_entry_title_from_segment', array(20));
      $this->_model->setReturnValueAt(1, 'get_channel_entry_title_from_segment', $segment_3_title);

      // These should never be called.
      $this->_model->expectNever('get_category_from_segment');
      $this->_model->expectNever('get_crumbly_template_from_segments');
      $this->_model->expectNever('get_crumbly_template_group_from_segment');
      $this->_model->expectNever('humanize');

      // Tagdata.
      $tagdata = 'tagdata';
      $this->EE->TMPL->tagdata = $tagdata;

      // Expected breadcrumbs.
      $breadcrumbs = array(
          array(
              'breadcrumb_segment'    => $segments[2],
              'breadcrumb_title'      => $segment_2_title,
              'breadcrumb_url'        => $site_url .$segments[1] .'/' .$segments[2] .'/'
          ),
          array(
              'breadcrumb_segment'    => $segments[3],
              'breadcrumb_title'      => $segment_3_title,
              'breadcrumb_url'        => $site_url .$segments[1] .'/' .$segments[2] .'/' .$segments[3] .'/'
          )
      );

      // Parsed tagdata
      $parsed_tagdata = 'parsed_tagdata';
      $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
      $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);
  
      // Run the tests.
      $this->_subject->breadcrumbs();
  }


  public function test__breadcrumbs__pages_uri_include_unassigned_success()
  {
      // URL segments.
      $segments       = array();
      $segments[1]    = 'about';
      $segments[2]    = 'people';
      $segments[3]    = 'john-doe';

      $this->EE->uri->setReturnValue('segment_array', $segments);
      $this->EE->TMPL->setReturnValue('fetch_param', 'yes', array('pages:include_unassigned', '*'));

      // Create URL expectations.
      $site_url = 'http://example.com/';

      $this->EE->functions->expectCallCount('create_url', 3);

      $this->EE->functions->returns('create_url',
        $site_url .$segments[1] .'/',
        array($segments[1]));

      $this->EE->functions->returns('create_url',
        $site_url .$segments[1] .'/' .$segments[2] .'/',
        array($segments[1] .'/' .$segments[2]));

      $this->EE->functions->returns('create_url',
        $site_url .$segments[1] .'/' .$segments[2] .'/' .$segments[3] .'/',
        array($segments[1] .'/' .$segments[2] .'/' .$segments[3]));

      // Retrieve the Pages information.
      $pages = array();
      $pages[$this->_site_id] = array(
        'url'       => '/' .implode('/', $segments) .'/',
        'uris'      => array(
          10 => '/' .$segments[1] .'/' .$segments[2],
          20 => '/' .$segments[1] .'/' .$segments[2] .'/' .$segments[3] .'/'
        ),
        'templates' => array('10' => 100, '20' => 200)
      );
      
      $this->EE->config->setReturnValue('item', $pages, array('site_pages'));

      // Breadcrumb titles.
      $segment_1_title    = 'About';
      $segment_2_title    = 'Our People';
      $segment_3_title    = 'Mr. Jonathan Doe';

      $this->_model->expectCallCount('get_channel_entry_title_from_segment', 2);

      // Unassinged 'about' segment title.
      $this->_model->expectOnce('humanize', array($segments[1]));
      $this->_model->setReturnValue('humanize', $segment_1_title, array($segments[1]));

      // 'people' segment title.
      $this->_model->expectAt(0, 'get_channel_entry_title_from_segment', array(10));
      $this->_model->setReturnValueAt(0, 'get_channel_entry_title_from_segment', $segment_2_title);

      // 'john-doe' segment title.
      $this->_model->expectAt(1, 'get_channel_entry_title_from_segment', array(20));
      $this->_model->setReturnValueAt(1, 'get_channel_entry_title_from_segment', $segment_3_title);

      // These should never be called.
      $this->_model->expectNever('get_category_from_segment');
      $this->_model->expectNever('get_crumbly_template_from_segments');
      $this->_model->expectNever('get_crumbly_template_group_from_segment');

      // Tagdata.
      $tagdata = 'tagdata';
      $this->EE->TMPL->tagdata = $tagdata;

      // Expected breadcrumbs.
      $breadcrumbs = array(
        array(
          'breadcrumb_segment'  => $segments[1],
          'breadcrumb_title'    => $segment_1_title,
          'breadcrumb_url'      => $site_url .$segments[1] .'/'
        ),
        array(
          'breadcrumb_segment'  => $segments[2],
          'breadcrumb_title'    => $segment_2_title,
          'breadcrumb_url'      => $site_url .$segments[1] .'/' .$segments[2] .'/'
        ),
        array(
          'breadcrumb_segment'  => $segments[3],
          'breadcrumb_title'    => $segment_3_title,
          'breadcrumb_url'      => $site_url .$segments[1] .'/' .$segments[2] .'/' .$segments[3] .'/'
        )
      );

      // Parsed tagdata
      $parsed_tagdata = 'parsed_tagdata';
      $this->EE->TMPL->expectOnce('parse_variables', array($tagdata, $breadcrumbs));
      $this->EE->TMPL->setReturnValue('parse_variables', $parsed_tagdata);
  
      // Run the tests.
      $this->_subject->breadcrumbs();
  }
}

/* End of file      : test.mod_crumbly.php */
/* File location    : third_party/crumbly/tests/test.mod_crumbly.php */
