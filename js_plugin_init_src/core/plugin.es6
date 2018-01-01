'use strict';

class {CLASS_NAME} {

    constructor(element, options) {

        let self = this;

        self.settings = $.extend({

        }, options);

        let $element = $(element);

    }
}


$.fn.{JQUERY_FUNCTION_NAME} = function() {
    let $this = this,
        opt = arguments[0],
        args = Array.prototype.slice.call(arguments, 1),
        length = $this.length,
        i,
        ret;
    for (i = 0; i < length; i++) {
        if (typeof opt == 'object' || typeof opt == 'undefined')
            $this[i].{FUNCTION_NAME_UNDERSCORE} = new {CLASS_NAME}($this[i], opt);
        else
            ret = $this[i].{FUNCTION_NAME_UNDERSCORE}[opt].apply($this[i].{FUNCTION_NAME_UNDERSCORE}, args);
        if (typeof ret != 'undefined') return ret;
    }
    return $this;
};