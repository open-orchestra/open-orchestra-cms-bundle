import OrchestraModel from '../OrchestraModel'

/**
 * @class ContentType
 */
class ContentType extends OrchestraModel
{
    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_content_type_show', urlParameter);
        }
    }

}

export default ContentType
