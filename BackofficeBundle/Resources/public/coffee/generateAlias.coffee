refreshAlias = ->
  $("input.alias-dest").val $("input.alias-source").val().replace(/[^a-z0-9]/gi,'_')
  return
stopRefreshAlias = ->
  $("input.alias-dest").unbind()
  return
