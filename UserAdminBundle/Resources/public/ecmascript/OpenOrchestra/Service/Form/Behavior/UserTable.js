import AbstractBehavior   from './AbstractBehavior'

/**
 * @class UserTable
 */
class UserTable extends AbstractBehavior
{
     /**
     * get extra events
     * 
     * @return {Object}
     */
    getExtraEvents() {
        return {
            'click .fa-close': '_deleteUser',
        }
    }
    
    /**
     * return selector
     * 
     * @return {String}
     */
    getSelector() {
        return '.group-users-list';
    }

    /**
     * Remove User from user in view
     */
    _deleteUser(event) {
        $(event.currentTarget).closest('tr').remove();
    }
}

// unique instance of UserTable
export default (new UserTable);
