import OrchestraView           from '../OrchestraView'
import Content                 from '../../Model/Content/Content'
import ApplicationError        from '../../../Service/Error/ApplicationError'
import ConfirmPublishModalView from '../Statusable/ConfirmPublishModalView'
import Application             from '../../Application'

/**
 * @class ContentToolbarView
 */
class ContentToolbarView extends OrchestraView
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
     * @param {Contents}    contentVersions
     * @param {Statuses}    statuses
     * @param {Content}     content
     * @param {ContentType} contentType
     */
    initialize({contentVersions, statuses, content, contentType}) {
        this._contentVersions = contentVersions;
        this._statuses = statuses;
        this._content = content;
        this._contentType = contentType;
    }

    /**
     * Render content toolbar
     */
    render() {
        let template = this._renderTemplate('Content/contentToolbarView',
            {
                contentVersions: this._contentVersions.models,
                statuses: this._statuses.models,
                contentType: this._contentType,
                content: this._content
            }
        );
        this.$el.html(template);

        return this;
    }

    /**
     * Show input version name to add a new version
     */
    newVersionForm() {
        let versionName = this._content.get('content_id');
        let template = this._renderTemplate('Content/newVersionForm', { versionName: versionName });
        $('.new-version-form-region', this.$el).html(template);
    }

    /**
     * Create a new version
     *
     * @private
     */
    _newVersion() {
        let versionName = $('#version_name', this.$el).val() + '_' + new Date().toLocaleString();
        new Content().save({version_name: versionName}, {
            apiContext: 'new-version',
            urlParameter: {
                contentId: this._content.get('content_id'),
                language: this._content.get('language'),
                originalVersion : this._content.get('version')
            },
            success: () => {
                let url = Backbone.history.generateUrl('editContent', {
                    contentTypeId: this._contentType.get('content_type_id'),
                    language: this._content.get('language'),
                    contentId: this._content.get('content_id')
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
     * Change version content
     *
     * @param {Object} event
     * @private
     */
    _changeVersion(event) {
        let version = $(event.currentTarget).val();
        if (null !== version) {
            let url = Backbone.history.generateUrl('editContent', {
                contentTypeId: this._contentType.get('content_type_id'),
                language: this._content.get('language'),
                contentId: this._content.get('content_id'),
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

        if (true === this._contentType.get('defining_versionable') && true === status.get('published_state')) {
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
        this._content.save({'status': status}, {
            apiContext: apiContext,
            success: () => {
                Backbone.history.loadUrl(Backbone.history.fragment);
            }
        });
    }
}

export default ContentToolbarView;
