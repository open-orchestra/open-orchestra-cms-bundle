import OrchestraModel from '../OrchestraModel'

/**
 * @class Log
 */
class Log extends OrchestraModel
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.idAttribute = 'log_id';
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        urlParameter.logId = this.get('log_id');
    }
}

export default Log
