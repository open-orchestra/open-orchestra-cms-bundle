import OrchestraView        from '../OrchestraView'
import ModalView            from '../../../Service/Modal/View/ModalView'
import UserGroups               from '../../Collection/Group/UserGroups'
import GroupListForUserView from './GroupListForUserView'

/**
 * @class GroupListModalView
 */
class GroupListModalView extends ModalView
{
    /**
     * Render Site selector
     */
    render() {
        let page = this._page;
        let collection = new UserGroups();
        let groupListForUserView = new GroupListForUserView({
            collection: collection,
            settings: {page: 0}
        });
        this.$el.html(groupListForUserView.render().$el)

        return this;
    }
}

export default GroupListModalView;
