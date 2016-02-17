DraftContentsView = AbstractWidgetContentListView.extend(

  getUrl: ->
    $('#widget-id-draft-contents').data('widget-apiurl')

  getListView: ->
    "OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboard/widget/draftContentsView"
)

jQuery ->
  appConfigurationView.setConfiguration 'dashboard_widgets', 'draft_contents', DraftContentsView
