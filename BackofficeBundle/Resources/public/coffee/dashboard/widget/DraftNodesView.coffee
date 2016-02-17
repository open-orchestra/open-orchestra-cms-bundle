DraftNodesView = AbstractWidgetNodeListView.extend(

  getUrl: ->
    $('#widget-id-draft-nodes').data('widget-apiurl')

  getListView: ->
    "OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboard/widget/draftNodesView"
)

jQuery ->
  appConfigurationView.setConfiguration 'dashboard_widgets', 'draft_nodes', DraftNodesView
