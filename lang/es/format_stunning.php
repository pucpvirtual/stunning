<?php

$string['nametopcoll'] = 'Impresionante vista de curso';
$string['formattopcoll'] = 'Impresionante vista de curso';

// Used in format.php.
$string['topcolltoggle'] = 'Alternar';
$string['topcollsidewidth'] = '28px';

// Toggle all - Moodle Tracker CONTRIB-3190.
$string['topcollall'] = 'Secciones.';  // Leave as AMOS maintains only the latest translation - so previous versions are still supported.
$string['topcollopened'] = 'Abrir todas';
$string['topcollclosed'] = 'Cerrar todas';

// Moodle 2.0 Enhancement - Moodle Tracker MDL-15252, MDL-21693 & MDL-22056 - http://docs.moodle.org/en/Development:Languages.
$string['sectionname'] = 'Sección';
$string['pluginname'] = 'Impresionante vista de curso';
$string['section0name'] = 'General';

// MDL-26105.
$string['page-course-view-topcoll'] = 'Cualquier página principal del curso en el formato de los temas colapsado';
$string['page-course-view-topcoll-x'] = 'Cualquier página principal del curso en el formato de los temas colapsado';

// Moodle 2.3 Enhancement.
$string['hidefromothers'] = 'Esconder sección';
$string['showfromothers'] = 'Mostrar sección';
$string['currentsection'] = 'Esta sección';
$string['markedthissection'] = 'Esta sección esta destacada con la sección actual';
$string['markthissection'] = 'Destacar esta sección como la sección actual';

// Layout enhancement - Moodle Tracker CONTRIB-3378.
$string['formatsettings'] = 'Reestablece la configuración del formato';
$string['formatsettingsinformation'] = '<br />Para Reestablece la configuración del formato de curso por defecto, clic en el icono de la derecha.';
$string['setlayout'] = 'Establecer plantilla';

// Negative view of layout, kept for previous versions until such time as they are updated.
$string['setlayout_default'] = 'Por defecto'; // 1.
$string['setlayout_no_toggle_section_x'] = 'No sección alternable x'; // 2.
$string['setlayout_no_section_no'] = 'No mostrar número de sección'; // 3.
$string['setlayout_no_toggle_section_x_section_no'] = 'No sección alternable x y no número de sección'; // 4.
$string['setlayout_no_toggle_word'] = 'No mortar palabra Alternar'; // 5.
$string['setlayout_no_toggle_word_toggle_section_x'] = 'No mostrar palabra Alternar y sección alternable x'; // 6.
$string['setlayout_no_toggle_word_toggle_section_x_section_no'] = 'No mostrar palabra Alternar, sección alternable x y número de sección'; // 7.
// Positive view of layout.
$string['setlayout_all'] = "Palabrar Alternar, 'Tema X '/ 'Semana X' / 'Día X' y número se sección"; // 1.
$string['setlayout_toggle_word_section_number'] = 'Palabra Alternar y número de sección'; // 2.
$string['setlayout_toggle_word_section_x'] = "Palabra Alternar y  'Tema X '/ 'Semana X' / 'Día X'"; // 3.
$string['setlayout_toggle_word'] = 'Toggle word'; // 4.
$string['setlayout_toggle_section_x_section_number'] = "'Tema X '/ 'Semana X' / 'Día X' and Número de sección"; // 5.
$string['setlayout_section_number'] = 'Número de sección'; // 6.
$string['setlayout_no_additions'] = 'Nada adicional'; // 7.
$string['setlayout_toggle_section_x'] = "'Tema X '/ 'Semana X' / 'Día X'"; // 8.

$string['setlayoutelements'] = 'Establecer elementos';
$string['setlayoutstructure'] = 'Establecer estructura';
$string['setlayoutstructuretopic'] = 'Tema';
$string['setlayoutstructureweek'] = 'Semana';
$string['setlayoutstructurelatweekfirst'] = 'Semana Actual primera';
$string['setlayoutstructurecurrenttopicfirst'] = 'Tema Actual primero';
$string['setlayoutstructureday'] = 'Día';
$string['resetlayout'] = 'Reestablece la estructura'; // CONTRIB-3529.
$string['resetalllayout'] = 'Reestablece la estructura de todas las Impresionantes vistas de cursos';

