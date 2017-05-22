/**
 * Override plugin tabs of bootstrap to manage responsive
 */
(function($) {
    // base plugin tab
    let _super = $.fn.tab;

    // new plugin
    let Tab = function (element, options) {
        _super.Constructor.apply(this, arguments);
    };

    var updateResponsiveTab = function($activeLink) {
        var $tabs = $activeLink.closest('.nav-tabs');
        if (false === $tabs.hasClass('nav-tabs-responsive')) {
            return false;
        }

        var $current = $activeLink.closest('li');
        var $parent = $current.closest('li.dropdown');
        $current = $parent.length > 0 ? $parent : $current;
        var $next = $current.next();
        var $prev = $current.prev();

        $tabs.find('>li').removeClass('next prev');
        $prev.addClass('prev');
        $next.addClass('next');
    };

    //extend method show
    Tab.prototype = $.extend({}, _super.Constructor.prototype, {
        constructor: Tab,
        _super: function () {
            var args = $.makeArray(arguments);
            _super.Constructor.prototype[args.shift()].apply(this, args);
        },
        show: function () {
            var $this    = this.element;
            updateResponsiveTab($this);
            this._super('show');
        }
    });

    // replace old plugin tab
    $.fn.tab = $.extend(function (option) {
        return this.each(function () {
            var $this = $(this);
            var data = $this.data('bs.tab');

            if (!data) $this.data('bs.tab', (data = new Tab(this)));
            if (typeof option == 'string') data[option]()
        });
    }, $.fn.tab);

    $(document).on('show.bs.tab', '.nav-tabs-responsive [data-toggle="tab"]', function(e) {
        var $target = $(e.target);
        updateResponsiveTab($target);
    });

})(jQuery);
