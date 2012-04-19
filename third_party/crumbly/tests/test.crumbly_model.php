<?php

/**
 * Crumbly model tests.
 *
 * @author      Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright   Experience Internet
 * @package     Crumbly
 */

require_once PATH_THIRD .'crumbly/models/crumbly_model.php';

class Test_crumbly_model extends Testee_unit_test_case {
  
  private $_namespace;
  private $_package_name;
  private $_package_version;
  private $_site_id;
  private $_subject;
  
  
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
    
    // Dummy properties.
    $this->_namespace       = 'example_namespace';
    $this->_package_name  = 'example_package';
    $this->_package_version = '1.0.0';
    
    // Dummy site ID value.
    $this->_site_id = 10;
    $this->EE->config->setReturnValue('item', $this->_site_id, array('site_id'));

    // Session cache.
    $this->EE->session->cache = array();
    
    // The test subject.
    $this->_subject = new Crumbly_model($this->_package_name, $this->_package_version, $this->_namespace);
  }


  public function test__constructor__package_name_package_version_namespace()
  {
    // Dummy values.
    $namespace        = 'Example_namespace';
    $package_name     = 'Example_package';
    $package_version  = '1.0.0';

    // Tests.
    $subject = new Crumbly_model($package_name, $package_version, $namespace);
    $this->assertIdentical(strtolower($namespace), $subject->get_namespace());
    $this->assertIdentical(strtolower($package_name), $subject->get_package_name());
    $this->assertIdentical($package_version, $subject->get_package_version());
  }


  public function test__delete_all_crumbly_glossary_terms__success()
  {
    $where = array('site_id' => $this->_site_id);
    $this->EE->db->expectOnce('delete', array('crumbly_glossary', $where));
  
    $this->assertIdentical(TRUE, $this->_subject->delete_all_crumbly_glossary_terms());
  }


  public function test__delete_all_crumbly_templates__success()
  {
    $where = array('site_id' => $this->_site_id);
    $this->EE->db->expectOnce('delete', array('crumbly_templates', $where));
  
    $this->assertIdentical(TRUE, $this->_subject->delete_all_crumbly_templates());
  }


  public function test__delete_all_crumbly_template_groups__success()
  {
    $where = array('site_id' => $this->_site_id);
    $this->EE->db->expectOnce('delete', array('crumbly_template_groups', $where));
  
    $this->assertIdentical(TRUE, $this->_subject->delete_all_crumbly_template_groups());
  }


  public function test__get_all_categories__success()
  {
    $this->EE->db->expectOnce('select', array('cat_id, cat_name, cat_url_title'));
    $this->EE->db->expectOnce('get_where', array('categories', array('site_id' => $this->_site_id)));

    $db_result  = $this->_get_mock('db_query');
    $db_rows  = array(
      array(
        'cat_id'    => '10',
        'cat_name'    => 'Mammals',
        'cat_url_title' => 'mammals'
      ),
      array(
        'cat_id'    => '20',
        'cat_name'    => 'Reptiles',
        'cat_url_title' => 'reptiles'
      ),
      array(
        'cat_id'    => '30',
        'cat_name'    => 'Birds',
        'cat_url_title' => 'birds'
      )
    );

    $this->EE->db->setReturnReference('get_where', $db_result);
    $db_result->setReturnValue('num_rows', count($db_rows));
    $db_result->setReturnValue('result_array', $db_rows);

    $expected_result = array();

    foreach ($db_rows AS $db_row)
    {
      $expected_result[] = new EI_category($db_row);
    }
  
    $actual_result = $this->_subject->get_all_categories();

    $this->assertIdentical(count($actual_result), count($expected_result));

    for ($count = 0, $length = count($actual_result); $count < $length; $count++)
    {
      $this->assertIdentical($actual_result[$count], $expected_result[$count]);
    }
  }

  public function test__get_all_categories__no_categories()
  {
    $this->EE->db->expectOnce('select', array('cat_id, cat_name, cat_url_title'));
    $this->EE->db->expectOnce('get_where', array('categories', array('site_id' => $this->_site_id)));

    $db_result  = $this->_get_mock('db_query');
    $db_rows  = array();

    $this->EE->db->setReturnReference('get_where', $db_result);
    $db_result->setReturnValue('result_array', $db_rows);

    $this->assertIdentical(array(), $this->_subject->get_all_categories());
  }


  public function test__get_all_categories__cached_in_session()
  {
    $db = $this->EE->db;
    $session = $this->EE->session;

    $categories = array(
      new EI_category(array('cat_id' => '10', 'cat_name' => 'cat', 'cat_url_title' => 'feline')),
      new EI_category(array('cat_id' => '20', 'cat_name' => 'dog', 'cat_url_title' => 'canine')),
      new EI_category(array('cat_id' => '30', 'cat_name' => 'ant', 'cat_url_title' => 'insect')),
      new EI_category(array('cat_id' => '40', 'cat_name' => 'dec', 'cat_url_title' => 'asinine'))
    );

    $session->cache = array($this->_namespace => array($this->_package_name => array('categories' => $categories)));
    $db->expectNever('select');
    $db->expectNever('get_where');

    $this->assertIdentical($categories, $this->_subject->get_all_categories());
  }


  public function test__get_all_crumbly_glossary_terms__success()
  {
    $this->EE->db->expectOnce('select', array('glossary_definition, glossary_term'));
    $this->EE->db->expectOnce('get_where', array('crumbly_glossary', array('site_id' => $this->_site_id)));

    $db_result  = $this->_get_mock('db_query');
    $db_rows  = array(
      array(
        'glossary_definition' => 'Definition A',
        'glossary_term'     => 'term_a'
      ),
      array(
        'glossary_definition' => 'Definition B',
        'glossary_term'     => 'term_b'
      )
    );

    $this->EE->db->setReturnReference('get_where', $db_result);
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


  public function test__get_all_crumbly_glossary_terms__cached_in_session()
  {
    $db = $this->EE->db;
    $session = $this->EE->session;

    $glossary = array(
        new Crumbly_glossary_term(array('glossary_definition' => 'cat', 'glossary_term' => 'Feline')),
        new Crumbly_glossary_term(array('glossary_definition' => 'dog', 'glossary_term' => 'Canine')),
        new Crumbly_glossary_term(array('glossary_definition' => 'cow', 'glossary_term' => 'Bovine')),
        new Crumbly_glossary_term(array('glossary_definition' => 'horse', 'glossary_term' => 'Equine'))
    );

    $session->cache = array($this->_namespace => array($this->_package_name => array('crumbly_glossary' => $glossary)));
    $db->expectNever('select');
    $db->expectNever('get_where');

    $this->assertIdentical($glossary, $this->_subject->get_all_crumbly_glossary_terms());
  }


  public function test__get_all_crumbly_templates__success()
  {
    $this->EE->db->expectOnce('select', array('label, template_id'));
    $this->EE->db->expectOnce('get_where', array('crumbly_templates', array('site_id' => $this->_site_id)));
  
    $db_templates = $this->_get_mock('db_query');
    $db_rows    = array(
      array(
        'label'     => 'Template A',
        'template_id' => '10'
      ),
      array(
        'label'     => 'Template B',
        'template_id' => '20'
      ),
      array(
        'label'     => 'Template C',
        'template_id' => '30'
      )
    );

    $this->EE->db->setReturnReference('get_where', $db_templates);
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

    $this->EE->db->setReturnReference('get_where', $db_templates);
    $db_templates->setReturnValue('result_array', array());
  
    $this->assertIdentical(array(), $this->_subject->get_all_crumbly_templates());
  }


  public function test__get_all_crumbly_template__cached_in_session()
  {
    $db = $this->EE->db;
    $session = $this->EE->session;

    $templates = array(
      new Crumbly_template(array('label' => 'About Us', 'template_id' => '10')),
      new Crumbly_template(array('label' => 'Latest News', 'template_id' => '20')),
      new Crumbly_template(array('label' => 'Get in Touch', 'template_id' => '30')),
      new Crumbly_template(array('label' => 'Goodies', 'template_id' => '40'))
    );

    $session->cache = array($this->_namespace => array($this->_package_name => array('crumbly_templates' => $templates)));
    $db->expectNever('select');
    $db->expectNever('get_where');

    $this->assertIdentical($templates, $this->_subject->get_all_crumbly_templates());
  }


  public function test__get_all_crumbly_template_groups__success()
  {
    $this->EE->db->expectOnce('select', array('group_id, label'));
    $this->EE->db->expectOnce('get_where', array('crumbly_template_groups', array('site_id' => $this->_site_id)));
  
    $db_groups  = $this->_get_mock('db_query');
    $db_rows  = array(
      array(
        'group_id'  => '10',
        'label'   => 'Template A'
      ),
      array(
        'group_id'  => '20',
        'label'   => 'Template B'
      ),
      array(
        'group_id'  => '30',
        'label'   => 'Template C'
      )
    );

    $this->EE->db->setReturnReference('get_where', $db_groups);
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

    $this->EE->db->setReturnReference('get_where', $db_groups);
    $db_groups->setReturnValue('result_array', array());
  
    $this->assertIdentical(array(), $this->_subject->get_all_crumbly_template_groups());
  }


  public function test__get_all_crumbly_template_groups__cached_in_session()
  {
    $db = $this->EE->db;
    $session = $this->EE->session;

    $groups = array(
      new Crumbly_template_group(array('group_id' => '10', 'label' => 'About Us')),
      new Crumbly_template_group(array('group_id' => '10', 'label' => 'Latest News')),
      new Crumbly_template_group(array('group_id' => '20', 'label' => 'Get in Touch')),
      new Crumbly_template_group(array('group_id' => '20', 'label' => 'Goodies'))
    );

    $session->cache = array($this->_namespace => array($this->_package_name => array('crumbly_template_groups' => $groups)));
    $db->expectNever('select');
    $db->expectNever('get_where');

    $this->assertIdentical($groups, $this->_subject->get_all_crumbly_template_groups());
  }


  public function test__get_all_templates__success()
  {
    $hidden     = '_';
    $config     = $this->EE->config;
    $config->setReturnValue('item', $hidden, array('hidden_template_indicator'));

    $db         = $this->EE->db;
    $db_result  = $this->_get_mock('db_query');
    $db_rows    = array(
      array(
        'group_id'    => '10',
        'template_id' => '15',
        'template_name' => 'template_a'
      ),
      array(
        'group_id'    => '10',
        'template_id' => '25',
        'template_name' => 'template_b'
      )
    );

    $where_clauses = array('site_id' => $this->_site_id, 'template_type' => 'webpage');

    $db->expectOnce('select', array('group_id, template_id, template_name'));
    $db->expectOnce('where', array($where_clauses));
    $db->expectOnce('not_like', array('template_name', $hidden, 'after'));
    $db->expectOnce('get', array('templates'));

    $db->setReturnReference('get', $db_result);
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


  public function test__get_all_templates__hidden_template_indicator_not_set()
  {
    $hidden     = '.';
    $config     = $this->EE->config;
    $config->setReturnValue('item', '', array('hidden_template_indicator'));

    $db         = $this->EE->db;
    $db_result  = $this->_get_mock('db_query');
    $db_rows    = array();
    $where_clauses = array('site_id' => $this->_site_id, 'template_type' => 'webpage');

    $db->expectOnce('select', array('group_id, template_id, template_name'));
    $db->expectOnce('where', array($where_clauses));
    $db->expectOnce('not_like', array('template_name', $hidden, 'after'));
    $db->expectOnce('get', array('templates'));

    $db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('result_array', $db_rows);

    $this->_subject->get_all_templates();
  }


  public function test__get_all_templates__no_templates()
  {
    $db_result  = $this->_get_mock('db_query');
    $db_rows  = array();

    $this->EE->db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('result_array', $db_rows);

    $this->assertIdentical(array(), $this->_subject->get_all_templates());
  }


  public function test__get_all_templates__cached_in_session()
  {
    $session    = $this->EE->session;
    $templates  = array(
      new EI_template(array('group_id' => '10', 'template_id' => '5', 'template_name' => 'index')),
      new EI_template(array('group_id' => '10', 'template_id' => '15', 'template_name' => 'entry')),
      new EI_template(array('group_id' => '20', 'template_id' => '25', 'template_name' => 'index')),
      new EI_template(array('group_id' => '20', 'template_id' => '35', 'template_name' => 'directions'))
    );

    $session->cache = array($this->_namespace => array($this->_package_name => array('templates' => $templates)));
    
    $this->EE->db->expectNever('get');
    $this->assertIdentical($templates, $this->_subject->get_all_templates());
  }


  public function test__get_all_template_groups__success()
  {
    $config     = $this->EE->config;
    $hidden     = '_';
    $config->setReturnValue('item', $hidden, array('hidden_template_indicator'));

    $db         = $this->EE->db;
    $db_result  = $this->_get_mock('db_query');
    $db_rows  = array(
      array(
        'group_id'    => '10',
        'group_name'  => 'group_a'
      ),
      array(
        'group_id'    => '20',
        'group_name'  => 'group_b'
      ),
      array(
        'group_id'    => '30',
        'group_name'  => 'group_c'
      )
    );

    $db->expectOnce('select', array('group_id, group_name'));
    $db->expectOnce('where', array('site_id', $this->_site_id));
    $db->expectOnce('not_like', array('group_name', $hidden, 'after'));
    $db->expectOnce('get', array('template_groups'));

    $db->setReturnReference('get', $db_result);
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


  public function test__get_all_template_groups__hidden_template_indicator_not_set()
  {
    $config     = $this->EE->config;
    $hidden     = '.';
    $config->setReturnValue('item', '', array('hidden_template_indicator'));

    $db         = $this->EE->db;
    $db_result  = $this->_get_mock('db_query');

    $db->expectOnce('select', array('group_id, group_name'));
    $db->expectOnce('where', array('site_id', $this->_site_id));
    $db->expectOnce('not_like', array('group_name', $hidden, 'after'));
    $db->expectOnce('get', array('template_groups'));

    $db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('result_array', array());

    $this->_subject->get_all_template_groups();
  }


  public function test__get_all_template_groups__no_templates()
  {
    $db_result  = $this->_get_mock('db_query');
    $db_rows  = array();

    $this->EE->db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('result_array', $db_rows);

    $this->assertIdentical(array(), $this->_subject->get_all_template_groups());
  }


  public function test__get_all_template_groups__cached_in_session()
  {
    $db = $this->EE->db;
    $session = $this->EE->session;

    $groups = array(
      new EI_template_group(array('group_id' => '10', 'group_name' => 'about')),
      new EI_template_group(array('group_id' => '10', 'group_name' => 'blog')),
      new EI_template_group(array('group_id' => '20', 'group_name' => 'contact')),
      new EI_template_group(array('group_id' => '20', 'group_name' => 'products'))
    );

    $session->cache = array($this->_namespace => array($this->_package_name => array('template_groups' => $groups)));
    $db->expectNever('get');

    $this->assertIdentical($groups, $this->_subject->get_all_template_groups());
  }


  public function test__get_channel_entry_title_from_segment__url_title_success()
  {
    // Shortcuts.
    $db = $this->EE->db;

    // Dummy values.
    $url_title  = 'white_stripes';
    $title    = 'The White Stripes';

    $query_result   = $this->_get_mock('db_query');
    $query_row      = new StdClass();
    $query_row->title = $title;

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
    $db = $this->EE->db;

    // Dummy values.
    $entry_id = '10';
    $title    = 'The White Stripes';

    $query_result   = $this->_get_mock('db_query');
    $query_row      = new StdClass();
    $query_row->title = $title;

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
    $db = $this->EE->db;

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
    $db = $this->EE->db;

    // Dummy values.
    $url_title    = 'white_stripes';
    $query_result = $this->_get_mock('db_query');

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


  public function test__get_category_from_segment__category_id_segment_success()
  {
    $cat_id   = '12';
    $segment  = 'C' .$cat_id;
    $db_result  = $this->_get_mock('db_query');
    $db_row   = array(
      'cat_id'    => $cat_id,
      'cat_name'    => 'Chairs & Sofas',
      'cat_url_title' => 'seating'
    );

    $this->EE->db->expectOnce('select', array('cat_id, cat_name, cat_url_title'));
    $this->EE->db->expectOnce('get_where', array('categories', array('cat_id' => $cat_id), 1));

    $this->EE->db->setReturnReference('get_where', $db_result);
    $db_result->setReturnValue('num_rows', 1);
    $db_result->setReturnValue('row_array', $db_row);

    $expected_result = new EI_category($db_row);
    $this->assertIdentical($expected_result, $this->_subject->get_category_from_segment($segment));
  }


  public function test__get_category_from_segment__category_id_segment_failure()
  {
    $cat_id   = '12';
    $segment  = 'C' .$cat_id;
    $db_result  = $this->_get_mock('db_query');

    $this->EE->db->expectOnce('select', array('cat_id, cat_name, cat_url_title'));
    $this->EE->db->expectOnce('get_where', array('categories', array('cat_id' => $cat_id), 1));

    $this->EE->db->setReturnReference('get_where', $db_result);
    $db_result->setReturnValue('num_rows', 0);
    $db_result->expectNever('row_array');

    $this->assertIdentical(FALSE, $this->_subject->get_category_from_segment($segment));
  }


  public function test__get_category_from_segment__category_url_title_segment_success()
  {
    $segment  = 'seating';
    $db_result  = $this->_get_mock('db_query');
    $db_row   = array(
      'cat_id'    => '10',
      'cat_name'    => 'Chairs & Sofas',
      'cat_url_title' => $segment
    );

    $this->EE->db->expectOnce('select', array('cat_id, cat_name, cat_url_title'));
    $this->EE->db->expectOnce('get_where', array('categories', array('cat_url_title' => $segment), 1));

    $this->EE->db->setReturnReference('get_where', $db_result);
    $db_result->setReturnValue('num_rows', 1);
    $db_result->setReturnValue('row_array', $db_row);

    $expected_result = new EI_category($db_row);
    $this->assertIdentical($expected_result, $this->_subject->get_category_from_segment($segment));
  }


  public function test__get_category_from_segment__invalid_segment()
  {
    $segment = FALSE;

    $this->EE->db->expectNever('select');
    $this->EE->db->expectNever('get_where');
  
    $this->assertIdentical(FALSE, $this->_subject->get_category_from_segment($segment));
  }


  public function test__get_crumbly_template_from_segments__success()
  {
    $group_segment    = 'bands';
    $template_segment = 'white-stripes';
    $db_result      = $this->_get_mock('db_query');
    $db_row       = array('template_id' => '10', 'label' => 'The White Stripes');

    $this->EE->db->expectOnce('select', array('crumbly_templates.template_id, crumbly_templates.label'));
    $this->EE->db->expectOnce('from', array('crumbly_templates'));
    $this->EE->db->expectCallCount('join', 2);
    $this->EE->db->expectAt(0, 'join', array('templates', 'templates.template_id = crumbly_templates.template_id', 'inner'));
    $this->EE->db->expectAt(1, 'join', array('template_groups', 'template_groups.group_id = templates.group_id', 'inner'));
    $this->EE->db->expectOnce('where', array(array(
      'crumbly_templates.site_id' => $this->_site_id,
      'templates.template_name' => $template_segment,
      'template_groups.group_name' => $group_segment
    )));
    $this->EE->db->expectOnce('limit', array(1));
    $this->EE->db->expectOnce('get');

    $db_result->expectOnce('num_rows');
    $db_result->expectOnce('row_array');

    $this->EE->db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('num_rows', 1);
    $db_result->setReturnValue('row_array', $db_row);
    
    $expected_result = new Crumbly_template($db_row);

    $this->assertIdentical($expected_result, $this->_subject->get_crumbly_template_from_segments($group_segment, $template_segment));
  }


  public function test__get_crumbly_template_from_segments__no_matches()
  {
    $group_segment    = 'bands';
    $template_segment = 'white-stripes';
    $db_result      = $this->_get_mock('db_query');

    $this->EE->db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('num_rows', 0);
    $db_result->expectNever('row_array');
  
    $this->assertIdentical(FALSE, $this->_subject->get_crumbly_template_from_segments($group_segment, $template_segment));
  }


  public function test__get_crumbly_template_from_segments__invalid_template_segment()
  {
    $group_segment    = 'bands';
    $template_segment = '';

    $this->EE->db->expectNever('select');
    $this->EE->db->expectNever('from');
    $this->EE->db->expectNever('join');
    $this->EE->db->expectNever('where');
    $this->EE->db->expectNever('limit');
    $this->EE->db->expectNever('get');
  
    $this->assertIdentical(FALSE, $this->_subject->get_crumbly_template_from_segments($group_segment, $template_segment));
  }


  public function test__get_crumbly_template_from_segments__invalid_group_segment()
  {
    $group_segment    = '';
    $template_segment = 'white-stripes';

    $this->EE->db->expectNever('select');
    $this->EE->db->expectNever('from');
    $this->EE->db->expectNever('join');
    $this->EE->db->expectNever('where');
    $this->EE->db->expectNever('limit');
    $this->EE->db->expectNever('get');
  
    $this->assertIdentical(FALSE, $this->_subject->get_crumbly_template_from_segments($group_segment, $template_segment));
  }


  public function test__get_crumbly_template_group_from_segment__success()
  {
    $segment  = 'bands';
    $db_result  = $this->_get_mock('db_query');
    $db_row   = array(
      'group_id'    => '10',
      'label'     => 'Awesome Bands'
    );

    $this->EE->db->expectOnce('select', array('crumbly_template_groups.group_id, crumbly_template_groups.label'));
    $this->EE->db->expectOnce('from', array('crumbly_template_groups'));
    $this->EE->db->expectOnce('join', array('template_groups', 'template_groups.group_id = crumbly_template_groups.group_id', 'inner'));
    $this->EE->db->expectOnce('where', array(array(
      'crumbly_template_groups.site_id' => $this->_site_id,
      'template_groups.group_name'    => $segment
    )));
    $this->EE->db->expectOnce('limit', array(1));
    $this->EE->db->expectOnce('get');

    $db_result->expectOnce('num_rows');
    $db_result->expectOnce('row_array');

    $this->EE->db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('num_rows', 1);
    $db_result->setReturnValue('row_array', $db_row);
    
    $expected_result = new Crumbly_template_group($db_row);

    $this->assertIdentical($expected_result, $this->_subject->get_crumbly_template_group_from_segment($segment));
  }


  public function test__get_crumbly_template_group_from_segment__no_matches()
  {
    $segment  = 'bands';
    $db_result  = $this->_get_mock('db_query');

    $this->EE->db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('num_rows', 0);
    $db_result->expectNever('row_array');
  
    $this->assertIdentical(FALSE, $this->_subject->get_crumbly_template_group_from_segment($segment));
  }


  public function test__get_crumbly_template_group_from_segment__invalid_segment()
  {
    $segment = '';

    $this->EE->db->expectNever('select');
    $this->EE->db->expectNever('from');
    $this->EE->db->expectNever('join');
    $this->EE->db->expectNever('where');
    $this->EE->db->expectNever('limit');
    $this->EE->db->expectNever('get');
  
    $this->assertIdentical(FALSE, $this->_subject->get_crumbly_template_group_from_segment($segment));
  }
  
  
  public function test__get_package_theme_url__end_slash_exists()
  {
    // Dummy values.
    $config_theme_url = 'http://example.com/themes/';
    $return_theme_url = 'http://example.com/themes/third_party/' .$this->_package_name .'/';
    
    // Expectations and return values.
    $this->EE->config->expectOnce('item', array('theme_folder_url'));
    $this->EE->config->setReturnValue('item', $config_theme_url, array('theme_folder_url'));

    // Run the tests.
    $this->assertIdentical($return_theme_url, $this->_subject->get_package_theme_url());
  }
    

  public function test__get_package_theme_url__no_end_slash_exists()
  {
    // Dummy values.
    $config_theme_url = 'http://example.com/themes';
    $return_theme_url = 'http://example.com/themes/third_party/' .$this->_package_name .'/';
    
    // Expectations and return values.
    $this->EE->config->expectOnce('item', array('theme_folder_url'));
    $this->EE->config->setReturnValue('item', $config_theme_url, array('theme_folder_url'));

    // Run the tests.
    $this->assertIdentical($return_theme_url, $this->_subject->get_package_theme_url());
  }
    

  public function test__get_site_id__success()
  {
    // Expectations.
    $this->EE->config->expectOnce('item', array('site_id'));
    
    // Tests.
    $this->assertIdentical(intval($this->_site_id), $this->_subject->get_site_id());
  }
  
  
  public function test__get_templates_by_template_group__success()
  {
    $group_id       = 10;
    $other_group_id = 20;

    $db_result = $this->_get_mock('db_query');
    $db_rows = array(
      array(
        'group_id'    => $group_id,
        'template_id' => '15',
        'template_name' => 'template_a'
      ),
      array(
          'group_id'      => $other_group_id,
          'template_id'   => '20',
          'template_name' => 'template_b'
      ),
      array(
        'group_id'    => $group_id,
        'template_id' => '25',
        'template_name' => 'template_c'
      )
    );

    $this->EE->db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('result_array', $db_rows);

    $expected_result = array(
        new EI_template($db_rows[0]),
        new EI_template($db_rows[2])
    );

    $actual_result = $this->_subject->get_templates_by_template_group($group_id);
    $this->assertIdentical(count($actual_result), count($expected_result));

    for ($count = 0, $length = count($actual_result); $count < $length; $count++)
    {
      $this->assertIdentical($actual_result[$count], $expected_result[$count]);
    }
  }


  public function test__get_templates_by_template_group__no_templates()
  {
    $group_id = 10;
    $db_result  = $this->_get_mock('db_query');

    $this->EE->db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('result_array', array());
  
    $this->assertIdentical(array(), $this->_subject->get_templates_by_template_group($group_id));
  }


  public function test__get_templates_by_template_group__invalid_group_id()
  {
    $group_id = FALSE;

    $this->EE->db->expectNever('get');
    $this->assertIdentical(FALSE, $this->_subject->get_templates_by_template_group($group_id));
  }


  public function test__humanize__no_glossary_underscore_success()
  {
    // Dummy values.
    $machine  = 'about_us';
    $human    = 'About Us';

    // Retrieve the word separator.
    $this->EE->config->expectOnce('item', array('word_separator'));
    $this->EE->config->setReturnValue('item', 'underscore', array('word_separator'));

    // Run the tests.
    $this->assertIdentical($human, $this->_subject->humanize($machine, FALSE));
  }


  public function test__humanize__no_glossary_dash_success()
  {
    // Dummy values.
    $machine  = 'about-us';
    $human    = 'About Us';

    // Retrieve the word separator.
    $this->EE->config->expectOnce('item', array('word_separator'));
    $this->EE->config->setReturnValue('item', 'dash', array('word_separator'));

    // Run the tests.
    $this->assertIdentical($human, $this->_subject->humanize($machine, FALSE));
  }


  public function test__humanize__no_glossary_undefined_separator_dash()
  {
    // Dummy values.
    $machine  = 'about-us';
    $human    = 'About Us';

    // Retrieve the word separator.
    $this->EE->config->expectOnce('item', array('word_separator'));
    $this->EE->config->setReturnValue('item', FALSE, array('word_separator'));

    // Run the tests.
    $this->assertIdentical($human, $this->_subject->humanize($machine, FALSE));
  }


  public function test__humanize__no_glossary_url_decodes_the_string()
  {
    // Dummy values.
    $machine  = urlencode('jack_white -- blunderbuss');
    $human    = 'Jack White -- Blunderbuss';

    // Retrieve the word separator.
    $this->EE->config->expectOnce('item', array('word_separator'));
    $this->EE->config->setReturnValue('item', 'underscore', array('word_separator'));

    // Run the tests.
    $this->assertIdentical($human, $this->_subject->humanize($machine, FALSE));
  }


  public function test__humanize__glossary_success()
  {
    $machine  = 'room';
    $human    = 'Zimmer';

    $db_glossary    = $this->_get_mock('db_query');
    $db_glossary_rows = array(array('glossary_term' => $machine, 'glossary_definition' => $human));

    $this->EE->db->setReturnReference('get_where', $db_glossary, array('crumbly_glossary', '*'));
    $db_glossary->setReturnValue('result_array', $db_glossary_rows);

    $this->assertIdentical($human, $this->_subject->humanize($machine));
  }


  public function test__humanize__no_machine_string()
  {
    $machine  = '';
    $human    = '';

    $this->EE->db->expectNever('get_where');
    $this->assertIdentical($human, $this->_subject->humanize($machine));
  }


  public function test__install_module_glossary_table__success()
  {
    $fields = array(
      'site_id' => array(
        'constraint'    => 5,
        'type'        => 'INT',
        'unsigned'      => TRUE
      ),
      'glossary_definition' => array(
        'constraint'    => 255,
        'type'        => 'VARCHAR'
      ),
      'glossary_term' => array(
        'constraint'    => 255,
        'type'        => 'VARCHAR'
      )
    );

    $this->EE->dbforge->expectOnce('add_field', array($fields));
    $this->EE->dbforge->expectOnce('add_key', array('site_id'));
    $this->EE->dbforge->expectOnce('create_table', array('crumbly_glossary', TRUE));
  
    $this->_subject->install_module_glossary_table();
  }


  public function test__install_module_register__success()
  {
    // Dummy values.
    $query_data = array(
      'has_cp_backend'    => 'y',
      'has_publish_fields'  => 'n',
      'module_name'     => ucfirst($this->_package_name),
      'module_version'    => $this->_package_version
    );
    
    // Expectations.
    $this->EE->db->expectOnce('insert', array('modules', $query_data));
    
    // Tests.
    $this->_subject->install_module_register();
  }
  
    
  public function test__install_module_templates_table__success()
  {
    $fields = array(
      'template_id' => array(
        'constraint'    => 10,
        'type'        => 'INT',
        'unsigned'      => TRUE
      ),
      'site_id' => array(
        'constraint'    => 5,
        'type'        => 'INT',
        'unsigned'      => TRUE
      ),
      'label' => array(
        'constraint'    => 255,
        'type'        => 'VARCHAR'
      )
    );

    $this->EE->dbforge->expectOnce('add_field', array($fields));
    $this->EE->dbforge->expectCallCount('add_key', 2);
    $this->EE->dbforge->expectAt(0, 'add_key', array('site_id'));
    $this->EE->dbforge->expectAt(1, 'add_key', array('template_id', TRUE));
    $this->EE->dbforge->expectOnce('create_table', array('crumbly_templates', TRUE));
  
    $this->_subject->install_module_templates_table();
  }


  public function test__install_module_template_groups_table__success()
  {
    $fields = array(
      'group_id' => array(
        'constraint'    => 10,
        'type'        => 'INT',
        'unsigned'      => TRUE
      ),
      'site_id' => array(
        'constraint'    => 5,
        'type'        => 'INT',
        'unsigned'      => TRUE
      ),
      'label' => array(
        'constraint'    => 255,
        'type'        => 'VARCHAR'
      )
    );

    $this->EE->dbforge->expectOnce('add_field', array($fields));
    $this->EE->dbforge->expectCallCount('add_key', 2);
    $this->EE->dbforge->expectAt(0, 'add_key', array('site_id'));
    $this->EE->dbforge->expectAt(1, 'add_key', array('group_id', TRUE));
    $this->EE->dbforge->expectOnce('create_table', array('crumbly_template_groups', TRUE));
  
    $this->_subject->install_module_template_groups_table();
  }


  public function test__save_crumbly_glossary_term__success()
  {
    $definition   = 'Definition';
    $term     = 'term';
    $glossary_term  = new Crumbly_glossary_term(array(
      'glossary_definition' => $definition,
      'glossary_term'     => $term
    ));

    $insert_data = array(
      'glossary_definition' => $definition,
      'glossary_term'     => $term,
      'site_id'       => $this->_site_id
    );

    $this->EE->db->expectOnce('insert', array('crumbly_glossary', $insert_data));
    $this->assertIdentical(TRUE, $this->_subject->save_crumbly_glossary_term($glossary_term));
  }


  public function test__save_crumbly_glossary_term__missing_glossary_definition()
  {
    $glossary_term = new Crumbly_glossary_term(array('glossary_term' => 'term'));
    $this->EE->db->expectNever('insert');
    $this->assertEqual(FALSE, $this->_subject->save_crumbly_glossary_term($glossary_term));
  }


  public function test__save_crumbly_glossary_term__missing_glossary_term()
  {
    $glossary_term = new Crumbly_glossary_term(array('glossary_definition' => 'Definition'));
    $this->EE->db->expectNever('insert');
    $this->assertEqual(FALSE, $this->_subject->save_crumbly_glossary_term($glossary_term));
  }


  public function test__save_crumbly_template__success()
  {
    $template = new Crumbly_template(array(
      'label'     => 'Example template',
      'template_id' => 20
    ));

    $insert_data = array(
      'label'     => $template->get_label(),
      'template_id' => $template->get_template_id(),
      'site_id'   => $this->_site_id
    );

    $this->EE->db->expectOnce('insert', array('crumbly_templates', $insert_data));
    $this->assertEqual(TRUE, $this->_subject->save_crumbly_template($template));
  }


  public function test__save_crumbly_template__missing_label()
  {
    $template = new Crumbly_template(array('template_id' => 20));
    $this->EE->db->expectNever('insert');
    $this->assertIdentical(FALSE, $this->_subject->save_crumbly_template($template));
  }


  public function test__save_crumbly_template__missing_template_id()
  {
    $template = new Crumbly_template(array('label' => 'Example label'));
    $this->EE->db->expectNever('insert');
    $this->assertIdentical(FALSE, $this->_subject->save_crumbly_template($template));
  }


  public function test__save_crumbly_template_group__success()
  {
    $group = new Crumbly_template_group(array(
      'group_id'  => 10,
      'label'   => 'Example group'
    ));

    $insert_data = array(
      'group_id'  => $group->get_group_id(),
      'label'   => $group->get_label(),
      'site_id' => $this->_site_id
    );

    $this->EE->db->expectOnce('insert', array('crumbly_template_groups', $insert_data));
    $this->assertIdentical(TRUE, $this->_subject->save_crumbly_template_group($group));
  }


  public function test__save_crumbly_template_group__missing_group_id()
  {
    $group = new Crumbly_template_group(array('label' => 'Example group'));
    $this->EE->db->expectNever('insert');
    $this->assertIdentical(FALSE, $this->_subject->save_crumbly_template_group($group));
  }


  public function test__save_crumbly_template_group__missing_label()
  {
    $group = new Crumbly_template_group(array('group_id' => 10));
    $this->EE->db->expectNever('insert');
    $this->assertIdentical(FALSE, $this->_subject->save_crumbly_template_group($group));
  }


  public function test__uninstall_module__success()
  {
    $db_module_result       = $this->_get_mock('db_query');
    $db_module_row        = new StdClass();
    $db_module_row->module_id = '10';
    $module_name        = ucfirst($this->_package_name);
    
    $this->EE->db->expectOnce('select', array('module_id'));
    $this->EE->db->expectOnce('get_where', array('modules', array('module_name' => $module_name), 1));
    $this->EE->db->setReturnReference('get_where', $db_module_result);
    $db_module_result->setReturnValue('num_rows', 1);
    $db_module_result->setReturnValue('row', $db_module_row);
    
    $this->EE->db->expectCallCount('delete', 2);
    $this->EE->db->expectAt(0, 'delete', array('module_member_groups', array('module_id' => $db_module_row->module_id)));
    $this->EE->db->expectAt(1, 'delete', array('modules', array('module_name' => $module_name)));
        
    $this->EE->dbforge->expectCallCount('drop_table', 3);
    $this->EE->dbforge->expectAt(0, 'drop_table', array('crumbly_glossary'));
    $this->EE->dbforge->expectAt(1, 'drop_table', array('crumbly_templates'));
    $this->EE->dbforge->expectAt(2, 'drop_table', array('crumbly_template_groups'));

    $this->assertIdentical(TRUE, $this->_subject->uninstall_module());
  }
  
  
  public function test__uninstall_module__module_not_found()
  {
    // Dummy values.
    $db_module_result = $this->_get_mock('db_query');
    
    // Expectations.
    $this->EE->db->expectOnce('select');
    $this->EE->db->expectOnce('get_where');
    $this->EE->db->expectNever('delete');
    
    // Return values.
    $this->EE->db->setReturnReference('get_where', $db_module_result);
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


/* End of file    : test.crumbly_model.php */
/* File location  : third_party/crumbly/tests/test.crumbly_model.php */
