import OrchestraView from 'OpenOrchestra/Application/View/OrchestraView'

/**
 * @class BreadcrumbView
 */
class BreadcrumbView extends OrchestraView
{
    /**
     * @param options
     */
    constructor(options) {
        super(options);
        this._breadcrumb = []
    }

    /**
     * Render error
     */
    render() {
        let template = this._renderTemplate('Breadcrumb/breadcrumbView', {
            breadcrumb: this._breadcrumb
        });
        this.$el.html(template);

        return this;
    }

    /**
     * @param {Array} items
     */
    setItems(items) {
        this._breadcrumb = items;
    }

    /**
     * highlight sub menu
     *
     * @param {string} item
     */
    highlight(item) {
        $('li', this.$el).removeClass('active');
        $('#' + item, this.$el).addClass('active');
    }
}

export default BreadcrumbView;
