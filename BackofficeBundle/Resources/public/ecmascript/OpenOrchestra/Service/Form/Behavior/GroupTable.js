import AbstractBehavior   from './AbstractBehavior'
import Application        from '../../../Application/Application'
import GroupListModalView from '../../../Application/View/Group/GroupListModalView'

/**
 * @class GroupTable
 */
class GroupTable extends AbstractBehavior
{
    /**
     * bind extra events
     * 
     * @param {Object} view - instance of AbstractFormView
     */
    getExtraEvents() {
        return {
            'click .fa-close': '_deleteGroup',
            'click .open-group-list': '_openGroupList'
        }
    }
    
    /**
     * return selector
     * 
     * @return {String}
     */
    getSelector() {
        return '.group-list';
    }

    /**
     * Remove group from user in view
     */
    _deleteGroup(event) {
        $(event.currentTarget).closest('tr').remove();
    }

    /**
     * Open group list in modal
     */
    _openGroupList(event) {
        this._diplayLoader(Application.getRegion('modal'));
        let groupListModalView = new GroupListModalView();
        Application.getRegion('modal').html(groupListModalView.render().$el);
        groupListModalView.show();
    }
}

// unique instance of GroupTable
export default (new GroupTable);
