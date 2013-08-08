M.course = M.course || {};

M.course.format = M.course.format || {};

/**
 * Get sections config for this format
 *
 * The section structure is:
 * <ul class="ctopics">
 *  <li class="section">...</li>
 *  <li class="section">...</li>
 *   ...
 * </ul>
 *
 * @return {object} section list configuration
 */
M.course.format.get_config = function() {
    return {
        container_node : 'ul',
        container_class : 'ctopics',
        section_node : 'li',
        section_class : 'section'
    };
}

/**
 * Swap section
 *
 * @param {YUI} Y YUI3 instance.
 * @param {string} node1 node to swap to.
 * @param {string} node2 node to swap with.
 * @return {NodeList} section list.
 */
M.course.format.swap_sections = function(Y, node1, node2) {
    var CSS = {
        COURSECONTENT : '.course-content',
        SECTIONADDMENUS : '.section_add_menus'
    };

    var sectionlist = Y.Node.all(CSS.COURSECONTENT+' '+M.course.format.get_section_selector(Y));
    // Swap menus
    sectionlist.item(node1).one(CSS.SECTIONADDMENUS).swap(sectionlist.item(node2).one(CSS.SECTIONADDMENUS));
}


/**
 * Process sections after ajax response
 *
 * @param {YUI} Y YUI3 instance
 * @param {array} response ajax response
 * @param {string} sectionfrom first affected section
 * @param {string} sectionto last affected section
 * @return void
 */
M.course.format.process_sections = function(Y, sectionlist, response, sectionfrom, sectionto) {
    var CSS = {
        SECTIONNAME     : '.the_toggle h3'
    },
    SELECTORS = {
        LEFTCONTENT     : '.left .cps_centre',
        SECTIONLEFTSIDE : '.left .section-handle img'
    };

    if (response.action == 'move') {
        if (sectionfrom > sectionto) { // MDL-34798
            var temp = sectionto;
            sectionto = sectionfrom;
            sectionfrom = temp;
        }

        // Update titles and move icons in all affected sections.
        var leftcontent, ele, str, stridx, newstr;

        for (var i = sectionfrom; i <= sectionto; i++) {
            // Update section title.
            sectionlist.item(i).one(CSS.SECTIONNAME).setContent(response.sectiontitles[i]);
            // If the left content section number exists, then set it.
            leftcontent = sectionlist.item(i).one(SELECTORS.LEFTCONTENT);
            if (leftcontent) { // Only set if the section number is shown otherwise JS crashes and stops working.
                leftcontent.setContent(i);
            }
            // Update move icon.  MDL-37901.
            ele = sectionlist.item(i).one(SELECTORS.SECTIONLEFTSIDE);
            str = ele.getAttribute('alt');
            stridx = str.lastIndexOf(' ');
            newstr = str.substr(0, stridx +1) + i;
            ele.setAttribute('alt', newstr);
            ele.setAttribute('title', newstr); // For FireFox as 'alt' is not refreshed.

            if (response.current !== -1) {
                if (sectionlist.item(i).hasClass('current')) {
                    // Remove the current class as section has been moved.  MDL-33546.
                    sectionlist.item(i).removeClass('current');
                }
            }
        }
        // If there is a current section, apply corresponding class in order to highlight it.  MDL-33546.
        if (response.current !== -1) {
            // Add current class to the required section.
            sectionlist.item(response.current).addClass('current');
        }
    }
}
