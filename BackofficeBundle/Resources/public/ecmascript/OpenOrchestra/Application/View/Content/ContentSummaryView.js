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
        let template = this._renderTemplate('Content/summaryView',
            {
                contentTypes: this.contentTypes.models,
                Application: Application
            }
        );
        this.$el.append(template);

        return this;
    }
}

export default ContentSummaryView;
