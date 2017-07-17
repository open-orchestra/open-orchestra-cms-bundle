import OrchestraModel from 'OpenOrchestra/Application/Model/OrchestraModel'

/**
 * @class Redirection
 */
class Redirection extends OrchestraModel
{
    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        urlParameter.redirectionId = this.get('redirection_id');
        switch (method) {
            case "delete":
                return Routing.generate('open_orchestra_api_redirection_delete', urlParameter);
        }
    }
}

export default Redirection
