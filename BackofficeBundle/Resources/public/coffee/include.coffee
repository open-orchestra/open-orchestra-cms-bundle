_underscore_template = _.template
_.template = (str, data) ->

  # match "<% include template-id %>"
  _underscore_template str.replace(/<%\s*include\s*(.*?)\s*%>/g, (match, templateId) ->
    el = $("#" + templateId)
    (if el then el.html() else "")
  ), data