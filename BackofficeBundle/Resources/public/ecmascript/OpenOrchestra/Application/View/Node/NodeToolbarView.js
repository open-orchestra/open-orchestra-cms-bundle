import OrchestraView           from '../OrchestraView'
import Application             from '../../Application'
import Status                  from '../../Model/Status/Status'
import Node                    from '../../Model/Node/Node'
import ApplicationError        from '../../../Service/Error/ApplicationError'
import ConfirmPublishModalView from '../Statusable/ConfirmPublishModalView'

/**
 * @class NodeToolbarView
 */
class NodeToolbarView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.className = 'container-fluid search-engine';
        this.events = {
            'click .dropdown-workflow li a': '_changeStatus',
            'click .btn-new-version': 'newVersionForm',
            'change #select-version': '_changeVersion',
            'click .btn-validate-new-version': '_newVersion'
        }
    }

    /**
     * Initialize
     * @param {Node}     node
     * @param {Statuses} statuses
     * @param {Nodes}    nodeVersions
     * @param {string}   routeName
     */
    initialize({node, statuses, nodeVersions, routeName}) {
        this._node = node;
        this._nodeVersions = nodeVersions;
        this._statuses = statuses;
        this._routeName = routeName;
    }

    /**
     * Render node toolbar
     */
    render() {
        let template = this._renderTemplate('Node/nodeToolbarView',
            {
                node: this._node,
                statuses: this._statuses.models,
                nodeVersions: this._nodeVersions.models
            }
        );
        this.$el.html(template);

        return this;
    }

    /**
     * Show input version name to add a new version
     */
    newVersionForm() {
        let versionName = this._node.get('name');
        let template = this._renderTemplate('Node/newVersionForm', { versionName: versionName });
        $('.new-version-form-region', this.$el).html(template);
    }

    /**
     * Create a new version
     *
     * @private
     */
    _newVersion() {
        let versionName = $('#version_name', this.$el).val() + '_' + new Date().toLocaleString();
        new Node().save({version_name: versionName}, {
            urlParameter: {
                nodeId: this._node.get('node_id'),
                language: this._node.get('language'),
                originalVersion : this._node.get('version')
            },
            success: () => {
                let url = Backbone.history.generateUrl(this._routeName, {
                    nodeId: this._node.get('node_id'),
                    language: this._node.get('language')
                });
                if (url === Backbone.history.fragment) {
                    Backbone.history.loadUrl(url);
                } else {
                    Backbone.history.navigate(url, true);
                }
            }
        })
    }

    /**
     * Change version node
     *
     * @param {Object} event
     * @private
     */
    _changeVersion(event) {
        let version = $(event.currentTarget).val();
        if (null !== version) {
            let url = Backbone.history.generateUrl(this._routeName, {
                nodeId: this._node.get('node_id'),
                language: this._node.get('language'),
                version: version
            });
            Backbone.history.navigate(url, true);
        }
    }

    /**
     * @param {Object} event
     * @private
     */
    _changeStatus(event) {
        let statusId = $(event.currentTarget).attr('data-id');
        let status = this._statuses.findWhere({id: statusId});
        if (typeof status == "undefined") {
            throw new ApplicationError('Status with id '+statusId+ 'not found');
        }

        if (true === status.get('published_state')) {
            let confirmPublishModalView = new ConfirmPublishModalView({
                status: status,
                callbackConfirmPublish: $.proxy(this._saveUpdateStatus, this)
            });
            Application.getRegion('modal').html(confirmPublishModalView.render().$el);
            confirmPublishModalView.show();
        } else {
            this._saveUpdateStatus(status);
        }
    }

    /**
     * @param {Status}  status
     * @param {boolean} saveOldPublishedVersion
     * @private
     */
    _saveUpdateStatus(status, saveOldPublishedVersion = false) {
        let apiContext = 'update_status';
        if (saveOldPublishedVersion) {
            apiContext = 'update_status_with_save_published';
        }
        this._node.save({'status': status}, {
            apiContext: apiContext,
            success: () => {
                Backbone.history.loadUrl(Backbone.history.fragment);
            }
        });
    }
}

export default NodeToolbarView;
