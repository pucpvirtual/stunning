<?php


defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/format/renderer.php');
require_once($CFG->dirroot . '/course/format/stunning/lib.php');
require_once($CFG->dirroot . '/course/format/stunning/togglelib.php');

class format_stunning_renderer extends format_section_renderer_base {

    private $tccolumnwidth = 100; // Default width in percent of the column(s).
    private $tccolumnpadding = 0; // Default padding in pixels of the column(s).
    private $mobiletheme = false; // As not using a mobile theme we can react to the number of columns setting.
    private $tablettheme = false; // As not using a tablet theme we can react to the number of columns setting.
    private $courseformat; // Our course format object as defined in lib.php;
    private $tcsettings; // Settings for the format - array.
    private $userpreference; // User toggle state preference - string.
    private $defaultuserpreference; // Default user preference when none set - bool - true all open, false all closed.
    private $togglelib;
    private $isoldtogglepreference = false;


    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);

        $this->togglelib = new stunning_togglelib;

        $page->set_other_editing_capability('moodle/course:setcurrentsection');
    }


    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'ctopics'));
    }


    protected function start_toggle_section_list() {
        $classes = 'ctopics topics';
        $style = '';
        if ($this->tcsettings['layoutcolumnorientation'] == 1) {
            $style .= 'width:' . $this->tccolumnwidth . '%;';  // Vertical columns.
        } else {
            $style .= 'width:100%;';  // Horizontal columns.
        }
        if ($this->mobiletheme === false) {
            $classes .= ' ctlayout';
        }
        $style .= ' padding:' . $this->tccolumnpadding . 'px;';
        $attributes = array('class' => $classes);
        $attributes['style'] = $style;
        return html_writer::start_tag('ul', $attributes);
    }


    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    protected function page_title() {
        return get_string('sectionname', 'format_stunning');
    }


    protected function section_right_content($section, $course, $onsectionpage) {
        $o = $this->output->spacer();

        if ($section->section != 0) {
            $controls = $this->section_edit_controls($course, $section, $onsectionpage);
            if (!empty($controls)) {
                $o .= implode('<br />', $controls);
            } else {
                if (empty($this->tcsettings)) {
                    $this->tcsettings = $this->courseformat->get_settings();
                }
                switch ($this->tcsettings['layoutelement']) { // Toggle section x.
                    case 1:
                    case 3:
                    case 5:
                    case 8:
                        // Get the specific words from the language files.
                        $topictext = null;
                        if (($this->tcsettings['layoutstructure'] == 1) || ($this->tcsettings['layoutstructure'] == 4)) {
                            $topictext = get_string('setlayoutstructuretopic', 'format_stunning');
                        } else if (($this->tcsettings['layoutstructure'] == 2) || ($this->tcsettings['layoutstructure'] == 3)) {
                            $topictext = get_string('setlayoutstructureweek', 'format_stunning');
                        } else {
                            $topictext = get_string('setlayoutstructureday', 'format_stunning');
                        }

                        $o .= html_writer::tag('span', $topictext.html_writer::empty_tag('br').
                                               $section->section, array('class' => 'cps_centre'));
                        break;
                }
            }
        }

        return $o;
    }

    protected function section_left_content($section, $course, $onsectionpage) {
        $o = $this->output->spacer();

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (course_get_format($course)->is_section_current($section)) {
                $o .= get_accesshide(get_string('currentsection', 'format_' . $course->format));
            }
            if (empty($this->tcsettings)) {
                $this->tcsettings = $this->courseformat->get_settings();
            }
            switch ($this->tcsettings['layoutelement']) {
                case 1:
                case 2:
                case 5:
                case 6:
                    $o .= html_writer::tag('span', $section->section, array('class' => 'cps_centre'));
                    break;
            }
        }
        return $o;
    }


    protected function section_edit_controls($course, $section, $onsectionpage = false) {
        global $PAGE;

        if (!$PAGE->user_is_editing()) {
            return array();
        }

        $coursecontext = context_course::instance($course->id);

        if ($onsectionpage) {
            $url = course_get_url($course, $section->section);
        } else {
            $url = course_get_url($course);
        }
        $url->param('sesskey', sesskey());

        if (empty($this->tcsettings)) {
            $this->tcsettings = $this->courseformat->get_settings();
        }
        $controls = array();
        if ((($this->tcsettings['layoutstructure'] == 1) || ($this->tcsettings['layoutstructure'] == 4)) &&
              has_capability('moodle/course:setcurrentsection', $coursecontext)) {
            if ($course->marker == $section->section) {  // Show the "light globe" on/off.
                $url->param('marker', 0);
                $controls[] = html_writer::link($url, html_writer::empty_tag('img',
                                    array('src' => $this->output->pix_url('i/marked'),
                                          'class' => 'icon ', 'alt' => get_string('markedthissection', 'format_stunning'))),
                                    array('title' => get_string('markedthissection', 'format_stunning'),
                                          'class' => 'editing_highlight'));
            } else {
                $url->param('marker', $section->section);
                $controls[] = html_writer::link($url, html_writer::empty_tag('img',
                                    array('src' => $this->output->pix_url('i/marker'),
                                          'class' => 'icon', 'alt' => get_string('markthissection', 'format_stunning'))),
                                    array('title' => get_string('markthissection', 'format_stunning'),
                                          'class' => 'editing_highlight'));
            }
        }

        return array_merge($controls, parent::section_edit_controls($course, $section, $onsectionpage));
    }


    protected function section_summary($section, $course, $mods) {
        $classattr = 'section main section-summary clearfix';
        $linkclasses = '';

        // If section is hidden then display grey section link.
        if (!$section->visible) {
            $classattr .= ' hidden';
            $linkclasses .= ' dimmed_text';
        } else if (course_get_format($course)->is_section_current($section)) {
            $classattr .= ' current';
        }

        $o = '';
        $liattributes = array('id' => 'section-'.$section->section, 'class' => $classattr);
        if ($this->tcsettings['layoutcolumnorientation'] == 2) { // Horizontal column layout.
            $liattributes['style'] = 'width:' . $this->tccolumnwidth . '%;';
        }
        $o .= html_writer::start_tag('li', $liattributes);

        $o .= html_writer::tag('div', '', array('class' => 'left side'));
        $o .= html_writer::tag('div', '', array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        $title = get_section_name($course, $section);
        if ($section->uservisible) {
            $title = html_writer::tag('a', $title,
                    array('href' => course_get_url($course, $section->section), 'class' => $linkclasses));
        }
        $o .= $this->output->heading($title, 3, 'section-title');

        $o.= html_writer::start_tag('div', array('class' => 'summarytext'));
        $o.= $this->format_summary_text($section);
        $o.= html_writer::end_tag('div');
        $o.= $this->section_activity_summary($section, $course, null);

        $context = context_course::instance($course->id);
        $o .= $this->section_availability_message($section,
                has_capability('moodle/course:viewhiddensections', $context));

        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');

        return $o;
    }


    protected function section_header($section, $course, $onsectionpage, $sectionreturn = null) {
        $o = '';
        global $PAGE;

        $sectionstyle = '';
        $rightcurrent = '';
        $context = context_course::instance($course->id);

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            } else if (course_get_format($course)->is_section_current($section)) {
                $section->toggle = true; // Open current section regardless of toggle state.
                $sectionstyle = ' current';
                $rightcurrent = ' left';
            }
        }

        $liattributes = array('id' => 'section-' . $section->section,
            'class' => 'section main clearfix' . $sectionstyle);
        if ($this->tcsettings['layoutcolumnorientation'] == 2) { // Horizontal column layout.
            $liattributes['style'] = 'width:' . $this->tccolumnwidth . '%;';
        }
        $o .= html_writer::start_tag('li', $liattributes);

        if (($this->mobiletheme === false) && ($this->tablettheme === false)) {
            $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
            $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side'));
        }

        if (($this->mobiletheme === false) && ($this->tablettheme === false)) {
            $rightcontent = '';
            if (($section->section != 0) && $PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
                $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));

                $rightcontent .= html_writer::link($url, html_writer::empty_tag('img',
                                    array('src' => $this->output->pix_url('t/edit'),
                                          'class' => 'iconsmall edit tceditsection', 'alt' => get_string('edit'))),
                                    array('title' => get_string('editsummary'), 'class' => 'tceditsection'));
                $rightcontent .= html_writer::empty_tag('br');
            }
            $rightcontent .= $this->section_right_content($section, $course, $onsectionpage);
            $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        }
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        if (($onsectionpage == false) && ($section->section != 0)) {
            $o .= html_writer::start_tag('div',
                    array('class' => 'sectionhead toggle toggle-'.$this->tcsettings['toggleiconset'],
                    'id' => 'toggle-' . $section->section));

            if ((!($section->toggle === null)) && ($section->toggle == true)) {
                $toggleclass = 'toggle_open';
                $sectionclass = ' sectionopen';
            } else {
                $toggleclass = 'toggle_closed';
                $sectionclass = '';
            }
            $toggleclass .= ' the_toggle';
            $toggleurl = new moodle_url('/course/view.php', array('id' => $course->id));
            $o .= html_writer::start_tag('a', array('class' => $toggleclass, 'href' => $toggleurl));

            if (empty($this->tcsettings)) {
                $this->tcsettings = $this->courseformat->get_settings();
            }

            $title = $this->courseformat->get_stunning_section_name($course, $section, true);
            if (($this->mobiletheme === false) && ($this->tablettheme === false)) {
                $o .= $this->output->heading($title, 3, 'sectionname');
            } else {
                $o .= html_writer::tag('h3', $title); // Moodle H3's look bad on mobile / tablet with CT so use plain.
            }

            $o .= html_writer::end_tag('a');
            $o .= html_writer::end_tag('div');
            $o .= html_writer::start_tag('div', array('class' => 'sectionbody toggledsection'.$sectionclass,
                                                      'id' => 'toggledsection-' . $section->section));
            if ($section->section != 0 && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                $o .= html_writer::link(course_get_url($course, $section->section), $title);
            }

            if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
                $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));
                $o.= html_writer::link($url, html_writer::empty_tag('img', array('src' => $this->output->pix_url('t/edit'),
                                    'class' => 'iconsmall edit', 'alt' => get_string('edit'))),
                                    array('title' => get_string('editsummary')));
            }

            $o .= html_writer::start_tag('div', array('class' => 'summary'));
            $o .= $this->format_summary_text($section);

            $o .= html_writer::end_tag('div');

            $o .= $this->section_availability_message($section, has_capability('moodle/course:viewhiddensections', $context));
        } else {
            // When on a section page, we only display the general section title, if title is not the default one.
            $hasnamesecpg = ($section->section == 0 && (string) $section->name !== '');

            if ($hasnamesecpg) {
                $o .= $this->output->heading($this->section_title($section, $course), 3, 'sectionname');
            }
            $o .= html_writer::start_tag('div', array('class' => 'summary'));
            $o .= $this->format_summary_text($section);

            if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
                $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));
                $o.= html_writer::link($url, html_writer::empty_tag('img', array('src' => $this->output->pix_url('t/edit'),
                                    'class' => 'iconsmall edit', 'alt' => get_string('edit'))),
                                    array('title' => get_string('editsummary')));
            }
            $o .= html_writer::end_tag('div');

            $o .= $this->section_availability_message($section, has_capability('moodle/course:viewhiddensections', $context));
        }
        return $o;
    }


    protected function section_footer() {
        $o = html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');
        return $o;
    }


 
    private function print_noticeboard($course) {
        global $OUTPUT;

        if($this->tcsettings['extraelements'] == 4   || $this->tcsettings['extraelements']  ==  2 ) return;


        if ($forum = forum_get_course_forum($course->id, 'news')) {
            $cm = get_coursemodule_from_instance('forum', $forum->id);
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);

            if(count(forum_get_discussions($cm, "d.timemodified DESC", true, -1,1, $userlastmodified=false, -1, 0))>0){
              echo forum_print_latest_discussions($course, $forum, 1, 'plain', '', -1, -1, -1, 100, $cm);
            }
        }
    }


    private function print_othercourse($course) {
        global $PAGE,$DB;
        $userisediting = $PAGE->user_is_editing();
         $links = $DB->get_records('format_stunning_link',array('courseid'=>$course->id),'position');

            echo html_writer::start_tag('div', array('id' => 'morecourse'));
                echo html_writer::start_tag('ul',array('class'=>'courses'));
                if(count($links)!= 0){
                    echo html_writer::start_tag('li');
                        echo html_writer::tag('a','Cursos:',array('class'=>'course-label'));
                    echo html_writer::end_tag('li');                    
                }


                foreach($links as $l){
                        echo html_writer::start_tag('li');
                            echo html_writer::link(new moodle_url('/course/view.php',array('id'=>$l->link)),$l->name);
                        echo html_writer::end_tag('li');
                }

                if($userisediting){
                    echo html_writer::start_tag('li');
                        echo html_writer::link($this->courseformat->stunning_moodle_url('editlink.php', array('courseid'=>$course->id)),get_string('addlink','format_stunning'));
                        echo html_writer::link($this->courseformat->stunning_moodle_url('orderlink.php', array('courseid'=>$course->id)),get_string('edit'));
                    echo html_writer::end_tag('li');
                }

                echo html_writer::end_tag('ul');
                echo html_writer::tag('div','',array('class'=>'clearfix'));    
            echo html_writer::end_tag('div');

   
        
    }

    private function print_menu_acitivity($course){
        global $DB,$PAGE;

        $userisediting = $PAGE->user_is_editing();
        $context = context_course::instance($course->id);
        $menuAct = $DB->get_records('format_stunning_icon',array('courseid'=>$course->id),'position');

        if($this->tcsettings['extraelements'] == 4   || $this->tcsettings['extraelements']  ==  1 ) $class = '' ;
        else $class = 'w-profile';

        if(count($menuAct) > 0 ||$userisediting ){
            echo html_writer::start_tag('div', array('id' => 'shadebox', 'class'=>$class));        
            echo html_writer::start_tag('div', array('id' => 'shadebox_content'));
            echo html_writer::start_tag('ul',array('class'=>"icons"));

                foreach($menuAct as $act){

                    if(empty($act->url)){

                        $sql = "SELECT cm.id, m.name
                                FROM {course_modules} cm 
                                INNER JOIN {modules} m ON m.id = cm.module
                                WHERE cm.id = ?";

                        $params = array($act->activityid);
                        $dataact = $DB->get_record_sql($sql,$params);
                        $acturl  = new moodle_url('/mod/'.$dataact->name.'/view.php',array('id'=>$dataact->id));
                    }else{
                         $acturl = "http://".$DB->get_field('format_stunning_url','url',array('id'=>$act->url));
                    }

                    echo html_writer::start_tag('li');
                        echo html_writer::start_tag('div',array('style'=>""));
                            echo html_writer::start_tag('a',array('href'=>$acturl));
                                echo html_writer::empty_tag('img', array('src' => $act->imagepath,'style'=>"max-width:70px;"));
                            echo html_writer::end_tag('a');
                            if($userisediting){
                            echo html_writer::start_tag('div');
                                    echo html_writer::link($this->courseformat->stunning_moodle_url('editicon.php', array('courseid'=>$course->id,'editid'=>$act->id)),get_string('edit'));
                                    echo html_writer::link($this->courseformat->stunning_moodle_url('delete.php', array('courseid'=>$course->id,'id'=>$act->id)),get_string('delete'),array('class'=>'delete'));
                            echo html_writer::end_tag('div');
                          }
                        echo html_writer::end_tag('div');
                    echo html_writer::end_tag('li');

                }

            if($userisediting){ 
                echo html_writer::start_tag('li');
                        echo html_writer::tag('div','',array('style'=>"background:#c9c9c9;height:85px;width:85px;"));
                        echo html_writer::link($this->courseformat->stunning_moodle_url('editicon.php', array('courseid'=>$course->id)),get_string('addicon','format_stunning'));
                        echo html_writer::link($this->courseformat->stunning_moodle_url('orderimage.php', array('courseid'=>$course->id)),get_string('reorder','format_stunning'));
                echo html_writer::end_tag('li');
            }  
            echo html_writer::end_tag('ul');
            echo html_writer::end_tag('div');
            echo html_writer::tag('div', '&nbsp;', array('class' => 'clearer'));
            echo html_writer::end_tag('div');
        }
    }

    private function print_profile_block(){
        global $USER;

        if($this->tcsettings['extraelements'] == 4   || $this->tcsettings['extraelements']  ==  1 ) return;

        $usercontext = get_context_instance(CONTEXT_USER, $USER->id);
        $pictururl = new moodle_url('/pluginfile.php/'.$usercontext->id.'/user/icon/bcpcampus/f1', array('rev'=>$USER->picture));

        echo html_writer::start_tag('div',array('class'=>'inner-profiel-block'));
            echo html_writer::start_tag('div');
                echo html_writer::empty_tag('img',array('src'=>$pictururl));
            echo html_writer::end_tag('div');
            echo html_writer::start_tag('div');
                echo $USER->firstname .' '. $USER->lastname;
            echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
    }

    private function print_banner($course){
        global $DB,$PAGE;
        $userisediting = $PAGE->user_is_editing();
        $context = context_course::instance($course->id);
        $record = $DB->get_record('format_stunning_banner', array('courseid'=>$course->id));

        
            echo html_writer::start_tag('div');
              if(!empty($record->url)){
                if($record->type == 'img'){
                    echo html_writer::start_tag('div');
                    echo html_writer::empty_tag('img',array('src'=>$record->url,'style'=>'max-width:800px!important;display:block;margin:0 auto;'));
                    echo html_writer::end_tag('div');
                }else{
                    echo html_writer::start_tag('div');
                    echo html_writer::tag('embed','',array('src'=>$record->url,'style'=>'max-width:800px!important;display:block;margin:0 auto;'));
                    echo html_writer::end_tag('div');
                }
              }else{
                if($userisediting)  echo html_writer::tag('div','',array('style'=>"background:#c9c9c9;height:100px;width:100%;"));
              }
            if($userisediting) echo html_writer::link($this->courseformat->stunning_moodle_url('editbanner.php', array('courseid'=>$course->id)),get_string('editbanner','format_stunning'));
            echo html_writer::end_tag('div');
    }


    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        global $PAGE,$USER;

        $modinfo = get_fast_modinfo($course);
        $this->courseformat = course_get_format($course); // Needed for collapsed topics settings retrieval.
        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
            // This section doesn't exist.
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return;
        }

        if (!$sectioninfo->uservisible) {
            if (!$course->hiddensections) {
                echo $this->start_section_list();
                echo $this->section_hidden($displaysection);
                echo $this->end_section_list();
            }
            // Can't view this section.
            return;
        }

        //*****************LINKS*************//
        $this->print_menu_acitivity($course);
        echo html_writer::tag('div','',array('class'=>'clearfix'));
        $this->print_banner($course);
        $this->print_othercourse($course);
      
        /*** USER PROFILE**/
        $this->print_profile_block();

        $this->print_noticeboard($course);

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);

        $thissection = $modinfo->get_section_info(0);
        if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
            echo $this->start_section_list();
            echo $this->section_header($thissection, $course, true, $displaysection);
            echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
            echo $this->courserenderer->course_section_add_cm_control($course, $thissection->section, $displaysection);
            echo $this->section_footer();
            echo $this->end_section_list();
        }

        // Start single-section div.
        echo html_writer::start_tag('div', array('class' => 'single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
        $sectiontitle = '';
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation header headingblock'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        // Title attributes.
        $titleattr = 'mdl-align title';
        if (!$thissection->visible) {
            $titleattr .= ' dimmed_text';
        }
        $sectiontitle .= html_writer::tag('div', get_section_name($course, $thissection), array('class' => $titleattr));
        $sectiontitle .= html_writer::end_tag('div');



        echo $sectiontitle;
        // Now the list of sections..
        echo $this->start_section_list();

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);
        echo $this->section_header($thissection, $course, true, $displaysection);

        // Show completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();

        echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
        echo $this->courserenderer->course_section_add_cm_control($course, $thissection->section, $displaysection);
        echo $this->section_footer();
        echo $this->end_section_list();

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection),
            array('class' => 'mdl-align'));
        $sectionbottomnav .= html_writer::end_tag('div');
        echo $sectionbottomnav;

        // Close single-section div.
        echo html_writer::end_tag('div');
    }


    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        global $PAGE,$USER;

        $userisediting = $PAGE->user_is_editing();
        $context = context_course::instance($course->id);
        $modinfo = get_fast_modinfo($course);
        $this->courseformat = course_get_format($course);
        $course = $this->courseformat->get_course();
        if (empty($this->tcsettings)) {
            $this->tcsettings = $this->courseformat->get_settings();
        }


        // Title with completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();
        echo $this->output->heading($this->page_title(), 2, 'accesshide');

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, 0);

        // Now the list of sections..
        $this->tccolumnwidth = 100; // Reset to default.


        //*****************LINKS*************//
        $this->print_menu_acitivity($course);
        $this->print_profile_block();
        echo html_writer::tag('div','',array('class'=>'clearfix'));
        $this->print_banner($course);
        $this->print_othercourse($course);
        /*** USER PROFILE**/

        
        echo $this->start_section_list();

        $sections = $modinfo->get_section_info_all();
        // General section if non-empty.
        $thissection = $sections[0];
        unset($sections[0]);
        if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
            echo $this->section_header($thissection, $course, false, 0);
            echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
            echo $this->courserenderer->course_section_add_cm_control($course, $thissection->section, 0);
            echo $this->section_footer();
            $this->print_noticeboard($course);
          
        }

        if ($course->numsections > 0) {
            if ($course->numsections > 1) {
                if ($PAGE->user_is_editing() || $course->coursedisplay != COURSE_DISPLAY_MULTIPAGE) {
                    // Collapsed Topics all toggles.
                    echo $this->toggle_all();
                }
            }
            $currentsectionfirst = false;
            if ($this->tcsettings['layoutstructure'] == 4) {
                $currentsectionfirst = true;
            }

            if (($this->tcsettings['layoutstructure'] != 3) || ($userisediting)) {
                $section = 1;
            } else {
                $timenow = time();
                $weekofseconds = 604800;
                $course->enddate = $course->startdate + ($weekofseconds * $course->numsections);
                $section = $course->numsections;
                $weekdate = $course->enddate;      // This should be 0:00 Monday of that week.
                $weekdate -= 7200;                 // Subtract two hours to avoid possible DST problems.
            }

            $numsections = $course->numsections; // Because we want to manipulate this for column breakpoints.
            if (($this->tcsettings['layoutstructure'] == 3) && ($userisediting == false)) {
                $loopsection = 1;
                $numsections = 0;
                while ($loopsection <= $course->numsections) {
                    $nextweekdate = $weekdate - ($weekofseconds);
                    if ((($thissection->uservisible ||
                            ($thissection->visible && !$thissection->available && $thissection->showavailability))
                            && ($nextweekdate <= $timenow)) == true) {
                        $numsections++; // Section not shown so do not count in columns calculation.
                    }
                    $weekdate = $nextweekdate;
                    $section--;
                    $loopsection++;
                }
                // Reset.
                $section = $course->numsections;
                $weekdate = $course->enddate;      // This should be 0:00 Monday of that week.
                $weekdate -= 7200;                 // Subtract two hours to avoid possible DST problems.
            }

            if ($numsections < $this->tcsettings['layoutcolumns']) {
                $this->tcsettings['layoutcolumns'] = $numsections;  // Help to ensure a reasonable display.
            }
            if (($this->tcsettings['layoutcolumns'] > 1) && ($this->mobiletheme === false)) {
                if ($this->tcsettings['layoutcolumns'] > 4) {
                    // Default in config.php (and reset in database) or database has been changed incorrectly.
                    $this->tcsettings['layoutcolumns'] = 4;

                    // Update....
                    $this->courseformat->update_stunning_columns_setting($this->tcsettings['layoutcolumns']);
                }

                if (($this->tablettheme === true) && ($this->tcsettings['layoutcolumns'] > 2)) {
                    // Use a maximum of 2 for tablets.
                    $this->tcsettings['layoutcolumns'] = 2;
                }

                $this->tccolumnwidth = 100 / $this->tcsettings['layoutcolumns'];
                $this->tccolumnwidth -= 1; // Allow for the padding in %.
                $this->tccolumnpadding = 2; // 'px'.
            } else if ($this->tcsettings['layoutcolumns'] < 1) {
                // Distributed default in plugin settings (and reset in database) or database has been changed incorrectly.
                $this->tcsettings['layoutcolumns'] = 1;

                // Update....
                $this->courseformat->update_stunning_columns_setting($this->tcsettings['layoutcolumns']);
            }

            echo $this->end_section_list();
            echo $this->start_toggle_section_list();

            $loopsection = 1;
            $canbreak = false; // Once the first section is shown we can decide if we break on another column.
            $columncount = 1;
            $columnbreakpoint = 0;
            $shownsectioncount = 0;

            if ($this->userpreference != null) {
                $this->isoldtogglepreference = $this->togglelib->is_old_preference($this->userpreference);
                if ($this->isoldtogglepreference == true) {
                    $ts1 = base_convert(substr($this->userpreference, 0, 6), 36, 2);
                    $ts2 = base_convert(substr($this->userpreference, 6, 12), 36, 2);
                    $thesparezeros = "00000000000000000000000000";
                    if (strlen($ts1) < 26) {
                        // Need to PAD.
                        $ts1 = substr($thesparezeros, 0, (26 - strlen($ts1))) . $ts1;
                    }
                    if (strlen($ts2) < 27) {
                        // Need to PAD.
                        $ts2 = substr($thesparezeros, 0, (27 - strlen($ts2))) . $ts2;
                    }
                    $tb = $ts1 . $ts2;
                } else {
                    // Check we have enough digits for the number of toggles in case this has increased.
                    $numdigits = $this->togglelib->get_required_digits($course->numsections);
                    if ($numdigits > strlen($this->userpreference)) {
                        if ($this->defaultuserpreference == 0) {
                            $dchar = $this->togglelib->get_min_digit();
                        } else {
                            $dchar = $this->togglelib->get_max_digit();
                        }
                        for ($i = strlen($this->userpreference); $i < $numdigits; $i++) {
                            $this->userpreference .= $dchar;
                        }
                    }
                    $this->togglelib->set_toggles($this->userpreference);
                }
            } else {
                $numdigits = $this->togglelib->get_required_digits($course->numsections);
                if ($this->defaultuserpreference == 0) {
                    $dchar = $this->togglelib->get_min_digit();
                } else {
                    $dchar = $this->togglelib->get_max_digit();
                }
                $this->userpreference = '';
                for ($i = 0; $i < $numdigits; $i++) {
                    $this->userpreference .= $dchar;
                }
                $this->togglelib->set_toggles($this->userpreference);
            }

            while ($loopsection <= $course->numsections) {
                if (($this->tcsettings['layoutstructure'] == 3) && ($userisediting == false)) {
                    $nextweekdate = $weekdate - ($weekofseconds);
                }
                $thissection = $modinfo->get_section_info($section);

                /* Show the section if the user is permitted to access it, OR if it's not available
                   but showavailability is turned on. */
                if (($this->tcsettings['layoutstructure'] != 3) || ($userisediting)) {
                    $showsection = $thissection->uservisible ||
                            ($thissection->visible && !$thissection->available && $thissection->showavailability);
                } else {
                    $showsection = ($thissection->uservisible ||
                            ($thissection->visible && !$thissection->available && $thissection->showavailability))
                            && ($nextweekdate <= $timenow);
                }
                if (($currentsectionfirst == true) && ($showsection == true)) {
                    // Show  the section if we were meant to and it is the current section:....
                    $showsection = ($course->marker == $section);
                } else if (($this->tcsettings['layoutstructure'] == 4) && ($course->marker == $section)) {
                    $showsection = false; // Do not reshow current section.
                }
                if (!$showsection) {
                    // Hidden section message is overridden by 'unavailable' control (showavailability option).
                    if ($this->tcsettings['layoutstructure'] != 4) {
                        if (($this->tcsettings['layoutstructure'] != 3) || ($userisediting)) {
                            if (!$course->hiddensections && $thissection->available) {
                                echo $this->section_hidden($section);
                            }
                        }
                    }
                } else {
                    $shownsectioncount++;
                    if (!$PAGE->user_is_editing() && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                        // Display section summary only.
                        echo $this->section_summary($thissection, $course, null);
                    } else {
                        if ($this->isoldtogglepreference == true) {
                            $togglestate = substr($tb, $section, 1);
                            if ($togglestate == '1') {
                                $thissection->toggle = true;
                            } else {
                                $thissection->toggle = false;
                            }
                        } else {
                            $thissection->toggle = $this->togglelib->get_toggle_state($thissection->section);
                        }
                        echo $this->section_header($thissection, $course, false, 0);
                        if ($thissection->uservisible) {
                            echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                            echo $this->courserenderer->course_section_add_cm_control($course, $thissection->section, 0);
                        }
                        echo html_writer::end_tag('div');
                        echo $this->section_footer();
                    }
                }

                if ($currentsectionfirst == false) {

                    unset($sections[$section]);
                }
                if (($this->tcsettings['layoutstructure'] != 3) || ($userisediting)) {
                    $section++;
                } else {
                    $section--;
                    if (($this->tcsettings['layoutstructure'] == 3) && ($userisediting == false)) {
                        $weekdate = $nextweekdate;
                    }
                }

                if ($this->mobiletheme === false) { // Only break in non-mobile themes.
                    if ($this->tcsettings['layoutcolumnorientation'] == 1) {  // Only break columns in horizontal mode.
                        if (($canbreak == false) && ($currentsectionfirst == false) && ($showsection == true)) {
                            $canbreak = true;
                            $columnbreakpoint = ($shownsectioncount + ($numsections / $this->tcsettings['layoutcolumns'])) - 1;
                            if ($this->tcsettings['layoutstructure'] == 4) {
                                $columnbreakpoint -= 1;
                            }
                        }

                        if (($currentsectionfirst == false) && ($canbreak == true) && ($shownsectioncount >= $columnbreakpoint) &&
                            ($columncount < $this->tcsettings['layoutcolumns'])) {
                            echo $this->end_section_list();
                            echo $this->start_toggle_section_list();
                            $columncount++;
                            // Next breakpoint is...
                            $columnbreakpoint += $numsections / $this->tcsettings['layoutcolumns'];
                        }
                    }
                }

                $loopsection++;
                if (($currentsectionfirst == true) && ($loopsection > $course->numsections)) {
                    // Now show the rest.
                    $currentsectionfirst = false;
                    $loopsection = 1;
                    $section = 1;
                }
                if ($section > $course->numsections) {
                    // Activities inside this section are 'orphaned', this section will be printed as 'stealth' below.
                    continue;
                }
            }
        }

        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $course->numsections or empty($modinfo->sections[$section])) {
                    // This is not stealth section or it is empty.
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->courserenderer->course_section_cm_list($course, $thissection->section, 0);
                echo $this->stealth_section_footer();
            }

            echo $this->end_section_list();

            echo html_writer::start_tag('div', array('id' => 'changenumsections', 'class' => 'mdl-right'));

            // Increase number of sections.
            $straddsection = get_string('increasesections', 'moodle');
            $url = new moodle_url('/course/changenumsections.php',
                            array('courseid' => $course->id,
                                'increase' => true,
                                'sesskey' => sesskey()));
            $icon = $this->output->pix_icon('t/switch_plus', $straddsection);
            echo html_writer::link($url, $icon . get_accesshide($straddsection), array('class' => 'increase-sections'));

            if ($course->numsections > 0) {
                // Reduce number of sections sections.
                $strremovesection = get_string('reducesections', 'moodle');
                $url = new moodle_url('/course/changenumsections.php',
                                array('courseid' => $course->id,
                                    'increase' => false,
                                    'sesskey' => sesskey()));
                $icon = $this->output->pix_icon('t/switch_minus', $strremovesection);
                echo html_writer::link($url, $icon . get_accesshide($strremovesection), array('class' => 'reduce-sections'));
            }

            echo html_writer::end_tag('div');
        } else {
            echo $this->end_section_list();
        }
    }

 
    public function toggle_all() {
        $o = '';

        // Toggle all.
        $o .= html_writer::start_tag('li', array('class' => 'tcsection main clearfix', 'id' => 'toggle-all'));

        if (($this->mobiletheme === false) || ($this->tablettheme === false)) {
            $o.= html_writer::tag('div', $this->output->spacer(), array('class' => 'left side'));
        }
        $o .= html_writer::tag('div', $this->output->spacer(), array('class' => 'right side'));

        $o .= html_writer::start_tag('div', array('class' => 'content'));
        $iconsetclass = ' toggle-'.$this->tcsettings['toggleiconset'];
        if ($this->tcsettings['toggleallhover'] == 2) {
            $iconsetclass .= '-hover'.$iconsetclass;
        }
        $o .= html_writer::start_tag('div', array('class' => 'sectionbody'.$iconsetclass));
        $o .= html_writer::start_tag('h4', null);
        $o .= html_writer::tag('a', get_string('topcollopened', 'format_stunning'),
                               array('class' => 'on', 'href' => '#', 'id' => 'toggles-all-opened'));
        $o .= html_writer::tag('a', get_string('topcollclosed', 'format_stunning'),
                               array('class' => 'off', 'href' => '#', 'id' => 'toggles-all-closed'));
        $o .= html_writer::end_tag('h4');
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');

        return $o;
    }

    public function set_portable($portable) {
        switch ($portable) {
            case 1:
                $this->mobiletheme = true;
            break;
            case 2:
                $this->tablettheme = true;
            break;
            default:
                $this->mobiletheme = false;
                $this->tablettheme = false;
            break;
        }
    }

    public function set_user_preference($preference) {
        $this->userpreference = $preference;
    }

    public function set_default_user_preference($defaultpreference) {
        $this->defaultuserpreference = $defaultpreference;
    }



}
