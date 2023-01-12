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

//values
// 0 : None;
// 1 : Cursos;
// 2 : Autoevaluación;
$page = optional_param('page', 0, PARAM_INT);

//Quiero saber si el usuario respondió si es docente o no.
$sql2 = "SELECT u.id, (SELECT i.data FROM {user_info_data} as i WHERE i.fieldid = 44 AND i.userid = ".$USER->id.") as esdocente FROM mdl_user_info_data as i JOIN mdl_user as u on u.id=i.userid where u.id = ".$USER->id." limit 1";
$toform2 = $DB->get_record_sql($sql2);

//Necesito saber si el usuario respondió la encuesta 2 completamente.
$sql = "SELECT (SELECT i.data FROM {user_info_data} as i WHERE i.fieldid = 24 AND i.userid = ". $USER->id .") as nivel_escolaridad, (SELECT i.data FROM {user_info_data} as i WHERE i.fieldid = 39 AND i.userid = ". $USER->id .") as diplomados, (SELECT i.data FROM {user_info_data} as i WHERE i.fieldid = 40 AND i.userid = ". $USER->id .") as cursos, (SELECT i.data FROM {user_info_data} as i WHERE i.fieldid = 41 AND i.userid = ". $USER->id .") as areas from {user_info_data} limit 1";
$toform = $DB->get_record_sql($sql);
$cant = 0;
    foreach($toform as $key=>$value){
        if(!is_null($value)){
            $cant++;
        }
        if($key == id){
            $cant--;
        }
    }
$percentage3 = ($cant * 100)/4;
$percentage3 = intval($percentage3);

//Si es docente y el porcentaje de respuesta es menor a 100
if($toform2->esdocente == 1 && $percentage3 < 100){
    redirect("/local/registerciae/studies.php?id=".$USER->id."&repo=1");
}else if (is_null($toform2->esdocente)){ //si no hay respuesta
    require_once("forms/docente.php");

    $PAGE->set_url('/local/registerciae/docente2.php');
    $PAGE->set_context(context_system::instance());

    require_login();

    $mform = new docente_form(); 
    $toform = [];

    $strpagetitle = get_string('registerciae', 'local_registerciae');
    $strpageheading = get_string('registerciae', 'local_registerciae');

    $PAGE->set_title($strpagetitle." ".$obj->name);
    $PAGE->set_heading($strpageheading." ".$obj->name);

    $user_info_field = $DB->get_records('user_info_field', null, null, 'id, shortname, name, param1');

    if($mform->is_cancelled()){
        redirect("/local/repositoryciae/index.php", '', 10);
    }elseif($fromform = $mform->get_data()){
        if($fromform->id != 0){        
            //Update data
            $user = $DB->get_record('user', ['id'=>$fromform->id]);
        
            foreach ($user_info_field as $field){
                switch($field->shortname){
                    case "esdocente":
                        $teacher_field = $DB->get_record('user_info_data', ['userid'=>$fromform->id, 'fieldid'=>$field->id]);
                        if($teacher_field){
                            $teacher_field->data = $fromform->esdocente;
                            $DB->update_record('user_info_data', $teacher_field);
                        }else{
                            $newfield = new stdClass();
                            $newfield->userid = $fromform->id;
                            $newfield->fieldid = $field->id;
                            $newfield->data = $fromform->esdocente;
                            $newfield->dataformat = 0;
                            $user_field = $DB->insert_record('user_info_data', $newfield, true, false);
                        }
                        break;
                }
            }
        }
        if($fromform->page == 1){
            redirect("/course/index.php?categoryid=10");
        }else if ($fromform->page == 2){
            redirect("/course/view.php?id=50&section=0");
        }
    }else{
        $sql = "SELECT u.id, (SELECT i.data from {user_info_data} as i where i.fieldid = 44 and i.userid = u.id) as esdocente from {user} as u join {user_info_data} as i on u.id=i.userid where u.id = ".$USER->id." limit 1";
        
        $toform = $DB->get_record_sql($sql);
        $toform->page = $page;
        $mform->set_data($toform);


        $data = new stdClass();
        $data->id = $USER->id; 
        echo $OUTPUT->header();
        echo $OUTPUT->render_from_template('local_registerciae/docente', $data);
        $mform->display();
        echo $OUTPUT->footer();
    }
}else if ($toform2->esdocente == 1 && $percentage3 == 100){
    if($page == 1){
        redirect("/course/index.php?categoryid=10");
    }else if($page == 2){
        redirect("/course/view.php?id=50&section=0");
    }
}









