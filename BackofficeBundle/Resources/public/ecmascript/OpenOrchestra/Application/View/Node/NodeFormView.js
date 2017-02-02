import AbstractFormView from '../../../Service/Form/View/AbstractFormView'
import Redirections     from '../../Collection/Redirection/Redirections'

/**
 * @class NodeFormView
 */
class NodeFormView extends AbstractFormView
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
                language: this._language,
                nodeId: this._nodeId,
                siteLanguages: this._siteLanguages,
                version: this._version
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
}

export default NodeFormView;
