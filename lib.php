<?php

require_once($CFG->dirroot . '/course/format/lib.php'); // For format_base.

class format_stunning extends format_base {

    private $settings;

    /**
     * Returns the format's settings and gets them if they do not exist.
     * @return type The settings as an array.
     */
    public function get_settings() {
        if (empty($this->settings) == true) {
            $this->settings = $this->get_format_options();
        }
        return $this->settings;
    }

    /**
     * Indicates this format uses sections.
     *
     * @return bool Returns true
     */
    public function uses_sections() {
        return true;
    }


    public function get_section_name($section) {
        $course = $this->get_course();
        // Don't add additional text as called in creating the navigation.
        return $this->get_stunning_section_name($course, $section, false);
    }


    public function get_stunning_section_name($course, $section, $additional) {
        $thesection = $this->get_section($section);
        if (is_null($thesection)) {
            $thesection = new stdClass;
            $thesection->name = '';
            if (is_object($section)) {
                $thesection->section = $section->section;
            } else {
                $thesection->section = $section;
            }
        }
        $o = '';
        $tcsettings = $this->get_settings();
        $coursecontext = context_course::instance($course->id);

        // We can't add a node without any text.
        if ((string) $thesection->name !== '') {
            $o .= format_string($thesection->name, true, array('context' => $coursecontext));
            if (($tcsettings['layoutstructure'] == 2) || ($tcsettings['layoutstructure'] == 3) ||
                ($tcsettings['layoutstructure'] == 5)) {
                $o .= ' ';
                if ($additional == true) { // br tags break backups!
                    $o .= html_writer::empty_tag('br');
                }
                $o .= $this->get_section_dates($section, $course, $tcsettings);
            }
        } else if ($thesection->section == 0) {
            $o = get_string('section0name', 'format_stunning');
        } else {
            if (($tcsettings['layoutstructure'] == 1) || ($tcsettings['layoutstructure'] == 4)) {
                $o = get_string('sectionname', 'format_stunning') . ' ' . $thesection->section;
            } else {
                $o = $this->get_section_dates($thesection, $course, $tcsettings);
            }
        }


        if (($additional == true) && ($thesection->section != 0)) {
            switch ($tcsettings['layoutelement']) {
                case 1:
                case 2:
                case 3:
                case 4:
                    $o .= ' - ' . get_string('topcolltoggle', 'format_stunning'); // The word 'Toggle'.
                    break;
            }
        }

        return $o;
    }

    public function get_section_dates($section, $course, $tcsettings) {
        $dateformat = get_string('strftimedateshort');
        $o = '';
        if ($tcsettings['layoutstructure'] == 5) {
            $day = $this->format_stunning_get_section_day($section, $course);

            $weekday = userdate($day, $dateformat);
            $o = $weekday;
        } else {
            $dates = $this->format_stunning_get_section_dates($section, $course);

            // We subtract 24 hours for display purposes.
            $dates->end = ($dates->end - 86400);

            $weekday = userdate($dates->start, $dateformat);
            $endweekday = userdate($dates->end, $dateformat);
            $o = $weekday . ' - ' . $endweekday;
        }
        return $o;
    }


    public function get_view_url($section, $options = array()) {
        $course = $this->get_course();
        $url = new moodle_url('/course/view.php', array('id' => $course->id));

        $sr = null;
        if (array_key_exists('sr', $options)) {
            $sr = $options['sr'];
        }
        if (is_object($section)) {
            $sectionno = $section->section;
        } else {
            $sectionno = $section;
        }
        if ($sectionno !== null) {
            if ($sr !== null) {
                if ($sr) {
                    $usercoursedisplay = COURSE_DISPLAY_MULTIPAGE;
                    $sectionno = $sr;
                } else {
                    $usercoursedisplay = COURSE_DISPLAY_SINGLEPAGE;
                }
            } else {
                $usercoursedisplay = $course->coursedisplay;
            }
            if ($sectionno != 0 && $usercoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                $url->param('section', $sectionno);
            } else {
                if (!empty($options['navigation'])) {
                    return null;
                }
                $url->set_anchor('section-' . $sectionno);
            }
        }
        return $url;
    }


