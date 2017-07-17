import OrchestraView    from 'OpenOrchestra/Application/View/OrchestraView'
import Application      from 'OpenOrchestra/Application/Application'
import SiteSelectorView from 'OpenOrchestra/Application/View/Header/SiteSelectorView'
import ConfirmModalView from 'OpenOrchestra/Service/ConfirmModal/View/ConfirmModalView'

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
        let template = this._renderTemplate(
            'Header/headerView',
            {
                user: Application.getContext().get('user')
            }
        );
        this.$el.html(template);

        let $siteSelector = new SiteSelectorView({sites: this.sites}).render().$el;
        $('.site-selector', this.$el).replaceWith($siteSelector);

        return this;
    }

    /**
     * Show log Out modal
     */
    _showLogOutModal() {
        let logOutModalView = new ConfirmModalView({
            confirmTitle: Translator.trans('open_orchestra_backoffice.header.sign_out'),
            confirmMessage: Translator.trans('open_orchestra_backoffice.log_out_modal.security_message'),
            yesCallback: () => { window.location.href = Routing.generate('fos_user_security_logout')}
        });
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
