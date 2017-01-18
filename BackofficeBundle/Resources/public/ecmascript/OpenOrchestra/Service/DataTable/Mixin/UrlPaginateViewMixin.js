let UrlPaginateViewMixin = (superclass) => class extends superclass {

    /**
     * @inheritDoc
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events = this.events || {};
        this.events['draw.dt table'] = '_updatePage';
    }

    /**
     * @param {Object} event
     *
     * @private
     */
    _updatePage(event) {
        console.log('updatePage');
        let api = $(event.target).DataTable();
        let page = api.page.info().page + 1;
        let url = this.generateUrlUpdatePage(page);
        Backbone.history.navigate(url);
    }

    /**
     * Generate url of list when page is updated
     *
     * @param {string} page
     */
    generateUrlUpdatePage(page) {
        throw new TypeError("Please implement abstract method generateUrlUpdatePage.");
    }
};

export default UrlPaginateViewMixin;
