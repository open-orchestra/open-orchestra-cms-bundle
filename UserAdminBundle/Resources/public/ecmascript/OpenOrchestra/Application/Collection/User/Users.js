import DataTableCollection from '../../../Service/DataTable/Collection/DataTableCollection'
import User                from '../../Model/User/User'

/**
 * @class Users
 */
class Users extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = User;
    }

    /**
     * @inheritdoc
     */
    toJSON(options) {
        return {
            'users': super.toJSON(options)
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                return this._getSyncReadUrl(options, urlParameter);

            case "delete":
                return Routing.generate('open_orchestra_api_user_delete_multiple');
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
        let apiContext = options.apiContext || null;

        switch (apiContext) {
            case "user_list":
                return Routing.generate('open_orchestra_api_user_list');
            case "members_list":
                return Routing.generate('open_orchestra_api_user_list_in_group', urlParameter);
        }
    }
}

export default Users
