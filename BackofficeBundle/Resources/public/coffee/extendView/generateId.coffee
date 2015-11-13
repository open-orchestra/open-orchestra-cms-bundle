extendView = extendView || {}
extendView['generateId'] = {
  events:
    'focusout input.generate-id-source': 'generateId'
    'blur input.generate-id-dest': 'stopGenerateId'

  generateId: ->
    sourceId = $(".generate-id-source").val()
    if $('.generate-id-dest').val().length is 0 and sourceId?
      $('.generate-id-dest').val(sourceId.latinise().replace(/[^a-z0-9]/gi,'_'))
    return

  stopGenerateId: ->
    $('.generate-id-source').unbind()
    return
}
