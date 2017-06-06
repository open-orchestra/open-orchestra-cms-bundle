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
    var msobservers = {
        'callbacks': {},

        'initialize': function (selector, callback) {
            // Wrap the callback so that we can ensure that it is only called once per element.
            var seen = [];
            callbackOnce = function () {
                if (seen.indexOf(this) == -1) {
                    seen.push(this);
                    $(this).each(callback);
                }
            };

            // See if the selector matches any elements already on the page.
            $(selector).each(callbackOnce);

            // Then, add it to the list of selector observers.
            this.addCallback(selector, callbackOnce);
        },

        'addCallback': function (selector, callback) {
            if (typeof this.callbacks[selector] == 'undefined') {
                this.callbacks[selector] = [];
            }
            this.callbacks[selector].push(callback);
        },

        'removeSelector': function(selector) {
            if (this.callbacks.hasOwnProperty(selector)) {
                delete(this.callbacks[selector]);
            }
        }
    };

    var observer = new MutationObserver(function (mutations) {
        // Stop watching selectors when they disappear from the DOM
        mutations.forEach(function(mutation) {
            if (mutation.type == 'childList' && mutation.removedNodes.length > 0) {
                for (selector in msobservers.callbacks) {
                    for (var index = 0; index < mutation.removedNodes.length; index++) {
                        if ($(selector, $(mutation.removedNodes[index])).length > 0) {
                            msobservers.removeSelector(selector);
                            break;
                        }
                    }
                }
            }
        });

        // The MutationObserver watches for when new elements are added to the DOM.
        for (selector in msobservers.callbacks) {
            for (var i = 0; i < msobservers.callbacks[selector].length; i++) {
                $(selector).each(msobservers.callbacks[selector][i]);
            }
        }
    });

    // Observe the entire document.
    observer.observe(document.documentElement, {childList: true, subtree: true, attributes: true});

    // Handle .initialize() calls.
    $.fn.initialize = function (callback) {
        msobservers.initialize(this.selector, callback);
    };

    // Handle manual .destroy() calls.
    $.fn.destroy = function () {
        msobservers.removeSelector(this.selector);
    };
})(jQuery);
