import OrchestraCollection from '../OrchestraCollection'
import Content             from '../../Model/Content/Content'

/**
 * @class Keywords
 */
class ContentsWidget extends OrchestraCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Content;
    }

    /**
     * Parse server response to create nested object
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('contents')) {
            return response.contents
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(options) {
        let readUrl = Routing.generate('open_orchestra_api_content_list_author_and_site');
        if (
            options.hasOwnProperty('parameter') &&
            options.parameter.hasOwnProperty('published') &&
            false === options.parameter.published
        ) {
            readUrl = Routing.generate('open_orchestra_api_content_list_author_and_site_not_published');
        }

        return {
            'read': readUrl
        }
    }
}

export default ContentsWidget
