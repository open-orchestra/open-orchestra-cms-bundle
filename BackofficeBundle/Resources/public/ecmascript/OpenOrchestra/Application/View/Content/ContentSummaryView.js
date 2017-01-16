import OrchestraView from '../OrchestraView'
import Application   from '../../Application'

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
     * Render node tree
     */
    render() {
        for (let contentType of this.contentTypes.models) {
            let template = this._renderTemplate('Content/summaryElementView',
                    {
                        name: contentType.get('name'),
                        contentTypeId: contentType.get('content_type_id'),
                        language: Application.getContext().user.language.contribution
                    }
                );
            this.$el.append(template);
        }

        return this;
    }
}

export default ContentSummaryView;
