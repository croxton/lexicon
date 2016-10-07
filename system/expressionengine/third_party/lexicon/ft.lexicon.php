<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Lexicon Class
 *
 * @author    Mark Croxton <mcroxton@hallmark-design.oc.ku>
 * @copyright Copyright (c) 2016 Mark Croxton
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */

class Lexicon_ft extends EE_Fieldtype {

    public $info = array(
        'name'      => 'Lexicon',
        'version'   => '1.0.0',
    );

    public $has_array_data = TRUE;

    protected $fields = array(
        'lang_en'       => '',
        'lang_pl'       => '',
        'lang_ru'       => '',
        'lang_tr'       => '',
    );

    /**
     * Fieldtype Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->EE->lang->loadfile('lexicon');
    }

    /*
     * Register acceptable content types
     */
    public function accepts_content_type($name)
    {
        return ($name == 'channel' || $name == 'grid');
    }


    // --------------------------------------------------------------------

    /**
     * Include the JS and CSS files,
     * but only the first time
     */
    private function _include_css($field_type = NULL)
    {
        if ( ! $this->EE->session->cache(__CLASS__, 'css'))
        {
            $this->EE->cp->add_to_head('<style type="text/css">
                .lexicon label { display:block; }
                .lexicon input { width:100%; }
                .lexicon textarea { height:100px; }
                .lexicon td { vertical-align: top; }
                .matrix .lexicon input, .grid_cell .lexicon input { width:98.5%; }
                .lexicon_phrases { padding: 2px 10px 3px 10px ! important; }
            </style>');

            if ($field_type == 'element')
            {
                $this->EE->cp->add_to_head('<style type="text/css">
                    .lexicon input { padding:4px; border-color: #D1D5DE; }
                    .lexicon input:focus { padding: 4px; border-color: #000; border-width: 1px; }
                </style>');
            }

            $this->EE->session->set_cache(__CLASS__, 'css', TRUE);
        }
    }


    // --------------------------------------------------------------------


    /**
     * Display the settings form for each custom field
     * 
     * @param $data mixed The field settings
     * @return string Override the field custom settings with custom html
     * @access public
     */
    public function display_settings($data)
    {
       $rows = $this->_field_settings($data);

        foreach ($rows as $row)
        {
            ee()->table->add_row($row[0], $row[1]);
        }
    }

    /**
     * Display Grid Cell Settings
     */
    public function grid_display_settings($settings)
    {
        return $this->_field_settings($settings);
    }

    /**
     * Display Matrix Cell Settings
     */
    public function display_cell_settings($settings)
    {
        return $this->_field_settings($settings);
    }

    /**
     * Low Variables settings UI
     */
    public function display_var_settings($settings)
    {
        return $this->_field_settings($settings);
    }

    /**
     * Content Elements settings UI
     */
    function display_element_settings($settings)
    {
        return $this->_field_settings($settings);
    }


    /**
     * Fallback settings values
     * 
     * @param array
     * @return array 
     * @access private
     */ 
    private function _default_settings($data)
    {
        return array_merge(array(
            "style" => 'text_input',
        ), $data);
    }

    /**
     * Display field settings
     * 
     * @param mixed
     * @return string
     * @access private
     */
    private function _field_settings($data)
    {
        $data = $this->_default_settings($data);

        $row = array();
        
        $row[] = array(
            'Style',
            $this->_build_multi_radios($data['style'], 'lexicon_field_settings[style]')
        ); 

        return $row; 
    }

    /**
     * Builds a string of radio buttons
     *
     * @return string
     * @access private
     */
    private function _build_multi_radios($data, $name)
    {
        return form_radio($name, 'text_input', ($data == 'text_input') ) . NL
            . 'Text input' . NBS.NBS.NBS.NBS.NBS . NL
            . form_radio($name, 'textarea', ($data == 'textarea') ) . NL
            . 'Textarea';
    }


    // --------------------------------------------------------------------


    /**
     * Save Field Settings
     */
    function save_settings()
    {
         // remove empty values
        return $this->_sanitize_settings( $this->EE->input->post('lexicon_field_settings') );
    }

    /**
     * Matrix - save settings
     * 
     * @return array
     * @access public
     */
    public function save_cell_settings($settings)
    {
        return $this->_sanitize_settings($settings['lexicon_field_settings']);
    }

    /**
     * Low variables - save settings
     * 
     * @return array
     * @access public
     */
    public function save_var_settings()
    {
        return $this->_sanitize_settings($this->EE->input->post('lexicon_field_settings'));
    }

    /**
     * Sanitize settings data
     * 
     * @param array
     * @return array
     * @access private
     */
    private function _sanitize_settings($settings)
    {
        return array_filter($settings);
    }


    // --------------------------------------------------------------------


    /**
     * Display Field
     */
    public function display_field($field_data)
    {
        return $this->_display_form($this->field_name, $field_data);
    }

    /**
     * Display Grid Cell
     */
    public function grid_display_field($field_data)
    {
        return $this->_display_form($this->field_name, $field_data, TRUE);
    }

    /**
     * Display Matrix Cell
     */
    public function display_cell($cell_data)
    {
        return $this->_display_form($this->cell_name, $cell_data, TRUE);
    }

    /**
     * Display for Low Variables
     */
    public function display_var_field($field_data)
    {
        return $this->_display_form($this->field_name, $field_data);
    }

    /**
     * Display for Content Elements
     *
     * @param mixed element value
     * @return string html
     */
    function display_element($data) {
        return $this->_display_form($this->field_name, $data, FALSE, 'element');
    }

    /**
     * Generate the publish page UI
     */
    private function _display_form($name, $data, $is_cell=FALSE, $field_type='custom')
    {
        ee()->load->helper('form');

        $this->_include_css($field_type);

        // Set default values
        if (!is_array($data)) 
        {
            $data = $this->pre_process($data);
        }
        if (!is_array($data)) 
        {
            $data = array();
        }

        $data = array_merge($this->fields, $data);

        // fields markup
        $fields = '<table width="100%" class="lexicon">';

        // input style
        $style = isset($this->settings['style']) ? $this->settings['style'] : 'text_input';

        foreach(array_keys($this->fields) as $field)
        {
            $fields .= '<tr>';
            $fields .= '<td width="20%">';
            $fields .= form_label($this->EE->lang->line('lexicon_'.$field), $name.'_'.$field);
            $fields .= '</td>';
            $fields .= '<td class="lexicon_phrases">';
            $fields .= 
                $style == "text_input" 
                ? form_input($name.'['.$field.']', $data[$field], 'id="'.$name.'_'.$field.'" class="lexicon_'.$field.'"')
                : form_textarea($name.'['.$field.']', $data[$field], 'id="'.$name.'_'.$field.'" class="lexicon_'.$field.'"');
            $fields .= '</td>';
            $fields .= '</tr>';
        }

         $fields .= '</table>';

        return $fields;
    }


    // --------------------------------------------------------------------


    /**
     * Save Field
     */
    public function save($data)
    {
        if ($data == $this->fields)
        {
            return '';
        }
        else
        {
            return json_encode($data);
        }
    }

    /**
     * Save Cell
     */
    public function save_cell($data)
    {
        return $this->save($data);
    }

    /**
     * Save Low Variable
     */
    public function save_var_field($data)
    {
        return $this->save($data);
    }


    // --------------------------------------------------------------------


    /**
     * Unserialize the data
     */
    public function pre_process($data)
    {
        $data = htmlspecialchars_decode($data);
        $decoded = json_decode($data);
        $decoded = $decoded ? $decoded : @unserialize($data);
        return array_merge($this->fields, (array) $decoded);
    }


    // --------------------------------------------------------------------


    /**
     * Display Tag
     */
    public function replace_tag($data, $params=array(), $tagdata=FALSE)
    {
        $output = '';

        if ( ! $tagdata) // Single tag
        {   
            if ($lang = isset($params['lang']) ? $params['lang'] : FALSE)
            {
                $output = $data['lang_'.$lang];
            }
        }
        else // Tag pair
        {
            // Replace the variables
            $output = $this->EE->TMPL->parse_variables($tagdata, array($data));
        }

        return $output;
    }

    /**
     * Display Low Variables tag
     */
    public function display_var_tag($var_data, $tagparams, $tagdata)
    {
        $data = $this->pre_process($var_data);
        return $this->replace_tag($data, $tagparams, $tagdata);
    }

    /**
     * Replace for Content Elements
     *
     * @param data mixed
     * @param params tag params
     * @param taggdata html markup
     * @return string html
     */
    public function replace_element_tag($var_data, $tagparams = array(), $tagdata) 
    {
        return $this->replace_tag($var_data, $tagparams, $tagdata);
    }
}

/* End of file ft.lexicon.php */