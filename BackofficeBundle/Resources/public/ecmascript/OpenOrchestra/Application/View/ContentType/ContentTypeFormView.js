import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import Application          from '../../Application'
import ContentType          from '../../Model/ContentType/ContentType'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'

/**
 * @class ContentTypeFormView
 */
class ContentTypeFormView extends mix(AbstractFormView).with(FormViewButtonsMixin) 
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
        this._contentTypeId = contentTypeId;
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

    /**
     * Redirect to edit user view
     *
     * @param {mixed}  data
     * @param {string} textStatus
     * @param {object} jqXHR
     * @private
     */
    _redirectEditElement(data, textStatus, jqXHR) {
        let contentTypeId = jqXHR.getResponseHeader('contentTypeId');
        let name = jqXHR.getResponseHeader('name');
        if (null === contentTypeId || null === name) {
            throw new ApplicationError('Invalid contentTypeId or name');
        }
        let url = Backbone.history.generateUrl('editContentType', {
            contentTypeId: contentTypeId,
            name: name
        });
        let message = new FlashMessage(data, 'success');
        FlashMessageBag.addMessageFlash(message);
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }

    /**
     * Delete
     * @param {event} event
     */
    _deleteElement(event) {
        let contentType = new ContentType({'content_type_id': this._contentTypeId});
        contentType.destroy({
            success: () => {
                let url = Backbone.history.generateUrl('listContentType');
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default ContentTypeFormView;
