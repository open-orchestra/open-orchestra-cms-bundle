import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import Redirections         from '../../Collection/Redirection/Redirections'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'
import Node                 from '../../Model/Node/Node'

/**
 * @class NodeFormView
 */
class NodeFormView extends mix(AbstractFormView).with(FormViewButtonsMixin)
{
    /**
     * Pre initialize
     * @param {Object} options
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['change #oo_node_status'] = this._toggleCheckboxSaveOldPublishedVersion;
    }

    /**
     * Initialize
     * @param {Form}   form
     * @param {Array}  siteLanguages
     * @param {string} siteId
     * @param {string} nodeId
     * @param {string} language
     * @param {string} version
     */
    initialize({form, siteLanguages, siteId, nodeId, language, version}) {
        super.initialize({form : form});
        this._siteLanguages = siteLanguages;
        this._siteId = siteId;
        this._nodeId = nodeId;
        this._language = language;
        this._version = version;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Node/nodeEditView',
            {
                language     : this._language,
                nodeId       : this._nodeId,
                siteLanguages: this._siteLanguages,
                version      : this._version,
                title        : $('#oo_node_name', this._form.$form).val()
            }
        );
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();

        this._renderRedirections();

        return this;
    }

    /**
     * @inheritDoc
     */
    _renderForm() {
        super._renderForm();

        // hide checkbox oo_node_save_old_published_version by default
        $('#oo_node_saveOldPublishedVersion', this.$el).closest('.form-group').hide();
    }

    /**
     * @private
     */
    _toggleCheckboxSaveOldPublishedVersion(event) {
        let formGroupCheckbox = $('#oo_node_saveOldPublishedVersion', this.$el).closest('.form-group');
        formGroupCheckbox.hide();
        if ($('option:selected', $(event.currentTarget)).attr('data-published-state')) {
            formGroupCheckbox.show();
        }
    }

    /**
     * @private
     */
    _renderRedirections() {
        new Redirections().fetch({
            urlParameter: {
                locale: this._language,
                nodeId: this._nodeId,
                siteId: this._siteId
            },
            success: (redirections) => {
                if (redirections.length > 0) {
                    let template = this._renderTemplate('Node/redirectionListView', { redirections: redirections.models });
                    $('.tab-seo', this.$el).append(template);
                }
            }
        });
    }

    /**
     * Delete
     * @param {event} event
     */
    _deleteElement(event) {
        let node = new Node({id: this._nodeId});
        node.destroy({
            success: () => {
                let url = Backbone.history.generateUrl('showNodes', {
                    language: this._language
                });
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default NodeFormView;
