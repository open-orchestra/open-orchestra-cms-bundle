LastNodesView = AbstractWidgetNodeListView.extend(

  getUrl: ->
    $('#widget-id-last-nodes').data('widget-apiurl')

  getListView: ->
    "OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboard/widget/lastNodesView"
)

jQuery ->
  appConfigurationView.setConfiguration 'dashboard_widgets', 'last_nodes', 'LastNodesView'
