<?php if ( ! defined('BASEPATH')) exit('Direct script access is not permitted.');

/**
 * Crumbly module.
 *
 * @author			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright		Experience Internet
 * @package			Crumbly */

class Crumbly {
	
	/* --------------------------------------------------------------
	 * PUBLIC PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * Return data.
	 *
	 * @access	public
	 * @var 	string
	 */
	public $return_data = '';
	
	
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
	
	
		
	
		/* --------------------------------------------------------------
	 * TEMPLATE TAG METHODS
	 * ------------------------------------------------------------ */
	
		/**
	 * 'breadcrumbs' template tag.
	 *
	 * @access	public
	 * @return	string
	 */
	public function breadcrumbs()
	{
		
	}
	
	
		}


/* End of file		: mod.crumbly.php */
/* File location	: third_party/crumbly/mod.crumbly.php */