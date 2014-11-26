NodeLanguageView = OrchestraView.extend(
  tagName: "li"

  initialize: (options) ->
    @language = options.language
    @nodeId = options.nodeId
    @currentLanguage = options.currentLanguage
    @loadTemplates [
      "nodeLanguage"
    ]
    return

  render: ->
    $(@el).append @renderTemplate('nodeLanguage',
      language: @language
      currentLanguage: @currentLanguage
    )
    return
)
