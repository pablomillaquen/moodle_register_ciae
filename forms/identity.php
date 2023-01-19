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

 class identity_form extends moodleform
 {
    //Add elements to form
    public function definition()
    {
        global $CFG, $DB;

        $user_info_field = $DB->get_records('user_info_field', null, null, 'id, shortname, name, param1');

        $niveles_arr = array();
        $identidad_arr = array();

        foreach ($user_info_field as $field){
            switch($field->shortname){
                case "niveles":
                    $n_arr = explode(PHP_EOL, $field->param1);
                    foreach($n_arr as $n){
                        $niveles_arr[] = $n;
                    }
                    break;
                case "identidad_terr":
                    $i_arr = explode(PHP_EOL, $field->param1);
                    foreach($i_arr as $i){
                        $identidad_arr[$i] = $i;
                    }
                    break;
                default:
                    break;    
            }
        }
        
        $mform = $this->_form;

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'repo', 0);
        $mform->setType('repo', PARAM_INT);

        $select = $mform->addElement('select', 'niveles', get_string('niveles', 'local_registerciae'), $niveles_arr);
        $select->setMultiple(true);

        $mform->addElement('select', 'identidad_terr', get_string('identidad_terr', 'local_registerciae'), $identidad_arr);

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