// Colour enhancement - Moodle Tracker CONTRIB-3529.
$string['setcolour'] = 'Establecer color';
$string['colourrule'] = "Indicar un color en formato RGB o seis dígitos hexadecimales.";
$string['settoggleforegroundcolour'] = 'Color del encabezado';
$string['settogglebackgroundcolour'] = 'Color de fondo de la sección alternable';
$string['settogglebackgroundhovercolour'] = 'Color de fondo de la sección alternable cuando el mouse esta sobre está';
$string['resetcolour'] = 'Reestablece los colores';
$string['resetallcolour'] = 'Reestablece los colores de tolas las Impresionantes vistas de curso';

// Columns enhancement.
$string['setlayoutcolumns'] = 'Establecer columnas';
$string['one'] = 'Uno';
$string['two'] = 'Dos';
$string['three'] = 'Tres';
$string['four'] = 'Cuatro';
$string['setlayoutcolumnorientation'] = 'Establecer orientación de columnas';
$string['columnvertical'] = 'Vertical';
$string['columnhorizontal'] = 'Horizontal';

// MDL-34917 - implemented in M2.5 but needs to be here to support M2.4- versions.
$string['maincoursepage'] = 'Página principal del curso';

// Help.
$string['setlayoutelements_help'] = 'Cuanta información sobre las secciones alternables desea que se muestre.';
$string['setlayoutstructure_help'] = "La estructura del curso. Puede elegir 
'Temas' - Donde cada sección es presentada como un tema en un orden numerado.

'Semanal' - Donde cada sección es presentada como una semana ordenada de forma ascendente desde el inicio del curso.

'Semanal Actual Primero' - La cual es idéntico que el formato semanal pero la semana  actual es mostrada primero y le preceden las semanas de forma descendente excepto cuando se está en modo de edición donde se muestra la estructura igual al de 'Semanal'

'Tema Actual Primero' - El cual es idéntico a 'Temas' excepto que el tema actual es presentado primero siempre que este haya sido establecido

'Días' - Donde cada sección es presentada como un día en forma ascendente ordenado desde la fecha de inicio del curso.";

$string['setextraelements_help'] = 'Indica si desea que aparezca el bloque de perfil y el bloque de noticias.';
$string['setlayout_help'] = 'Contiene la configuración sobre la estructura del formato de curso.';
$string['resetlayout_help'] = 'Reestablece la estructura por defecto de como se ve el curso cuando recien se configuró como Impresionante vista de curso.';
$string['resetalllayout_help'] = 'Reestablece la estructura por defecto de todos los cursos con formato Impresionante vista de curso.';
// Moodle Tracker CONTRIB-3529.
$string['setcolour_help'] = 'Contiene la configuración de color del formato de curso.';
$string['settoggleforegroundcolour_help'] = 'Establece el color del texto alternable.';
$string['settogglebackgroundcolour_help'] = 'Establece el color de fondo de la sección alternable.';
$string['settogglebackgroundhovercolour_help'] = 'Estable el color de fondo de la sección alternable cuando el mouse esta sobre está.';
$string['resetcolour_help'] = 'Reestablece los colores por defecto cuando recién se configuró como Impresionante vista de curso.';
$string['resetallcolour_help'] = 'Reestablece los colores por defecto de todos los cursos con formato Impresionante vista de curso.';
// Columns enhancement.
$string['setlayoutcolumns_help'] = 'Cuantas columnas usar.';
$string['setlayoutcolumnorientation_help'] =
'Vertical - Secciones van de arriba a abajo.

Horizontal - Secciones van de izquierda a derecha.';

// Moodle 2.4 Course format refactoring - MDL-35218.
$string['numbersections'] = 'Número de secciones';
$string['ctreset'] = 'Impresionante vista de cuso opciones de restablecimiento';
$string['ctreset_help'] = 'Reestablece la configuración por defecto de la Impresionante vista de curso';

