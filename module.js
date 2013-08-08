
M.format_stunning = M.format_stunning || {};

// Namespace variables:
M.format_stunning.thesparezeros = "00000000000000000000000000"; // A constant of 26 0's to be used to pad the storage state of the toggles when converting between base 2 and 36, this is to be compact.
M.format_stunning.togglestate;
M.format_stunning.courseid;
M.format_stunning.togglePersistence = 1; // Toggle persistence - 1 = on, 0 = off.
M.format_stunning.ourYUI;
M.format_stunning.numSections;

// Namespace constants:
M.format_stunning.TOGGLE_6 = 1;
M.format_stunning.TOGGLE_5 = 2;
M.format_stunning.TOGGLE_4 = 4;
M.format_stunning.TOGGLE_3 = 8;
M.format_stunning.TOGGLE_2 = 16;
M.format_stunning.TOGGLE_1 = 32;


M.format_stunning.init = function(Y, theCourseId, theToggleState, theNumSections, theTogglePersistence, theDefaultTogglePersistence) {
    "use strict";
    // Init.
    this.ourYUI = Y;
    this.courseid = theCourseId;
    this.togglestate = theToggleState;
    this.numSections = parseInt(theNumSections);
    this.togglePersistence = theTogglePersistence;

    if (this.togglestate !== null) {
        if (this.is_old_preference(this.togglestate) == true) {
            // Old preference, so convert to new.
            this.convert_to_new_preference();
        }
        // Check we have enough digits for the number of toggles in case this has increased.
        var numdigits = this.get_required_digits(this.numSections);
        if (numdigits > this.togglestate.length) {
            var dchar;
            if (theDefaultTogglePersistence == 0) {
                dchar = this.get_min_digit();
            } else {
                dchar = this.get_max_digit();
            }
            for (var i = this.togglestate.length; i < numdigits; i++) {
                this.togglestate += dchar;
            }
        } else if (numdigits < this.togglestate.length) {
            // Shorten to save space.
            this.togglestate = this.togglestate.substring(0, numdigits);
        }
    } else {
        // Reset to default.
        if (theDefaultTogglePersistence == 0) {
            this.resetState(this.get_min_digit());
        } else {
            this.resetState(this.get_max_digit());
        }
    }


    Y.delegate('click', this.toggleClick, Y.config.doc, 'ul.ctopics .toggle', this);

    Y.delegate('click', this.deleteicon, Y.config.doc, 'ul.icons .delete', this);

    // Event handlers for all opened / closed.
    var allopen = Y.one("#toggles-all-opened");
    if (allopen) {
        allopen.on('click', this.allOpenClick);
    }
    var allclosed = Y.one("#toggles-all-closed");
    if (allclosed) {
        allclosed.on('click', this.allCloseClick);
    }
};

M.format_stunning.deleteicon = function(e){
     if(!confirm(M.util.get_string('wanttodelete','format_stunning'))){
        e.preventDefault();
     }
}



M.format_stunning.toggleClick = function(e) {
    var toggleIndex = parseInt(e.currentTarget.get('id').replace("toggle-", ""));
    e.preventDefault();
    this.toggle_topic(e.currentTarget, toggleIndex);
};

M.format_stunning.allOpenClick = function(e) {
    e.preventDefault();
    M.format_stunning.ourYUI.all(".toggledsection").addClass('sectionopen');
    M.format_stunning.ourYUI.all(".toggle a").addClass('toggle_open').removeClass('toggle_closed');
    M.format_stunning.resetState(M.format_stunning.get_max_digit());
    M.format_stunning.save_toggles();
};

M.format_stunning.allCloseClick = function(e) {
    e.preventDefault();
    M.format_stunning.ourYUI.all(".toggledsection").removeClass('sectionopen');
    M.format_stunning.ourYUI.all(".toggle a").addClass('toggle_closed').removeClass('toggle_open');
    M.format_stunning.resetState(M.format_stunning.get_min_digit());
    M.format_stunning.save_toggles();
};

M.format_stunning.resetState = function(dchar) {
    M.format_stunning.togglestate = "";
    var numdigits = M.format_stunning.get_required_digits(M.format_stunning.numSections);
    for (var i = 0; i < numdigits; i++) {
        M.format_stunning.togglestate += dchar;
    }
};


