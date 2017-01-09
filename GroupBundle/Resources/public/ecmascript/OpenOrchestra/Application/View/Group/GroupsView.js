import AbstractCollectionView from '../../../Service/DataTable/View/AbstractCollectionView'
import GroupListView          from '../../View/Group/GroupListView'
import Application            from '../../Application'

/**
 * @class GroupsView
 */
class GroupsView extends AbstractCollectionView
{
    /**
     * @inheritdoc
     */
    initialize({collection, settings, sites}) {
        super.initialize({collection: collection, settings: settings});
        this._sites = sites;
    }

    /**
     * Render groups view
     */
    render() {
        if (0 === this._collection.recordsTotal) {
            let template = this._renderTemplate('List/emptyListView' , {
                title: Translator.trans('open_orchestra_group.group.title_list'),
                urlAdd: ''
            });
            this.$el.html(template);
        } else {
            let template = this._renderTemplate('Group/groupsView',
                {
                    sites: this._sites.models,
                    language: Application.getContext().language
                }
            );
            this.$el.html(template);
            this._listView = new GroupListView({
                collection: this._collection,
                settings: this._settings
            });
            $('.groups-list', this.$el).html(this._listView.render().$el);
        }

        return this;
    }
}

export default GroupsView;
