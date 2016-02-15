extendView = extendView || {}
extendView['OpenOrchestra.AreaFlex.AddRow'] = {

    ###*
     * Show modal to add a row
    ###
    showFormAddRow: () ->
      adminFormViewClass = appConfigurationView.setConfiguration(@options.entityType, 'showOrchestraModal', OpenOrchestra.AreaFlex.AreaFlexFormRowView)
      adminFormViewClass = appConfigurationView.getConfiguration(@options.entityType, 'showAdminForm')
      new adminFormViewClass(
        url: @options.area.get("links")._self_form_new_row
        entityType: @options.entityType
      )
}
