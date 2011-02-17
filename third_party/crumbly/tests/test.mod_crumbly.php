<?php

/**
 * Crumbly module tests.
 *
 * @author			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright		Experience Internet
 * @package			Crumbly */

require_once PATH_THIRD .'crumbly/mcp.crumbly' .EXT;
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
		
		// The test subject.
		$this->_subject = new Crumbly();
	}
	
}


/* End of file		: test.mod_crumbly.php */
/* File location	: third_party/crumbly/tests/test.mod_crumbly.php */