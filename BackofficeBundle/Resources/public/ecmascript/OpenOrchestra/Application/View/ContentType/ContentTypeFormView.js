import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import Application          from '../../Application'
import ContentType          from '../../Model/ContentType/ContentType'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'
import ApplicationError     from '../../../Service/Error/ApplicationError'

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
        this.events['click #oo_content_type_linkedToSite'] = '_toggleAlwaysShared';
    }

    /**
     * Initialize
     * @param {Form}   form
     * @param {Array}  name
     * @param {string} contentTypeId
     */
    initialize({form, name, contentTypeId = null}) {
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
        this._toggleAlwaysShared();

        return this;
    }

    /**
     * toggle input always shared
     */
    _toggleAlwaysShared() {
        if ($('#oo_content_type_linkedToSite:checked', this._$formRegion).length == 0) {
            $('#oo_content_type_alwaysShared', this._$formRegion).closest('div.form-group').hide();
        } else {
            $('#oo_content_type_alwaysShared', this._$formRegion).closest('div.form-group').show();
        }
    }

   /**
    * Redirect to edit content type view
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
       Backbone.Events.trigger('form:deactivate', this);
       Backbone.history.navigate(url, true);
    }

   /**
    * Delete content type
    */
   _deleteElement() {
        if (null === this._contentTypeId) {
            throw new ApplicationError('Invalid contentTypeId');
        }
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
