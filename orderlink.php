<?php
require_once('../../../config.php');
require_once('forms.php');

$courseid = required_param('courseid',PARAM_INT);

$context = get_context_instance(CONTEXT_COURSE,$courseid);
$contextid = $context->id;

$url = new moodle_url('/course/format/stunning/orderlink.php', array('courseid' => $courseid));

require_login();

if (isguestuser()) {
    die();
}

$PAGE->set_url($url);
$PAGE->set_context($context);

$mform = new stunning_link_edit_form($url,array('courseid'=>$courseid));

if ($mform->is_cancelled()) {
    // Someone has hit the 'cancel' button.
    redirect(new moodle_url('/course/view.php', array('id' => $courseid)));
} else if ($formdata = $mform->get_data()) { 

		foreach($formdata->name as $id => $name){

			if(isset($formdata->delete)){
				if($formdata->delete[$id] == 1 ){
					 $DB->delete_records('format_stunning_link',array('id'=>$id));
					 continue;		
				}	
			} 

		$record = new stdClass();
		$record->id= $id;
		$record->position =$formdata->order[$id];
		if(empty($name)) $record->name = $name;
		$DB->update_record('format_stunning_link',$record);
	}

	 redirect(new moodle_url('/course/view.php', array('id' => $courseid)));
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
