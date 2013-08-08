<?php

require_once('../../../config.php');

$id = required_param('id',PARAM_INT);
$courseid = required_param('courseid',PARAM_INT);

$urlid = $DB->get_field('format_stunning_icon','url',array('id'=>$id));

if(!empty($urlid)) $DB->delete_records('format_stunning_url',array('id'=>$urlid));

$DB->delete_records('format_stunning_icon',array('id'=>$id));

redirect($CFG->wwwroot."/course/view.php?id=".$courseid);


