<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * Crumbly model.
 *
 * @author          Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright       Experience Internet
 * @package         Crumbly
 */

require_once dirname(__FILE__) .'/../config.php';
require_once dirname(__FILE__) .'/../classes/crumbly_glossary_term.php';
require_once dirname(__FILE__) .'/../classes/crumbly_template.php';
require_once dirname(__FILE__) .'/../classes/crumbly_template_group.php';
require_once dirname(__FILE__) .'/../classes/EI_category.php';
require_once dirname(__FILE__) .'/../classes/EI_template.php';
require_once dirname(__FILE__) .'/../classes/EI_template_group.php';

class Crumbly_model extends CI_Model {

  private $EE;
  private $_namespace;
  private $_package_name;
  private $_package_version;
  private $_site_id;


  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */

  /**
   * Constructor.
   *
   * @access  public
   * @param   string    $package_name     Package name. Used during testing.
   * @param   string    $package_version  Package version. Used during testing.
   * @param   string    $namespace        Namespace. Used during testing.
   * @return  void
   */
  public function __construct($package_name = '', $package_version = '',
    $namespace = ''
  )
  {
    parent::__construct();

    $this->EE =& get_instance();

    $this->_namespace = $namespace
      ? strtolower($namespace) : 'experience';

    /**
     * Constants defined in the NSM Add-on Updater config.php file, so we don't 
     * have the package name and version defined in multiple locations.
     */

    $this->_package_name = $package_name
      ? strtolower($package_name)
      : strtolower(CRUMBLY_NAME);

    $this->_package_version = $package_version
      ? $package_version
      : CRUMBLY_VERSION;

    // Initialise the add-on cache.
    if ( ! array_key_exists($this->_namespace, $this->EE->session->cache))
    {
      $this->EE->session->cache[$this->_namespace] = array();
    }

    if ( ! array_key_exists($this->_package_name,
      $this->EE->session->cache[$this->_namespace])
    )
    {
      $this->EE->session->cache[$this->_namespace][$this->_package_name]
        = array();
    }
  }


  /**
   * Deletes all the Crumbly glossary terms for the current site.
   *
   * @access  public
   * @return  bool
   */
  public function delete_all_crumbly_glossary_terms()
  {
    $this->EE->db->delete('crumbly_glossary',
      array('site_id' => $this->get_site_id()));

    return TRUE;
  }


  /**
   * Deletes all the Crumbly templates for the current site.
   *
   * @access  public
   * @return  bool
   */
  public function delete_all_crumbly_templates()
  {
    $this->EE->db->delete('crumbly_templates',
      array('site_id' => $this->get_site_id()));

    return TRUE;
  }


  /**
   * Deletes all the Crumbly template groups for the current site.
   *
   * @access  public
   * @return  bool
   */
  public function delete_all_crumbly_template_groups()
  {
    $this->EE->db->delete('crumbly_template_groups',
      array('site_id' => $this->get_site_id()));

    return TRUE;
  }


  /**
   * Returns all the categories for the current site.
   *
   * @access  public
   * @return  array
   */
  public function get_all_categories()
  {
    $cache =& $this->_get_cache();

    if ( ! array_key_exists('categories', $cache))
    {
      $db_categories = $this->EE->db
        ->select('cat_id, cat_name, cat_url_title')
        ->get_where('categories', array('site_id' => $this->get_site_id()));

      $categories = array();

      foreach ($db_categories->result_array() AS $db_category)
      {
        $categories[] = new EI_category($db_category);
      }

      $cache['categories'] = $categories;
    }

    return $cache['categories'];
  }


  /**
   * Returns all the Crumbly glossary terms for the current site.
   *
   * @access  public
   * @return  array
   */
  public function get_all_crumbly_glossary_terms()
  {
    $cache =& $this->_get_cache();

    if ( ! array_key_exists('crumbly_glossary', $cache))
    {
      $db_terms = $this->EE->db
        ->select('glossary_definition, glossary_term')
        ->get_where('crumbly_glossary',
            array('site_id' => $this->get_site_id()));

      $glossary = array();

      foreach ($db_terms->result_array() AS $db_term)
      {
        $glossary[] = new Crumbly_glossary_term($db_term);
      }

      $cache['crumbly_glossary'] = $glossary;
    }

    return $cache['crumbly_glossary'];
  }


