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
 * Displays information about all the assignment modules in the requested course
 *
 * @package   local_registerciae
 * @copyright 2023 Pablo Millaquén {@link http://mltecnologias.cl}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../config.php';
global $USER, $DB, $CFG;

$id = optional_param('id', 0, PARAM_INT);
$repo = optional_param('repo', 0, PARAM_INT);
$lang = current_language();


$PAGE->set_url('/local/registerciae/identity.php');
$PAGE->set_context(context_system::instance());
$contextid = $PAGE->context->id;
require_login();

require_once("forms/identity.php");

$strpagetitle = get_string('registerciae', 'local_registerciae');
$strpageheading = get_string('registerciae', 'local_registerciae');
$PAGE->set_title($strpagetitle." ".$obj->name);
$PAGE->set_heading($strpageheading." ".$obj->name);

$mform = new identity_form(); 
$toform = [];

$user_info_field = $DB->get_records('user_info_field', null, null, 'id, shortname, name, param1');

if($mform->is_cancelled()){
    redirect("/local/registerciae/index.php", '', 10);
}elseif($fromform = $mform->get_data()){
    if($fromform->id != 0){        
        //Update data
        $user = $DB->get_record('user', ['id'=>$fromform->id]);
     
        foreach ($user_info_field as $field){
            switch($field->shortname){
                case "niveles":
                    $nivel_field = $DB->get_record('user_info_data', ['userid'=>$fromform->id, 'fieldid'=>$field->id]);
                    if($nivel_field){
                        $nivel_field->data = $fromform->niveles;
                        $DB->update_record('user_info_data', $nivel_field);
                    }else{
                        $newfield = new stdClass();
                        $newfield->userid = $fromform->id;
                        $newfield->fieldid = $field->id;
                        $newfield->data = $fromform->niveles;
                        $newfield->dataformat = 0;
                        $user_field = $DB->insert_record('user_info_data', $newfield, true, false);
                    }
                    break;
                case "identidad_terr":
                    $identidad_field = $DB->get_record('user_info_data', ['userid'=>$fromform->id, 'fieldid'=>$field->id]);
                    if($identidad_field){
                        $identidad_field->data = $fromform->identidad_terr;
                        $DB->update_record('user_info_data', $identidad_field);
                    }else{
                        $newfield = new stdClass();
                        $newfield->userid = $fromform->id;
                        $newfield->fieldid = $field->id;
                        $newfield->data = $fromform->identidad_terr;
                        $newfield->dataformat = 0;
                        $user_field = $DB->insert_record('user_info_data', $newfield, true, false);
                    }
                    break;
            }
        }
    }
    if($fromform->repo == 1){
        redirect("/local/repositoryciae/index.php", 'Cambios guardados', 10,  \core\output\notification::NOTIFY_SUCCESS);
    }else{
        redirect("/local/registerciae/nextform3.php", 'Cambios guardados', 10,  \core\output\notification::NOTIFY_SUCCESS);
    }
}else{
    if($id != 0){
        $sql = "SELECT u.id, (SELECT i.data FROM {user_info_data} as i WHERE i.fieldid = 42 AND i.userid = ".$USER->id.") as niveles, (SELECT i.data FROM mdl_user_info_data as i WHERE i.fieldid = 43  AND i.userid = ".$USER->id.") as identidad_terr
        FROM mdl_user_info_data as i JOIN mdl_user as u on u.id=i.userid where u.id = ".$USER->id." limit 1";
        $toform = $DB->get_record_sql($sql);
    }
    $toform->repo = $repo;
    
    $mform->set_data($toform);
    
    $cant = 0;
    foreach($toform as $key=>$value){
        if(!is_null($value)){
            $cant++;
        }
        if($key == id){
            $cant--;
        }
    }
    $percentage = ($cant * 100)/2;
    $data = new stdClass();
    $data->image = "#";
    $data->title = "Ficha de identidad";
    $data->percentage = intval($percentage);

    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('local_registerciae/superior', $data);
    $mform->display();
    echo $OUTPUT->footer();
}


