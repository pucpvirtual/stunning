<?php

require_once('../../../config.php');
require_once('forms.php');

$courseid = required_param('courseid',PARAM_INT);
$editid  = optional_param('editid',0,PARAM_INT);
$context = get_context_instance(CONTEXT_COURSE,$courseid);
$contextid = $context->id;

$url = new moodle_url('/course/format/stunning/editbanner.php', array('courseid' => $courseid));

require_login();

if (isguestuser()) {
    die();
}

$PAGE->set_url($url);
$PAGE->set_context($context);

$item = $DB->get_record('format_stunning_banner', array('courseid'=>$courseid));

$mform = new stunning_banner_form($url,array('courseid'=>$courseid,'item'=>$item));


if ($mform->is_cancelled()) {
    // Someone has hit the 'cancel' button.
    redirect(new moodle_url('/course/view.php', array('id' => $courseid)));
} else if ($formdata = $mform->get_data()) { 

	$record = new stdClass();
	$record->url = $formdata->banner;
	$record->courseid = $courseid;
	$record->type = $formdata->type;

	if(!$item){
		$DB->insert_record('format_stunning_banner',$record);
	}else{
		$record->id = $item->id;
		$DB->update_record('format_stunning_banner',$record);
	}

	redirect(new moodle_url('/course/view.php', array('id' => $courseid)));
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
