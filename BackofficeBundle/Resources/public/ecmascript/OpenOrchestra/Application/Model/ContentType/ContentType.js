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
        let context = options.context || null;
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_content_type_show', urlParameter);
            case "delete":
                urlParameter.contentTypeId = this.get('content_type_id');
                return Routing.generate('open_orchestra_api_content_type_delete', urlParameter);
        }
    }
}

export default ContentType