// Toggle alignment - CONTRIB-4098.
$string['settogglealignment'] = 'Establece el alineamiento del texto alternable';
$string['settogglealignment_help'] = 'Establece el alineamiento del texto en la sección alternable.';
$string['left'] = 'Izquierda';
$string['center'] = 'Centro';
$string['right'] = 'Derecha';
$string['resettogglealignment'] = 'Reestablece el alineamiento de la sección alternable';
$string['resetalltogglealignment'] = 'Reestablece el alineamiento de la sección de todos los cursos con formato Impresionante vista de curso.';
$string['resettogglealignment_help'] = 'Reestablece el alineamiento por defecto cuando recién se configuró como Impresionante vista de curso.';
$string['resetalltogglealignment_help'] = 'Reestablece el alineamiento por defecto de todos los cursos con formato Impresionante vista de curso cuando recién se configuró el formato de curso.';

// Icon position - CONTRIB-4470.
$string['settoggleiconposition'] = 'Establece la posición del icono';
$string['settoggleiconposition_help'] = 'Establece si el icono debe ir a la izquierda o a la derecha del texto de la sección alternable.';
$string['defaulttoggleiconposition'] = 'Posición del icono';
$string['defaulttoggleiconposition_desc'] = 'Establece si el icono debe ir a la izquierda o a la derecha del texto de la sección alternable.';

// Icon set enhancement.
$string['settoggleiconset'] = 'Estable el juego de iconos';
$string['settoggleiconset_help'] = 'Estable el icono alternable.';
$string['settoggleallhover'] = 'Estable los todos iconos alternable en hover';
$string['settoggleallhover_help'] = 'Estable si el icono de alternar todos cambiara cuando pase el mouse sobre este.';
$string['arrow'] = 'Flecha';
$string['point'] = 'Punto';
$string['power'] = 'Poder';
$string['resettoggleiconset'] = 'Reestablece el juego de iconos';
$string['resetalltoggleiconset'] = 'Reestablece el icono alternable para todos los cursos con formato Impresionante Vista de curso';
$string['resettoggleiconset_help'] = 'Reestablce el icono alternable por defecto cuando recién se configuró como Impresionante vista de curso.';
$string['resetalltoggleiconset_help'] = 'Reestablece el icono alternable para todos los cursos con formato Impresionante Vista de curso cuando recién se configuró el formato de curso.';

// Site Administration -> Plugins -> Course formats -> Collapsed Topics or Manage course formats - Settings.
$string['off'] = 'Off';
$string['on'] = 'On';
$string['defaultcoursedisplay'] = 'Visualización por defecto del curso';
$string['defaultcoursedisplay_desc'] = "Mostar todas las secciones de una sola página o mostrar la sección cero en cada sección por página.";
$string['defaultlayoutelement'] = 'Configuración de visualización por defecto';
// Negative view of layout, kept for previous versions until such time as they are updated.
$string['defaultlayoutelement_desc'] = "La configuración de visualización puede ser:

'Por defecto' muestra todo.

No 'Tema X '/ 'Semana X' / 'Día X'.

No Número de sección.

No 'Tema X '/ 'Semana X' / 'Día X' y no Número de sección.

No Palabra 'Alternable'.

No Palabra 'Alternable' y no 'Tema X '/ 'Semana X' / 'Día X'.

No Palabra 'Alternable', no 'Tema X '/ 'Semana X' / 'Día X' y no Número de sección.";
// Positive view of layout.
$string['defaultlayoutelement_descpositive'] = "La configuración de visualización puede ser:

Palabra Alternable, 'Tema X '/ 'Semana X' / 'Día X' y Número de sección.

Palabra Alternable y 'Tema X '/ 'Semana X' / 'Día X'.

Palabra Alternable y Número de sección.

'Tema X '/ 'Semana X' / 'Día X' y Número de sección.

Palabra Alternable.

'Tema X '/ 'Semana X' / 'Día X'.

Número de sección.

Nada adicional.";

$string['defaultlayoutstructure'] = 'Configuración por defecto de estructura';
$string['defaultlayoutstructure_desc'] = "La configuración de estructura puede ser una de esta:

Temas

Semanal

Semanal actual Primero

Tema Actual Primero

Día";

$string['defaultlayoutcolumns'] = 'Número por defecto de columnas';
$string['defaultlayoutcolumns_desc'] = "Número por defecto de columnas entre una y cuatro.";

$string['defaultlayoutcolumnorientation'] = 'Orientación por defecto de las columnas';
$string['defaultlayoutcolumnorientation_desc'] = "La orientación por defecto de las columnas: Vertial o Horizontal.";

