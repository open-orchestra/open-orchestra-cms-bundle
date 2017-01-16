import AbstractFormView from '../../../Service/Form/View/AbstractFormView'
import Application      from '../../Application'
import Site             from '../../Model/Site/Site'

/**
 * @class SiteFormView
 */
class SiteFormView extends AbstractFormView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        super.preinitialize();
        this.events['click #delete_oo_site'] = '_deleteSite';
    }

    /**
     * Initialize
     * @param {Form}   form
     * @param {Array}  name
     */
    initialize({form, name, siteId}) {
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
     * @return {Object}
     */
    getStatusCodeForm() {
        return {
            '200': $.proxy(this.refreshRender, this),
            '201': $.proxy(this.refreshRender, this),
            '422': $.proxy(this.refreshRender, this)
        }
    }

    /**
     * Delete
     * @param {event} event
     */
    _deleteSite(event) {
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
