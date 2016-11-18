import OrchestraView    from '../OrchestraView'
import app              from '../../Application'
import SiteSelectorView from './SiteSelectorView'
import LogOutModalView  from './LogOutModalView'
import FormBuilder      from '../../../Service/Form/Model/FormBuilder'
import UserFormView     from '../../View/User/UserFormView'


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
            'click .logout': '_showLogOutModal',
            'click .preference': '_showPreference',
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
                user: app.getContext().user
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
        app.getRegion('modal').html(logOutModalView.render().$el);
        logOutModalView.show();

        return false;
    }

    /**
     * Show Preference
     */
    _showPreference() {
        let url = Routing.generate('open_orchestra_user_admin_user_form', {userId : app.getContext().user.id});
        FormBuilder.createFormFromUrl(url, (form) => {
            let userFormView = new UserFormView({form : form});
            app.getRegion('content').html(userFormView.render().$el);
        });

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
