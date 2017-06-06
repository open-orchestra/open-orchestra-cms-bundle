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
        this.events['click button.submit-form'] = '_submitWithContextRefresh';
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
        let context = this;
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

        $('[name^="oo_site[aliases]"][name$="[language]"]', this.$el).each(function() {
            context._hideRemoveButton($(this).val());
        });

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
                    context.set('siteLanguages', eval(languages));
                    Application.setContext(context);
                }
                form._parseHtml(data);
            }
        })(this._form);
        this._form.submit(this.getStatusCodeForm(event));
    }

    /**
     * remove a form
     *
     * {Object} event
     */
    _removeForm(event) {
        let $table = $(event.target).closest('table');
        let $form = $(event.target).closest('tbody');
        let language = $('[name^="oo_site[aliases]"][name$="[language]"]', $form).val();
        $form.remove();
        if ($table.children('tbody').length === 0) {
            $('thead', $table).addClass('hide');
        }
        this._hideRemoveButton(language);
    }

    /**
     * hide remove button
     *
     * {String} language
     */
    _hideRemoveButton(language) {
        let $selects = $('[name^="oo_site[aliases]"][name$="[language]"]', this.$el).filter(function() {
            return $(this).val() === language;
        });
        if ($selects.length == 1) {
            $selects.closest('tbody').find('.remove-form').remove();
        }
    }

}

export default SiteFormView;
