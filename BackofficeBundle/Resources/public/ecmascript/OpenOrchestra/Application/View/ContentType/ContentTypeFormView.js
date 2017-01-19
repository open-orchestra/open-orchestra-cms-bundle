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
        this.events['change .content_type_change_type'] = '_contentTypeChangeType';
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

    /**
     * Content Type Change
     * @param {event} event
     */
    _contentTypeChangeType(event) {
        Backbone.Events.trigger('form:deactivate', this);                
        let $tr = $(event.target).closest('tr');
        let $table = $(event.target).closest('table');
        let containerId = $table.parent().attr('id');
        let index = $('tr', $table).index($tr)
        
        $('form', this.$el).ajaxSubmit({
            type: 'PATCH',
            context: this,
            success: function(response) {
                $tr.replaceWith($('#' + containerId + ' tr', response).eq(index).removeClass('hide'));
                Backbone.Events.trigger('form:activate', this);                
            }
        });
    }
}

export default ContentTypeFormView;
