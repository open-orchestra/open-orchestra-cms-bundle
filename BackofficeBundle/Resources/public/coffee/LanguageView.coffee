LanguageView = OrchestraView.extend(
  tagName: "li"

  initialize: (options) ->
    @options = options
    return

  render: ->
    $(@el).append @renderTemplate('language',
      language: @options.language
      currentLanguage: @options.currentLanguage
      parentCid: @options.cid
    )
    return
)
