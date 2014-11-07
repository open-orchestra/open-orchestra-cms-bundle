NodeLanguageView = Backbone.View.extend(
  tagName: "li"
  el: '#node-languages'
  initialize: (options) ->
    @language = options.language
    @nodeId = options.nodeId
    @currentLanguage = options.currentLanguage
    @nodeLanguage = _.template($("#nodeLanguage").html())
    return
  render: ->
    $(@el).append @nodeLanguage(
      language: @language
      currentLanguage: @currentLanguage
    )
    return
)
