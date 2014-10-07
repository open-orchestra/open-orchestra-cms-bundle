refreshAlias = ->
  $("input#node_alias").val $("input#node_name").val()
  return
stopRefreshAlias = ->
  $("input#node_alias").unbind()
  return