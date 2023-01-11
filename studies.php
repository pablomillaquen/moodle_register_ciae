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
$lang = current_language();


$PAGE->set_url('/local/registerciae/studies.php');
$PAGE->set_context(context_system::instance());
$contextid = $PAGE->context->id;
require_login();

require_once("forms/studies.php");

$strpagetitle = get_string('registerciae', 'local_registerciae');
$strpageheading = get_string('registerciae', 'local_registerciae');
$PAGE->set_title($strpagetitle." ".$obj->name);
$PAGE->set_heading($strpageheading." ".$obj->name);

$mform = new studies_form(); 
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
                case "nivel_escolaridad":
                    $nivel_field = $DB->get_record('user_info_data', ['userid'=>$fromform->id, 'fieldid'=>$field->id]);
                    if($nivel_field){
                        $nivel_field->data = $fromform->nivel_escolaridad;
                        $DB->update_record('user_info_data', $nivel_field);
                    }else{
                        $newfield = new stdClass();
                        $newfield->userid = $fromform->id;
                        $newfield->fieldid = $field->id;
                        $newfield->data = $fromform->nivel_escolaridad;
                        $newfield->dataformat = 0;
                        $user_field = $DB->insert_record('user_info_data', $newfield, true, false);
                    }
                    break;
                case "diplomados":
                    $diplomados_field = $DB->get_record('user_info_data', ['userid'=>$fromform->id, 'fieldid'=>$field->id]);
                    if($diplomados_field){
                        $diplomados_field->data = $fromform->diplomados;
                        $DB->update_record('user_info_data', $diplomados_field);
                    }else{
                        $newfield = new stdClass();
                        $newfield->userid = $fromform->id;
                        $newfield->fieldid = $field->id;
                        $newfield->data = $fromform->diplomados;
                        $newfield->dataformat = 0;
                        $user_field = $DB->insert_record('user_info_data', $newfield, true, false);
                    }
                    break;
                case "cursos":
                    $cursos_field = $DB->get_record('user_info_data', ['userid'=>$fromform->id, 'fieldid'=>$field->id]);
                    if($cursos_field){
                        $cursos_field->data = $fromform->cursos;
                        $DB->update_record('user_info_data', $cursos_field);
                    }else{
                        $newfield = new stdClass();
                        $newfield->userid = $fromform->id;
                        $newfield->fieldid = $field->id;
                        $newfield->data = $fromform->cursos;
                        $newfield->dataformat = 0;
                        $user_field = $DB->insert_record('user_info_data', $newfield, true, false);
                    }
                    break;
                case "areas":
                    $areas_field = $DB->get_record('user_info_data', ['userid'=>$fromform->id, 'fieldid'=>$field->id]);
                    if($areas_field){
                        $areas_field->data = $fromform->areas;
                        $DB->update_record('user_info_data', $areas_field);
                    }else{
                        $newfield = new stdClass();
                        $newfield->userid = $fromform->id;
                        $newfield->fieldid = $field->id;
                        $newfield->data = $fromform->areas;
                        $newfield->dataformat = 0;
                        $user_field = $DB->insert_record('user_info_data', $newfield, true, false);
                    }
                    break; 
            }
        }
    }
    redirect("/local/registerciae/nextform2.php", 'Cambios guardados', 10,  \core\output\notification::NOTIFY_SUCCESS);
}else{
    if($id != 0){
        $sql = "SELECT u.id, (SELECT i.data FROM {user_info_data} as i WHERE i.fieldid = 24 AND i.userid = ". $USER->id .") as nivel_escolaridad, (SELECT i.data FROM {user_info_data} as i WHERE i.fieldid = 39 AND i.userid = ". $USER->id .") as diplomados, (SELECT i.data FROM {user_info_data} as i WHERE i.fieldid = 40 AND i.userid = ". $USER->id .") as cursos, (SELECT i.data FROM {user_info_data} as i WHERE i.fieldid = 41 AND i.userid = ". $USER->id .") as areas from {user_info_data} as i join {user} as u on u.id=i.userid where u.id = ". $USER->id ." limit 1";
        $toform = $DB->get_record_sql($sql);
    }
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
    $percentage = ($cant * 100)/4;
    $data = new stdClass();
    $data->image = "#";
    $data->title = "Ficha de Formación académica";
    $data->percentage = intval($percentage);

    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('local_registerciae/superior', $data);
    $mform->display();
    echo $OUTPUT->footer();
}


