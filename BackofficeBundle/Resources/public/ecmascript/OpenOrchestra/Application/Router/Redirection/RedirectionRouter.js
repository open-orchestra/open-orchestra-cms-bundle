import OrchestraRouter     from 'OpenOrchestra/Application/Router/OrchestraRouter'
import Application         from 'OpenOrchestra/Application/Application'
import FormBuilder         from 'OpenOrchestra/Service/Form/Model/FormBuilder'
import Redirections        from 'OpenOrchestra/Application/Collection/Redirection/Redirections'
import RedirectionsView    from 'OpenOrchestra/Application/View/Redirection/RedirectionsView'
import RedirectionFormView from 'OpenOrchestra/Application/View/Redirection/RedirectionFormView'

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
            'redirection/list(/:page)'       : 'listRedirections',
            'redirection/new'                : 'newRedirection',
            'redirection/edit/:redirectionId': 'editRedirection'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.menu.configuration.title')
            },
            {
                label: Translator.trans('open_orchestra_backoffice.menu.configuration.redirection'),
                link: '#'+Backbone.history.generateUrl('listRedirections')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            '*' : 'navigation-redirection'
        };
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
        page = Number(page) - 1;
        let pageLength = Application.getConfiguration().getParameter('datatable').pageLength;
        $.when(
            redirections.fetch({apiContext: 'list'})
        ).done(() => {
            let redirectionsView = new RedirectionsView({
                collection: redirections,
                settings: {
                    page: page,
                    deferLoading: [redirections.recordsTotal, redirections.recordsFiltered],
                    data: redirections.models,
                    pageLength: pageLength
                }
            });
            let el = redirectionsView.render().$el;
            Application.getRegion('content').html(el);
        });
    }

    /**
     * New redirection
     */
    newRedirection() {
        let url = Routing.generate('open_orchestra_backoffice_redirection_new');

        this._displayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let redirectionFormView = new RedirectionFormView({
                form: form
             });
            Application.getRegion('content').html(redirectionFormView.render().$el);
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
