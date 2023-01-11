<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * List of all resources in course
 *
 * @package    local_registerform
 * @copyright  2023 Pablo Millaquén
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require_once("$CFG->libdir/formslib.php");

 class general_form extends moodleform
 {
    //Add elements to form
    public function definition()
    {
        global $CFG, $DB;

        $user_info_field = $DB->get_records('user_info_field', null, null, 'id, shortname, name, param1');

        $sexo_arr = array();
        $comuna_arr = array();
        $region_arr = array();
        $tipousuario_arr = array();

        foreach ($user_info_field as $field){
            switch($field->shortname){
                case "sexo":
                    $s_arr = explode(PHP_EOL, $field->param1);
                    foreach($s_arr as $s){
                        $sexo_arr[$s] = $s;
                    }
                    break;
                case "comuna":
                    $c_arr = explode(PHP_EOL, $field->param1);
                    foreach($c_arr as $c){
                        $comuna_arr[$c] = $c;
                    }
                    break;
                case "region":
                    $r_arr = explode(PHP_EOL, $field->param1);
                    foreach($r_arr as $r){
                        $region_arr[$r] = $r;
                    }
                    break;
                case "tipousuario":
                    $t_arr = explode(PHP_EOL, $field->param1);
                    foreach($t_arr as $t){
                        $tipousuario_arr[$t] = $t;
                    }
                    break;
                default:
                    break;    
            }
        }
        
        $mform = $this->_form;
    

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $attributes=array('size'=>'20');
        $mform->addElement('text', 'username', get_string('username'), $attributes);
        $mform->setType('username', PARAM_TEXT);
        
        $attributes=array('size'=>'20');
        $mform->addElement('text', 'firstname', get_string('firstname', 'local_registerciae'), $attributes);
        $mform->setType('lastname', PARAM_TEXT);

        $attributes=array('size'=>'20');
        $mform->addElement('text', 'lastname', get_string('lastname', 'local_registerciae'), $attributes);
        $mform->setType('lastname', PARAM_TEXT);

        $choices = array(0 => get_string('emaildisplayno'), 1 => get_string('emaildisplayyes'), 2 => get_string('emaildisplaycourse'));
        $mform->addElement('select', 'maildisplay', get_string('emaildisplay'), $choices);
        $mform->setDefault('maildisplay', core_user::get_property_default('maildisplay'));
        $mform->addHelpButton('maildisplay', 'emaildisplay');

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="25" ');
        $mform->setType('email', PARAM_NOTAGS);
        $mform->addRule('email', get_string('missingemail'), 'required', null, 'server');

        $mform->addElement('select', 'sexo', get_string('gender', 'local_registerciae'), $sexo_arr);

        $mform->addElement('select', 'comuna', get_string('commune', 'local_registerciae'), $comuna_arr);

        $mform->addElement('select', 'region', get_string('region', 'local_registerciae'), $region_arr);

        $attributes=array('size'=>'20');
        $mform->addElement('password', 'password', get_string('password'), $attributes);
        $mform->setType('password', PARAM_TEXT);

        $mform->addElement('select', 'tipousuario', get_string('usertype', 'local_registerciae'), $tipousuario_arr);

        $buttonArray = array();
        $buttonArray[] = $mform->createElement('submit', 'Guardar', get_string('save', 'local_registerciae'));
        $buttonArray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonArray, 'buttonar', '', '', false);

    }

    public function validation($data, $files)
    {
        return array();
    }
 }