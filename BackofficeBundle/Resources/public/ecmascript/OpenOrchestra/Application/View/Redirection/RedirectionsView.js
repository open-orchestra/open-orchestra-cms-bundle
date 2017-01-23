import AbstractCollectionView from '../../../Service/DataTable/View/AbstractCollectionView'
import RedirectionsListView   from '../../View/Redirection/RedirectionsListView'

/**
 * @class RedirectionsView
 */
class RedirectionsView extends AbstractCollectionView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        super.preinitialize({ removeMultiple: false });
    }

    /**
     * Render redirections view
     */
    render() {
        let template = this._renderTemplate('Redirection/redirectionsView');
        this.$el.html(template);
        this._listView = new RedirectionsListView({
            collection: this._collection,
            settings: this._settings
        });
        let html = this._listView.render().$el;
        $('.redirections-list', this.$el).html(html);

        return this;
    }
}

export default RedirectionsView;
