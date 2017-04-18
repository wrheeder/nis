<?php

class HistoryLister extends CompleteLister {

    public $columns = array();

    /** Pointer to last added grid column */
    public $last_column;

    /** Default grid controller */
    public $default_controller = 'Controller_MVCGrid';

    /** jQuery-UI icons to show as sort icons in header */
    public $sort_icons = array(
        'icon-sort',
        'icon-up-dir',
        'icon-down-dir',
    );

    /** Should we show header line */
    public $show_header = true;

    /**
     * Grid buttons
     *
     * @see addButton()
     */
    public $buttonset = null;

    /** No records message. See setNoRecords() */
    protected $no_records_message = "No matching records found";

    function init() {
        parent::init();
    }

    function defaultTemplate() {
        return array('histTempl');
    }

    function renderRows() {
        // precache template chunks
        $this->precacheTemplate();

        // extend CompleteLister renderRows method
        parent::renderRows();

        // if we have at least one data row rendered, then remove not_found message
        if ($this->total_rows) {
            $this->template->del('not_found');
        } elseif ($this->no_records_message) {
            $this->template->del('header');
            $this->template->set('not_found_message', $this->no_records_message);
        }
    }

    function precacheTemplate() {
        // Extract template chunks from grid template
        // header
        $header = $this->template->cloneRegion('header');
        $header_col = $header->cloneRegion('col');
        $header_sort = $header_col->cloneRegion('sort');
        $header->del('cols');

        // data row and column
        $row = $this->row_t;
        $col = $row->cloneRegion('col');
        $row->setHTML('row_id', '{$id}');
        $row->trySetHTML('odd_even', '{$odd_even}');
        $row->del('cols');

        // totals row and column
        if ($t_row = $this->totals_t) {
            $t_col = $t_row->cloneRegion('col');
            $t_row->del('cols');
        }

        // Add requested columns to row templates
        foreach ($this->columns as $name => $column) {

            // header row
            $header_col
                    ->set('descr', $column['descr'])
                    ->trySet('type', $column['type']);

            // sorting
            // TODO: rewrite this (and move into Advanced)
            if (isset($column['sortable'])) {
                $s = $column['sortable'];
                $header_sort
                        ->trySet('order', $s[0])
                        ->trySet('sorticon', $this->sort_icons[$s[0]]);
                $header_col
                        ->trySet('sortid', $sel = $this->name . '_sort_' . $name)
                        ->setHTML('sort', $header_sort->render());

                $this->js('click', $this->js()->reload(array($this->name . '_sort' => $s[1])))
                        ->_selector('#' . $sel);
            } else {
                $header_col
                        ->del('sort')
                        ->tryDel('sortid')
                        ->tryDel('sort_del');
            }

            // add thparams for header columns
            if ($column['thparam']) {
                $header_col->trySetHTML('thparam', $column['thparam']);
            } else {
                $header_col->tryDel('thparam');
            }
            $header->appendHTML('cols', $header_col->render());

            // data row
            $col->del('content')
                    ->setHTML('content', '{$' . $name . '}')
                    ->setHTML('tdparam', '{tdparam_' . $name . '}style="white-space:nowrap"{/}');
            $row->appendHTML('cols', $col->render());

            // totals row
            if (isset($t_row) && isset($t_col)) {
                $t_col
                        ->del('content')
                        ->setHTML('content', '{$' . $name . '}')
                        ->trySetHTML('tdparam', '{tdparam_' . $name . '}style="white-space:nowrap"{/}');
                $t_row
                        ->appendHTML('cols', $t_col->render());
            }
        }

        // Generate templates from rendered strings
        // header
        $this->template->setHTML('header', $this->show_header ? $header->render() : '');

        // data row
        $this->row_t = $this->api
                ->add('GiTemplate')
                ->loadTemplateFromString($row->render());

        // totals row
        if (isset($t_row) && $this->totals_t) {
            $this->totals_t = $this->api
                    ->add('GiTemplate')
                    ->loadTemplateFromString($t_row->render());
        }
    }

