import OrchestraView from '../OrchestraView'
import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'

/**
 * @class NewBlockListView
 */
class NewBlockListView extends OrchestraView
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
        let orderedBlockComponents = this._orderBlockComponentByCategory(this._listBlockComponents);
        let template = this._renderTemplate('Block/newBlockListView',
            {
                numberResult: this._listBlockComponents.length,
                orderedBlockComponents: orderedBlockComponents,
                categories: this._categories,
                language: this._language,
                messages: FlashMessageBag.getMessages()
            }
        );
        this.$el.html(template);

        return this;
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
            console.log(blockComponent);
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

        this.render();

        return false;
    }
}

export default NewBlockListView;
