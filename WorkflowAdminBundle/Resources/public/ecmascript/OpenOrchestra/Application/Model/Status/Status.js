import OrchestraModel from '../OrchestraModel'

/**
 * @class Status
 */
class Status extends OrchestraModel
{
    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        urlParameter.statusId = this.get('status_id');
        switch (method) {
            case "delete":
                return Routing.generate('open_orchestra_api_status_delete', urlParameter);
        }
    }
}

export default Status
