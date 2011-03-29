<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * Crumbly model.
 *
 * @author			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright		Experience Internet
 * @package			Crumbly
 * @version 		0.1.0
 */

require_once PATH_THIRD .'crumbly/classes/crumbly_glossary_term' .EXT;
require_once PATH_THIRD .'crumbly/classes/crumbly_template' .EXT;
require_once PATH_THIRD .'crumbly/classes/crumbly_template_group' .EXT;

class Crumbly_model extends CI_Model {
	
	/* --------------------------------------------------------------
	 * PRIVATE PROPERTIES
	 * ------------------------------------------------------------ */

	/**
	 * ExpressionEngine object reference.
	 *
	 * @access	private
	 * @var		object
	 */
	private $_ee;
	
	/**
	 * Package name.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_package_name;

	/**
	 * Package settings.
	 *
	 * @access	private
	 * @var		array
	 */
	private $_package_settings;
	
	/**
	 * Package version.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_package_version;
	
	/**
	 * The site ID.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_site_id;
	
	
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */

	/**
	 * Constructor.
	 *
	 * @access	public
	 * @param 	string		$package_name		The package name. Used during testing.
	 * @param	string		$package_version	The package version. Used during testing.
	 * @return	void
	 */
	public function __construct($package_name = '', $package_version = '')
	{
		parent::__construct();

		$this->_ee 				=& get_instance();
		$this->_package_name	= $package_name ? $package_name : 'crumbly';
		$this->_package_version	= $package_version ? $package_version : '0.1.0';
	}


	/**
	 * Deletes all the glossary terms for the current site from the database.
	 *
	 * @access	public
	 * @return	bool
	 */
	public function delete_all_glossary_terms()
	{
		$this->_ee->db->delete('crumbly_glossary', array('site_id' => $this->get_site_id()));
		return TRUE;
	}


	/**
	 * Returns all the glossary terms for the current site.
	 *
	 * @access	public
	 * @return	array
	 */
	public function get_all_glossary_terms()
	{
		$db_terms = $this->_ee->db->select('glossary_definition, glossary_term, glossary_term_id')
			->get_where('crumbly_glossary', array('site_id' => $this->get_site_id()));

		$terms = array();

		foreach ($db_terms->result_array() AS $db_term)
		{
			$terms[] = new Crumbly_glossary_term($db_term);
		}

		return $terms;
	}


	/**
	 * Retrieves a channel entry title, given a URL segment. Supports url_title and entry_id.
	 * Returns FALSE if the entry cannot be found.
	 *
	 * @access	public
	 * @param	int|string		$segment	The URL segment.
	 * @return	string|FALSE
	 */
	public function get_channel_entry_title_from_segment($segment = '')
	{
		// Get out early.
		if ( ! $segment OR is_numeric($segment) && intval($segment) <= 0)
		{
			return FALSE;
		}

		// Shortcuts.
		$db = $this->_ee->db;

		// Are we dealing with a URL title, or an entry ID?
		$query_clause = is_numeric($segment) && intval($segment) == $segment
			? array('entry_id' => $segment)
			: array('url_title' => $segment);

		$db_result = $db->select('title')->get_where('channel_titles', $query_clause, 1);

		return $db_result->num_rows()
			? $db_result->row()->title
			: FALSE;
	}
	
	
	/**
	 * Returns the package name.
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_package_name()
	{
		return $this->_package_name;
	}
	
	
	/**
	 * Returns the package settings.
	 *
	 * @access	public
	 * @return	array
	 */
	public function get_package_settings()
	{
		if ( ! $this->_package_settings)
		{
			$this->_package_settings = $this->_ee->config->item('crumbly_settings');
		}

		return $this->_package_settings;
	}


	/**
	 * Returns the package themes folder URL, appending a forward-slash, if required.
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_package_theme_url()
	{
		$theme_url = $this->_ee->config->item('theme_folder_url');
		$theme_url .= substr($theme_url, -1) == '/'
			? 'third_party/'
			: '/third_party/';

		return $theme_url .$this->get_package_name() .'/';
	}
	
	
	/**
	 * Returns the package version.
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_package_version()
	{
		return $this->_package_version;
	}


	/**
	 * Returns the site ID.
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_site_id()
	{
		if ( ! $this->_site_id)
		{
			$this->_site_id = $this->_ee->config->item('site_id');
		}
		
		return $this->_site_id;
	}
	
	
	/**
	 * Takes a string and attempts to "humanise" it.
	 *
	 * @access	public
	 * @param	string		$machine		The 'machine friendly' string (the URL segment).
	 * @param	bool		$use_glossary	Consult the glossary first?
	 * @return	string
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
			$settings = $this->get_package_settings();

			if (array_key_exists($machine, $settings['glossary']))
			{
				return $settings['glossary'][$machine];
			}
		}

		// Do the best we can.
		$separator = $this->_ee->config->item('word_separator') == 'underscore' ? '_' : '-';
		
		return ucwords(str_replace($separator, ' ', $machine));
	}


	/**
	 * Installs the module.
	 *
	 * @access	public
	 * @return	bool
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
	 * @access	public
	 * @return	void
	 */
	public function install_module_glossary_table()
	{
		$this->_ee->load->dbforge();

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

		$this->_ee->dbforge->add_field($fields);
		$this->_ee->dbforge->add_key('glossary_term_id', TRUE);
		$this->_ee->dbforge->add_key('site_id');
		$this->_ee->dbforge->create_table('crumbly_glossary', TRUE);
	}