  /**
   * Returns all the Crumbly templates for the current site.
   *
   * @access  public
   * @return  array
   */
  public function get_all_crumbly_templates()
  {
    $cache =& $this->_get_cache();

    if ( ! array_key_exists('crumbly_templates', $cache))
    {
      $db_templates = $this->EE->db
        ->select('label, template_id')
        ->get_where('crumbly_templates',
            array('site_id' => $this->get_site_id()));

      $templates = array();

      foreach ($db_templates->result_array() AS $db_template)
      {
        $templates[] = new Crumbly_template($db_template);
      }

      $cache['crumbly_templates'] = $templates;
    }

    return $cache['crumbly_templates'];
  }


  /**
   * Returns all the Crumbly template groups for the current site.
   *
   * @access  public
   * @return  array
   */
  public function get_all_crumbly_template_groups()
  {
    $cache =& $this->_get_cache();

    if ( ! array_key_exists('crumbly_template_groups', $cache))
    {
      $db_groups = $this->EE->db
        ->select('group_id, label')
        ->get_where('crumbly_template_groups',
            array('site_id' => $this->get_site_id()));

      $groups = array();

      foreach ($db_groups->result_array() AS $db_group)
      {
        $groups[] = new Crumbly_template_group($db_group);
      }

      $cache['crumbly_template_groups'] = $groups;
    }

    return $cache['crumbly_template_groups'];
  }


  /**
   * Returns all the 'webpage' templates for the current site.
   *
   * @access  public
   * @return  array
   */
  public function get_all_templates()
  {
    $cache =& $this->_get_cache();

    if ( ! array_key_exists('templates', $cache))
    {
      $hidden = ($hidden = $this->EE->config->item('hidden_template_indicator'))
        ? $hidden : '.';

      $db_templates = $this->EE->db
        ->select('group_id, template_id, template_name')
        ->where(array('site_id' => $this->get_site_id(),
            'template_type' => 'webpage'))
        ->not_like('template_name', $hidden, 'after')
        ->get('templates');

      $templates = array();

      foreach ($db_templates->result_array() AS $db_template)
      {
        $templates[] = new EI_template($db_template);
      }

      $cache['templates'] = $templates;
    }

    return $cache['templates'];
  }


  /**
   * Returns all the template groups for the current site.
   *
   * @access  public
   * @return  array
   */
  public function get_all_template_groups()
  {
    $cache =& $this->_get_cache();

    if ( ! array_key_exists('template_groups', $cache))
    {
      $hidden = ($hidden = $this->EE->config->item('hidden_template_indicator'))
        ? $hidden : '.';

      $db_groups = $this->EE->db
        ->select('group_id, group_name')
        ->where('site_id', $this->get_site_id())
        ->not_like('group_name', $hidden, 'after')
        ->get('template_groups');

      $groups = array();

      foreach ($db_groups->result_array() AS $db_group)
      {
        $groups[] = new EI_template_group($db_group);
      }

      $cache['template_groups'] = $groups;
    }

    return $cache['template_groups'];
  }


  /**
   * Retrieves a category from the specified category ID, or category URL title.
   *
   * @access  public
   * @param   string    $segment    URL segment containing the category ID or
   *                                URL title.
   * @return  EI_category|FALSE
   */
  public function get_category_from_segment($segment)
  {
    if ( ! is_string($segment))
    {
      return FALSE;
    }

    $clause = preg_match('/^c[0-9]+$/i', $segment)
      ? array('cat_id' => substr($segment, 1))
      : array('cat_url_title' => $segment);

    $db_category = $this->EE->db
      ->select('cat_id, cat_name, cat_url_title')
      ->get_where('categories', $clause, 1);

    return $db_category->num_rows()
      ? new EI_category($db_category->row_array())
      : FALSE;
  }