    function addColumn($formatters, $name = null, $descr = null) {
        if ($name === null) {
            $name = $formatters;
            $formatters = 'text';
        }

        if ($descr === null) {
            $descr = ucwords(str_replace('_', ' ', $name));
        }
        if (is_array($descr)) {
            $descr['descr'] = $this->api->_($descr['descr']);
        } else {
            $descr = $this->api->_($descr);
        }

        $this->columns[$name] = array('type' => $formatters);

        if (is_array($descr)) {
            $this->columns[$name] = array_merge($this->columns[$name], $descr);
        } else {
            $this->columns[$name]['descr'] = $descr;
        }

        if ($this->columns[$name]['icon']) {
            if ($this->columns[$name]['icon'][0] != '<') {
                $this->columns[$name]['icon'] = '<i class="icon-' .
                        $this->columns[$name]['icon'] . '"></i>&nbsp;';
            } else
                throw $this->exception('obsolete way of using icon. Do not specify HTML code, but juts the icon');
        }


        $this->last_column = $name;

        if (!is_string($formatters) && is_callable($formatters)) {
            $this->columns[$name]['fx'] = $formatters;
            return $this;
        }

        // TODO call addFormatter instead!
        $subtypes = explode(',', $formatters);
        foreach ($subtypes as $subtype) {
            if (strpos($subtype, '/')) {

                // add-on functionality:
                // http://agiletoolkit.org/codepad/gui/grid#codepad_gui_grid_view_example_7_ex
                if (!$this->elements[$subtype . '_' . $name]) {
                    $addon = $this->api->normalizeClassName($subtype, 'Controller_Grid_Format');
                    $this->elements[$subtype . '_' . $name] = $this->add($addon);
                }

                $addon = $this->getElement($subtype . '_' . $name);
                $addon->initField($name, $descr);
                return $addon;
            } elseif (!$this->hasMethod($m = 'init_' . $subtype)) {
                if (!$this->hasMethod($m = 'format_' . $subtype)) {
                    // exception if formatter doesn't exist
                    throw $this->exception('No such formatter')
                            ->addMoreInfo('formater', $subtype);
                }
            } else {
                // execute formatter init_*
                $this->$m($name, $descr);
            }
        }

        return $this;
    }

    function format_text($field)
    {
    }
    function format_shorttext($field)
    {
    }
    function format_timestamp($field)
    {
    }
    
    function formatRow() {
        // execute CompleteLister row formating
        parent::formatRow();

        if (!$this->columns) {
            throw $this->exception('No columns defined for grid');
        }

        foreach ($this->columns as $field => $column) {
            $this->current_row[$field . '_original'] = @$this->current_row[$field];

            // if model field has listData structure, then get value instead of key
            if ($this->model && $f = $this->model->hasElement($field)) {
                if ($f->type() !== 'boolean' && $values = $f->listData()) {
                    $this->current_row[$field] = $values[$this->current_row[$field]];
                }
            }

            // process formatters
            $this->executeFormatters($field, $column, 'format_');

            // setting cell parameters (tdparam)
            $this->applyTDParams($field);
        }
    }

    /**
     * Format field value using appropriate formatters
     *
     * @param string $field field name
     * @param array $column column configuration
     * @param string $formatter_prefix prefix of formatter methods
     * @param boolean $silent don't throw exception if formatter not found
     *
     * @return void
     */
    function executeFormatters($field, $column, $formatter_prefix = 'format_', $silent = false) {
        $formatters = explode(',', $column['type']);
        foreach ($formatters as $formatter) {
            if (!$formatter) {
                continue;
            }

            if ($this->hasMethod($m = $formatter_prefix . $formatter)) {
                // formatter method is included in this class
                $this->$m($field, $column);
            } elseif (strpos($formatter, '/')) {
                // add-on support:
                // http://agiletoolkit.org/codepad/gui/grid#codepad_gui_grid_view_example_7_ex
                $this->getElement($formatter . '_' . $field)
                        ->formatField($field, $column);
            } else {
                if (!$silent) {
                    throw new BaseException("Grid does not know how to format type: " . $formatter);
                }
            }
        }
    }

    /**
     * Apply TD parameters in appropriate template
     *
     * You can pass row template too. That's useful to set up totals rows, for example.
     *
     * @param string $field Fieldname
     * @param SQLite $row_template Optional row template
     *
     * @return void
     */
    function applyTDParams($field, &$row_template = null) {
        
    }

}
