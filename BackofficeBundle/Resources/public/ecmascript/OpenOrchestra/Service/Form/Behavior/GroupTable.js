import AbstractBehavior   from './AbstractBehavior'
import Application        from '../../../Application/Application'
import GroupListModalView from '../../../Application/View/Group/GroupListModalView'

/**
 * @class GroupTable
 */
class GroupTable extends AbstractBehavior
{
     /**
     * get extra events
     * 
     * @return {Object}
     */
    getExtraEvents() {
        return {
            'click .fa-close': '_deleteGroup',
            'click .open-group-list': '_openGroupList'
        }
    }
    
    /**
     * bind extra events
     * 
     * @param {Object} view - instance of AbstractFormView
     */
    bindExtraEvents(view) {
        Backbone.Events.on('group:select', _.bind(this._addGroups, view), this);
        super.bindExtraEvents(view);
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
    _openGroupList() {
        this._diplayLoader(Application.getRegion('modal'));
        let groupListModalView = new GroupListModalView();
        Application.getRegion('modal').html(groupListModalView.render().$el);
        groupListModalView.show();
    }

    /**
     * add groups selected in modal
     */
    _addGroups(selectedGroups) {
        let prototype = $('.prototype', this.$el).data('prototype');
        let container = $('.group-list table tbody', this.$el);
        for (let group of selectedGroups) {
            container.append(prototype.replace(/__([^_]*?)__/g, function(str, property) {
                return eval('group.get("' + property.split('.').join('").get("') + '")');
            }));
        }
    }
}

// unique instance of GroupTable
export default (new GroupTable);
