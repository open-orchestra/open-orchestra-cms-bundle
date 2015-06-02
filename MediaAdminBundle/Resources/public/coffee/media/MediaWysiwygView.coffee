MediaWysiwygView = MediaView.extend(
  mediaSelect : (event) ->
      event.preventDefault()
      tinyMCE.execCommand(
        'mceInsertContent',
        false,
        '<img alt="none" src="' + @$el.find('.superbox-img').attr('src') + '"/>');
      @$el.parents(".mediaModalContainer").find('.mediaModalClose').click().find('.mediaModalClose').click()
)
