<?php

require_once('../../../config.php');
require_once('forms.php');

$courseid = required_param('courseid',PARAM_INT);
$editid  = optional_param('editid',0,PARAM_INT);
$context = get_context_instance(CONTEXT_COURSE,$courseid);
$contextid = $context->id;

$url = new moodle_url('/course/format/stunning/editicon.php', array('editid' => $editid,'courseid' => $courseid));

$item = null;

if(!empty($editid)){

    $sql = "SELECT ti.imagepath, ti.activityid,ti.position,tu.url,ti.url urlid
            FROM  {format_stunning_icon} ti 
            LEFT JOIN {format_stunning_url} tu ON tu.id = ti.url
            WHERE ti.id = ? 
            ";
    $params = array('id'=>$editid);

    $item = $DB->get_record_sql($sql, $params);
}

require_login();

if (isguestuser()) {
    die();
}

$PAGE->set_url($url);
$PAGE->set_context($context);

$mform = new stunning_icon_form($url,array('courseid'=>$courseid,'item'=>$item));

if ($mform->is_cancelled()) {
    // Someone has hit the 'cancel' button.
    redirect(new moodle_url('/course/view.php', array('id' => $courseid)));
} else if ($formdata = $mform->get_data()) { // Form has been submitted.


        if(is_null($item)){
            $file_url = 0;
            if($formdata->activity == 0){
                $record = array('url'=>$formdata->url);
                $record = (object)$record;
                $file_url = $DB->insert_record('format_stunning_url',$record);
            }

            $sql = "SELECT MAX(position)
                    FROM {format_stunning_icon}
                    WHERE courseid = ?";

            $params = array($courseid);
            $position = $DB->get_field_sql($sql,$params);
            $position = (empty($position))? 0 : $position++;

            $record = array('imagepath'=>$formdata->icon,
                'activityid'=>$formdata->activity,
                'url'=>$file_url,
                'courseid'=>$courseid,
                'position'=>$position);

            $record = (object)$record;

            $DB->insert_record('format_stunning_icon',$record);  

        }else{
            $file_url = 0;
            if($formdata->activity == 0){
                $record = array('url'=>$formdata->url);
                $record = (object)$record;
                if($item->activityid != 0){
                    $file_url = $DB->insert_record('format_stunning_url',$record);    
                }else{
                    $record->id = $item->urlid;
                    $file_url = $item->urlid;
                    $DB->update_record('format_stunning_url',$record);
                }
            }else{
                if($item->activityid  == 0){
                    $DB->delete_records('format_stunning_url',array('id'=>$item->urlid));
                }

            }         
                $record = array('id'=>$editid,
                    'imagepath'=>$formdata->icon,
                    'activityid'=>$formdata->activity,
                    'url'=>$file_url);

                $record = (object)$record;

                $DB->update_record('format_stunning_icon',$record);
        }



         redirect(new moodle_url('/course/view.php', array('id' => $courseid)));
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();


