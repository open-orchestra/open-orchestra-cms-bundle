import OrchestraView    from '../OrchestraView'
import Application      from '../../Application'
import ApplicationError from '../../../Service/Error/ApplicationError'

/**
 * @class NavigationView
 */
class NavigationView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.tagName = 'nav';
        this.className = 'main-menu';
        this.attributes = {
            'role': 'navigation'
        };
        this.events = {
            'click .nav.level1 a': '_toggleSubLevel',
            'click .btn-menu': '_toggleMenu',
            'click .return a': '_returnLevel1Menu'
        }
    }

    /**
     * Render header
     */
    render() {
        /**
         * Orchestra.Config.Navigation is a parameter which contain the navigation configuration
         * described in files public/config/navigation.json of all bundles
         */
        if (typeof Orchestra.Config.Navigation === "undefined") {
            throw new ApplicationError('Navigation configuration is not found');
        }

        let template = this._renderTemplate('Navigation/navigationView',
            {
                navConfig: Orchestra.Config.Navigation
            }
        );

        this._resizeColumns();
        this._enabledScroll();

        this.$el.html(template);

        return this;
    }

    /**
     * Enabled niceScroll on menu
     * @private
     */
    _enabledScroll() {
        $('#left-column').niceScroll({
            horizrailenabled: false,
            cursorcolor: "#e6eef1",
            railpadding: { top: 0, right: 1, left: 0, bottom: 0 },
            cursorborder: 0
        }).resize();
    }

    /**
     * Toggle menu according window width
     * @private
     */
    _resizeColumns() {
        this.$el.removeClass('sublevel-open');
        let $selectorColumns = $('#left-column, #central-column');
        $selectorColumns.removeClass('toggle-left');
        if (this._isTablet()) {
            $selectorColumns.addClass('toggle-left');
        }
    }

    /**
     * Toggle menu
     * @private
     */
    _toggleMenu() {
        $('.tab-pane', this.$el).removeClass('active');
        this.$el.addClass('sublevel-closed').removeClass('sublevel-open');
        $('#central-column, #left-column').toggleClass('toggle-left');

        if (this._isTablet()) {
            $('#central-column').toggleClass('wide');
        }
    }

    /**
     * Toggle sub menu
     *
     * @param {Object} event
     * @private
     */
    _toggleSubLevel(event) {
        let target = $(event.currentTarget);
        let idSubMenu = target.attr('href');
        target.addClass('active');
        let subMenu = $('.sublevels '+idSubMenu, this.$el);
        if (0 !== subMenu.length) {
            this.$el.addClass('sublevel-open').removeClass('sublevel-closed');
            $('#central-column, #left-column').removeClass('toggle-left');
            if (this._isTablet()) {
                $('#central-column').addClass('wide');
            }
        } else {
            this.$el.addClass('sublevel-closed').removeClass('sublevel-open');
            $('.tab-pane', this.$el).removeClass('active');
            $('.nav li', this.$el).removeClass('active');
            event.stopPropagation();
        }
    }

    /**
     * Button return menu level1
     *
     * @private
     */
    _returnLevel1Menu() {
        this.$el.removeClass('sublevel-open').addClass('sublevel-closed');
        $('.tab-pane', this.$el).removeClass('active');
        $('.nav li', this.$el).removeClass('active');

        return false
    }

    /**
     * @returns {boolean}
     * @private
     */
    _isTablet() {
        let tabletWidth = 1024;

        return $(window).width() <= tabletWidth;
    }
}

export default NavigationView;
