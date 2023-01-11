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


$PAGE->set_url('/local/registerciae/index.php');
$PAGE->set_context(context_system::instance());

require_login();

$obj = $DB->get_record('user_info_field', ['id' => $fieldid]);

$strpagetitle = get_string('registerciae', 'local_registerciae');
$strpageheading = get_string('registerciae', 'local_registerciae');

$PAGE->set_title($strpagetitle." ".$obj->name);
$PAGE->set_heading($strpageheading." ".$obj->name);

$sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.maildisplay,u.email,(SELECT i.data from {user_info_data} as i where i.fieldid = 3 and i.userid = u.id) as sexo,(SELECT i.data from {user_info_data} as i where i.fieldid = 2 and i.userid = u.id) as comuna,(SELECT i.data from {user_info_data} as i where i.fieldid = 20 and i.userid = u.id) as region,(SELECT i.data from {user_info_data} as i where i.fieldid = 25 and i.userid = u.id) as tipousuario from {user} as u join {user_info_data} as i on u.id=i.userid where u.id = ".$USER->id." limit 1";
$toform = $DB->get_record_sql($sql);

$sql2 = "SELECT (SELECT i.data FROM {user_info_data} as i WHERE i.fieldid = 24 AND i.userid = ". $USER->id .") as nivel_escolaridad, (SELECT i.data FROM {user_info_data} as i WHERE i.fieldid = 39 AND i.userid = ". $USER->id .") as diplomados, (SELECT i.data FROM {user_info_data} as i WHERE i.fieldid = 40 AND i.userid = ". $USER->id .") as cursos, (SELECT i.data FROM {user_info_data} as i WHERE i.fieldid = 41 AND i.userid = ". $USER->id .") as areas from {user_info_data} limit 1";
$toform2 = $DB->get_record_sql($sql2);

$sql3 = "SELECT u.id, (SELECT i.data FROM {user_info_data} as i WHERE i.fieldid = 42 AND i.userid = ".$USER->id.") as niveles, (SELECT i.data FROM mdl_user_info_data as i WHERE i.fieldid = 43  AND i.userid = ".$USER->id.") as identidad_terr FROM mdl_user_info_data as i JOIN mdl_user as u on u.id=i.userid where u.id = ".$USER->id." limit 1";
$toform3 = $DB->get_record_sql($sql3);

$cant = 0;
foreach($toform as $key=>$value){
    if(!is_null($value)){
        $cant++;
    }
    if($key == id){
        $cant--;
    }
}
$percentage = ($cant * 100)/9;
$percentage  = intval($percentage);

$cant2 = 0;
foreach($toform2 as $key=>$value){
    if(!is_null($value)){
        $cant2++;
    }
}

$cant3 = 0;
    foreach($toform3 as $key=>$value){
        if(!is_null($value)){
            $cant3++;
        }
        if($key == id){
            $cant3--;
        }
    }
$percentage3 = ($cant3 * 100)/2;
$percentage3 = intval($percentage3);

$percentage2 = ($cant2 * 100)/4;
$percentage2 = intval($percentage2);


$results = new stdClass();
$results->locallink = $CFG->wwwroot."/local/registerciae/";
$results->percentage1 = $percentage;
$results->percentage2 = $percentage2;
$results->percentage3 = $percentage3;
$results->id = $USER->id; 

echo $OUTPUT->header();

echo $OUTPUT->render_from_template('local_registerciae/index', $results);

echo $OUTPUT->footer();