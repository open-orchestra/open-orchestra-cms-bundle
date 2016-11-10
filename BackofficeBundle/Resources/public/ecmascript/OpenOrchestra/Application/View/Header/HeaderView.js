import OrchestraView    from '../OrchestraView'
import Application      from '../../Application'
import SiteSelectorView from './SiteSelectorView'
import LogOutModalView  from './LogOutModalView'

/**
 * @class HeaderView
 */
class HeaderView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.tagName = 'div';
        this.className = 'header';
        this.events = {
            'click .expand': '_toggleFullscreen',
            'click .logout': '_showLogOutModal'
        }
    }

    /**
     * @param {SitesAvailable} sites
     */
    initialize({sites}) {
        this.sites = sites;
    }

    /**
     * Render header
     */
    render() {
        let $siteSelector = new SiteSelectorView({sites: this.sites}).render().$el;
        let template = this._renderTemplate(
            'Header/headerView',
            {
                user: Application.getContext().user
            }
        );
        this.$el.html(template);
        $('.site-selector', this.$el).replaceWith($siteSelector);

        return this;
    }

    /**
     * Show log Out modal
     */
    _showLogOutModal() {
        let logOutModalView = new LogOutModalView();
        Application.getRegion('modal').html(logOutModalView.render().$el);
        logOutModalView.show();

        return false;
    }

    /**
     * Toggle fullscreen
     */
    _toggleFullscreen() {
        let requestFullScreen = document.documentElement.requestFullscreen ||
                                document.documentElement.webkitRequestFullScreen ||
                                document.documentElement.mozRequestFullScreen ||
                                document.documentElement.msRequestFullscreen;

        let exitFullscreen = document.exitFullscreen ||
                             document.mozCancelFullScreen ||
                             document.webkitExitFullscreen ||
                             document.msExitFullscreen;

        if (this._isFullScreen()) {
            exitFullscreen.call(document);
        }
        requestFullScreen.call(document.documentElement);
    }

    /**
     * Check if document is in fullscreen
     */
    _isFullScreen() {
        return (document.fullScreenElement && document.fullScreenElement !== null) ||
               document.mozFullScreen ||
               document.webkitIsFullScreen;
    }
}

export default HeaderView;
