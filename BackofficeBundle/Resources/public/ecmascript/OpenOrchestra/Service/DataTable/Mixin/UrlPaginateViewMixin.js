let UrlPaginateViewMixin = (superclass) => class extends superclass {

    /**
     * @inheritDoc
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events = this.events || {};
        this.events['draw.dt table'] = '_updatePage';
        this._firstDraw = true;
    }

    /**
     * @param {Object} event
     *
     * @private
     */
    _updatePage(event) {
        let api = $(event.target).DataTable();
        let pageInfo = api.page.info();
        let page = pageInfo.page + 1;
        if (page > pageInfo.pages && 0 !== pageInfo.pages) {
            api.page('last').draw('page');
        }
        if (true === this._firstDraw) {
            this._firstDraw = false;
        } else {
            let url = this.generateUrlUpdatePage(page);
            Backbone.history.navigate(url);
        }
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
