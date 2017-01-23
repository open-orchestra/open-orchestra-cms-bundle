import DataTableCollection from '../../../Service/DataTable/Collection/DataTableCollection'
import Redirection         from '../../Model/Redirection/Redirection'

/**
 * @class Redirections
 */
class Redirections extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Redirection;
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                return this._getSyncReadUrl(options, urlParameter);
        }
    }

    /**
     * @param {Object} options
     * @param {Object} urlParameter
     *
     * @returns {string}
     * @private
     */
    _getSyncReadUrl(options, urlParameter) {
        let context = options.context || null;
        switch (context) {
            case "list":
                return Routing.generate('open_orchestra_api_redirection_list', urlParameter);
            default:
                return Routing.generate('open_orchestra_api_redirection_node_list', urlParameter);
        }
    }
}

export default Redirections
