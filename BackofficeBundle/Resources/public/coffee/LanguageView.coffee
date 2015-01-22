LanguageView = OrchestraView.extend(
  tagName: "li"

  initialize: (options) ->
    @language = options.language
    @currentLanguage = options.currentLanguage
    @parentCid = options.cid
    @loadTemplates [
      "language"
    ]
    return

  render: ->
    $(@el).append @renderTemplate('language',
      language: @language
      currentLanguage: @currentLanguage
      parentCid: @parentCid
    )
    return
)