  /**
   * Retrieves a channel entry title, given a URL segment. Supports url_title 
   * and entry_id. Returns FALSE if the entry cannot be found.
   *
   * @access  public
   * @param   int|string      $segment    The URL segment.
   * @return  string|FALSE
   */
  public function get_channel_entry_title_from_segment($segment)
  {
    // Get out early.
    if ( ! $segment OR is_numeric($segment) && intval($segment) <= 0)
    {
      return FALSE;
    }

    // Are we dealing with a URL title, or an entry ID?
    $query_clause = is_numeric($segment) && intval($segment) == $segment
      ? array('entry_id' => $segment)
      : array('url_title' => $segment);

    $db_result = $this->EE->db
      ->select('title')
      ->get_where('channel_titles', $query_clause, 1);

    return $db_result->num_rows()
      ? $db_result->row()->title
      : FALSE;
  }


  /**
   * Retrieves a Crumbly template from the specified template group, matching 
   * the given URL segment, if one exists.
   *
   * @access  public
   * @param   string      $group_segment          The template group URL title.
   * @param   string      $template_segment       The template URL title.
   * @return  Crumbly_template|FALSE
   */
  public function get_crumbly_template_from_segments($group_segment,
    $template_segment
  )
  {
    if ( ! $group_segment OR ! is_string($group_segment)
        OR ! $template_segment OR ! is_string($template_segment))
    {
      return FALSE;
    }

    $db_template = $this->EE->db
      ->select('crumbly_templates.template_id, crumbly_templates.label')
      ->from('crumbly_templates')
      ->join('templates',
          'templates.template_id = crumbly_templates.template_id', 'inner')
      ->join('template_groups',
          'template_groups.group_id = templates.group_id', 'inner')
      ->where(array(
          'crumbly_templates.site_id' => $this->get_site_id(),
          'templates.template_name'   => $template_segment,
          'template_groups.group_name' => $group_segment
        ))
      ->limit(1)
      ->get();

    return $db_template->num_rows()
      ? new Crumbly_template($db_template->row_array())
      : FALSE;
  }


  /**
   * Retrieves Crumbly template group for the given URL segment, if one exists.
   *
   * @access  public
   * @param   string      $segment        The URL segment.
   * @return  Crumbly_template_group|FALSE
   */
  public function get_crumbly_template_group_from_segment($segment)
  {
    if ( ! $segment OR ! is_string($segment))
    {
      return FALSE;
    }

    $db_group = $this->EE->db
      ->select('crumbly_template_groups.group_id, crumbly_template_groups.label')
      ->from('crumbly_template_groups')
      ->join('template_groups',
          'template_groups.group_id = crumbly_template_groups.group_id',
          'inner')
      ->where(array('crumbly_template_groups.site_id' => $this->get_site_id(),
          'template_groups.group_name' => $segment))
      ->limit(1)
      ->get();

    return $db_group->num_rows()
      ? new Crumbly_template_group($db_group->row_array())
      : FALSE;
  }


  /**
   * Returns the namespace.
   *
   * @access  public
   * @return  string
   */
  public function get_namespace()
  {
    return $this->_namespace;
  }


  /**
   * Returns the package name.
   *
   * @access  public
   * @return  string
   */
  public function get_package_name()
  {
    return $this->_package_name;
  }


  /**
   * Returns the package themes folder URL, appending a forward-slash, if 
   * required.
   *
   * @access  public
   * @return  string
   */
  public function get_package_theme_url()
  {
    $theme_url = $this->EE->config->item('theme_folder_url');
    $theme_url .= substr($theme_url, -1) == '/'
      ? 'third_party/'
      : '/third_party/';

    return $theme_url .$this->get_package_name() .'/';
  }


  /**
   * Returns the package version.
   *
   * @access  public
   * @return  string
   */
  public function get_package_version()
  {
    return $this->_package_version;
  }


  /**
   * Returns the site ID.
   *
   * @access  public
   * @return  string
   */
  public function get_site_id()
  {
    if ( ! $this->_site_id)
    {
      $this->_site_id = $this->EE->config->item('site_id');
    }

    return $this->_site_id;
  }


