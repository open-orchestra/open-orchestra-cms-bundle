import OrchestraView        from '../OrchestraView'
import ModalView            from '../../../Service/Modal/View/ModalView'
import UserGroups           from '../../Collection/Group/UserGroups'
import GroupListForUserView from './GroupListForUserView'

/**
 * @class GroupListModalView
 */
class GroupListModalView extends ModalView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.tagName = 'div';
        this.className = 'modal fade';
        this.events = {
            'hidden.bs.modal': 'hide',
            'click .select-group': '_selectGroup'
        }
    }

    /**
     * @inheritdoc
     */
    initialize({blockedGroups, selectedGroups}) {
        this._blockedGroups = blockedGroups;
        this._selectedGroups = selectedGroups;
    }

    /**
     * Render Site selector
     */
    render() {
        let page = this._page;
        this._collection = new UserGroups();
        let groupListForUserView = new GroupListForUserView({
            collection: this._collection,
            blockedGroups: this._blockedGroups,
            selectedGroups: this._selectedGroups
        });

        let template = this._renderTemplate('User/groupListModalView');
        this.$el.html(template);
        $('.modal-body', this.$el).html(groupListForUserView.render().$el);

        return this;
    }
    
    _selectGroup() {
        let formGroups = _.pluck($('[name="group"]', this.$el).removeAttr('disabled').serializeArray(), 'value');
        let selectedGroups = [];
        for (let group of this._collection.models) {
            if (formGroups.indexOf(group.get('id')) > -1) {
                selectedGroups.push(group);
            }
        }
        Backbone.Events.trigger('group:select', selectedGroups);
        this.$el.modal('hide');
    }
}

export default GroupListModalView;
