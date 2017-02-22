import AbstractBehavior   from './AbstractBehavior'
import Application        from '../../../Application/Application'
import SitesAvailable     from '../../../Application/Collection/Site/SitesAvailable'
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
            'click .fa-trash': '_deleteGroup',
            'click .open-groups-list': '_openGroupList'
        }
    }

    /**
     * bind extra events
     *
     * @param {Object} view - instance of AbstractFormView
     */
    bindExtraEvents(view) {
        view.on('group:select', _.bind(this._addGroups, view), this);
        super.bindExtraEvents(view);
    }

    /**
     * return selector
     *
     * @return {String}
     */
    getSelector() {
        return '.user-groups-list';
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
        let $radios = $(event.target).closest('.user-groups-list').find('[type="radio"]');
        let blockedGroups = _.pluck($radios.serializeArray(), 'value');
        let selectedGroups = blockedGroups;

        new SitesAvailable().fetch({
            success: (sites) => {
                let groupListModalView = new GroupListModalView(
                        {
                            groupTable: this,
                            sites: sites,
                            blockedGroups: blockedGroups,
                            selectedGroups: selectedGroups
                        }
                    );
                    Application.getRegion('modal').html(groupListModalView.render().$el);
                    groupListModalView.show();
            }
        });
    }

    /**
     * add groups selected in modal
     */
    _addGroups(selectedGroups) {
        let prototype = $('.prototype', this.$el).data('prototype');
        let container = $('.user-groups-list table tbody', this.$el);
        for (let group of selectedGroups) {
            if($('[type="radio"][value="' + group.get('id') + '"]', this.$el).length == 0) {
                container.append(prototype.replace(/__([^_]*?)__/g, function(str, property) {
                    return eval('group.get("' + property.split('.').join('").get("') + '")');
                }));
            }
        }
    }
}

// unique instance of GroupTable
export default (new GroupTable);
