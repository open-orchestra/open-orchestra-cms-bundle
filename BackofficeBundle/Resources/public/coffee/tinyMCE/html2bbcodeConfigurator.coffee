html2bbcodeConfigurator = ->
  finalTransformation : []
  transformation : []

  getTransformation: () ->
    concatTransformation = {}
    $.extend concatTransformation, @transformation
    $.extend concatTransformation, @finalTransformation
    return concatTransformation

  addTransformation: (tra) ->
    $.extend @transformation, tra
    return

html2bbcode = new html2bbcodeConfigurator()