	/**
	 * Register the module in the database.
	 *
	 * @access	public
	 * @return	void
	 */
	public function install_module_register()
	{
		$this->_ee->db->insert('modules', array(
			'has_cp_backend'		=> 'y',
			'has_publish_fields'	=> 'n',
			'module_name'			=> ucfirst($this->get_package_name()),		// Won't work without ucfirst.
			'module_version'		=> $this->get_package_version()
		));
	}
	
	
	/**
	 * Creates the 'Crumbly Templates' database table.
	 *
	 * @access	public
	 * @return	void
	 */
	public function install_module_templates_table()
	{
		$this->_ee->load->dbforge();

		$fields = array(
			'template_id' => array(
				'constraint'	=> 10,
				'type'			=> 'INT',
				'unsigned'		=> TRUE
			),
			'site_id' => array(
				'constraint'	=> 5,
				'type'			=> 'INT',
				'unsigned'		=> TRUE
			),
			'label' => array(
				'constraint'	=> 255,
				'type'			=> 'VARCHAR'
			)
		);

		$this->_ee->dbforge->add_field($fields);
		$this->_ee->dbforge->add_key('site_id');
		$this->_ee->dbforge->add_key('template_id', TRUE);
		$this->_ee->dbforge->create_table('crumbly_templates', TRUE);
	}
	
	
	/**
	 * Creates the 'Crumbly Template Groups' database table.
	 *
	 * @access	public
	 * @return	void
	 */
	public function install_module_template_groups_table()
	{
		$this->_ee->load->dbforge();

		$fields = array(
			'group_id' => array(
				'constraint'	=> 10,
				'type'			=> 'INT',
				'unsigned'		=> TRUE
			),
			'site_id' => array(
				'constraint'	=> 5,
				'type'			=> 'INT',
				'unsigned'		=> TRUE
			),
			'label' => array(
				'constraint'	=> 255,
				'type'			=> 'VARCHAR'
			)
		);

		$this->_ee->dbforge->add_field($fields);
		$this->_ee->dbforge->add_key('site_id');
		$this->_ee->dbforge->add_key('group_id', TRUE);
		$this->_ee->dbforge->create_table('crumbly_template_groups', TRUE);
	}


	/**
	 * Saves the specified glossary term to the database.
	 *
	 * @access	public
	 * @param	Crumbly_glossary_term		$glossary_term		The glossary term to save.
	 * @return	bool
	 */
	public function save_glossary_term(Crumbly_glossary_term $glossary_term)
	{
		if ( ! $glossary_term->get_glossary_definition() OR ! $glossary_term->get_glossary_term())
		{
			return FALSE;
		}

		$data = array(
			'glossary_definition'	=> $glossary_term->get_glossary_definition(),
			'glossary_term'			=> $glossary_term->get_glossary_term(),
			'site_id'				=> $this->get_site_id()
		);

		if ($glossary_term->get_glossary_term_id())
		{
			$where = array('glossary_term_id' => $glossary_term->get_glossary_term_id());
			$this->_ee->db->update('crumbly_glossary', $data, $where);
		}
		else
		{
			$this->_ee->db->insert('crumbly_glossary', $data);
		}

		return TRUE;
	}
	
	
	/**
	 * Uninstalls the module.
	 *
	 * @access	public
	 * @return	bool
	 */
	public function uninstall_module()
	{
		$module_name = ucfirst($this->get_package_name());

		// Retrieve the module information.
		$db_module = $this->_ee->db
			->select('module_id')
			->get_where('modules', array('module_name' => $module_name), 1);
		
		if ($db_module->num_rows() !== 1)
		{
			return FALSE;
		}
		
		$this->_ee->db->delete('module_member_groups', array('module_id' => $db_module->row()->module_id));
		$this->_ee->db->delete('modules', array('module_name' => $module_name));

		$this->_ee->load->dbforge();
		$this->_ee->dbforge->drop_table('crumbly_glossary');
		$this->_ee->dbforge->drop_table('crumbly_templates');
		$this->_ee->dbforge->drop_table('crumbly_template_groups');
		
		return TRUE;
	}
	
	
	/**
	 * Updates the module.
	 *
	 * @access	public
	 * @param 	string		$installed_version		The installed version.
	 * @return	bool
	 */
	public function update_module($installed_version = '')
	{
		if (version_compare($installed_version, $this->get_package_version(), '>='))
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
}

/* End of file		: crumbly_model.php */
/* File location	: third_party/crumbly/models/crumbly_model.php */
