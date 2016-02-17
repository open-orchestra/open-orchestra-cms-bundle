extendView = extendView || {}
extendView['OpenOrchestra.AreaFlex.AddRow'] = {

    ###*
     * Show modal to add a new row
    ###
    showFormAddRow: () ->
      url = @options.area.get("links")._self_form_new_row
      @showFormWithSelectLayout(url)

    ###*
     * Show modal for form with selected layout
    ###
    showFormWithSelectLayout: (url) ->
      adminFormViewClass = appConfigurationView.setConfiguration(@options.entityType, 'showOrchestraModal', OpenOrchestra.AreaFlex.AreaFlexFormRowView)
      adminFormViewClass = appConfigurationView.getConfiguration(@options.entityType, 'showAdminForm')
      new adminFormViewClass(
        url: url
        entityType: @options.entityType
      )
}
