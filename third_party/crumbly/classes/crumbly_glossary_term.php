<?php

/**
 * Crumbly Glossary term.
 *
 * @author      Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright   Experience Internet
 * @package     Crumbly
 */

require_once PATH_THIRD .'crumbly/helpers/EI_number_helper.php';

class Crumbly_glossary_term {

  private $_glossary_definition;
  private $_glossary_term;


  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */
  
  /**
   * Constructor.
   *
   * @access  public
   * @param array   $props    Instance properties.
   * @return  void
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
   * Returns glossary definition.
   *
   * @access  public
   * @return  string
   */
  public function get_glossary_definition()
  {
    return $this->_glossary_definition;
  }


  /**
   * Returns glossary term.
   *
   * @access  public
   * @return  string
   */
  public function get_glossary_term()
  {
    return $this->_glossary_term;
  }


  /**
   * Resets the instance properties.
   *
   * @access  public
   * @return  Crumbly_glossary_term
   */
  public function reset()
  {
    $this->_glossary_definition = '';
    $this->_glossary_term   = '';

    return $this;
  }
  
  
  /**
   * Sets glossary definition.
   *
   * @access  public
   * @param string    $glossary_definition    The glossary definition.
   * @return  string
   */
  public function set_glossary_definition($glossary_definition)
  {
    if (is_string($glossary_definition))
    {
      $this->_glossary_definition = $glossary_definition;
    }

    return $this->get_glossary_definition();
  }


  /**
   * Sets glossary term.
   *
   * @access  public
   * @param string    $glossary_term    The glossary term.
   * @return  string
   */
  public function set_glossary_term($glossary_term)
  {
    if (is_string($glossary_term))
    {
      $this->_glossary_term = $glossary_term;
    }

    return $this->get_glossary_term();
  }
  
  
  /**
   * Returns the instance properties as an associative array.
   *
   * @access  public
   * @return  array
   */
  public function to_array()
  {
    return array(
      'glossary_definition' => $this->get_glossary_definition(),
      'glossary_term'     => $this->get_glossary_term()
    );
  }
  
}

/* End of file    : crumbly_glossary_term.php */
/* File location  : third_party/crumbly/classes/crumbly_glossary_term.php */
