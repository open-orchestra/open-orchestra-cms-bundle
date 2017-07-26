import AbstractCollectionView from 'OpenOrchestra/Service/DataTable/View/AbstractCollectionView'
import RedirectionsListView   from 'OpenOrchestra/Application/View/Redirection/RedirectionsListView'

/**
 * @class RedirectionsView
 */
class RedirectionsView extends AbstractCollectionView
{
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
