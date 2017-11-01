<?php

define('UPDATE_URL', 'http://uniquecoder.com/system_updates/Ultimate');
define('LATEST_VERSION', 'http://uniquecoder.com/system_updates/Ultimate/latest_version.php');
define('TEMP_FOLDER', FCPATH . 'uploads' . '/');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function btn_edit($uri)
{
    return anchor($uri, '<i class="fa fa-pencil-square-o"></i>', array('class' => "btn btn-primary btn-xs", 'title' => 'Edit', 'data-toggle' => 'tooltip', 'data-placement' => 'top'));
}

function btn_update()
{
    return "<button data-toggle='tooltip' title=" . lang('update') . " data-placement='top' type='submit'  class='btn btn-xs btn-success'><i class='fa fa-check'></i></button>";

}

function btn_cancel($uri)
{
    return anchor($uri, '<i class="fa fa-times"></i>', array('class' => "btn btn-danger btn-xs", 'title' => lang('cancel'), 'data-toggle' => 'tooltip', 'data-placement' => 'top'));
}

function btn_edit_disable($uri)
{
    return anchor($uri, '<span class="fa fa-pencil-square-o"></span>', array('class' => "btn btn-primary btn-xs disabled", 'title' => 'Edit', 'data-toggle' => 'tooltip', 'data-placement' => 'top'));
}

