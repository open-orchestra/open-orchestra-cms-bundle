TableviewView = OrchestraView.extend(
  initialize: (options) ->
    @events = []
    @events['click a.ajax-delete-' + @cid] = 'clickDelete'
    @events['click a.ajax-edit-' + @cid] = 'clickEdit'
    @element = options.element
    @displayedElements = options.displayedElements
    @title = options.title
    @listUrl = options.listUrl
    @entityType = options.entityType
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
      titleWhite: 'Delete'
      titleColorized: 'this element'
      text: 'The removal will be final'
      yesCallbackParams: 
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
    redirectRoute = appRouter.generateUrl('showEntity',
      entityType: @entityType,
      entityId: @element.get('id'),
    )
    Backbone.history.navigate(redirectRoute , {trigger: true})
)
