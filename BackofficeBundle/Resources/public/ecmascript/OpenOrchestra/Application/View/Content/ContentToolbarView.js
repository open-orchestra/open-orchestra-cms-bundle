import OrchestraView from '../OrchestraView'
import Content       from '../../Model/Content/Content'

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
            'click .btn-manage-version': '_manageVersion',
            'click .btn-new-version': 'newVersionForm',
            'change #select-version': '_changeVersion',
            'click .btn-validate-new-version': '_newVersion'
        }
    }

    /**
     * Initialize
     * @param {Contents}        contentVersions
     * @param {string}          name
     * @param {string}          version
     * @param {string}          contentTypeId
     * @param {string}          contentId
     * @param {string}          language
     * @param {ContentFormView} contentFormView
     */
    initialize({contentVersions, name, version, contentTypeId, contentId, language, contentFormView}) {
        this._contentVersions = contentVersions;
        this._name = name;
        this._version = version;
        this._contentTypeId = contentTypeId;
        this._contentId = contentId;
        this._language = language;
        this._contentFormView = contentFormView;
    }

    /**
     * Render content toolbar
     */
    render() {
        let template = this._renderTemplate('Content/contentToolbarView',
            {
                contentVersions: this._contentVersions.models,
                version: this._version
            }
        );
        this.$el.html(template);

        return this;
    }

    /**
     * Show input version name to add a new version
     */
    newVersionForm() {
        let versionName = this._name + '_' + new Date().toLocaleString();
        let template = this._renderTemplate('Content/newVersionForm', { versionName: versionName });
        $('.new-version-form-region', this.$el).html(template);
    }

    /**
     * @private
     */
    _manageVersion() {
        this._contentFormView.manageVersion(this._contentVersions);
    }

    /**
     * Create a new version
     *
     * @private
     */
    _newVersion() {
        let versionName = $('#version_name', this.$el).val();
        new Content().save({version_name: versionName}, {
            apiContext: 'new-version',
            urlParameter: {
                contentId: this._contentId,
                language: this._language,
                originalVersion : this._version
            },
            success: () => {
                let url = Backbone.history.generateUrl('editContent', {
                    contentTypeId: this._contentTypeId,
                    language: this._language,
                    contentId: this._contentId
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
                contentTypeId: this._contentTypeId,
                language: this._language,
                contentId: this._contentId,
                version: version
            });
            Backbone.history.navigate(url, true);
        }
    }
}

export default ContentToolbarView;
