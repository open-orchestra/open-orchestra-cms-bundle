generateId = ->
  if checkInputId()
    contentTypeId = recupInput($(".generate-id-source"))
    $('.generate-id-dest').val(contentTypeId.latinise().replace(/[^a-z0-9]/gi,'_'))
  return

stopGenerateId = ->
  $('.generate-id-source').unbind()
  return

recupInput = (el) ->
  for i in el
    return i.value if i.value

checkInputId = ->
  if $('.generate-id-dest').val().length is 0
    return true
  else
    return false
