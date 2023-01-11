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


$PAGE->set_url('/local/registerciae/general.php');
$PAGE->set_context(context_system::instance());
$contextid = $PAGE->context->id;
require_login();

require_once("forms/general.php");

$strpagetitle = get_string('registerciae', 'local_registerciae');
$strpageheading = get_string('registerciae', 'local_registerciae');
$PAGE->set_title($strpagetitle." ".$obj->name);
$PAGE->set_heading($strpageheading." ".$obj->name);

$mform = new general_form(); 
$toform = [];

$user_info_field = $DB->get_records('user_info_field', null, null, 'id, shortname, name, param1');

if($mform->is_cancelled()){
    redirect("/local/registerciae/index.php", '', 10);
}elseif($fromform = $mform->get_data()){
    if($fromform->id != 0){        
        //Update data
        $user = $DB->get_record('user', ['id'=>$fromform->id]);
        $authplugin = get_auth_plugin($user->auth);
        $user->username = $fromform->username;
        $user->firstname = $fromform->firstname;
        $user->lastname = $fromform->lastname;
        $user->maildisplay = $fromform->maildisplay;
        $DB->update_record('user', $user);
        if($fromform->password){
            $authplugin->user_update_password($user, $fromform->password);
        }
        foreach ($user_info_field as $field){
            switch($field->shortname){
                case "sexo":
                    $gender_field = $DB->get_record('user_info_data', ['userid'=>$fromform->id, 'fieldid'=>$field->id]);
                    if($gender_field){
                        $gender_field->data = $fromform->sexo;
                        $DB->update_record('user_info_data', $gender_field);
                    }else{
                        $newfield = new stdClass();
                        $newfield->userid = $fromform->id;
                        $newfield->fieldid = $field->id;
                        $newfield->data = $fromform->sexo;
                        $newfield->dataformat = 0;
                        $user_field = $DB->insert_record('user_info_data', $newfield, true, false);
                    }
                    break;
                case "comuna":
                    $comuna_field = $DB->get_record('user_info_data', ['userid'=>$fromform->id, 'fieldid'=>$field->id]);
                    if($comuna_field){
                        $comuna_field->data = $fromform->comuna;
                        $DB->update_record('user_info_data', $comuna_field);
                    }else{
                        $newfield = new stdClass();
                        $newfield->userid = $fromform->id;
                        $newfield->fieldid = $field->id;
                        $newfield->data = $fromform->comuna;
                        $newfield->dataformat = 0;
                        $user_field = $DB->insert_record('user_info_data', $newfield, true, false);
                    }
                    break;
                case "region":
                    $region_field = $DB->get_record('user_info_data', ['userid'=>$fromform->id, 'fieldid'=>$field->id]);
                    if($region_field){
                        $region_field->data = $fromform->region;
                        $DB->update_record('user_info_data', $region_field);
                    }else{
                        $newfield = new stdClass();
                        $newfield->userid = $fromform->id;
                        $newfield->fieldid = $field->id;
                        $newfield->data = $fromform->region;
                        $newfield->dataformat = 0;
                        $user_field = $DB->insert_record('user_info_data', $newfield, true, false);
                    }
                    break;
                case "tipousuario":
                    $type_field = $DB->get_record('user_info_data', ['userid'=>$fromform->id, 'fieldid'=>$field->id]);
                    if($type_field){
                        $type_field->data = $fromform->tipousuario;
                        $DB->update_record('user_info_data', $type_field);
                    }else{
                        $newfield = new stdClass();
                        $newfield->userid = $fromform->id;
                        $newfield->fieldid = $field->id;
                        $newfield->data = $fromform->tipousuario;
                        $newfield->dataformat = 0;
                        $user_field = $DB->insert_record('user_info_data', $newfield, true, false);
                    }
                    break; 
            }
        }
    }
    redirect("/local/registerciae/nextform.php?id=".$USER->id, 'Cambios guardados', 10,  \core\output\notification::NOTIFY_SUCCESS);
}else{
    if($id != 0){
        $sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.maildisplay,u.email,(SELECT i.data from {user_info_data} as i where i.fieldid = 3 and i.userid = u.id) as sexo,(SELECT i.data from {user_info_data} as i where i.fieldid = 2 and i.userid = u.id) as comuna,(SELECT i.data from {user_info_data} as i where i.fieldid = 20 and i.userid = u.id) as region,(SELECT i.data from {user_info_data} as i where i.fieldid = 25 and i.userid = u.id) as tipousuario from {user} as u join {user_info_data} as i on u.id=i.userid where u.id = ".$USER->id." limit 1";
        $toform = $DB->get_record_sql($sql);
    }
    $mform->set_data($toform);
    
    $cant = 0;
    foreach($toform as $key=>$value){
        if(!is_null($value)){
            $cant++;
        }
    }
    $percentage = ($cant * 100)/10;
    $data = new stdClass();
    $data->image = "#";
    $data->title = "Ficha de antecedentes generales";
    $data->percentage = intval($percentage);

    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('local_registerciae/superior', $data);
    $mform->display();
    echo $OUTPUT->footer();
}


