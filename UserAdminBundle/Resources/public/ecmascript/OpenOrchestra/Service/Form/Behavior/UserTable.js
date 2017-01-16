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
            'click a.member-link': '_redirectToUser'
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

    /**
     * Redirect to User view
     */
    _redirectToUser(event) {
        event.preventDefault();
        let url = Backbone.history.generateUrl('editUser', {userId : $(event.currentTarget).data('id')});
        Backbone.history.navigate(url, true);
    }
}

// unique instance of UserTable
export default (new UserTable);
