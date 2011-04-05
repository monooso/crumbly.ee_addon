<?php

/**
 * Crumbly module control panel tests.
 *
 * @author			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright		Experience Internet
 * @package			Crumbly */

require_once PATH_THIRD .'crumbly/mcp.crumbly' .EXT;
require_once PATH_THIRD .'crumbly/tests/mocks/mock.crumbly_model' .EXT;

class Test_crumbly_cp extends Testee_unit_test_case {
	
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
		
		Mock::generate('Mock_crumbly_model', get_class($this) .'_mock_model');
		$this->_model				= $this->_get_mock('model');
		$this->_ee->crumbly_model	= $this->_model;
		
		$this->_subject = new Crumbly_mcp();
	}


	public function test__save_glossary__success()
	{
		$input = array(
			array('glossary_term' => 'hotels', 'glossary_definition' => 'Accommodation'),
			array('glossary_term' => 'staff', 'glossary_definition' => 'Corporate Minions')
		);

		$this->_ee->input->setReturnValue('post', $input, array('glossary'));

		$this->_model->expectOnce('delete_all_crumbly_glossary_terms');
		$this->_model->expectCallCount('save_crumbly_glossary_term', count($input));
		$this->_model->setReturnValue('save_crumbly_glossary_term', TRUE);

		for ($count = 0, $length = count($input); $count < $length; $count++)
		{
			$term = new Crumbly_glossary_term($input[$count]);
			$this->_model->expectAt($count, 'save_crumbly_glossary_term', array($term));
		}

		$message = 'message';
		$this->_ee->lang->setReturnValue('line', $message, array('msg_glossary_terms_saved'));
		$this->_ee->session->expectOnce('set_flashdata', array('message_success', $message));
	
		$this->_subject->save_glossary();
	}


	public function test__save_glossary__unable_to_save()
	{
		$input = array(
			array('glossary_term' => 'hotels', 'glossary_definition' => 'Accommodation'),
			array('glossary_term' => 'staff', 'glossary_definition' => 'Corporate Minions')
		);

		$this->_ee->input->setReturnValue('post', $input, array('glossary'));

		$this->_model->expectOnce('delete_all_crumbly_glossary_terms');
		$this->_model->expectCallCount('save_crumbly_glossary_term', count($input));
		$this->_model->setReturnValue('save_crumbly_glossary_term', FALSE);

		$message = 'message';
		$this->_ee->lang->setReturnValue('line', $message, array('msg_glossary_terms_not_saved'));
		$this->_ee->session->expectOnce('set_flashdata', array('message_failure', $message));
	
		$this->_subject->save_glossary();
	}


	public function test__save_glossary__no_input()
	{
		$input = FALSE;
		$this->_ee->input->setReturnValue('post', $input, array('glossary'));

		$this->_model->expectOnce('delete_all_crumbly_glossary_terms');
		$this->_model->expectNever('save_crumbly_glossary_term');
		
		$message = 'message';
		$this->_ee->lang->setReturnValue('line', $message, array('msg_glossary_terms_saved'));
		$this->_ee->session->expectOnce('set_flashdata', array('message_success', $message));
	
		$this->_subject->save_glossary();
	}
	
}


/* End of file		: test.mcp_crumbly.php */
/* File location	: third_party/crumbly/tests/test.mcp_crumbly.php */