    public function supports_ajax() {
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = true;
        $ajaxsupport->testedbrowsers = array('MSIE' => 8.0, 'Gecko' => 20061111, 'Opera' => 9.0, 'Safari' => 531, 'Chrome' => 6.0);
        return $ajaxsupport;
    }


    public function ajax_section_move() {
        $titles = array();
        $current = -1;  // MDL-33546.
        $weekformat = false;
        $tcsettings = $this->get_settings();
        if (($tcsettings['layoutstructure'] == 2) || ($tcsettings['layoutstructure'] == 3) ||
            ($tcsettings['layoutstructure'] == 5)) {
            $weekformat = true;
        }
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course);
        if ($sections = $modinfo->get_section_info_all()) {
            foreach ($sections as $number => $section) {
                $titles[$number] = $this->get_stunning_section_name($course, $section, true);
                if (($weekformat == true) && ($this->is_section_current($section))) {
                    $current = $number;  // Only set if a week based course to keep the current week in the same place.
                }
            }
        }
        return array('sectiontitles' => $titles, 'current' => $current, 'action' => 'move');
    }


    public function get_default_blocks() {
        return array(
            BLOCK_POS_LEFT => array(),
            BLOCK_POS_RIGHT => array('search_forums', 'news_items', 'calendar_upcoming', 'recent_activity')
        );
    }


    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;

        if ($courseformatoptions === false) {
            $courseconfig = get_config('moodlecourse');
            $courseformatoptions = array(
                'numsections' => array(
                    'default' => $courseconfig->numsections,
                    'type' => PARAM_INT,
                ),
                'hiddensections' => array(
                    'default' => $courseconfig->hiddensections,
                    'type' => PARAM_INT,
                ),
                'coursedisplay' => array(
                    'default' => get_config('format_stunning', 'defaultcoursedisplay'),
                    'type' => PARAM_INT,
                ),
                'extraelements' => array(
                    'default' => get_config('format_stunning', 'extraelements'),
                    'type' => PARAM_INT,
                ),
                'layoutelement' => array(
                    'default' => get_config('format_stunning', 'defaultlayoutelement'),
                    'type' => PARAM_INT,
                ),
                'layoutstructure' => array(
                    'default' => get_config('format_stunning', 'defaultlayoutstructure'),
                    'type' => PARAM_INT,
                ),
                'layoutcolumns' => array(
                    'default' => get_config('format_stunning', 'defaultlayoutcolumns'),
                    'type' => PARAM_INT,
                ),
                'layoutcolumnorientation' => array(
                    'default' => get_config('format_stunning', 'defaultlayoutcolumnorientation'),
                    'type' => PARAM_INT,
                ),
                'togglealignment' => array(
                    'default' => get_config('format_stunning', 'defaulttogglealignment'),
                    'type' => PARAM_INT,
                ),
                'toggleiconposition' => array(
                    'default' => get_config('format_stunning', 'defaulttoggleiconposition'),
                    'type' => PARAM_INT,
                ),
                'toggleiconset' => array(
                    'default' => get_config('format_stunning', 'defaulttoggleiconset'),
                    'type' => PARAM_ALPHA,
                ),
                'toggleallhover' => array(
                    'default' => get_config('format_stunning', 'defaulttoggleallhover'),
                    'type' => PARAM_INT,
                ),
                'toggleforegroundcolour' => array(
                    'default' => get_config('format_stunning', 'defaulttgfgcolour'),
                    'type' => PARAM_ALPHANUM,
                ),
                'togglebackgroundcolour' => array(
                    'default' => get_config('format_stunning', 'defaulttgbgcolour'),
                    'type' => PARAM_ALPHANUM,
                ),
                'togglebackgroundhovercolour' => array(
                    'default' => get_config('format_stunning', 'defaulttgbghvrcolour'),
                    'type' => PARAM_ALPHANUM,
                ),
            );
        }
        if ($foreditform && !isset($courseformatoptions['coursedisplay']['label'])) {
            global $COURSE;
            $coursecontext = context_course::instance($COURSE->id);

            $courseconfig = get_config('moodlecourse');
            $sectionmenu = array();
            for ($i = 0; $i <= $courseconfig->maxsections; $i++) {
                $sectionmenu[$i] = "$i";
            }
            $courseformatoptionsedit = array(
                'numsections' => array(
                    'label' => new lang_string('numbersections', 'format_stunning'),
                    'element_type' => 'select',
                    'element_attributes' => array($sectionmenu),
                ),
                'hiddensections' => array(
                    'label' => new lang_string('hiddensections'),
                    'help' => 'hiddensections',
                    'help_component' => 'moodle',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(0 => new lang_string('hiddensectionscollapsed'),
                              1 => new lang_string('hiddensectionsinvisible')
                        )
                    ),
                ),
                'coursedisplay' => array(
                    'label' => new lang_string('coursedisplay'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single'),
                            COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi')
                        )
                    ),
                    'help' => 'coursedisplay',
                    'help_component' => 'moodle',
                )
            );
            if (has_capability('format/stunning:changelayout', $coursecontext)) {
                $courseformatoptionsedit['layoutelement'] = array(
                    'label' => new lang_string('setlayoutelements', 'format_stunning'),
                    'help' => 'setlayoutelements',
                    'help_component' => 'format_stunning',
                    'element_type' => 'select',
                    'element_attributes' => array( // In insertion order and not numeric for sorting purposes.
                        array(1 => new lang_string('setlayout_all', 'format_stunning'),                             // Toggle word, toggle section x and section number.
                              3 => new lang_string('setlayout_toggle_word_section_x', 'format_stunning'),           // Toggle word and toggle section x.
                              2 => new lang_string('setlayout_toggle_word_section_number', 'format_stunning'),      // Toggle word and section number.
                              5 => new lang_string('setlayout_toggle_section_x_section_number', 'format_stunning'), // Toggle section x and section number.
                              4 => new lang_string('setlayout_toggle_word', 'format_stunning'),                     // Toggle word.
                              8 => new lang_string('setlayout_toggle_section_x', 'format_stunning'),                // Toggle section x.
                              6 => new lang_string('setlayout_section_number', 'format_stunning'),                  // Section number.
                              7 => new lang_string('setlayout_no_additions', 'format_stunning'))                    // No additions.
                    )
                );
                $courseformatoptionsedit['extraelements'] = array(
                    'label' => new lang_string('setextraelements', 'format_stunning'),
                    'help' => 'setextraelements',
                    'help_component' => 'format_stunning',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('setnotice', 'format_stunning'),                            
                              2 => new lang_string('setprofile', 'format_stunning'),          
                              3 => new lang_string('setnotice_profile', 'format_stunning'),          
                              4 => new lang_string('set_noelements', 'format_stunning'))
                    )
                );
                $courseformatoptionsedit['layoutstructure'] = array(
                    'label' => new lang_string('setlayoutstructure', 'format_stunning'),
                    'help' => 'setlayoutstructure',
                    'help_component' => 'format_stunning',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('setlayoutstructuretopic', 'format_stunning'),             // Topic.
                              2 => new lang_string('setlayoutstructureweek', 'format_stunning'),              // Week.
                              3 => new lang_string('setlayoutstructurelatweekfirst', 'format_stunning'),      // Latest Week First.
                              4 => new lang_string('setlayoutstructurecurrenttopicfirst', 'format_stunning'), // Current Topic First.
                              5 => new lang_string('setlayoutstructureday', 'format_stunning'))               // Day.
                    )
                );
                $courseformatoptionsedit['layoutcolumns'] = array(
                    'label' => new lang_string('setlayoutcolumns', 'format_stunning'),
                    'help' => 'setlayoutcolumns',
                    'help_component' => 'format_stunning',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('one', 'format_stunning'),   // Default.
                              2 => new lang_string('two', 'format_stunning'),   // Two.
                              3 => new lang_string('three', 'format_stunning'), // Three.
                              4 => new lang_string('four', 'format_stunning'))  // Four.
                    )
                );
                $courseformatoptionsedit['layoutcolumnorientation'] = array(
                    'label' => new lang_string('setlayoutcolumnorientation', 'format_stunning'),
                    'help' => 'setlayoutcolumnorientation',
                    'help_component' => 'format_stunning',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('columnvertical', 'format_stunning'),
                              2 => new lang_string('columnhorizontal', 'format_stunning')) // Default.
                    )
                );
                $courseformatoptionsedit['toggleiconposition'] = array(
                    'label' => new lang_string('settoggleiconposition', 'format_stunning'),
                    'help' => 'settoggleiconposition',
                    'help_component' => 'format_stunning',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('left', 'format_stunning'),   // Left.
                              2 => new lang_string('right', 'format_stunning'))  // Right.
                    )
                );
            } else {
                $courseformatoptionsedit['layoutelement'] =
                    array('label' => new lang_string('setlayoutelements', 'format_stunning'), 'element_type' => 'hidden');
                $courseformatoptionsedit['layoutstructure'] =
                    array('label' => new lang_string('setlayoutstructure', 'format_stunning'), 'element_type' => 'hidden');
                $courseformatoptionsedit['layoutcolumns'] =
                    array('label' => new lang_string('setlayoutcolumns', 'format_stunning'), 'element_type' => 'hidden');
                $courseformatoptionsedit['layoutcolumnorientation'] =
                    array('label' => new lang_string('setlayoutcolumnorientation', 'format_stunning'), 'element_type' => 'hidden');
                $courseformatoptionsedit['toggleiconposition'] =
                    array('label' => new lang_string('settoggleiconposition', 'format_stunning'), 'element_type' => 'hidden');
            }

            if (has_capability('format/stunning:changetogglealignment', $coursecontext)) {
                $courseformatoptionsedit['togglealignment'] = array(
                    'label' => new lang_string('settogglealignment', 'format_stunning'),
                    'help' => 'settogglealignment',
                    'help_component' => 'format_stunning',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('left', 'format_stunning'),   // Left.
                              2 => new lang_string('center', 'format_stunning'), // Centre.
                              3 => new lang_string('right', 'format_stunning'))  // Right.
                    )
                );
            } else {
                $courseformatoptionsedit['togglealignment'] =
                    array('label' => new lang_string('settogglealignment', 'format_stunning'), 'element_type' => 'hidden');
            }

            if (has_capability('format/stunning:changetoggleiconset', $coursecontext)) {
                $courseformatoptionsedit['toggleiconset'] = array(
                    'label' => new lang_string('settoggleiconset', 'format_stunning'),
                    'help' => 'settoggleiconset',
                    'help_component' => 'format_stunning',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array('arrow' => new lang_string('arrow', 'format_stunning'), // Arrow icon set.
                              'point' => new lang_string('point', 'format_stunning'), // Point icon set.
                              'power' => new lang_string('power', 'format_stunning')) // Power icon set.
                    )
                );
                $courseformatoptionsedit['toggleallhover'] = array(
                    'label' => new lang_string('settoggleallhover', 'format_stunning'),
                    'help' => 'settoggleallhover',
                    'help_component' => 'format_stunning',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('no'),
                              2 => new lang_string('yes'))
                    )
                );
            } else {
                $courseformatoptionsedit['toggleiconset'] =
                    array('label' => new lang_string('settoggleiconset', 'format_stunning'), 'element_type' => 'hidden');
                $courseformatoptionsedit['toggleallhover'] =
                    array('label' => new lang_string('settoggleallhover', 'format_stunning'), 'element_type' => 'hidden');
            }

            if (has_capability('format/stunning:changecolour', $coursecontext)) {
                $courseformatoptionsedit['toggleforegroundcolour'] = array(
                    'label' => new lang_string('settoggleforegroundcolour', 'format_stunning'),
                    'help' => 'settoggleforegroundcolour',
                    'help_component' => 'format_stunning',
                    'element_type' => 'stcolourpopup',
                    'element_attributes' => array(
                        array('tabindex' => -1, 'value' => get_config('format_stunning', 'defaulttgfgcolour'))
                    )
                );
                $courseformatoptionsedit['togglebackgroundcolour'] = array(
                    'label' => new lang_string('settogglebackgroundcolour', 'format_stunning'),
                    'help' => 'settogglebackgroundcolour',
                    'help_component' => 'format_stunning',
                    'element_type' => 'stcolourpopup',
                    'element_attributes' => array(
                        array('tabindex' => -1, 'value' => get_config('format_stunning', 'defaulttgbgcolour'))
                    )
                );
                $courseformatoptionsedit['togglebackgroundhovercolour'] = array(
                    'label' => new lang_string('settogglebackgroundhovercolour', 'format_stunning'),
                    'help' => 'settogglebackgroundhovercolour',
                    'help_component' => 'format_stunning',
                    'element_type' => 'stcolourpopup',
                    'element_attributes' => array(
                        array('tabindex' => -1, 'value' => get_config('format_stunning', 'defaulttgbghvrcolour'))
                    )
                );
            } else {
                $courseformatoptionsedit['toggleforegroundcolour'] =
                    array('label' => new lang_string('settoggleforegroundcolour', 'format_stunning'), 'element_type' => 'hidden');
                $courseformatoptionsedit['togglebackgroundcolour'] =
                    array('label' => new lang_string('settogglebackgroundcolour', 'format_stunning'), 'element_type' => 'hidden');
                $courseformatoptionsedit['togglebackgroundhovercolour'] =
                    array('label' => new lang_string('settogglebackgroundhovercolour', 'format_stunning'), 'element_type' => 'hidden');
            }
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
    }

    /**
     * Adds format options elements to the course/section edit form
     *
     * This function is called from {@link course_edit_form::definition_after_data()}
     *
     * @param MoodleQuickForm $mform form the elements are added to
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form
     * @return array array of references to the added form elements
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
        global $CFG;
        MoodleQuickForm::registerElementType('stcolourpopup', "$CFG->dirroot/course/format/stunning/js/st_colourpopup.php",
                                             'MoodleQuickForm_stcolourpopup');

        $elements = parent::create_edit_form_elements($mform, $forsection);
        if ($forsection == false) {
            global $COURSE, $USER;
            /*
             Increase the number of sections combo box values if the user has increased the number of sections
             using the icon on the course page beyond course 'maxsections' or course 'maxsections' has been
             reduced below the number of sections already set for the course on the site administration course
             defaults page.  This is so that the number of sections is not reduced leaving unintended orphaned
             activities / resources.
             */
            $maxsections = get_config('moodlecourse', 'maxsections');
            $numsections = $mform->getElementValue('numsections');
            $numsections = $numsections[0];
            if ($numsections > $maxsections) {
                $element = $mform->getElement('numsections');
                for ($i = $maxsections+1; $i <= $numsections; $i++) {
                    $element->addOption("$i", $i);
                }
            }

            $coursecontext = context_course::instance($COURSE->id);

            $changelayout = has_capability('format/stunning:changelayout', $coursecontext);
            $changecolour = has_capability('format/stunning:changecolour', $coursecontext);
            $changetogglealignment = has_capability('format/stunning:changetogglealignment', $coursecontext);
            $changetoggleiconset = has_capability('format/stunning:changetoggleiconset', $coursecontext);
            $resetall = is_siteadmin($USER); // Site admins only.

            if ($changelayout || $changecolour || $changetogglealignment || $changetoggleiconset || $resetall) {
                $elements[] = $mform->addElement('header', 'ctreset', get_string('ctreset', 'format_stunning'));
            }

            if ($changelayout) {
                $mform->addHelpButton('ctreset', 'ctreset', 'format_stunning', '', true);
                $elements[] = $mform->addElement('checkbox', 'resetlayout', get_string('resetlayout', 'format_stunning'), false);
                $mform->addHelpButton('resetlayout', 'resetlayout', 'format_stunning', '', true);
            }

            if ($changecolour) {
                $elements[] = $mform->addElement('checkbox', 'resetcolour', get_string('resetcolour', 'format_stunning'), false);
                $mform->addHelpButton('resetcolour', 'resetcolour', 'format_stunning', '', true);
            }

            if ($changetogglealignment) {
                $elements[] = $mform->addElement('checkbox', 'resettogglealignment', get_string('resettogglealignment', 'format_stunning'), false);
                $mform->addHelpButton('resettogglealignment', 'resettogglealignment', 'format_stunning', '', true);
            }

            if ($changetoggleiconset) {
                $elements[] = $mform->addElement('checkbox', 'resettoggleiconset', get_string('resettoggleiconset', 'format_stunning'), false);
                $mform->addHelpButton('resettoggleiconset', 'resettoggleiconset', 'format_stunning', '', true);
            }

            if ($resetall) {
                $elements[] = $mform->addElement('checkbox', 'resetalllayout', get_string('resetalllayout', 'format_stunning'), false);
                $mform->addHelpButton('resetalllayout', 'resetalllayout', 'format_stunning', '', true);

                $elements[] = $mform->addElement('checkbox', 'resetallcolour', get_string('resetallcolour', 'format_stunning'), false);
                $mform->addHelpButton('resetallcolour', 'resetallcolour', 'format_stunning', '', true);

                $elements[] = $mform->addElement('checkbox', 'resetalltogglealignment', get_string('resetalltogglealignment', 'format_stunning'), false);
                $mform->addHelpButton('resetalltogglealignment', 'resetalltogglealignment', 'format_stunning', '', true);

                $elements[] = $mform->addElement('checkbox', 'resetalltoggleiconset', get_string('resetalltoggleiconset', 'format_stunning'), false);
                $mform->addHelpButton('resetalltoggleiconset', 'resetalltoggleiconset', 'format_stunning', '', true);
            }
        }

        return $elements;
    }

    public function update_course_format_options($data, $oldcourse = null) {
        global $DB; // MDL-37976.

        $resetlayout = false;
        $resetcolour = false;
        $resettogglealignment = false;
        $resettoggleiconset = false;
        $resetalllayout = false;
        $resetallcolour = false;
        $resetalltogglealignment = false;
        $resetalltoggleiconset = false;
        if (isset($data->resetlayout) == true) {
            $resetlayout = true;
            unset($data->resetlayout);
        }
        if (isset($data->resetcolour) == true) {
            $resetcolour = true;
            unset($data->resetcolour);
        }
        if (isset($data->resetalllayout) == true) {
            $resetalllayout = true;
            unset($data->resetalllayout);
        }
        if (isset($data->resettogglealignment) == true) {
            $resettogglealignment = true;
            unset($data->resettogglealignment);
        }
        if (isset($data->resettoggleiconset) == true) {
            $resettoggleiconset = true;
            unset($data->resettoggleiconset);
        }
        if (isset($data->resetallcolour) == true) {
            $resetallcolour = true;
            unset($data->resetallcolour);
        }
        if (isset($data->resetalltogglealignment) == true) {
            $resetalltogglealignment = true;
            unset($data->resetalltogglealignment);
        }
        if (isset($data->resetalltoggleiconset) == true) {
            $resetalltoggleiconset = true;
            unset($data->resetalltoggleiconset);
        }

        if ($oldcourse !== null) {
            $data = (array) $data;
            $oldcourse = (array) $oldcourse;
            $options = $this->course_format_options();
            foreach ($options as $key => $unused) {
                if (!array_key_exists($key, $data)) {
                    if (array_key_exists($key, $oldcourse)) {
                        $data[$key] = $oldcourse[$key];
                    } else if ($key === 'numsections') {
                        /* If previous format does not have the field 'numsections'
                         * and $data['numsections'] is not set,
                         * we fill it with the maximum section number from the DB */
                        $maxsection = $DB->get_field_sql('SELECT max(section) from {course_sections} WHERE course = ?', array($this->courseid));
                        if ($maxsection) {
                            // If there are no sections, or just default 0-section, 'numsections' will be set to default.
                            $data['numsections'] = $maxsection;
                        }
                    }
                }
            }
        }
        $changes = $this->update_format_options($data);

        // Now we can do the reset.
        if (($resetalllayout) || ($resetallcolour) || ($resetalltogglealignment) || ($resetalltoggleiconset)) {
            $this->reset_stunning_setting(0, $resetalllayout, $resetallcolour, $resetalltogglealignment, $resetalltoggleiconset);
            $changes = true;
        } else if (($resetlayout) || ($resetcolour) || ($resettogglealignment) || ($resettoggleiconset)) {
            $this->reset_stunning_setting($this->courseid, $resetlayout, $resetcolour, $resettogglealignment, $resettoggleiconset);
            $changes = true;
        }

        return $changes;
    }


    public function is_section_current($section) {
        $tcsettings = $this->get_settings();
        if (($tcsettings['layoutstructure'] == 2) || ($tcsettings['layoutstructure'] == 3)) {
            if ($section->section < 1) {
                return false;
            }

            $timenow = time();
            $dates = $this->format_stunning_get_section_dates($section, $this->get_course());

            return (($timenow >= $dates->start) && ($timenow < $dates->end));
        } else if ($tcsettings['layoutstructure'] == 5) {
            if ($section->section < 1) {
                return false;
            }

            $timenow = time();
            $day = $this->format_stunning_get_section_day($section, $this->get_course());
            $onedayseconds = 86400;
            return (($timenow >= $day) && ($timenow < ($day + $onedayseconds)));
        } else {
            return parent::is_section_current($section);
        }
    }


    private function format_stunning_get_section_dates($section, $course) {
        $oneweekseconds = 604800;
        /* Hack alert. We add 2 hours to avoid possible DST problems. (e.g. we go into daylight
           savings and the date changes. */
        $startdate = $course->startdate + 7200;

        $dates = new stdClass();
        if (is_object($section)) {
            $section = $section->section;
        }

        $dates->start = $startdate + ($oneweekseconds * ($section - 1));
        $dates->end = $dates->start + $oneweekseconds;

        return $dates;
    }


    private function format_stunning_get_section_day($section, $course) {
        $onedayseconds = 86400;
        /* Hack alert. We add 2 hours to avoid possible DST problems. (e.g. we go into daylight
           savings and the date changes. */
        $startdate = $course->startdate + 7200;

        if (is_object($section)) {
            $section = $section->section;
        }

        $day = $startdate + ($onedayseconds * ($section - 1));

        return $day;
    }


    public function reset_stunning_setting($courseid, $layout, $colour, $togglealignment, $toggleiconset) {
        global $DB, $USER, $COURSE;

        $coursecontext = context_course::instance($COURSE->id);

        $currentcourseid = 0;
        if ($courseid == 0) {
            $records = $DB->get_records('course_format_options', array('format' => $this->format), '', 'id,courseid');
        } else {
            $records = $DB->get_records('course_format_options', array('courseid' => $courseid, 'format' => $this->format), '', 'id,courseid');
        }

        $resetallifall = ((is_siteadmin($USER)) || ($courseid != 0)); // Will be true if reset all capability or a single course.

        $updatedata = array();
        if ($layout && has_capability('format/stunning:changelayout', $coursecontext) && $resetallifall) {
            $updatedata['coursedisplay'] = get_config('format_stunning', 'defaultcoursedisplay');
            $updatedata['layoutelement'] = get_config('format_stunning', 'defaultlayoutelement');
            $updatedata['layoutstructure'] = get_config('format_stunning', 'defaultlayoutstructure');
            $updatedata['layoutcolumns'] = get_config('format_stunning', 'defaultlayoutcolumns');
            $updatedata['layoutcolumnorientation'] = get_config('format_stunning', 'defaultlayoutcolumnorientation');
            $updatedata['toggleiconposition'] = get_config('format_stunning', 'defaulttoggleiconposition');
        }
        if ($togglealignment && has_capability('format/stunning:changetogglealignment', $coursecontext) && $resetallifall) {
            $updatedata['togglealignment'] = get_config('format_stunning', 'defaulttogglealignment');
        }
        if ($colour && has_capability('format/stunning:changecolour', $coursecontext) && $resetallifall) {
            $updatedata['toggleforegroundcolour'] = get_config('format_stunning', 'defaulttgfgcolour');
            $updatedata['togglebackgroundcolour'] = get_config('format_stunning', 'defaulttgbgcolour');
            $updatedata['togglebackgroundhovercolour'] = get_config('format_stunning', 'defaulttgbghvrcolour');
        }
        if ($toggleiconset && has_capability('format/stunning:changetoggleiconset', $coursecontext) && $resetallifall) {
            $updatedata['toggleiconset'] = get_config('format_stunning', 'defaulttoggleiconset');
            $updatedata['toggleallhover'] = get_config('format_stunning', 'defaulttoggleallhover');
        }

        foreach ($records as $record) {
            if ($currentcourseid != $record->courseid) {
                $currentcourseid = $record->courseid; // Only do once per course.
                if (($layout) || ($togglealignment) || ($colour) || ($toggleiconset)) {
                    $ourcourseid = $this->courseid;
                    $this->courseid = $currentcourseid;
                    $this->update_format_options($updatedata);
                    $this->courseid = $ourcourseid;
                }
            }
        }
    }


    public function restore_stunning_setting($courseid, $layoutelement, $layoutstructure, $layoutcolumns, $tgfgcolour, $tgbgcolour, $tgbghvrcolour) {
        $currentcourseid = $this->courseid;  // Save for later - stack data model.
        $this->courseid = $courseid;
        // Create data array.
        $data = array(
            'layoutelement' => $layoutelement,
            'layoutstructure' => $layoutstructure,
            'layoutcolumns' => $layoutcolumns,
            'toggleforegroundcolour' => $tgfgcolour,
            'togglebackgroundcolour' => $tgbgcolour,
            'togglebackgroundhovercolour' => $tgbghvrcolour);

        $this->update_course_format_options($data);

        $this->courseid = $currentcourseid;
    }

    /**
     * Updates the number of columns when the renderer detects that they are wrong.
     * @param int $layoutcolumns The layout columns to use, see tcconfig.php.
     */
    public function update_stunning_columns_setting($layoutcolumns) {
        // Create data array.
        $data = array('layoutcolumns' => $layoutcolumns);

        $this->update_course_format_options($data);
    }
    
    /*****Links for course format****/
    public function stunning_moodle_url($url, array $params = null) {
        return new moodle_url('/course/format/stunning/' . $url, $params);
    }
}



/**
 * The string that is used to describe a section of the course.
 *
 * @return string The section description.
 */
function callback_stunning_definition() {
    return get_string('sectionname', 'format_stunning');
}

/**
 * Deletes the user preference entries for the given course upon course deletion.
 * CONTRIB-3520.
 */
function format_stunning_delete_course($courseid) {
    global $DB;
    $DB->delete_records("user_preferences", array("name" => 'topcoll_toggle_' . $courseid));
}
