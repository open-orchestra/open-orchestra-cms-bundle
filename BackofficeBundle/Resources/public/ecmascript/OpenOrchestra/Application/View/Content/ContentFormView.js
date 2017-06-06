import AbstractFormView          from '../../../Service/Form/View/AbstractFormView'
import Content                   from '../../Model/Content/Content'
import Contents                  from '../../Collection/Content/Contents'
import Statuses                  from '../../Collection/Status/Statuses'
import TrashFormViewButtonsMixin from '../../../Service/Form/Mixin/TrashFormViewButtonsMixin'
import ContentToolbarView        from './ContentToolbarView'
import ContentVersionsView       from './ContentVersionsView'
import FlashMessageBag           from '../../../Service/FlashMessage/FlashMessageBag'

/**
 * @class ContentFormView
 */
class ContentFormView extends mix(AbstractFormView).with(TrashFormViewButtonsMixin)
{
    /**
     * Initialize
     * @param {Form}        form
     * @param {ContentType} contentType
     * @param {Content}     content
     * @param {Array}       siteLanguages
     */
    initialize({form, contentType, content, siteLanguages}) {
        super.initialize({form : form});
        this._contentType = contentType;
        this._content = content;
        this._siteLanguages = siteLanguages;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Content/contentEditView', {
            contentType: this._contentType,
            content: this._content,
            siteLanguages: this._siteLanguages,
            messages: FlashMessageBag.getMessages()
        });
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();

        return this;
    }

    /**
     * @inheritDoc
     */
    _renderForm() {
        if (true === this._contentType.get('defining_versionable') || true === this._contentType.get('defining_statusable')) {
            this._renderContentActionToolbar($('.content-action-toolbar', this.$el));
        }
        super._renderForm();

        // activate tab data
        $('.nav-tabs a.nav-tab-data', this._$formRegion).tab('show');
        $('.tab-content .tab-pane', this._$formRegion).removeClass('active');
        $('.tab-content .tab-data', this._$formRegion).addClass('active');
    }

    /**
     * @param {Object} $selector
     * @private
     */
    _renderContentActionToolbar($selector) {
        this._displayLoader($selector);

        let statuses = new Statuses();
        let contentVersions = new Contents();
        $.when(
            statuses.fetch({
                apiContext: 'content',
                urlParameter: {
                    language: this._content.get('language'),
                    contentId: this._content.get('content_id'),
                    version: this._content.get('version')
                }
            }),
            contentVersions.fetch({
                apiContext: 'list-version',
                urlParameter: {
                    language: this._content.get('language'),
                    contentId: this._content.get('content_id')
                }
            })
        ).done( () => {
            let contentToolbarView = new ContentToolbarView({
                contentVersions: contentVersions,
                statuses: statuses,
                content: this._content,
                contentType: this._contentType
            });
            $selector.html(contentToolbarView.render().$el);
        });
    }

    /**
     * Manage Version
     * @param {Contents} contentVersions
     */
    manageVersion(contentVersions) {
        let contentVersionsView = new ContentVersionsView({
            collection: contentVersions,
            contentId: this._content.get('content_id'),
            language: this._content.get('language'),
            contentTypeId: this._contentType.get('content_type_id')
        });
        this._$formRegion.html(contentVersionsView.render().$el);
    }

    /**
     * Delete
     * @param {event} event
     */
    _deleteElement(event) {
        let content = new Content({'id': this._content.get('content_id')});

        content.destroy({
            apiContext: 'delete-multiple',
            success: () => {
                let url = Backbone.history.generateUrl('listContent', {
                    contentTypeId: this._contentType.get('content_type_id'),
                    language: this._content.get('language')
                });
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default ContentFormView;
