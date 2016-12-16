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
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_user_show', {'email' : this.get('email')});
        }
    }
}

export default User
