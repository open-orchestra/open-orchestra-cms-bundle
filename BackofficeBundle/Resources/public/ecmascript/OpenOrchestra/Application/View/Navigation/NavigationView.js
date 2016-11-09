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
                includeTemplate: this._renderTemplate,
                navConfig: Orchestra.Config.Navigation
            });

        this.$el.html(template);

        return this;
    }

    /**
     * Toggle menu
     * @private
     */
    _toggleMenu() {
        this.$el.addClass('sublevel-closed').removeClass('sublevel-open');
        this.$el.toggleClass('toggle-left');
        $('#central-column').toggleClass('toggle-left');
    }

    /**
     * Toggle sub menu
     *
     * @param {Object} event
     * @private
     */
    _toggleSubLevel(event) {
        let idSubMenu = $(event.currentTarget).attr('href');
        let subMenu = $('.sublevels '+idSubMenu, this.$el);
        if (0 !== subMenu.length) {
            this.$el.addClass('sublevel-open').removeClass('sublevel-closed');
            this.$el.removeClass('toggle-left');
            $('#central-column').removeClass('toggle-left');
        } else {
            this.$el.addClass('sublevel-closed').removeClass('sublevel-open');
            event.stopPropagation();
        }
    }

    /**
     * Button return menu level1
     *
     * @param {Object} event
     * @private
     */
    _returnLevel1Menu(event) {
        event.stopPropagation();
        this.$el.removeClass('sublevel-open').addClass('sublevel-closed');

        return false
    }
}

export default NavigationView;
