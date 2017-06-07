/*!
 * Open Orchestra jQuery initialize
 *
 * Refacto of jQuery initialize - v1.0.0 - 12/14/2016
 * https://github.com/adampietrasiak/jquery.initialize
 *
 * Copyright (c) 2015-2016 Adam Pietrasiak
 * Released under the MIT license
 * https://github.com/timpler/jquery.initialize/blob/master/LICENSE
 */
;(function ($) {
    var prefix = 'jquery_initialize';
    var count = 0;
    var msobservers = {
        'callbacks': {},

        'initialize': function (id, selector, callback) {
            // Wrap the callback so that we can ensure that it is only called once per element.
            var seen = [];
            callbackOnce = function () {
                if (seen.indexOf(this) == -1) {
                    seen.push(this);
                    $(this).each(callback);
                }
            };

            // See if the selector matches any elements already on the page.
            $('#' + id + ' ' + selector).each(callbackOnce);

            // Then, add it to the list of selector observers.
            this.addCallback(id, selector, callbackOnce);
        },

        'addCallback': function (id, selector, callback) {
            if (typeof this.callbacks[id] == 'undefined') {
                this.callbacks[id] = [];
            }
            if (typeof this.callbacks[id][selector] == 'undefined') {
                this.callbacks[id][selector] = [];
            }
            this.callbacks[id][selector].push(callback);
        },

        'removeId': function(id) {
            if (this.callbacks.hasOwnProperty(id)) {
                delete(this.callbacks[id]);
            }
        }
    };

    var observer = new MutationObserver(function (mutations) {
        // Stop watching selectors when they disappear from the DOM
        mutations.forEach(function(mutation) {
            if (mutation.type == 'childList' && mutation.removedNodes.length > 0) {
                for (id in msobservers.callbacks) {
                    for (var index = 0; index < mutation.removedNodes.length; index++) {
                        if ($(mutation.removedNodes[index]).attr('id') == id) {
                            msobservers.removeId(id);
                        }
                    }
                }
            }
        });

        // The MutationObserver watches for when new elements are added to the DOM.
        for (id in msobservers.callbacks) {
            for (selector in msobservers.callbacks[id]) {
                for (var i = 0; i < msobservers.callbacks[id][selector].length; i++) {
                    $('#' + id + ' ' + selector).each(msobservers.callbacks[id][selector][i]);
                }
            }
        }
    });

    // Observe the entire document.
    observer.observe(document.documentElement, {childList: true, subtree: true, attributes: true});

    // Handle .initialize() calls.
    $.fn.initialize = function (selector, callback) {
        let id = $(this).attr('id');
        if (typeof id == 'undefined') {
            while ($("#" + id).length > 0) {
                id = prefix + '_' + count;
                count++;
            }
        }
        $(this).attr('id', id);
        msobservers.initialize(id, selector, callback);
    };

    // Handle manual .destroy() calls.
    $.fn.destroy = function (id) {
        msobservers.removeId(id);
    };
})(jQuery);
