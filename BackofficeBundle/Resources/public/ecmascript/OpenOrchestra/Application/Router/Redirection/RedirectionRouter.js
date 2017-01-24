import OrchestraRouter     from '../OrchestraRouter'
import Application         from '../../Application'
import FormBuilder         from '../../../Service/Form/Model/FormBuilder'
import Redirections        from '../../Collection/Redirection/Redirections'
import RedirectionsView    from '../../View/Redirection/RedirectionsView'
import RedirectionFormView from '../../View/Redirection/RedirectionFormView'

/**
 * @class RedirectionRouter
 */
class RedirectionRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'redirection/list(/:page)': 'listRedirections',
            'redirection/edit/:redirectionId': 'editRedirection'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.navigation.configuration.title')
            },
            {
                label: Translator.trans('open_orchestra_backoffice.navigation.configuration.redirections'),
                link: '#'+Backbone.history.generateUrl('listRedirections')
            }
        ]
    }

    /**
     * List Site
     *
     * @param {int} page
     */
    listRedirections(page) {
        if (null === page) {
            page = 1
        }
        this._displayLoader(Application.getRegion('content'));
        let redirections = new Redirections();
        $.when(
            redirections.fetch({context: 'list'})
        ).done(() => {
            let redirectionsView = new RedirectionsView({
                collection: redirections,
                settings: {
                    page: page,
                    deferLoading: [redirections.recordsTotal, redirections.recordsFiltered],
                    data: redirections.models,
                    pageLength: 10
                }
            });
            let el = redirectionsView.render().$el;
            Application.getRegion('content').html(el);
        });
    }

    /**
     * Edit redirection
     *
     * @param {string} redirectionId
     */
    editRedirection(redirectionId) {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_redirection_form', {
            redirectionId : redirectionId
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let redirectionFormView = new RedirectionFormView({
                form: form,
                redirectionId: redirectionId
            });
            Application.getRegion('content').html(redirectionFormView.render().$el);
        });
    }
}

export default RedirectionRouter;
