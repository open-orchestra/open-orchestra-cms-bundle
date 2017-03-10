import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import Application          from '../../Application'
import Site                 from '../../Model/Site/Site'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'
import ApplicationError     from '../../../Service/Error/ApplicationError'

/**
 * @class SiteFormView
 */
class SiteFormView extends mix(AbstractFormView).with(FormViewButtonsMixin)
{
    /**
     * Pre initialize
     * @param {Object} options
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['click button.submit-form'] = '_submitWithContextRefresh'
    }

    /**
     * Initialize
     * @param {Form}   form
     * @param {string} siteId
     */
    initialize({form, siteId = null}) {
        super.initialize({form : form});
        this._siteId = siteId;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Site/siteEditView');
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();

        return this;
    }

    /**
     * Render a form
     *
     * @private
     */
    _renderForm() {
        super._renderForm();
        let title = $('#oo_site_name', this.$el).val();
        if (null === this._siteId) {
            title = Translator.trans('open_orchestra_backoffice.table.sites.new');
        }
        $('#page-name', this.$el).html(title);

        return this;
    }

    /**
     * Redirect to edit site view
     *
     * @param {mixed}  data
     * @param {string} textStatus
     * @param {object} jqXHR
     * @private
     */
    _redirectEditElement(data, textStatus, jqXHR) {
        let siteId = jqXHR.getResponseHeader('siteId');
        let name = jqXHR.getResponseHeader('name');
        if (null === siteId || null === name) {
            throw new ApplicationError('Invalid siteId or name');
        }
        let url = Backbone.history.generateUrl('editSite', {
            siteId: siteId,
            name: name
        });
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }

    /**
     * Delete
     */
    _deleteElement() {
        if (null === this._siteId) {
            throw new ApplicationError('Invalid siteId');
        }
        let site = new Site({'site_id': this._siteId});
        site.destroy({
            success: () => {
                let url = Backbone.history.generateUrl('listSite');
                Backbone.history.navigate(url, true);
            }
        });
    }

    /**
     * Submit form
     * @param {object} event
     */
    _submitWithContextRefresh(event) {
        event.preventDefault();
        this._form._formSuccess = (function(form){
            return (data, textStatus, jqXHR) => {
                let languages = jqXHR.getResponseHeader('languages');
                if (null !== languages) {
                    let context = Application.getContext();
                    context.siteLanguages = eval(languages);
                    Application.setContext(context);
                }
                form._parseHtml(data);
            }
        })(this._form);
        this._form.submit(this.getStatusCodeForm(event));
    }
}

export default SiteFormView;
