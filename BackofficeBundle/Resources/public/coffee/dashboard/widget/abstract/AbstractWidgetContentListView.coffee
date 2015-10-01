AbstractWidgetContentListView = AbstractWidgetListView.extend(

  generateUrl: (entity) ->
    parameter =
      'entityType': "contents_" + entity.content_type
      'entityId': entity.id
      'language': entity.language
      'version': entity.version
    url = appRouter.generateUrl('showEntity', parameter)

    return url
)
