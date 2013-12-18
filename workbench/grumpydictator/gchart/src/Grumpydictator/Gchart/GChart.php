<?php

namespace Grumpydictator\Gchart;

use Carbon\Carbon as Carbon;

class GChart {

  private $_cols        = [];
  private $_rows        = [];
  private $_data        = [];
  private $_certainty   = [];
  private $_interval    = [];
  private $_annotations = [];

  public function __construct() {

  }

  public function addColumn($name, $type, $role = null) {
    if (is_null($role)) {
      $role = count($this->_cols) == 0 ? 'domain' : 'data';
    }
    $this->_cols[] = array('name' => $name, 'type' => $type, 'role' => $role, 'id'   => \Str::slug($name));
  }
  public function addCell($row,$index,$value) {
    if(is_null($row)) {
      $row = count($this->_rows)-1 === -1 ? 0 : count($this->_rows)-1;
    }
    $this->_rows[$row][$index] = $value;
  }

  public function addRow() {
    $args          = func_get_args();
    $this->_rows[] = $args;
  }

  /**
   * Count starts at zero!
   * @param int $index
   */
  public function addCertainty($index) {
    $this->_certainty[] = $index;
  }

  public function addInterval($index) {
    $this->_interval[] = $index;
  }
  /**
   * Annotations are added to a column:
   * @param type $index
   */
  public function addAnnotation($index) {
    $this->_annotations[] = $index;
  }

  public function generate() {
    $this->_data = array();

    foreach ($this->_cols as $index => $column) {
      $this->_data['cols'][] = array(
          'id'    => $column['id'],
          'label' => $column['name'],
          'type'  => $column['type'],
          'p'     => array('role' => $column['role'])
      );
      if (in_array($index, $this->_annotations)) {
        // add an annotation column
        $this->_data['cols'][] = array(
            'type' => 'string',
            'p'    => array(
                'role' => 'annotation'
            )
        );
        $this->_data['cols'][] = array(
            'type' => 'string',
            'p'    => array(
                'role' => 'annotationText'
            )
        );
        // add an annotation text column
      }

      if (in_array($index, $this->_certainty)) {
        // add a certainty column:
        $this->_data['cols'][] = array(
            'type' => 'boolean',
            'p'    => array(
                'role' => 'certainty'
            )
        );
      }
      if (in_array($index, $this->_interval)) {
        $this->_data['cols'][] = array(
            'type' => 'number',
            'p'    => array(
                'role' => 'interval'
            )
        );

        $this->_data['cols'][] = array(
            'type' => 'number',
            'p'    => array(
                'role' => 'interval'
            )
        );
      }
    }

    $this->_data['rows'] = array();
    foreach ($this->_rows as $rowindex => $row) {
      foreach ($row as $cellindex => $value) {
        // catch date and properly format for JSON
        if (isset($this->_cols[$cellindex]['type']) && $this->_cols[$cellindex]['type'] == 'date') {
          $month                                                = intval($value->format('n')) - 1;
          $dateStr                                              = $value->format('Y, ' . $month . ', j');
          $this->_data['rows'][$rowindex]['c'][$cellindex]['v'] = 'Date(' . $dateStr . ')';
          unset($month, $dateStr);
        } else if(is_array($value)) {
          $this->_data['rows'][$rowindex]['c'][$cellindex]['v'] = $value['v'];
          $this->_data['rows'][$rowindex]['c'][$cellindex]['f'] = $value['f'];
        } else {
          $this->_data['rows'][$rowindex]['c'][$cellindex]['v'] = $value;
        }
      }
    }
  }

  public function getData() {
    return $this->_data;
  }

}