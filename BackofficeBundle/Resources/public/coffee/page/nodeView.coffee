NodeView = Backbone.View.extend(
  el: '#content'
  events:
    'click i#none' : 'clickButton'
    'change select#selectbox': 'changeVersion'
    'click a.change-language': 'changeLanguage'
  initialize: (options) ->
    @node = options.node
    @version = @node.get('version')
    @language = @node.get('language')
    key = "click i." + @node.cid
    @events[key] = "clickButton"
    _.bindAll this, "render", "addAreaToView", "clickButton"
    @nodeTemplate = _.template($("#nodeView").html())
    @nodeTitle = _.template($("#nodeTitle").html())
    @render()
    return
  clickButton: (event) ->
    $('.modal-title').text @node.get('name')
    url = @node.get('links')._self_form
    deleteurl = @node.get('links')._self_delete
    if @node.attributes.alias is ''
      view = new adminFormView(
        url: url
        deleteurl: deleteurl
        triggers: [
          {
            event: "keyup input.alias-source"
            name: "refreshAlias"
            fct: refreshAlias
          }
          {
            event: "blur input.alias-dest"
            name: "stopRefreshAlias"
            fct: stopRefreshAlias
          }
        ]
      )
    else
      view = new adminFormView(
        url: url
        deleteurl: deleteurl
      )
  duplicateNode: ->
    viewContext = this
    $.ajax
      url: @node.get('links')._self_duplicate
      method: 'POST'
      success: (response) ->
        Backbone.history.loadUrl(Backbone.history.fragment)
    return
  render: ->
    title = @nodeTitle(node: @node)
    $(@el).html @nodeTemplate(
      node: @node
      title: title
    )
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
    $('.js-widget-panel', @$el).html($('#generated-panel', @$el).html()).show()
    for area of @node.get('areas')
      @addAreaToView(@node.get('areas')[area])
    @addVersionToView()
    @addLanguagesToView()
    @addPreviewLink()
    if @node.attributes.status.published
      $('.ui-model *', @el).unbind()
      $('.js-widget-panel').hide()
      $('span.action', @el).hide()
    else
      $("ul.ui-model-areas, ul.ui-model-blocks", @$el).each ->
        refreshUl $(this)
    return
  addAreaToView: (area) ->
    areaElement = new Area
    areaElement.set area
    areaView = new AreaView(
      area: areaElement
      node_id: @node.get('node_id'),
      displayClass: (if @node.get("bo_direction") is "v" then "inline" else "block")
    )
    @$el.find('ul.ui-model-areas').first().append  areaView.render().el
    return
  addVersionToView: ->
    viewContext = this
    $.ajax
      type: "GET"
      url: @node.get('links')._self_version
      success: (response) ->
        nodeCollection = new NodeCollectionElement
        nodeCollection.set response
        for nodeVersion of nodeCollection.get('nodes')
          viewContext.addChoiceToSelectBox(nodeCollection.get('nodes')[nodeVersion])
        return
  addChoiceToSelectBox: (nodeVersion) ->
    nodeVersionElement = new Node
    nodeVersionElement.set nodeVersion
    view = new NodeVersionView(
      node: nodeVersionElement
      version: @version
    )
    this.$el.find('optgroup#versions').append view.render()
  changeVersion: (event) ->
    Backbone.history.navigate('#node/show/' + @node.get('node_id') + '/' + @language + '/' + event.currentTarget.value, {trigger: true}) if $(':selected', this.$el).closest('optgroup').attr('id') == 'versions'
    @duplicateNode() if $(':selected', this.$el).closest('optgroup').attr('id') == 'duplicate'
    return
  addLanguagesToView: ->
    viewContext = this
    $.ajax
      type: "GET"
      url: @node.get('links')._site
      success: (response) ->
        site = new Site
        site.set response
        for language of site.get('languages')
          viewContext.addLanguageToPanel(site.get('languages')[language])
        return
  addLanguageToPanel: (language) ->
    view = new NodeLanguageView(
      language: language
      nodeId: @node.get('node_id')
      currentLanguage: @language
    )
    this.$el.find('#node-languages').append view.render()
  changeLanguage: (event) ->
    Backbone.history.navigate('#node/show/' + @node.get('node_id') + '/' + $(event.currentTarget).data('language'), {trigger: true})
  addPreviewLink: ->
    previewLink = @node.get('links')._self_preview
    view = new PreviewLinkView(
      previewLink: previewLink
    )
    this.$el.find('#preview').append view.render()
)
