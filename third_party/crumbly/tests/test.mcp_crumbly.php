<?php

/**
 * Crumbly module control panel tests.
 *
 * @author      Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright   Experience Internet
 * @package     Crumbly
 */

require_once PATH_THIRD .'crumbly/mcp.crumbly.php';
require_once PATH_THIRD .'crumbly/models/crumbly_model.php';

class Test_crumbly_cp extends Testee_unit_test_case {
  
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
    $this->_model             = $this->_get_mock('model');
    $this->EE->crumbly_model  = $this->_model;
    
    $this->_subject = new Crumbly_mcp();
  }


  public function test__save_glossary__success()
  {
    $input = array(
      array('glossary_term' => 'hotels', 'glossary_definition' => 'Accommodation'),
      array('glossary_term' => 'staff', 'glossary_definition' => 'Corporate Minions')
    );

    $this->EE->input->setReturnValue('post', $input, array('glossary'));

    $this->_model->expectOnce('delete_all_crumbly_glossary_terms');
    $this->_model->expectCallCount('save_crumbly_glossary_term', count($input));
    $this->_model->setReturnValue('save_crumbly_glossary_term', TRUE);

    for ($count = 0, $length = count($input); $count < $length; $count++)
    {
      $term = new Crumbly_glossary_term($input[$count]);
      $this->_model->expectAt($count, 'save_crumbly_glossary_term', array($term));
    }

    $message = 'message';
    $this->EE->lang->setReturnValue('line', $message, array('msg_glossary_terms_saved'));
    $this->EE->session->expectOnce('set_flashdata', array('message_success', $message));
    $this->EE->functions->expectOnce('redirect', array(new PatternExpectation('/method=glossary/')));
  
    $this->_subject->save_glossary();
  }


  public function test__save_glossary__unable_to_save()
  {
    $input = array(
      array('glossary_term' => 'hotels', 'glossary_definition' => 'Accommodation'),
      array('glossary_term' => 'staff', 'glossary_definition' => 'Corporate Minions')
    );

    $this->EE->input->setReturnValue('post', $input, array('glossary'));

    $this->_model->expectOnce('delete_all_crumbly_glossary_terms');
    $this->_model->expectCallCount('save_crumbly_glossary_term', count($input));
    $this->_model->setReturnValue('save_crumbly_glossary_term', FALSE);

    $message = 'message';
    $this->EE->lang->setReturnValue('line', $message, array('msg_glossary_terms_not_saved'));
    $this->EE->session->expectOnce('set_flashdata', array('message_failure', $message));
    $this->EE->functions->expectOnce('redirect', array(new PatternExpectation('/method=glossary/')));
  
    $this->_subject->save_glossary();
  }


  public function test__save_glossary__no_input()
  {
    $input = FALSE;
    $this->EE->input->setReturnValue('post', $input, array('glossary'));

    $this->_model->expectOnce('delete_all_crumbly_glossary_terms');
    $this->_model->expectNever('save_crumbly_glossary_term');
    
    $message = 'message';
    $this->EE->lang->setReturnValue('line', $message, array('msg_glossary_terms_saved'));
    $this->EE->session->expectOnce('set_flashdata', array('message_success', $message));
    $this->EE->functions->expectOnce('redirect', array(new PatternExpectation('/method=glossary/')));
  
    $this->_subject->save_glossary();
  }


  public function test__save_templates__success()
  {
    $input = array(
      array('template_id' => '10', 'label' => 'Lovely Template'),
      array('template_id' => '20', 'label' => 'Another Lovely Template')
    );

    $this->EE->input->setReturnValue('post', $input, array('templates'));

    $this->_model->expectOnce('delete_all_crumbly_templates');
    $this->_model->expectCallCount('save_crumbly_template', count($input));
    $this->_model->setReturnValue('save_crumbly_template', TRUE);

    for ($count = 0, $length = count($input); $count < $length; $count++)
    {
      $template = new Crumbly_template($input[$count]);
      $this->_model->expectAt($count, 'save_crumbly_template', array($template));
    }

    $message = 'message';
    $this->EE->lang->setReturnValue('line', $message, array('msg_templates_saved'));
    $this->EE->session->expectOnce('set_flashdata', array('message_success', $message));
    $this->EE->functions->expectOnce('redirect', array(new PatternExpectation('/method=templates/')));

    $this->_subject->save_templates();
  }
  

  public function test__save_templates__unable_to_save()
  {
    $input = array(
      array('template_id' => '10', 'label' => 'Lovely Template'),
      array('template_id' => '20', 'label' => 'Another Lovely Template')
    );

    $this->EE->input->setReturnValue('post', $input, array('templates'));

    $this->_model->expectOnce('delete_all_crumbly_templates');
    $this->_model->expectCallCount('save_crumbly_template', count($input));
    $this->_model->setReturnValue('save_crumbly_template', FALSE);

    $message = 'message';
    $this->EE->lang->setReturnValue('line', $message, array('msg_templates_not_saved'));
    $this->EE->session->expectOnce('set_flashdata', array('message_failure', $message));
    $this->EE->functions->expectOnce('redirect', array(new PatternExpectation('/method=templates/')));
  
    $this->_subject->save_templates();
  }


  public function test__save_templates__no_input()
  {
    $input = FALSE;
    $this->EE->input->setReturnValue('post', $input, array('templates'));

    $this->_model->expectOnce('delete_all_crumbly_templates');
    $this->_model->expectNever('save_crumbly_template');
    
    $message = 'message';
    $this->EE->lang->setReturnValue('line', $message, array('msg_templates_saved'));
    $this->EE->session->expectOnce('set_flashdata', array('message_success', $message));
    $this->EE->functions->expectOnce('redirect', array(new PatternExpectation('/method=templates/')));
  
    $this->_subject->save_templates();
  }


  public function test__save_template_groups__success()
  {
    $input = array(
      array('group_id' => '10', 'label' => 'Lovely Group'),
      array('group_id' => '20', 'label' => 'Another Lovely Group')
    );

    $this->EE->input->setReturnValue('post', $input, array('template_groups'));

    $this->_model->expectOnce('delete_all_crumbly_template_groups');
    $this->_model->expectCallCount('save_crumbly_template_group', count($input));
    $this->_model->setReturnValue('save_crumbly_template_group', TRUE);

    for ($count = 0, $length = count($input); $count < $length; $count++)
    {
      $group = new Crumbly_template_group($input[$count]);
      $this->_model->expectAt($count, 'save_crumbly_template_group', array($group));
    }

    $message = 'message';
    $this->EE->lang->setReturnValue('line', $message, array('msg_template_groups_saved'));
    $this->EE->session->expectOnce('set_flashdata', array('message_success', $message));
    $this->EE->functions->expectOnce('redirect', array(new PatternExpectation('/method=template_groups$/')));

    $this->_subject->save_template_groups();
  }
  

  public function test__save_template_groups__unable_to_save()
  {
    $input = array(
      array('group_id' => '10', 'label' => 'Lovely Group'),
      array('group_id' => '20', 'label' => 'Another Lovely Group')
    );

    $this->EE->input->setReturnValue('post', $input, array('template_groups'));

    $this->_model->expectOnce('delete_all_crumbly_template_groups');
    $this->_model->expectCallCount('save_crumbly_template_group', count($input));
    $this->_model->setReturnValue('save_crumbly_template_group', FALSE);

    $message = 'message';
    $this->EE->lang->setReturnValue('line', $message, array('msg_template_groups_not_saved'));
    $this->EE->session->expectOnce('set_flashdata', array('message_failure', $message));
    $this->EE->functions->expectOnce('redirect', array(new PatternExpectation('/method=template_groups$/')));

    $this->_subject->save_template_groups();
  }
  

  public function test__save_template_groups__no_input()
  {
    $input = FALSE;
    $this->EE->input->setReturnValue('post', $input, array('template_groups'));

    $this->_model->expectOnce('delete_all_crumbly_template_groups');
    $this->_model->expectNever('save_crumbly_template_group');

    $message = 'message';
    $this->EE->lang->setReturnValue('line', $message, array('msg_template_groups_saved'));
    $this->EE->session->expectOnce('set_flashdata', array('message_success', $message));
    $this->EE->functions->expectOnce('redirect', array(new PatternExpectation('/method=template_groups$/')));

    $this->_subject->save_template_groups();
  }
  

}


/* End of file    : test.mcp_crumbly.php */
/* File location  : third_party/crumbly/tests/test.mcp_crumbly.php */
