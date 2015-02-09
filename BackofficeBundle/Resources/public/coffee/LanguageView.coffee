LanguageView = OrchestraView.extend(
  tagName: "li"

  initialize: (options) ->
    @options = options
    @loadTemplates [
      "language"
    ]
    return

  render: ->
    $(@el).append @renderTemplate('language',
      language: @options.language
      currentLanguage: @options.currentLanguage
      parentCid: @options.cid
    )
    return
)
