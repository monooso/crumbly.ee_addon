<?php

/**
 * Crumbly category.
 *
 * @author			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright		Experience Internet
 * @package			Crumbly
 */

require_once PATH_THIRD .'crumbly/helpers/EI_number_helper' .EXT;

class Crumbly_category {

	private $_cat_id;
	private $_label;


	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @param	array		$props		Instance properties.
	 * @return	void
	 */
	public function __construct(Array $props = array())
	{
		$this->reset();

		foreach ($props AS $key => $val)
		{
			$method_name = 'set_' .$key;

			if (method_exists($this, $method_name))
			{
				$this->$method_name($val);
			}
		}
	}


	/**
	 * Returns category ID.
	 *
	 * @access	public
	 * @return	int
	 */
	public function get_cat_id()
	{
		return $this->_cat_id;
	}


	/**
	 * Returns the label.
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_label()
	{
		return $this->_label;
	}


	/**
	 * Resets the instance properties.
	 *
	 * @access	public
	 * @return	Crumbly_template
	 */
	public function reset()
	{
		$this->_cat_id	= 0;
		$this->_label	= '';

		return $this;
	}
	
	
	/**
	 * Sets category ID>
	 *
	 * @access	public
	 * @param	int		$cat_id		The category ID.
	 * @return	int
	 */
	public function set_cat_id($cat_id)
	{
		if (valid_int($cat_id, 1))
		{
			$this->_cat_id = intval($cat_id);
		}

		return $this->get_cat_id();
	}


	/**
	 * Sets the label.
	 *
	 * @access	public
	 * @param	string		$label		The label.
	 * @return	string
	 */
	public function set_label($label)
	{
		if (is_string($label))
		{
			$this->_label = $label;
		}

		return $this->get_label();
	}


	/**
	 * Returns the instance properties as an associative array.
	 *
	 * @access	public
	 * @return	array
	 */
	public function to_array()
	{
		return array(
			'cat_id'	=> $this->get_cat_id(),
			'label'		=> $this->get_label()
		);
	}
	
}

/* End of file		: crumbly_category.php */
/* File location	: third_party/crumbly/classes/crumbly_category.php */
