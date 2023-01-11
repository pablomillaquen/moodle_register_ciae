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

 class studies_form extends moodleform
 {
    //Add elements to form
    public function definition()
    {
        global $CFG, $DB;

        $user_info_field = $DB->get_records('user_info_field', null, null, 'id, shortname, name, param1');

        $nivel_escolaridad_arr = array();
        $diplomados_arr = array();
        $areas_arr = array();

        foreach ($user_info_field as $field){
            switch($field->shortname){
                case "nivel_escolaridad":
                    $e_arr = explode(PHP_EOL, $field->param1);
                    foreach($e_arr as $e){
                        $nivel_escolaridad_arr[$e] = $e;
                    }
                    break;
                case "diplomados":
                    $d_arr = explode(PHP_EOL, $field->param1);
                    foreach($d_arr as $d){
                        $diplomados_arr[$d] = $d;
                    }
                    break;
                case "areas":
                    $a_arr = explode(PHP_EOL, $field->param1);
                    foreach($a_arr as $a){
                        $areas_arr[$a] = $a;
                    }
                    break;
                default:
                    break;    
            }
        }
        
        $mform = $this->_form;
    

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('select', 'nivel_escolaridad', get_string('nivel_escolaridad', 'local_registerciae'), $nivel_escolaridad_arr);

        $mform->addElement('select', 'diplomados', get_string('diplomados', 'local_registerciae'), $diplomados_arr);

        $mform->addElement('textarea', 'cursos', get_string('cursos', 'local_registerciae'), 'wrap="virtual" rows="6" cols="50"');
        $mform->addHelpButton('cursos', 'cursos', 'local_registerciae');

        $mform->addElement('select', 'areas', get_string('areas', 'local_registerciae'), $areas_arr);

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