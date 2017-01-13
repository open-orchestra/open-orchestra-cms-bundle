import OrchestraView    from '../../../Application/View/OrchestraView'
import Application      from '../../../Application/Application'
import ConfirmModalView from '../../ConfirmModal/View/ConfirmModalView'

/**
 * @class AbstractCollectionView
 */
class AbstractCollectionView extends OrchestraView
{
    /**
     * Constructor
     */
    constructor (options) {
        super(options);
        if (this.constructor === AbstractCollectionView) {
            throw TypeError("Can not construct abstract class");
        }
    }

    /**
     * @inheritdoc
     */
    preinitialize({removeMultiple = true}) {
        this._removeMultiple = removeMultiple;
        this.events = {
            'click .search-engine button.submit': '_search'
        };
        if (true === this._removeMultiple) {
            this.events['click .btn-delete:not(.disabled)'] =  '_confirmDelete';
        }
    }

    /**
     * List view should be an instance of AbstractDataTableView
     *
     * @param {OrchestraCollection} collection
     * @param {Object}              settings
     */
    initialize({collection, settings}) {
        this._collection = collection;
        if (true === this._removeMultiple) {
            this._collection.bind('change:delete', this._toggleButtonDelete, this);
        }
        this._settings = settings;
        this._listView = null;
    }

    /**
     * Search in collection
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _search(event) {
        event.stopPropagation();
        if (null === this._listView) {
            throw TypeError("Parameter listView should be an instance of AbstractDataTableView");
        }
        let formData = $('form.search-engine', this.$el).serializeArray();
        let filters = {};
        for (let data of formData) {
            filters[data.name] = data.value;
        }
        this._listView.filter(filters);

        return false;
    }

    /**
     * Enable/disable button delete when a model of collection is marked than deletable
     *
     * @private
     */
    _toggleButtonDelete() {
        let models = this._collection.where({'delete': true});
        $('.btn-delete', this.$el).removeClass('disabled');
        if(0 === models.length) {
            $('.btn-delete', this.$el).addClass('disabled');
        }
    }

    /**
     * Show modal confirm to delete models
     *
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _confirmDelete(event) {
        event.stopPropagation();
        let confirmModalView = new ConfirmModalView({
            confirmTitle: Translator.trans('open_orchestra_backoffice.confirm_remove.title'),
            confirmMessage: Translator.trans('open_orchestra_backoffice.confirm_remove.message'),
            yesCallback: this._remove,
            context: this
        });

        Application.getRegion('modal').html(confirmModalView.render().$el);
        confirmModalView.show();

        return false;
    }

    /**
     * Remove
     *
     * @private
     */
    _remove() {
        if (null === this._listView) {
            throw TypeError("Parameter listView should be an instance of AbstractDataTableView");
        }
        let models = this._collection.where({'delete': true});
        this._collection.destroyModels(models, {
            success: () => {
                this._listView.api.draw(false);
            }
        });
    }
}

export default AbstractCollectionView;
