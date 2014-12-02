generateContentTypeId = ->
  if checkInputId()
    contentTypeId = recupInput($(".content_type_source"))
    $('.content_type_dest').val(contentTypeId.latinise().replace(/[^a-z0-9]/gi,'_'))
  return

stopGenerateContentTypeId = ->
  $('.content_type_source').unbind()
  return

recupInput = (el) ->
  for i in el
    return i.value if i.value

checkInputId = ->
  if $('.content_type_dest').val().length is 0
    return true
  else
    return false
