extendView = extendView || {}
###*
 * Extend view for show form Area
 * Required option view
 * {string} entityType
 * {object} area
###
extendView['OpenOrchestra.AreaFlex.AddRow'] = {

    ###*
     * Show modal to add a new row
    ###
    showFormAddRow: () ->
      url = @options.area.get("links")._self_form_new_row
      title = @$el.attr('data-title-new-row')
      @showFormWithSelectLayout(url, title)

    ###*
     * Show modal for form with selected layout
     * @param {string} url
     * @param {string} title
    ###
    showFormWithSelectLayout: (url, title) ->
      adminFormViewClass = appConfigurationView.setConfiguration(@options.entityType, 'showOrchestraModal', OpenOrchestra.AreaFlex.AreaFlexFormRowView)
      adminFormViewClass = appConfigurationView.getConfiguration(@options.entityType, 'showAdminForm')
      new adminFormViewClass(
        url: url
        entityType: @options.entityType
        title: title
      )
}
