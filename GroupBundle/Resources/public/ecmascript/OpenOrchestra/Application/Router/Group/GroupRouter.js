import OrchestraRouter from '../OrchestraRouter'
import Application     from '../../Application'
import FormBuilder     from '../../../Service/Form/Model/FormBuilder'
import Groups          from '../../Collection/Group/Groups'
import GroupsView      from '../../View/Group/GroupsView'
import SitesAvailable  from '../../../Application/Collection/Site/SitesAvailable'

/**
 * @class GroupRouter
 */
class GroupRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'group/list(/:page)': 'listGroup'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label:Translator.trans('open_orchestra_group.navigation.group.title')
            },
            {
                label: Translator.trans('open_orchestra_group.navigation.group.groups'),
                link: '#'+Backbone.history.generateUrl('listGroup')
            }
        ]
    }

    /**
     *  List Group
     *
     * @param {String} page
     */
    listGroup(page) {
        if (null === page) {
            page = 1
        }
        this._diplayLoader(Application.getRegion('content'));
        let pageLength = 10;
        page = Number(page) - 1;
        new SitesAvailable().fetch({
            success: (sites) => {
                new Groups().fetch({
                    data : {
                        start: page * pageLength,
                        length: pageLength
                    },
                    success: (groups) => {
                        let groupsView = new GroupsView({
                            collection: groups,
                            settings: {
                                page: page,
                                deferLoading: [groups.recordsTotal, groups.recordsFiltered],
                                data: groups.models,
                                pageLength: pageLength
                            },
                            sites: sites
                        });
                        let el = groupsView.render().$el;
                        Application.getRegion('content').html(el);
                    }
                });
            }
        });
    }
}

export default GroupRouter;
