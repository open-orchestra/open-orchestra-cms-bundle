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
    @actions =
      'form': true
      'delete' : true
    if (typeof options.actions isnt "undefined")
      for action of options.actions
        @actions[action] = options.actions[action]
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
      actions: @actions
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
    displayLoader('div[role="container"]')
    Backbone.history.navigate(Backbone.history.fragment + '/edit')
    title = @title
    listUrl = @listUrl
    $.ajax
      url: @element.get('links')._self_form
      method: 'GET'
      success: (response) ->
        view = new FullPageFormView(
          html: response
          title: title
          listUrl: listUrl
        )
)
