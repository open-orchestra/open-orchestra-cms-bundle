import ModalView          from '../../../Service/Modal/View/ModalView'

/**
 * @class AbstractRestoreModalView
 */
class AbstractRestoreModalView extends ModalView
{
    preinitialize(options) {
        super.preinitialize(options);
        this.events = this.events || [] ;
        this.events['click .valid-edit'] = '_validRestoreAndEdit';
        this.events['click .valid'] = '_validRestore';
    }

    /**
     * @param {OrchestraModel} model
     * @param {Object}         listApi
     */
    initialize({model: model, listApi: listApi}) {
        this._model = model;
        this._listApi = listApi;
    }

    /**
     * Render modal restore entity
     */
    render() {
        let template = this._renderTemplate('TrashItem/restoreModalView', {
            model: this._model
        });
        this.$el.append(template);

        return this;
    }

    /**
     * @private
     */
    _validRestoreAndEdit() {
        throw new TypeError("Please implement abstract method _validRestoreAndEdit.");

    }

    /**
     * @private
     */
    _validRestore() {
        this._model.destroy({
             success: () => {
                this._listApi.draw(false);
                this.hide();
             }
        });
    }
}

export default AbstractRestoreModalView;
