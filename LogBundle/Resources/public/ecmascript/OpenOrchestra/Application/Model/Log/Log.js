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
        this.idAttribute = 'id';
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        urlParameter.logId = this.get('id');
    }
}

export default Log