  /**
   * Retrieves all of the 'webpage' templates belonging to the specified 
   * template group.
   *
   * @access  public
   * @param   int|string      $group_id       The template group ID.
   * @return  array|FALSE
   */
  public function get_templates_by_template_group($group_id)
  {
    if ( ! valid_int($group_id, 1))
    {
      return FALSE;
    }

    $all_templates = $this->get_all_templates();
    $group_templates = array();

    foreach ($all_templates AS $template)
    {
      if ($template->get_group_id() == $group_id)
      {
        $group_templates[] = $template;
      }
    }

    return $group_templates;
  }


  /**
   * Takes a string and attempts to "humanise" it.
   *
   * @access  public
   * @param   string      $machine        The 'machine friendly' string (the URL 
   *                                      segment).
   * @param   bool        $use_glossary   Consult the glossary first?
   * @return  string
   */
  public function humanize($machine = '', $use_glossary = TRUE)
  {
    // Get out early.
    if ( ! $machine OR ! is_string($machine))
    {
        return '';
    }

    // By default, we always start by checking the glossary.
    if ($use_glossary !== FALSE)
    {
      $glossary = $this->get_all_crumbly_glossary_terms();

      foreach ($glossary AS $glossary_item)
      {
        if ($glossary_item->get_glossary_term() == $machine)
        {
          return $glossary_item->get_glossary_definition();
        }
      }
    }

    // Do the best we can.
    $separator = $this->EE->config->item('word_separator') == 'underscore'
      ? '_' : '-';

    return ucwords(str_replace($separator, ' ', urldecode($machine)));
  }


  /**
   * Installs the module.
   *
   * @access  public
   * @return  bool
   */
  public function install_module()
  {
    $this->install_module_register();
    $this->install_module_glossary_table();
    $this->install_module_templates_table();
    $this->install_module_template_groups_table();

    return TRUE;
  }


  /**
   * Creates the 'Crumbly Glossary' database table.
   *
   * @access  public
   * @return  void
   */
  public function install_module_glossary_table()
  {
    $this->EE->load->dbforge();

    $fields = array(
      'site_id' => array(
        'constraint'  => 5,
        'type'        => 'INT',
        'unsigned'    => TRUE
      ),
      'glossary_definition' => array(
        'constraint'  => 255,
        'type'        => 'VARCHAR'
      ),
      'glossary_term' => array(
        'constraint'  => 255,
        'type'        => 'VARCHAR'
      )
    );

    $this->EE->dbforge->add_field($fields);
    $this->EE->dbforge->add_key('site_id');
    $this->EE->dbforge->create_table('crumbly_glossary', TRUE);
  }


  /**
   * Register the module in the database.
   *
   * @access  public
   * @return  void
   */
  public function install_module_register()
  {
    $this->EE->db->insert('modules', array(
      'has_cp_backend'      => 'y',
      'has_publish_fields'  => 'n',
      'module_name'         => ucfirst($this->get_package_name()),
      'module_version'      => $this->get_package_version()
    ));
  }


  /**
   * Creates the 'Crumbly Templates' database table.
   *
   * @access  public
   * @return  void
   */
  public function install_module_templates_table()
  {
    $this->EE->load->dbforge();

    $fields = array(
      'template_id' => array(
        'constraint'  => 10,
        'type'        => 'INT',
        'unsigned'    => TRUE
      ),
      'site_id' => array(
        'constraint'  => 5,
        'type'        => 'INT',
        'unsigned'    => TRUE
      ),
      'label' => array(
        'constraint'  => 255,
        'type'        => 'VARCHAR'
      )
    );

    $this->EE->dbforge->add_field($fields);
    $this->EE->dbforge->add_key('site_id');
    $this->EE->dbforge->add_key('template_id', TRUE);
    $this->EE->dbforge->create_table('crumbly_templates', TRUE);
  }


