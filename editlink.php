<?php

require_once('../../../config.php');
require_once('forms.php');

$courseid = required_param('courseid',PARAM_INT);
$editid  = optional_param('editid',0,PARAM_INT);
$context = get_context_instance(CONTEXT_COURSE,$courseid);
$contextid = $context->id;

$url = new moodle_url('/course/format/stunning/editlink.php', array('editid' => $editid,'courseid' => $courseid));

$item = null;

if(!empty($editid)){

    $sql = "SELECT link, position,name FROM  {format_topcoll_link} WHERE id = ?";
    $params = array('id'=>$editid);

    $item = $DB->get_record_sql($sql, $params);
}

require_login();

if (isguestuser()) {
    die();
}

$PAGE->set_url($url);
$PAGE->set_context($context);

$mform = new stunning_link_form($url,array('courseid'=>$courseid,'item'=>$item));

if ($mform->is_cancelled()) {
    // Someone has hit the 'cancel' button.
    redirect(new moodle_url('/course/view.php', array('id' => $courseid)));
} else if ($formdata = $mform->get_data()) { // Form has been submitted.

        $record = array('name'=>$formdata->name,
                        'link'=>$formdata->course,
                        'courseid'=>$courseid);

        $record = (object)$record;

        if(is_null($item)){
            $sql = "SELECT MAX(position)
                    FROM {format_stunning_link}
                    WHERE courseid = ?";

            $params = array($courseid);
            $position = $DB->get_field_sql($sql,$params);
            $position = (empty($position))? 0 : $position++;

            $record->position = $position;

            $edit = 'insert_record';

        }else{
            $record->id = $item->id;
            $position->id = $item->position;
            $edit = 'update_record';
        }

        $DB->$edit('format_stunning_link',$record);

        redirect(new moodle_url('/course/view.php', array('id' => $courseid)));
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();