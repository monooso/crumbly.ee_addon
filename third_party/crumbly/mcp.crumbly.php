<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * Crumbly module control panel.
 *
 * @author			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright		Experience Internet
 * @package			Crumbly */

class Crumbly_mcp {

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
	 * Model.
	 *
	 * @access	private
	 * @var		object
	 */
	private $_model;
	
	
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
		$this->_ee =& get_instance();
		$this->_ee->load->model('crumbly_model');
		$this->_model = $this->_ee->crumbly_model;
	}
	
	
	/**
	 * Module index page.
	 *
	 * @access	public
	 * @return	string
	 */
	public function index()
	{
		return '<p>Crumbly index page.</p>';
	}
	
}


/* End of file		: mcp.crumbly.php */
/* File location	: third_party/crumbly/mcp.crumbly.php */
