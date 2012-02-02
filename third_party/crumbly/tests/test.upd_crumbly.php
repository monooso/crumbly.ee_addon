<?php

/**
 * Crumbly module update tests.
 *
 * @author      Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright   Experience Internet
 * @package     Crumbly
 */

require_once PATH_THIRD .'crumbly/upd.crumbly.php';
require_once PATH_THIRD .'crumbly/models/crumbly_model.php';

class Test_crumbly_upd extends Testee_unit_test_case {
  
  private $_model;
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
    
    Mock::generate('Crumbly_model', get_class($this) .'_mock_model');
    $this->_model   = $this->_get_mock('model');
    $this->_subject = new Crumbly_upd();
  }

  
}


/* End of file    : test.upd_crumbly.php */
/* File location  : third_party/crumbly/tests/test.upd_crumbly.php */
