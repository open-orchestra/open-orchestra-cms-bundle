import OrchestraView from '../OrchestraView'
import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'

/**
 * @class AbstractNewBlockComponentListView
 */
class AbstractNewBlockComponentListView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.events = {
            'click .search-engine button.submit': '_search'
        }
    }

    /**
     * Initialize
     * @param {BlockComponents} blockComponents
     * @param {string}          language
     */
    initialize({blockComponents, language}) {
        this._blockComponents = blockComponents;
        this._listBlockComponents = blockComponents.models;
        this._categories = _.uniq(this._blockComponents.pluck('category'), false, (blockCategory) => {return blockCategory.get('key')});
        this._language = language;

    }

    /**
     * Render list block component
     */
    render() {
        let template = this._renderTemplate('Block/newBlockListComponentView',
            {
                categories: this._categories,
                messages: FlashMessageBag.getMessages(),
                labelButtonBack: this._getLabelButtonBack(),
                urlButtonBack: this._getUrlButtonBack()
            }
        );
        this.$el.html(template);

        if (this._listBlockComponents.length > 0) {
            this._renderListComponent();
        } else {
            $('.list-block-component', this.$el).html(this._renderTemplate('List/emptyListView'));
        }

        return this;
    }

    /**
     * Render list
     * @private
     */
    _renderListComponent() {
        let orderedBlockComponents = this._orderBlockComponentByCategory(this._listBlockComponents);
        let template = this._renderTemplate('Block/blockListComponentView',
            {
                numberResult: this._listBlockComponents.length,
                orderedBlockComponents: orderedBlockComponents,
                categories: this._categories,
                addBlockUrl: $.proxy(this._getAddBlockUrl, this)
            }
        );

        $('.list-block-component', this.$el).html(template);
    }


    /**
     * @private
     *
     * @return string
     */
    _getLabelButtonBack() {
        throw new TypeError("Please implement abstract method _getLabelButtonBack.");
    }

    /**
     * @private
     *
     * @return string
     */
    _getUrlButtonBack() {
        throw new TypeError("Please implement abstract method _getUrlButtonBack.");
    }

    /**
     * @private
     * @param {BlockComponent} blockComponent
     *
     * @return string
     */
    _getAddBlockUrl(blockComponent) {
        throw new TypeError("Please implement abstract method _getAddBlockUrl.");
    }

    /**
     * @param blockComponents
     *
     * @returns {Object}
     * @private
     */
    _orderBlockComponentByCategory(blockComponents) {
        let orderedList = {};
        for (let blockComponent of blockComponents) {
            let blockCategory = blockComponent.get('category').get('label');
            if (!orderedList.hasOwnProperty(blockCategory)) {
                orderedList[blockCategory] = [];
            }
            orderedList[blockCategory].push(blockComponent);
        }

        return orderedList;
    }

    /**
     * Search block component in list
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _search(event) {
        event.stopPropagation();
        let formData = $('form.search-engine', this.$el).serializeArray();
        let filters = {};
        for (let data of formData) {
            filters[data.name] = data.value;
        }
        this._listBlockComponents = this._blockComponents.filter((blockComponent) => {
            let pattern = new RegExp(filters.name,"gi");
            let testName = pattern.test(blockComponent.get("name"));
            if ('' !== filters.category ) {
                return testName && filters.category === blockComponent.get("category").get("key");
            }

            return testName;
        });

        this._renderListComponent();

        return false;
    }
}

export default AbstractNewBlockComponentListView;
