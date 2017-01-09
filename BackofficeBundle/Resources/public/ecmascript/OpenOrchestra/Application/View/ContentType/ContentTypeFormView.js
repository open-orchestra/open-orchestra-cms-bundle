import AbstractFormView from '../../../Service/Form/View/AbstractFormView'
import Application      from '../../Application'

/**
 * @class ContentTypeFormView
 */
class ContentTypeFormView extends AbstractFormView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        super.preinitialize();
        this.events['click #oo_content_type_linkedToSite'] = '_toogleAlwaysShared';
    }

    /**
     * Initialize
     * @param {Form}   form
     * @param {Array}  name
     */
    initialize({form, name, contentTypeId}) {
        super.initialize({form : form});
        this._name = name;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('ContentType/contentTypeEditView', {
            name: this._name
        });
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();
        this._toogleAlwaysShared();

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
    _toogleAlwaysShared(event) {
        if ($('#oo_content_type_linkedToSite:checked', this._$formRegion).length == 0) {
            $('#oo_content_type_alwaysShared', this._$formRegion).closest('div.form-group').hide();
        } else {
            $('#oo_content_type_alwaysShared', this._$formRegion).closest('div.form-group').show();
        }
    }
}

export default ContentTypeFormView;
