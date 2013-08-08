<?php


defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $name = 'format_stunning/defaultcoursedisplay';
    $title = get_string('defaultcoursedisplay', 'format_stunning');
    $description = get_string('defaultcoursedisplay_desc', 'format_stunning');
    $default = COURSE_DISPLAY_SINGLEPAGE;
    $choices = array(
        COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single'),
        COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi')
    );

    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    $name = 'format_stunning/showextraelements';
    $title = get_string('extraelements', 'format_stunning');
    $description = get_string('extraelements_detail', 'format_stunning');
    $default = 4;
    $choices = array( 
        1 => new lang_string('setnotice', 'format_stunning'),                            
        2 => new lang_string('setprofile', 'format_stunning'),          
        3 => new lang_string('setnotice_profile', 'format_stunning'),          
        4 => new lang_string('set_noelements', 'format_stunning'),          
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));


    $name = 'format_stunning/defaultlayoutelement';
    $title = get_string('defaultlayoutelement', 'format_stunning');
    $description = get_string('defaultlayoutelement_descpositive', 'format_stunning');
    $default = 1;
    $choices = array( // In insertion order and not numeric for sorting purposes.
        1 => new lang_string('setlayout_all', 'format_stunning'),                             // Toggle word, toggle section x and section number - default.
        3 => new lang_string('setlayout_toggle_word_section_x', 'format_stunning'),           // Toggle word and toggle section x.
        2 => new lang_string('setlayout_toggle_word_section_number', 'format_stunning'),      // Toggle word and section number.
        5 => new lang_string('setlayout_toggle_section_x_section_number', 'format_stunning'), // Toggle section x and section number.
        4 => new lang_string('setlayout_toggle_word', 'format_stunning'),                     // Toggle word.
        8 => new lang_string('setlayout_toggle_section_x', 'format_stunning'),                // Toggle section x.
        6 => new lang_string('setlayout_section_number', 'format_stunning'),                  // Section number.
        7 => new lang_string('setlayout_no_additions', 'format_stunning')                     // No additions.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));


    $name = 'format_stunning/defaultlayoutstructure';
    $title = get_string('defaultlayoutstructure', 'format_stunning');
    $description = get_string('defaultlayoutstructure_desc', 'format_stunning');
    $default = 1;
    $choices = array(
        1 => new lang_string('setlayoutstructuretopic', 'format_stunning'),             // Topic.
        2 => new lang_string('setlayoutstructureweek', 'format_stunning'),              // Week.
        3 => new lang_string('setlayoutstructurelatweekfirst', 'format_stunning'),      // Latest Week First.
        4 => new lang_string('setlayoutstructurecurrenttopicfirst', 'format_stunning'), // Current Topic First.
        5 => new lang_string('setlayoutstructureday', 'format_stunning')                // Day.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default number of columns between 1 and 4.
    $name = 'format_stunning/defaultlayoutcolumns';
    $title = get_string('defaultlayoutcolumns', 'format_stunning');
    $description = get_string('defaultlayoutcolumns_desc', 'format_stunning');
    $default = 1;
    $choices = array(
        1 => new lang_string('one', 'format_stunning'),   // Default.
        2 => new lang_string('two', 'format_stunning'),   // Two.
        3 => new lang_string('three', 'format_stunning'), // Three.
        4 => new lang_string('four', 'format_stunning')   // Four.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default column orientation - 1 = vertical and 2 = horizontal.
    $name = 'format_stunning/defaultlayoutcolumnorientation';
    $title = get_string('defaultlayoutcolumnorientation', 'format_stunning');
    $description = get_string('defaultlayoutcolumnorientation_desc', 'format_stunning');
    $default = 2;
    $choices = array(
        1 => new lang_string('columnvertical', 'format_stunning'),
        2 => new lang_string('columnhorizontal', 'format_stunning') // Default.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default toggle foreground colour in hexidecimal RGB without preceeding '#'.
    $name = 'format_stunning/defaulttgfgcolour';
    $title = get_string('defaulttgfgcolour', 'format_stunning');
    $description = get_string('defaulttgfgcolour_desc', 'format_stunning');
    $default = '#000000';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // Default toggle background colour in hexidecimal RGB without preceeding '#'.
    $name = 'format_stunning/defaulttgbgcolour';
    $title = get_string('defaulttgbgcolour', 'format_stunning');
    $description = get_string('defaulttgbgcolour_desc', 'format_stunning');
    $default = '#e2e2f2';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // Default toggle background hover colour in hexidecimal RGB without preceeding '#'.
    $name = 'format_stunning/defaulttgbghvrcolour';
    $title = get_string('defaulttgbghvrcolour', 'format_stunning');
    $description = get_string('defaulttgbghvrcolour_desc', 'format_stunning');
    $default = '#eeeeff';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

   
    $name = 'format_stunning/defaulttogglepersistence';
    $title = get_string('defaulttogglepersistence', 'format_stunning');
    $description = get_string('defaulttogglepersistence_desc', 'format_stunning');
    $default = 1;
    $choices = array(
        0 => new lang_string('off', 'format_stunning'), // Off.
        1 => new lang_string('on', 'format_stunning')   // On.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));


    $name = 'format_stunning/defaulttogglealignment';
    $title = get_string('defaulttogglealignment', 'format_stunning');
    $description = get_string('defaulttogglealignment_desc', 'format_stunning');
    $default = 2;
    $choices = array(
        1 => new lang_string('left', 'format_stunning'),   // Left.
        2 => new lang_string('center', 'format_stunning'), // Centre.
        3 => new lang_string('right', 'format_stunning')   // Right.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));


    $name = 'format_stunning/defaulttoggleiconposition';
    $title = get_string('defaulttoggleiconposition', 'format_stunning');
    $description = get_string('defaulttoggleiconposition_desc', 'format_stunning');
    $default = 1;
    $choices = array(
        1 => new lang_string('left', 'format_stunning'),   // Left.
        2 => new lang_string('right', 'format_stunning')   // Right.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));


    $name = 'format_stunning/defaulttoggleiconset';
    $title = get_string('defaulttoggleiconset', 'format_stunning');
    $description = get_string('defaulttoggleiconset_desc', 'format_stunning');
    $default = 'arrow';
    $choices = array(
        'arrow' => new lang_string('arrow', 'format_stunning'), // Arrow icon set.
        'point' => new lang_string('point', 'format_stunning'), // Point icon set.
        'power' => new lang_string('power', 'format_stunning')  // Power icon set.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));


    $name = 'format_stunning/defaulttoggleallhover';
    $title = get_string('defaulttoggleallhover', 'format_stunning');
    $description = get_string('defaulttoggleallhover_desc', 'format_stunning');
    $default = 2;
    $choices = array(
        1 => new lang_string('no'),
        2 => new lang_string('yes')
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));


    $name = 'format_stunning/defaultuserpreference';
    $title = get_string('defaultuserpreference', 'format_stunning');
    $description = get_string('defaultuserpreference_desc', 'format_stunning');
    $default = 0;
    $choices = array(
        0 => new lang_string('topcollclosed', 'format_stunning'),
        1 => new lang_string('topcollopened', 'format_stunning')
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));
}
