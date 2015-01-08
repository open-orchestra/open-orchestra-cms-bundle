TableviewView = OrchestraView.extend(
  initialize: (options) ->
    @events = []
    @events['click a.ajax-delete-' + @cid] = 'clickDelete'
    @events['click a.ajax-edit-' + @cid] = 'clickEdit'
    @element = options.element
    @displayedElements = options.displayedElements
    @title = options.title
    @listUrl = options.listUrl
    _.bindAll this, "render"
    @loadTemplates [
      'tableviewView',
      'tableviewActions'
    ]
    return

  render: ->
    $(@el).append('<tr></tr>')
    row = $(@el).find('tr:last-of-type')
    for displayedElement in @displayedElements
      row.append @renderTemplate('tableviewView'
        value: @element.get(displayedElement)
      )
    row.append @renderTemplate('tableviewActions',
      links: @element.get('links')
      cid: @cid
    )

  clickDelete: (event) ->
    event.preventDefault()
    smartConfirm(
      'fa-trash-o',
      'Delete this element',
      'The removal will be final',
      callBackParams:
        url: @element.get('links')._self_delete
        row: $(event.target).closest('tr')
      yesCallback: (params) ->
        $.ajax
          url: params.url
          method: 'DELETE'
          success: (response) ->
            return
        params.row.hide()
    )

  clickEdit: (event) ->
    event.preventDefault()
    if @element.get('language')
      redirectUrl = appRouter.generateUrl('showEntityWithLanguage', appRouter.addParametersToRoute(
        'entityId': @element.get('id')
        'language': @element.get('language')
      ))
    else
      redirectUrl = appRouter.generateUrl('showEntity', appRouter.addParametersToRoute(
        'entityId': @element.get('id')
      ))
    Backbone.history.navigate(redirectUrl, {trigger: true})
)