$string['defaulttgfgcolour'] = 'Color por defecto del encabezado';
$string['defaulttgfgcolour_desc'] = "Color del encabezado en hexadecimal, RGB.";

$string['defaulttgbgcolour'] = 'Color por defecto del fondo';
$string['defaulttgbgcolour_desc'] = "Color del fondo en hexadecimal, RGB.";

$string['defaulttgbghvrcolour'] = 'Color por defecto del fondo cuando el mouse esta sobre este';
$string['defaulttgbghvrcolour_desc'] = "Color por defecto del fondo cuando el mouse esta sobre este en hexadecimal, RGB.";

$string['defaulttogglepersistence'] = 'Persistencia de la alternación';
$string['defaulttogglepersistence_desc'] = "'On' o 'Off'.  Deseara desactivar esta opción por un tema de rendimiento de AJAX";

$string['defaulttogglealignment'] = 'Alineamiento del texto por defecto';
$string['defaulttogglealignment_desc'] = "'Izquierda', 'Centro' o 'Derecha'.";

$string['defaulttoggleiconset'] = 'Juego de iconos alternables por defecto';
$string['defaulttoggleiconset_desc'] = "'Flecha' => juego de iconos Flecha.

'Punto' => juego de iconos Punto

'Poder' => juego de iconos Poder.";

$string['defaulttoggleallhover'] = 'Efecto hover de todos los iconos alternables por defecto';
$string['defaulttoggleallhover_desc'] = "'No' o 'Sí'.";

// Default user preference.
$string['defaultuserpreference'] = 'Que hacer con las secciones alternables cuando el usuario ingresa por primera vez o se añaden más secciones';
$string['defaultuserpreference_desc'] = 'Establece que hacer con las secciones alternables cuando el usuario ingresa por primera vez al curso o se añaden más secciones.';

// Capabilities.
$string['stunning:changelayout'] = 'Cambio o reestablece la estructura';
$string['stunning:changecolour'] = 'Cambio o reestablece el color';
$string['stunning:changetogglealignment'] = 'Cambio o reestablece el alineamiento del texto alternable';
$string['stunning:changetoggleiconset'] = 'Cambio o reestablece el juego de iconos alternables';

$string['setextraelements'] = 'Establece elementos adicionales';
$string['extraelements'] = 'Elementos Adicionales';
$string['extraelements_detail'] = 'Elementos adicionales a mostrar, bloque de perfil y bloque de noticias';
$string['setnotice'] = 'Mostar el bloque de noticias';
$string['setprofile'] = 'Mostar el bloque de perfil';
$string['setnotice_profile'] = 'Mostar el bloque de perfil y el bloque de noticias';
$string['set_noelements'] = 'No añadir elementos adicionales';
$string['order'] = 'Orden';
$string['order_help'] = 'Indica el orden en que aparecen, mientras mas bajo se el valor más pronto aparecera';
$string['delete'] = '¿Eliminar?';
$string['delete_help'] = 'Activar esto para que el enlace sea eliminado';
$string['norecords'] = 'No existen registros para este curso';
$string['imageurl'] = 'URL de la imagen';
$string['imageurl_help'] = 'La url con la dirección de la imagen. Esta debe tener un ancho máximo de 80px';
$string['banner'] = 'La URL del Banner';
$string['banner_help'] = 'La url con la dirección de la imagen/SWF. Esta de tener un ancho máximo 800px';
$string['type'] = 'Tipo';
$string['type_help'] = 'El tipo de banner a agregar puede ser imagen o un SWF';
$string['activity'] = 'Actividad';
$string['activity_help'] = 'La actividad a la cual se enlaza la imagen, seleccione otro para añadir una url personalizada';
$string['url'] = 'URl';
$string['url_help'] = ' Sirve para añadir una url personalizada para enlazar a la imagen. Solo se activa cuando se selecciona la opción otro en  el campo  actividad.';
$string['addlink'] = 'Agregar Enlace';
$string['addicon'] = 'Agregar Icono';
$string['reorder'] = 'Reordenar';
$string['image'] = 'Imagen';
$string['editbanner'] = 'Editar Banner';
