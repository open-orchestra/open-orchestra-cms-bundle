extendView = extendView || {}
extendView['showVideo'] = {
  events:
    'change #video_videoType': 'showVideoForm'

  showVideoForm: (event) ->
    $('#form-youtube, #form-dailymotion, #form-vimeo').appendTo $('#inactive-form-part')
    $('#form-' + $(event.target).val()).appendTo '#active-form-part'
    OpenOrchestra.FormBehavior.formBehaviorLibrary.activateBehaviors @, @$el.find('form')
    return
}
