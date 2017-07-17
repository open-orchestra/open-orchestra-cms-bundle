import OrchestraCollection from 'OpenOrchestra/Application/Collection/OrchestraCollection'
import Content             from 'OpenOrchestra/Application/Model/Content/Content'

/**
 * @class ContentsWidget
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
    _getSyncUrl(method, options) {
        switch (method) {
            case "read":
                if (
                    options.hasOwnProperty('parameter') &&
                    options.parameter.hasOwnProperty('published') &&
                    false === options.parameter.published
                ) {
                    return Routing.generate('open_orchestra_api_content_list_author_and_site_not_published');
                }

                return Routing.generate('open_orchestra_api_content_list_author_and_site');
        }
    }
}

export default ContentsWidget
