extendView = extendView || {}
extendView['generateId'] = {
  events:
    'focusout input.generate-id-source': 'generateId'
    'blur input.generate-id-dest': 'stopGenerateId'

  recupInput: (el) ->
    for i in el
      return i.value if i.value
    
  checkInputId: ->
    if $('.generate-id-dest').val().length is 0
      return true
    else
      return false

  generateId: ->
    if @checkInputId()
      contentTypeId = @recupInput($(".generate-id-source"))
      $('.generate-id-dest').val(contentTypeId.latinise().replace(/[^a-z0-9]/gi,'_')) if contentTypeId
    return

  stopGenerateId: ->
    $('.generate-id-source').unbind()
    return
}