  /**
   * Creates the 'Crumbly Template Groups' database table.
   *
   * @access  public
   * @return  void
   */
  public function install_module_template_groups_table()
  {
    $this->EE->load->dbforge();

    $fields = array(
      'group_id' => array(
        'constraint'  => 10,
        'type'        => 'INT',
        'unsigned'    => TRUE
      ),
      'site_id' => array(
        'constraint'  => 5,
        'type'        => 'INT',
        'unsigned'    => TRUE
      ),
      'label' => array(
        'constraint'  => 255,
        'type'        => 'VARCHAR'
      )
    );

    $this->EE->dbforge->add_field($fields);
    $this->EE->dbforge->add_key('site_id');
    $this->EE->dbforge->add_key('group_id', TRUE);
    $this->EE->dbforge->create_table('crumbly_template_groups', TRUE);
  }


  /**
   * Saves the specified Crumbly glossary term to the database.
   *
   * @access  public
   * @param   Crumbly_glossary_term   $glossary_term  The glossary term to save.
   * @return  bool
   */
  public function save_crumbly_glossary_term(
    Crumbly_glossary_term $glossary_term
  )
  {
    if ( ! $glossary_term->get_glossary_definition()
      OR ! $glossary_term->get_glossary_term()
    )
    {
      return FALSE;
    }

    $data = array_merge($glossary_term->to_array(),
      array('site_id' => $this->get_site_id()));

    $this->EE->db->insert('crumbly_glossary', $data);
    return TRUE;
  }


  /**
   * Saves the specified Crumbly template to the database.
   *
   * @access  public
   * @param   Crumbly_template        $template       The template to save.
   * @return  bool
   */
  public function save_crumbly_template(Crumbly_template $template)
  {
    if ( ! $template->get_label() OR ! $template->get_template_id())
    {
      return FALSE;
    }

    $data = array_merge($template->to_array(),
      array('site_id' => $this->get_site_id()));

    $this->EE->db->insert('crumbly_templates', $data);
    return TRUE;
  }


  /**
   * Saves the specified Crumbly template group to the database.
   *
   * @access  public
   * @param   Crumbly_template_group    $group    The template group to save.
   * @return  bool
   */
  public function save_crumbly_template_group(Crumbly_template_group $group)
  {
    if ( ! $group->get_group_id() OR ! $group->get_label())
    {
      return FALSE;
    }

    $data = array_merge($group->to_array(),
      array('site_id' => $this->get_site_id()));

    $this->EE->db->insert('crumbly_template_groups', $data);
    return TRUE;
  }


  /**
   * Uninstalls the module.
   *
   * @access  public
   * @return  bool
   */
  public function uninstall_module()
  {
    $module_name = ucfirst($this->get_package_name());

    // Retrieve the module information.
    $db_module = $this->EE->db
      ->select('module_id')
      ->get_where('modules', array('module_name' => $module_name), 1);

    if ($db_module->num_rows() !== 1)
    {
      return FALSE;
    }

    $this->EE->db->delete('module_member_groups',
      array('module_id' => $db_module->row()->module_id));

    $this->EE->db->delete('modules', array('module_name' => $module_name));

    $this->EE->load->dbforge();
    $this->EE->dbforge->drop_table('crumbly_glossary');
    $this->EE->dbforge->drop_table('crumbly_templates');
    $this->EE->dbforge->drop_table('crumbly_template_groups');

    return TRUE;
  }


  /**
   * Updates the module.
   *
   * @access  public
   * @param   string      $installed_version      The installed version.
   * @return  bool
   */
  public function update_module($installed_version = '')
  {
    if (version_compare($installed_version, $this->get_package_version(), '>='))
    {
      return FALSE;
    }

    return TRUE;
  }


  /* --------------------------------------------------------------
   * PRIVATE METHODS
   * ------------------------------------------------------------ */

  /**
   * Returns a reference to the package cache.
   *
   * @access  private
   * @return  array
   */
  private function &_get_cache()
  {
    return $this->EE->session->cache[$this->get_namespace()]
      [$this->get_package_name()];
  }


}


/* End of file      : crumbly_model.php */
/* File location    : third_party/crumbly/models/crumbly_model.php */
