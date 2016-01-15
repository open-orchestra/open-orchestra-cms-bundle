extendView = extendView || {}
extendView['generateId'] = {
  events:
    'focusout input.generate-id-source': 'generateId'
    'blur input.generate-id-dest': 'stopGenerateId'

  generateId: ->
    sourceId = $(".generate-id-source", @$el).val()
    if $('.generate-id-dest', @$el).val().length is 0 and sourceId?
      $('.generate-id-dest', @$el).val(sourceId.latinise().replace(/[^a-z0-9]/gi,'_'))
    return

  stopGenerateId: ->
    $('.generate-id-source', @$el).unbind()
    return
}
