<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * Crumbly model.
 *
 * @author			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright		Experience Internet
 * @package			Crumbly
 * @version 		0.1.0
 */

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
		return array(
			'glossary' => array('room' => 'Zimmer'),
			'template_groups' => array(
				'about' => array(
					'title' => 'About Us',
					'templates' => array(
						'founder'	=> 'Our Founder',
						'history'	=> 'Our History',
						'team'		=> 'Our Team',
					)
				),
				'blog' => array(
					'title' => 'News',
					'templates' => array(
						'archive'	=> 'Archived News',
						'story'		=> 'News Story'
					)
				)
			)
		);
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
		
		return TRUE;
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
			'module_name'			=> $this->get_package_name(),
			'module_version'		=> $this->get_package_version()
		));
	}
	
	
	/**
	 * Uninstalls the module.
	 *
	 * @access	public
	 * @return	bool
	 */
	public function uninstall_module()
	{
		// Retrieve the module information.
		$db_module = $this->_ee->db
			->select('module_id')
			->get_where('modules', array('module_name' => $this->get_package_name()), 1);
		
		if ($db_module->num_rows() !== 1)
		{
			return FALSE;
		}
		
		// Delete module from the module_member_groups table.
		$this->_ee->db->delete('module_member_groups', array('module_id' => $db_module->row()->module_id));
		
		// Delete the module from the modules table.
		$this->_ee->db->delete('modules', array('module_name' => $this->get_package_name()));
		
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
