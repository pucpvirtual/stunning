<?phpdefined('MOODLE_INTERNAL') || die();$capabilities = array(    'format/stunning:changelayout' => array(        'captype' => 'write',        'contextlevel' => CONTEXT_COURSE,        'archetypes' => array(            'editingteacher' => CAP_ALLOW,            'manager' => CAP_ALLOW        )    ),    'format/stunning:changecolour' => array(        'captype' => 'write',        'contextlevel' => CONTEXT_COURSE,        'archetypes' => array(            'editingteacher' => CAP_ALLOW,            'manager' => CAP_ALLOW        )    ),    'format/stunning:changetogglealignment' => array(        'captype' => 'write',        'contextlevel' => CONTEXT_COURSE,        'archetypes' => array(            'editingteacher' => CAP_ALLOW,            'manager' => CAP_ALLOW        )    ),    'format/stunning:changetoggleiconset' => array(        'captype' => 'write',        'contextlevel' => CONTEXT_COURSE,        'archetypes' => array(            'editingteacher' => CAP_ALLOW,            'manager' => CAP_ALLOW        )    ));