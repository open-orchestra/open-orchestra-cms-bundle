import OrchestraModel from '../OrchestraModel'

/**
 * @class User
 */
class User extends OrchestraModel
{
    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_user_show', {'email' : this.get('email')});
            case "update":
                return Routing.generate('open_orchestra_api_user_remove_group', urlParameter);
        }
    }
}

export default User
