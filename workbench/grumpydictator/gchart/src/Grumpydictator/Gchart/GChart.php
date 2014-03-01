<?php

namespace Grumpydictator\Gchart;

/**
 * Class GChart
 *
 * @package Grumpydictator\Gchart
 */
class GChart
{

    private $_cols = [];
    private $_rows = [];
    private $_data = [];
    private $_certainty = [];
    private $_interval = [];
    private $_annotations = [];

    /**
     * Construct.
     */
    public function __construct()
    {

    }

    /**
     * Add a column to the chart.
     *
     * @param      $name
     * @param      $type
     * @param null $role
     */
    public function addColumn($name, $type, $role = null)
    {
        if (is_null($role)) {
            $role = count($this->_cols) == 0 ? 'domain' : 'data';
        }
        $this->_cols[] = ['name' => $name, 'type' => $type, 'role' => $role,
                          'id'   => \Str::slug($name)];
        return (count($this->_cols) -1);
    }

    /**
     * Add a cell value to the chart data.
     *
     * @param $row
     * @param $index
     * @param $value
     */
    public function addCell($row, $index, $value)
    {
        if (is_null($row)) {
            $row = count($this->_rows) - 1 === -1 ? 0 : count($this->_rows) - 1;
        }
        $this->_rows[$row][$index] = $value;
    }

    /**
     * Add a row to the chart data.
     */
    public function addRow()
    {
        $args = func_get_args();
        $this->_rows[] = $args;
    }
    public function addRowArray($array) {
        $this->_rows[] = $array;
    }

    /**
     * Add certainty to a column. Count starts at zero!
     *
     * @param int $index
     */
    public function addCertainty($index)
    {
        $this->_certainty[] = $index;
    }

    /**
     * Add interval to a column. Count starts at zero!
     *
     * @param int $index
     */

    public function addInterval($index)
    {
        $this->_interval[$index][] = true;
    }

    /**
     * Annotations are added to a column:
     *
     * @param int $index
     */
    public function addAnnotation($index)
    {
        $this->_annotations[] = $index;
    }

    /**
     * Generate the actual chart JSON.
     */
    public function generate()
    {
        $this->_data = [];

        foreach ($this->_cols as $index => $column) {
            $this->_data['cols'][] = ['id'    => $column['id'],
                                      'label' => $column['name'],
                                      'type'  => $column['type'],
                                      'p'     => ['role' => $column['role']]];
            if (in_array($index, $this->_annotations)) {
                // add an annotation column
                $this->_data['cols'][] = ['type' => 'string',
                                          'p'    => ['role' => 'annotation']];
                $this->_data['cols'][] = ['type' => 'string',
                                          'p'    => ['role' => 'annotationText']];
                // add an annotation text column
            }
            // add the intervals:

            if (in_array($index, $this->_certainty)) {
                // add a certainty column:
                $this->_data['cols'][] = ['type' => 'boolean',
                                          'p'    => ['role' => 'certainty']];
            }

            if (isset($this->_interval[$index])) {



                // add intervals for each one found.
                foreach($this->_interval[$index] as $nr => $bool) {
                    $this->_data['cols'][] = ['type' => 'number',
                                              'id' => 'i'.$index.$nr,
                                              'p'    => ['role' => 'interval']];
                }

            }

        }



        $this->_data['rows'] = [];
        foreach ($this->_rows as $rowindex => $row) {
            foreach ($row as $cellindex => $value) {
                // catch date and properly format for JSON
                if (isset($this->_cols[$cellindex]['type'])
                    && $this->_cols[$cellindex]['type'] == 'date'
                ) {
                    $month = intval($value->format('n')) - 1;
                    $dateStr = $value->format('Y, ' . $month . ', j');
                    $this->_data['rows'][$rowindex]['c'][$cellindex]['v']
                        = 'Date(' . $dateStr . ')';
                    unset($month, $dateStr);
                } else {
                    if (is_array($value)) {
                        $this->_data['rows'][$rowindex]['c'][$cellindex]['v']
                            = $value['v'];
                        $this->_data['rows'][$rowindex]['c'][$cellindex]['f']
                            = $value['f'];
                    } else {
                        $this->_data['rows'][$rowindex]['c'][$cellindex]['v']
                            = $value;
                    }
                }
            }
        }
    }

    /**
     * Returns the chart data.
     * @return array
     */
    public function getData()
    {
//        echo '<pre>';
//        var_dump($this->_data);
//        echo '</pre>';exit;
        return $this->_data;
    }

}