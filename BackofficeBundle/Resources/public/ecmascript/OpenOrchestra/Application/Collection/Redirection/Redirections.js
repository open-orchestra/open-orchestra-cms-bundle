import OrchestraCollection from '../OrchestraCollection'
import Redirection         from '../../Model/Redirection/Redirection'

/**
 * @class Redirections
 */
class Redirections extends OrchestraCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Redirection;
    }

    /**
     * @inheritDoc
     */
    parse(response) {
        if (response.hasOwnProperty('redirections')) {
            return response.redirections
        }
    }
    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_redirection_node_list', urlParameter);
        }
    }
}

export default Redirections
