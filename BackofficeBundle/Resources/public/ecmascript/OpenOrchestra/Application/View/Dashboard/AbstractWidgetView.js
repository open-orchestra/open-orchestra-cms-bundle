import OrchestraView from '../OrchestraView'

/**
 * @class AbstractWidgetView
 */
class AbstractWidgetView extends OrchestraView
{
    /**
     * Constructor
     *
     * @param {Object} options
     */
    constructor(options) {
        super(options);
        if (this.constructor === AbstractWidgetView) {
            throw TypeError("Can not construct abstract class");
        }
    }

    /**
     * @inheritdoc
     */
    preinitialize() {
        this.tagName = 'div';
        this.className = 'col-lg-6';
    }

    /**
     * @inheritdoc
     */
    initialize({collection}) {
        this.collection = collection;
    }

    /**
     * Get title widget translation key
     *
     * @return {String}
     */
    getTitleKey() {
        throw new TypeError("Please implement abstract method getTitleKey.");
    }

    /**
     * Get link edit element
     *
     * @return {String}
     */
    getEditLink() {
        throw new TypeError("Please implement abstract method getEditLink.");
    }

    /**
     * Render
     */
    render() {
        this._renderTemplate('openorchestrabackoffice/underscore/Dashboard/listWidgetView',
            {
                title: this.getTitleKey(),
                editLink: this.getEditLink(),
                entities: this.collection.models
            },
            (template) => {
                this.$el.html(template);
            }
        );

        return this;
    }
}

export default AbstractWidgetView;
