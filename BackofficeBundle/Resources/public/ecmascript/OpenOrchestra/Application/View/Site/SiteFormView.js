import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import Application          from '../../Application'
import Site                 from '../../Model/Site/Site'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'
import ApplicationError     from '../../../Service/Error/ApplicationError'
import ConfirmModalView     from '../../../Service/ConfirmModal/View/ConfirmModalView'

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
        this.events['click button.submit-form'] = '_submitWithContextRefresh';
        this.events['click .remove-form'] = '_confirmRemove';
    }

    /**
     * Initialize
     * @param {Form}   form
     * @param {string} siteId
     * @param {boolean}             inPlatformContext
     */
    initialize({form, siteId = null, inPlatformContext}) {
        super.initialize({form : form});
        this._siteId = siteId;
        this._inPlatformContext = inPlatformContext;
    }

    /**
     * @inheritdoc
     */
    render() {
        let title = $('#oo_site_name', this._form.$form).val();
        if (null === this._siteId) {
            title = Translator.trans('open_orchestra_backoffice.table.sites.new');
        }
        let template = this._renderTemplate('Site/siteEditView', {
            title: title,
            inPlatformContext : this._inPlatformContext
        });
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();

        return this;
    }

    /**
     * @inheritdoc
     */
    getStatusCodeForm(event) {
        let statusCodeForm = {
            '422': $.proxy(this.refreshRender, this),
            '200': Application.getContext().refreshContext,
            '201': $.proxy(this._redirectEditElement, this)
        };

        if ($(event.currentTarget).hasClass('submit-continue-form')) {
            statusCodeForm['201'] = $.proxy(this._redirectNewElement, this);
        }

        return statusCodeForm;
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
        let url = Backbone.history.generateUrl(this._inPlatformContext ? 'editPlatformSite' : 'editSite', {
            siteId: siteId
        });
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url);
        Application.getContext().refreshContext();
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
                let url = Backbone.history.generateUrl(this._inPlatformContext ? 'listPlatformSite' : 'listSite');
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

    /**
     * Show modal confirm to remove
     *
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _confirmRemove(event) {
        console.log('_confirmRemove');
        event.stopPropagation();
        let confirmModalView = new ConfirmModalView({
            confirmTitle: Translator.trans('open_orchestra_backoffice.confirm_remove_prototype.title'),
            confirmMessage: Translator.trans('open_orchestra_backoffice.confirm_remove_prototype.message'),
            yesCallback: this._removeForm,
            context: this,
            callbackParameter: [event]
        });

        Application.getRegion('modal').html(confirmModalView.render().$el);
        confirmModalView.show();

        return false;
    }

    /**
     * remove a form
     *
     * {Object} event
     */
    _removeForm(event) {
        let $table = $(event.target).closest('table');
        let $form = $(event.target).closest('tbody');
        let language = $('input[name^="oo_site[aliases]"][name$="[language]"]', $form).val();
        $form.remove();
        if ($table.children('tbody').length === 0) {
            $('thead', $table).addClass('hide');
        }
    }
}

export default SiteFormView;
