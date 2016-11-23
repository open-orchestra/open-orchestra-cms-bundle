import OrchestraCollection from '../OrchestraCollection'
import Node                from '../../Model/Node/Node'

/**
 * @class NodesWidgets
 */
class NodesWidget extends OrchestraCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Node;
    }

    /**
     * Parse server response to create nested object
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('nodes')) {
            return response.nodes
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(options) {
        let readUrl = Routing.generate('open_orchestra_api_node_list_author_and_site');
        if (
            options.hasOwnProperty('parameter') &&
            options.parameter.hasOwnProperty('published') &&
            false === options.parameter.published
        ) {
            readUrl = Routing.generate('open_orchestra_api_node_list_author_and_site_not_published');
        }

        return {
            'read': readUrl
        }
    }
}

export default NodesWidget
