import OrchestraModel from '../OrchestraModel'

/**
 * @class TrashItem
 */
class TrashItem extends OrchestraModel
{
    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "delete":
                urlParameter.trashItemId = this.get('id');
                return Routing.generate('open_orchestra_api_trashcan_restore', urlParameter);
        }
    }
}

export default TrashItem
