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
     * Initialize
     * @param {Form}   form
     * @param {Array}  name
     * @param {string} siteId
     */
    initialize({form, name, siteId = null}) {
        super.initialize({form : form});
        this._name = name;
        this._siteId = siteId;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Site/siteEditView', {
            name: this._name
        });
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();

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
}

export default SiteFormView;
