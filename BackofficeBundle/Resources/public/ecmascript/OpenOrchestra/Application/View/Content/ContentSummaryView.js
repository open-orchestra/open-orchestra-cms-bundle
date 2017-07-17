import OrchestraView from 'OpenOrchestra/Application/View/OrchestraView'
import Application   from 'OpenOrchestra/Application/Application'

/**
 * @class ContentSummaryView
 */
class ContentSummaryView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.tagName = 'div';
        this.className = 'well contents clearfix';
    }

    /**
     * Initialize
     * @param {Array}  contentTypes
     */
    initialize({contentTypes}) {
        this.contentTypes = contentTypes;
    }

    /**
     * Render content type list
     */
    render() {
        let template = '';
        if (this.contentTypes.models.length > 0) {
            template = this._renderTemplate('Content/summaryView',
                {
                    contentTypes: this.contentTypes.models,
                    Application: Application
                }
            );
        } else {
            template = this._renderTemplate('List/emptyListView' ,
                {
                    title: Translator.trans('open_orchestra_backoffice.content_types.title'),
                    urlAdd: '',
                }
            );
        }
        this.$el.append(template);

        return this;
    }
}

export default ContentSummaryView;
