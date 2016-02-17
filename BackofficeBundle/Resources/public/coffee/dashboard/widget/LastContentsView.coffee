LastContentsView = AbstractWidgetContentListView.extend(

  getUrl: ->
    $('#widget-id-last-contents').data('widget-apiurl')

  getListView: ->
    "OpenOrchestraBackofficeBundle:BackOffice:Underscore/dashboard/widget/lastContentsView"
)

jQuery ->
  appConfigurationView.setConfiguration 'dashboard_widgets', 'last_contents', LastContentsView
