WysiwygSelectView = OrchestraView.extend(
  events:
    'click #sendToTiny': 'sendToTiny'
    'change #media_crop_format' : 'changeCropFormat'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'domContainer'
      'html'
      'thumbnails'
    ])
    @loadTemplates [
        'OpenOrchestraMediaAdminBundle:BackOffice:Underscore/Include/previewImageView',
      ]
    return

  render: (options) ->
    @setElement $(@options.html).append(@renderTemplate('OpenOrchestraMediaAdminBundle:BackOffice:Underscore/Include/previewImageView'
      src: @options.thumbnails["original"]
    ))
    @options.domContainer.html @$el
    $("option", "#media_crop_format").first().val("original")

  changeCropFormat: (event) ->
    format = $(event.currentTarget).val()
    $('#preview_thumbnail', @$el).attr 'src', @options.thumbnails[format]
 
  sendToTiny: (event) ->
    modalContainer = @$el.closest(".mediaModalContainer")
    editorId = modalContainer.data("input")
    tinymce.get(editorId).execCommand(
      'mceInsertContent',
      false,
      '<img src="' + @options.thumbnails[$('#media_crop_format').val()] + '"/>'
    )
    modalContainer.find('.mediaModalClose').click()
)