M.format_stunning.toggle_topic = function(targetNode, toggleNum) {
    "use strict";
    var targetLink = targetNode.one('a');
    var state;
    if (!targetLink.hasClass('toggle_open')) {
        targetLink.addClass('toggle_open').removeClass('toggle_closed');
        targetNode.next('.toggledsection').addClass('sectionopen');
        state = true;
    } else {
        targetLink.addClass('toggle_closed').removeClass('toggle_open');
        targetNode.next('.toggledsection').removeClass('sectionopen');
        state = false;
    }
    this.set_toggle_state(toggleNum, state);
    this.save_toggles();
};


M.format_stunning.to2baseString = function(thirtysix) {
    "use strict";
    // Break apart the string because integers are signed 32 bit and therefore can only store 31 bits, therefore a 53 bit number will cause overflow / carry with loss of resolution.
    var firstpart = parseInt(thirtysix.substring(0,6),36);
    var secondpart = parseInt(thirtysix.substring(6,12),36);
    var fps = firstpart.toString(2);
    var sps = secondpart.toString(2);

    // Add in preceding 0's if base 2 sub strings are not long enough
    if (fps.length < 26) {
        // Need to PAD.
        fps = this.thesparezeros.substring(0,(26 - fps.length)) + fps;
    }
    if (sps.length < 27) {
        // Need to PAD.
        sps = this.thesparezeros.substring(0,(27 - sps.length)) + sps;
    }

    return fps + sps;
};


M.format_stunning.save_toggles = function() {
    "use strict";
    /*if (this.togglePersistence == 1) { // Toggle persistence - 1 = on, 0 = off.
        M.util.set_user_preference('topcoll_toggle_'+this.courseid , this.togglestate);
    }*/
};

// New base 64 code:
M.format_stunning.is_old_preference = function(pref) {
    "use strict";
    var retr = false;
    var firstchar = pref[0];

    if ((firstchar == '0') || (firstchar == '1')) {
        retr = true;
    }

    return retr;
};

M.format_stunning.convert_to_new_preference = function() {
    "use strict";
    var toggleBinary = this.to2baseString(this.togglestate);
    var bin, value;
    this.togglestate = "";
    var logbintext = "";

    for (var i = 1; i <= 43; i = i+6) {
        bin = toggleBinary.substring(i, i+6);
        value = parseInt(bin, 2);
        this.togglestate += this.encode_value_to_character(value);
        logbintext += bin + ' ';
    }

    bin = toggleBinary.substring(49, 53);
    logbintext += bin + ' ';
    value = parseInt(bin, 2);
    value = value << 2;
    this.togglestate += this.encode_value_to_character(value);
};

M.format_stunning.set_toggle_state = function(togglenum, state) {
    "use strict";
    var togglecharpos = this.get_toggle_pos(togglenum);
    var toggleflag = this.get_toggle_flag(togglenum, togglecharpos);
    var value = this.decode_character_to_value(this.togglestate.charAt(togglecharpos-1));
    if (state == true) {
        value |= toggleflag;
    } else {
        value &= ~toggleflag;
    }
    var newchar = this.encode_value_to_character(value);
    //this.togglestate[togglecharpos-1] = newchar;
    var start = this.togglestate.substring(0,togglecharpos-1);
    var end = this.togglestate.substring(togglecharpos);
    this.togglestate = start + newchar + end;
};

M.format_stunning.get_required_digits = function(numtoggles) {
    "use strict";
    return this.get_toggle_pos(numtoggles);
};

M.format_stunning.get_toggle_pos = function(togglenum) {
    "use strict";
    return Math.ceil(togglenum / 6);
};

M.format_stunning.get_min_digit = function() {
    "use strict";
    return ':';
};

M.format_stunning.get_max_digit = function() {
    "use strict";
    return 'y';
};

M.format_stunning.get_toggle_flag = function(togglenum, togglecharpos) {
    "use strict";
    var toggleflagpos = togglenum - ((togglecharpos-1)*6);
    var flag;
    switch (toggleflagpos) {
        case 1:
            flag = this.TOGGLE_1;
            break;
        case 2:
            flag = this.TOGGLE_2;
            break;
        case 3:
            flag = this.TOGGLE_3;
            break;
        case 4:
            flag = this.TOGGLE_4;
            break;
        case 5:
            flag = this.TOGGLE_5;
            break;
        case 6:
            flag = this.TOGGLE_6;
            break;
    }
    return flag;
};

M.format_stunning.decode_character_to_value = function(character) {
    "use strict";
    return character.charCodeAt(0) - 58;
}

M.format_stunning.encode_value_to_character = function(val) {
    "use strict";
    return String.fromCharCode(val + 58);
};
