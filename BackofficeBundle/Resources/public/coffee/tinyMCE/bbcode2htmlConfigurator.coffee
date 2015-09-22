bbcode2htmlConfigurator = ->
  transformation : []

  getTransformation: () ->
    return @transformation

  addTransformation: (tra) ->
    $.extend @transformation, tra
    return

bbcode2html = new bbcode2htmlConfigurator()
