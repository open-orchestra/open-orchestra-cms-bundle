AbstractWidgetNodeListView = AbstractWidgetListView.extend(

  generateUrl: (entity) ->
    parameter =
      'nodeId': entity.node_id
      'language': entity.language
      'version': entity.version
    url = appRouter.generateUrl('showNodeWithLanguageAndVersion', parameter)

    return url
)
