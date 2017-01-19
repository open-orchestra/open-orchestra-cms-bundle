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
}

export default Log