function btn_edit_modal($uri)
{
    return anchor($uri, '<span class="fa fa-pencil-square-o"></span>', array('class' => "btn btn-primary btn-xs", 'title' => 'Edit', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-toggle' => 'modal', 'data-target' => '#myModal'));
}


function btn_banned_modal($uri)
{
    return anchor($uri, '<span class="fa fa-close"></span>', array('class' => "btn btn-danger btn-xs", 'title' => 'Edit', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-toggle' => 'modal', 'data-target' => '#myModal'));
}

function btn_delete($uri)
{
    return anchor($uri, '<i class="fa fa-trash-o"></i>', array(
        'class' => "btn btn-danger btn-xs", 'title' => 'Delete', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => "return confirm('" . lang('delete_alert') . "');"
    ));
}

function btn_delete_disable($uri)
{
    return anchor($uri, '<i class="fa fa-trash-o"></i>', array(
        'class' => "btn btn-danger btn-xs disabled", 'title' => 'Delete', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => "return confirm('You are about to delete a record. This cannot be undone. Are you sure?');"
    ));
}

function btn_active($uri)
{
    return anchor($uri, '<i class="fa fa-check"></i>', array(
        'class' => "btn btn-success btn-xs", 'title' => 'Active', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => "return confirm('You are about to active new sesion . This cannot be undone. Are you sure?');"
    ));
}

function btn_print()
{
    return anchor('', '<span class="glyphicon glyphicon-print"></i></span>', array('class' => "btn btn-primary btn-xs", 'title' => 'Print', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => 'printDiv("printableArea")'));
}

function btn_atndc_print()
{
    return anchor('', '<span class="glyphicon glyphicon-print"></i></span>', array('class' => "btn btn-customs btn-xs", 'title' => 'Print', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => 'printDiv("printableArea")'));
}

function btn_pdf($uri)
{
    return anchor($uri, '<span <i class="fa fa-file-pdf-o"></i></span>', array('class' => "btn btn-primary btn-xs", 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'PDF'));
}

function btn_make_pdf($uri)
{
    return anchor($uri, '<span <i class="fa fa-file-pdf-o""></i></span>', array('class' => "btn btn-primary btn-xs", 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Generate&nbsp;PDF'));
}

function btn_excel($uri)
{
    return anchor($uri, '<span <i class="fa fa-file-excel-o"></i></span>', array('class' => "btn btn-primary btn-xs", 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Excel'));
}

function btn_view($uri)
{
    return anchor($uri, '<span class="fa fa-list-alt"></span>', array('class' => "btn btn-info btn-xs", 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'View'));
}

function btn_view_modal($uri)
{
    return anchor($uri, '<span class="fa fa-list-alt"></span>', array('class' => "btn btn-info btn-xs", 'title' => 'View', 'data-toggle' => 'modal', 'data-target' => '#myModal_lg'));
}

function btn_save($uri)
{
    return anchor($uri, '<span <i class="fa fa-plus-circle"></i></span>', array('class' => "btn btn-success btn-xs", 'title' => 'Save', 'data-toggle' => 'tooltip', 'data-placement' => 'top'));
}

function btn_add()
{
    return "<button type='submit' name='add' value='1' class='btn btn-info'>" . lang('add') . "</button>";
}

function btn_publish($uri)
{
    return anchor($uri, '<i class="fa fa-check"></i>', array(
        'class' => "btn btn-success btn-xs", 'title' => lang('click_to_published'), 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => "return confirm('You are about to unpublish this data. Are you sure?');"
    ));
}

function btn_unpublish($uri)
{
    return anchor($uri, '<i class="fa fa-times"></i>', array(
        'class' => "btn btn-danger btn-xs", 'title' => lang('click_to_unpublished'), 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => "return confirm('You are about to publish this data. Are you sure?');"
    ));
}

function btn_approve($uri)
{
    return anchor($uri, '<i class="fa fa-times"></i>', array(
        'class' => "btn btn-danger btn-xs", 'title' => 'Click to Reject', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => "return confirm('You are about to unpublish this data. Are you sure?');"
    ));
}

function btn_reject($uri)
{
    return anchor($uri, '<i class="fa fa-check"></i>', array(
        'class' => "btn btn-success btn-xs", 'title' => 'Click to Approve', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => "return confirm('You are about to publish this data. Are you sure?');"
    ));
}


function slug_it($str, $options = array())
{
    // Make sure string is in UTF-8 and strip invalid UTF-8 characters
    $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
    $defaults = array(
        'delimiter' => '-',
        'limit' => null,
        'lowercase' => true,
        'replacements' => array(
            '
            /\b(ѓ)\b/i' => 'gj',
            '/\b(ч)\b/i' => 'ch',
            '/\b(ш)\b/i' => 'sh',
            '/\b(љ)\b/i' => 'lj'
        ),
        'transliterate' => true
    );
    // Merge options
    $options = array_merge($defaults, $options);
    $char_map = array(
        // Latin
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'AE',
        'Ç' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ð' => 'D',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ő' => 'O',
        'Ø' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ű' => 'U',
        'Ý' => 'Y',
        'Þ' => 'TH',
        'ß' => 'ss',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'ae',
        'ç' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'd',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ő' => 'o',
        'ø' => 'o',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ű' => 'u',
        'ý' => 'y',
        'þ' => 'th',
        'ÿ' => 'y',
        // Latin symbols
        '©' => '(c)',
        // Greek
        'Α' => 'A',
        'Β' => 'B',
        'Γ' => 'G',
        'Δ' => 'D',
        'Ε' => 'E',
        'Ζ' => 'Z',
        'Η' => 'H',
        'Θ' => '8',
        'Ι' => 'I',
        'Κ' => 'K',
        'Λ' => 'L',
        'Μ' => 'M',
        'Ν' => 'N',
        'Ξ' => '3',
        'Ο' => 'O',
        'Π' => 'P',
        'Ρ' => 'R',
        'Σ' => 'S',
        'Τ' => 'T',
        'Υ' => 'Y',
        'Φ' => 'F',
        'Χ' => 'X',
        'Ψ' => 'PS',
        'Ω' => 'W',
        'Ά' => 'A',
        'Έ' => 'E',
        'Ί' => 'I',
        'Ό' => 'O',
        'Ύ' => 'Y',
        'Ή' => 'H',
        'Ώ' => 'W',
        'Ϊ' => 'I',
        'Ϋ' => 'Y',
        'α' => 'a',
        'β' => 'b',
        'γ' => 'g',
        'δ' => 'd',
        'ε' => 'e',
        'ζ' => 'z',
        'η' => 'h',
        'θ' => '8',
        'ι' => 'i',
        'κ' => 'k',
        'λ' => 'l',
        'μ' => 'm',
        'ν' => 'n',
        'ξ' => '3',
        'ο' => 'o',
        'π' => 'p',
        'ρ' => 'r',
        'σ' => 's',
        'τ' => 't',
        'υ' => 'y',
        'φ' => 'f',
        'χ' => 'x',
        'ψ' => 'ps',
        'ω' => 'w',
        'ά' => 'a',
        'έ' => 'e',
        'ί' => 'i',
        'ό' => 'o',
        'ύ' => 'y',
        'ή' => 'h',
        'ώ' => 'w',
        'ς' => 's',
        'ϊ' => 'i',
        'ΰ' => 'y',
        'ϋ' => 'y',
        'ΐ' => 'i',
        // Turkish
        'Ş' => 'S',
        'İ' => 'I',
        'Ç' => 'C',
        'Ü' => 'U',
        'Ö' => 'O',
        'Ğ' => 'G',
        'ş' => 's',
        'ı' => 'i',
        'ç' => 'c',
        'ü' => 'u',
        'ö' => 'o',
        'ğ' => 'g',
        // Russian
        'А' => 'A',
        'Б' => 'B',
        'В' => 'V',
        'Г' => 'G',
        'Д' => 'D',
        'Е' => 'E',
        'Ё' => 'Yo',
        'Ж' => 'Zh',
        'З' => 'Z',
        'И' => 'I',
        'Й' => 'J',
        'К' => 'K',
        'Л' => 'L',
        'М' => 'M',
        'Н' => 'N',
        'О' => 'O',
        'П' => 'P',
        'Р' => 'R',
        'С' => 'S',
        'Т' => 'T',
        'У' => 'U',
        'Ф' => 'F',
        'Х' => 'H',
        'Ц' => 'C',
        'Ч' => 'Ch',
        'Ш' => 'Sh',
        'Щ' => 'Sh',
        'Ъ' => '',
        'Ы' => 'Y',
        'Ь' => '',
        'Э' => 'E',
        'Ю' => 'Yu',
        'Я' => 'Ya',
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'yo',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'j',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'c',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'sh',
        'ъ' => '',
        'ы' => 'y',
        'ь' => '',
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya',
        // Ukrainian
        'Є' => 'Ye',
        'І' => 'I',
        'Ї' => 'Yi',
        'Ґ' => 'G',
        'є' => 'ye',
        'і' => 'i',
        'ї' => 'yi',
        'ґ' => 'g',
        // Czech
        'Č' => 'C',
        'Ď' => 'D',
        'Ě' => 'E',
        'Ň' => 'N',
        'Ř' => 'R',
        'Š' => 'S',
        'Ť' => 'T',
        'Ů' => 'U',
        'Ž' => 'Z',
        'č' => 'c',
        'ď' => 'd',
        'ě' => 'e',
        'ň' => 'n',
        'ř' => 'r',
        'š' => 's',
        'ť' => 't',
        'ů' => 'u',
        'ž' => 'z',
        // Polish
        'Ą' => 'A',
        'Ć' => 'C',
        'Ę' => 'e',
        'Ł' => 'L',
        'Ń' => 'N',
        'Ó' => 'o',
        'Ś' => 'S',
        'Ź' => 'Z',
        'Ż' => 'Z',
        'ą' => 'a',
        'ć' => 'c',
        'ę' => 'e',
        'ł' => 'l',
        'ń' => 'n',
        'ó' => 'o',
        'ś' => 's',
        'ź' => 'z',
        'ż' => 'z',
        // Latvian
        'Ā' => 'A',
        'Č' => 'C',
        'Ē' => 'E',
        'Ģ' => 'G',
        'Ī' => 'i',
        'Ķ' => 'k',
        'Ļ' => 'L',
        'Ņ' => 'N',
        'Š' => 'S',
        'Ū' => 'u',
        'Ž' => 'Z',
        'ā' => 'a',
        'č' => 'c',
        'ē' => 'e',
        'ģ' => 'g',
        'ī' => 'i',
        'ķ' => 'k',
        'ļ' => 'l',
        'ņ' => 'n',
        'š' => 's',
        'ū' => 'u',
        'ž' => 'z',
    );

    // Make custom replacements
    $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
    // Transliterate characters to ASCII
    if ($options['transliterate']) {
        $str = str_replace(array_keys($char_map), $char_map, $str);
    }
    // Replace non-alphanumeric characters with our delimiter
    $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
    // Remove duplicate delimiters
    $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
    // Truncate slug to max. characters
    $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
    // Remove delimiter from ends
    $str = trim($str, $options['delimiter']);
    return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}

function display_money($value, $currency = false, $decimal = 2)
{

    switch (config_item('money_format')) {
        case 1:
            $value = number_format($value, $decimal, '.', ',');
            break;
        case 2:
            $value = number_format($value, $decimal, ',', '.');
            break;
        case 3:
            $value = number_format($value, $decimal, '.', '');
            break;
        case 4:
            $value = number_format($value, $decimal, ',', '');
            break;
        default:
            $value = number_format($value, $decimal, '.', ',');
            break;
    }
    switch (config_item('currency_position')) {
        case 1:
            $return = $currency . ' ' . $value;
            break;
        case 2:
            $return = $value . ' ' . $currency;
            break;
        case false:
            $return = $value;
            break;
        default:
            $return = $currency . ' ' . $value;
            break;
    }

    return $return;
}

function custom_form_Fields($id, $edit_id = null, $col_sm = null)
{

    $CI = &get_instance();

    $all_field = $CI->db->where('form_id', $id)->get('tbl_custom_field')->result();

    $table = $CI->db->where('form_id', $id)->get('tbl_form')->row()->tbl_name;
    $filed_id = str_replace('tbl_', '', $table);
    $html = null;
    if (!empty($all_field)) {
        foreach ($all_field as $v_fileds) {
            $name = strtolower(preg_replace('/\s+/', '_', $v_fileds->field_label));

            if (!empty($edit_id)) {
                $showValue = $CI->db->where($filed_id . '_id', $edit_id)->get($table)->row($name);
            }

            if (!empty($showValue)) {
                $value = $showValue;
            } else {
                $val = json_decode($v_fileds->default_value);
                $value = $val[0];
            }
            if (!empty($col_sm)) {
                $col = 'col-lg-2';
            } else {
                $col = 'col-lg-3';
            }


            if ($v_fileds->required == 'on') {
                $required = 'required';
                $l_required = '<span class="required">*</span>';
            } else {
                $required = null;
                $l_required = null;
            }
            if (!empty($v_fileds->help_text)) {
                $help_text = '<i title="' . $v_fileds->help_text . '" class="fa fa-question-circle" data-toggle="tooltip" data-placement="top"></i>';
            } else {
                $help_text = null;
            }

            if ($v_fileds->field_type == 'text' && $v_fileds->status == 'active') {

                $html .= '<div class="form-group">
                <label class="' . $col . ' control-label">' . $v_fileds->field_label . ' ' . $l_required . '  ' . $help_text . '</label>
                <div class="col-lg-5">
                <input type="text" name="' . $name . '" class="form-control" ' . $required . ' value="' . $value . '">
                </div>
                </div>';
            }
            if ($v_fileds->field_type == 'textarea' && $v_fileds->status == 'active') {

                $html .= '<div class="form-group">
                <label class="' . $col . ' control-label">' . $v_fileds->field_label . ' ' . $l_required . '  ' . $help_text . '</label>
                <div class="col-lg-5">
                <textarea name="' . $name . '" class="form-control" ' . $required . '>' . $value . '</textarea>
                </div>
                </div>';
            }
            if ($v_fileds->field_type == 'dropdown' && $v_fileds->status == 'active') {

                $html .= '<div class="form-group">
                <label class="' . $col . ' control-label">' . $v_fileds->field_label . ' ' . $l_required . '  ' . $help_text . '</label>
                <div class="col-lg-5">
                <select name="' . $name . '" class="form-control select_box" style="width:100%" ' . $required . '>
                ' . dropdownField($v_fileds->default_value, $value) . '

                </select>
                </div>
                </div>';
            }
            if ($v_fileds->field_type == 'date' && $v_fileds->status == 'active') {

                $html .= '<div class="form-group">
                <label class="' . $col . ' control-label">' . $v_fileds->field_label . ' ' . $l_required . '  ' . $help_text . '</label>
                <div class="col-lg-5">
                <div class="input-group">
                <input type="text" name="' . $name . '" class="form-control datepicker" value="' . (!empty($value) ? $value : date('Y-m-d')) . '">
                <div class="input-group-addon">
                <a href="#"><i class="fa fa-calendar"></i></a>
                </div>
                </div>
                </div>
                </div>';
            }
            if ($v_fileds->field_type == 'checkbox' && $v_fileds->status == 'active') {
                $val = json_decode($v_fileds->default_value);
                $html .= '<div class="form-group">
                <label class="' . $col . ' control-label">' . $v_fileds->field_label . ' ' . $l_required . '  ' . $help_text . '</label>
                <div class="col-lg-5">
                <div class="checkbox c-checkbox">
                   <label class="needsclick">
                   <input type="checkbox" name="' . $name . '" ' . (!empty($value) && $value == 'on' ? 'checked' : $value = $val[0]) . ' ' . $required . '>
                   <span class="fa fa-check"></span>
                   </label>
                </div>
                </div>
                </div>';
            }
            if ($v_fileds->field_type == 'numeric' && $v_fileds->status == 'active') {

                $html .= '<div class="form-group">
                <label class="' . $col . ' control-label">' . $v_fileds->field_label . ' ' . $l_required . '  ' . $help_text . '</label>
                <div class="col-lg-5">
                <input type="number" name="' . $name . '" class="form-control" ' . $required . ' value="' . $value . '">
                </div>
                </div>';
            }
        }
    }
    return $html;

}


function dropdownField($value, $editValue = null)
{
    $html = null;
    foreach (json_decode($value) as $optionValue) {
        $html .= '<option value="' . $optionValue . '" ' . (!empty($editValue) && $editValue == $optionValue ? "selected" : null) . '>' . $optionValue . '</option>';
    }
    return $html;
}

function save_custom_field($id, $edit_id = null)
{
    $CI = &get_instance();
    $CI->load->model('admin_model');

    $all_field = $CI->db->where('form_id', $id)->get('tbl_custom_field')->result();
    $table = $CI->db->where('form_id', $id)->get('tbl_form')->row()->tbl_name;

    $filed_id = str_replace('tbl_', '', $table);
    $table_id = $filed_id . '_id';
    $custom = array();
    if (!empty($all_field)) {
        foreach ($all_field as $v_fileds) {
            $name = strtolower(preg_replace('/\s+/', '_', $v_fileds->field_label));
            $custom[$name] = $CI->input->post($name, true);
        }
        $CI->admin_model->_table_name = $table; //table name
        $CI->admin_model->_primary_key = $table_id;
        $CI->admin_model->save($custom, $edit_id);
    }

}

function custom_form_label($id, $show_id)
{

    $CI = &get_instance();
    $CI->load->model('admin_model');

    $all_field = $CI->db->where('form_id', $id)->get('tbl_custom_field')->result();

    $table = $CI->db->where('form_id', $id)->get('tbl_form')->row()->tbl_name;
    $filed_id = str_replace('tbl_', '', $table);
    $table_id = $filed_id . '_id';

    $showValue = array();
    if (!empty($all_field)) {
        foreach ($all_field as $v_fileds) {
            if ($v_fileds->show_on_details == 'on') {
                $name = strtolower(preg_replace('/\s+/', '_', $v_fileds->field_label));
                $showValue[$v_fileds->field_label] = $CI->db->where($table_id, $show_id)->get($table)->row($name);
            }
        }
    }
    return $showValue;
}

function url_encode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function url_decode($data)
{
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function can_action($menu_id, $action)
{
    $CI = &get_instance();
    $designations_id = $CI->session->userdata('designations_id');
    $where = array('designations_id' => $designations_id, $action => $menu_id);
    $user_type = $CI->session->userdata('user_type');
    if ($user_type == 1) {
        return true;
    } else {
        $can_do = $CI->db->where($where)->get('tbl_user_role')->row();
        if (!empty($can_do)) {
            return true;
        }
    }
}

function can_do($menu_id)
{
    $CI = &get_instance();
    $designations_id = $CI->session->userdata('designations_id');
    $user_type = $CI->session->userdata('user_type');
    if ($user_type == 1) {
        return true;
    } else {
        $can_do = $CI->db->where(array('designations_id' => $designations_id, 'menu_id' => $menu_id))->get('tbl_user_role')->result();
        if (!empty($can_do)) {
            return true;
        }
    }
}

function value_exists_in_array_by_key($array, $key, $val)
{
    foreach ($array as $item) {
        if (isset($item[$key]) && $item[$key] == $val) {
            return true;
        }
    }
    return false;
}

function clear_textarea_breaks($text)
{
    $_text = '';
    $_text = $text;
    $breaks = array(
        "<br />",
        "<br>",
        "<br/>",
        "'",
        "-",
        "^",
        "/",
        "%",
    );
    $_text = str_ireplace($breaks, "", $_text);
    $_text = trim($_text);
    return $_text;
}

function set_mysql_timezone($timezone)
{
    $offset = timezone_offset_get(new DateTimeZone($timezone), new DateTime());
    $sign = ($offset > 0) ? '+' : '-';
    $offset = gmdate('H:i', abs($offset));
    $zone = $sign . $offset;
    $CI = &get_instance();
    $CI->db->query("SET time_zone='$zone'");
    return true;
}

function access_denied($permission = '', $module = null)
{
    set_message('danger', lang('access_denied'));
    $activity = array(
        'user' => $this->session->userdata('user_id'),
        'module' => $module,
        'module_field_id' => $this->session->userdata('user_id'),
        'activity' => 'access_denied',
        'value1' => 'Tried to access page where don\'t have permission [' . $permission . ']',
    );

    $CI = &get_instance();
    $CI->load->model('account_model');
    $CI->admin_model->_table_name = 'tbl_activities';
    $CI->admin_model->_primary_key = 'activities_id';
    $CI->admin_model->save($activity);

    if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
        redirect($_SERVER['HTTP_REFERER']);
    } else {
        redirect('404');
    }
}

function check_installation()
{
    if (is_dir(FCPATH . 'install')) {
        echo '<h3>Delete the install folder</h3>';
        die;
    }
}

/**
 * Function that will replace the dropbox link size for the images
 * This function is used to preview dropbox image attachments
 * @param  string $url
 * @param  string $bounding_box
 * @return string
 */
function optimize_dropbox_thumbnail($url, $bounding_box = '800')
{
    $url = str_replace('bounding_box=75', 'bounding_box=' . $bounding_box, $url);

    return $url;
}

function protected_file_url_by_path($path)
{
    return str_replace(FCPATH, '', $path);
}

function _mime_content_type($filename)
{
    if (function_exists('mime_content_type'))
        return mime_content_type($filename);
    else if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $filename);
        return $mimetype;
    } else
        return get_mime_by_extension($filename);
}

/**
 * Add user notifications
 * @param array $values array of values [description,fromuserid,touserid,fromcompany,isread]
 */
function add_notification($values)
{
    $CI =& get_instance();
    foreach ($values as $key => $value) {
        $data[$key] = $value;
    }
    if (!empty($data['from_user_id'])) {
        $user_id = $CI->session->userdata('user_id');
        $data['from_user_id'] = $user_id;
        $data['name'] = $CI->db->where('user_id', $user_id)->get('tbl_account_details')->row()->fullname;
    }
    // Prevent sending notification to non active users.
    if (isset($data['to_user_id']) && $data['to_user_id'] != 0) {
        $CI->db->where('user_id', $data['to_user_id']);
        $user = $CI->db->get('tbl_users')->row();
        if (!$user) {
            return false;
        }
        if ($user) {
            if ($user->activated == 0) {
                return false;
            }
        }
    }
    $data['date'] = date('Y-m-d H:i:s');
    $CI->db->insert('tbl_notifications', $data);
    return true;
}

function staffImage($user_id = null)
{
    $CI =& get_instance();
    if (empty($user_id)) {
        $user_id = $CI->session->userdata('user_id');
    }
    $userInfo = $CI->db->where('user_id', $user_id)->get('tbl_account_details')->row();
    if (!empty($userInfo)) {
        return $userInfo->avatar;
    } else {
        return 'assets/img/user/default_avatar.jpg';
    }
}

function fullname($user_id = null)
{
    $CI =& get_instance();
    if (empty($user_id)) {
        $user_id = $CI->session->userdata('user_id');
    }
    $userInfo = $CI->db->where('user_id', $user_id)->get('tbl_account_details')->row();
    if (!empty($userInfo)) {
        return $userInfo->fullname;
    }
}

/**
 *
 * @param  $array - data
 * @param  $key - value you want to pluck from array
 *
 * @return plucked array only with key data
 */
if (!function_exists('array_pluck')) {
    function array_pluck($array, $key)
    {
        return array_map(function ($v) use ($key) {
            return is_object($v) ? $v->$key : $v[$key];
        }, $array);
    }
}

/**
 * Short Time ago function
 * @param  datetime $time_ago
 * @return mixed
 */
function time_ago($time_ago)
{
    $time_ago = strtotime($time_ago);
    $cur_time = time();
    $time_elapsed = $cur_time - $time_ago;
    $seconds = $time_elapsed;
    $minutes = round($time_elapsed / 60);
    $hours = round($time_elapsed / 3600);
    $days = round($time_elapsed / 86400);
    $weeks = round($time_elapsed / 604800);
    $months = round($time_elapsed / 2600640);
    $years = round($time_elapsed / 31207680);
    // Seconds
    if ($seconds <= 60) {
        return lang('time_ago_just_now');
    } //Minutes
    elseif ($minutes <= 60) {
        if ($minutes == 1) {
            return lang('time_ago_minute');
        } else {
            return lang('time_ago_minutes', $minutes);
        }
    } //Hours
    elseif ($hours <= 24) {
        if ($hours == 1) {
            return lang('time_ago_hour');
        } else {
            return lang('time_ago_hours', $hours);
        }
    } //Days
    elseif ($days <= 7) {
        if ($days == 1) {
            return lang('time_ago_yesterday');
        } else {
            return lang('time_ago_days', $days);
        }
    } //Weeks
    elseif ($weeks <= 4.3) {
        if ($weeks == 1) {
            return lang('time_ago_week');
        } else {
            return lang('time_ago_weeks', $weeks);
        }
    } //Months
    elseif ($months <= 12) {
        if ($months == 1) {
            return lang('time_ago_month');
        } else {
            return lang('time_ago_months', $months);
        }
    } //Years
    else {
        if ($years == 1) {
            return lang('time_ago_year');
        } else {
            return lang('time_ago_years', $years);
        }
    }
}

function daysleft($time)
{
    $to_date = strtotime($time); //Future date.
    $cur_date = strtotime(date('Y-m-d'));
    $timeleft = $to_date - $cur_date;
    $daysleft = round((($timeleft / 24) / 60) / 60);
    if ($daysleft == 1) {
        $result = $daysleft . ' ' . lang('day') . ' ' . lang('left');
    } else if ($daysleft > 1) {
        $result = $daysleft . ' ' . lang('days') . ' ' . lang('left');
    } else if ($daysleft == -1) {
        $result = $daysleft . ' ' . lang('day') . ' ' . lang('gone');
    } else if ($daysleft > -1) {
        $result = $daysleft . ' ' . lang('days') . ' ' . lang('gone');
    }
    return $result;

}