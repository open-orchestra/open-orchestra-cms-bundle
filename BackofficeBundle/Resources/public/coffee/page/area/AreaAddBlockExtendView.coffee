extendView = extendView || {}
###*
 * Extend view for show form block
 * Required option view
 * {string} entityType
 * {object} area
###
extendView['OpenOrchestra.Page.Area.AddBlock'] = {

    ###*
     * Show modal to add a new row
    ###
    showFormAddBlock: () ->
      url_block_list = @options.area.get("links")._block_list
      title = @$el.attr('data-title-add-block')
      viewClass = appConfigurationView.getConfiguration(@options.entityType, 'showAddBlocksModal')
      console.log @options.area
      new viewClass(
        urlBlockList: url_block_list
        title: title
        domContainer: $('#OrchestraBOModal')
        entityType: @options.entityType
        area: @options.area
      )
}
