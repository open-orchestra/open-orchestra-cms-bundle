superboxViewParam = []

SuperboxView = OrchestraView.extend(
  className: 'superbox-show'
  el: '#content'

  events:
    'change select#media_crop_format': 'changeView'
    'click a#crop_action_button': 'setUpCrop'
    'click a#upload_action_button': 'setupOverrideForm'
    'click a#crop_button': 'cropImage'

  initialize: (options) ->
    @media = options.media
    @listUrl = options.listUrl
    @loadTemplates [
      'superboxView'
    ]

  render: ->
    currentView = this
    $(@el).html @renderTemplate('superboxView',
      media: @media
      listUrl: @listUrl
    )
    $('.js-widget-title', @$el).text @media.get('name')
    @addPreview()
    @setupCropForm()
    @setupMetaForm()
    displayLoader('#alternative-loader')

  setUpCrop: ->
    superboxViewParam['jcrop_api'].destroy() if superboxViewParam['jcrop_api'] != undefined

    $(".media-override-format-form").hide()
    $('#crop-group').show()

    superboxViewParam['$preview'] = $('#preview-pane', @$el)
    superboxViewParam['$pimg'] = $('#preview-pane .preview-container img', @$el)
    superboxViewParam['$pcnt'] = $('#preview-pane .preview-container', @$el)
    superboxViewParam['xsize'] = superboxViewParam['$pcnt'].width()
    superboxViewParam['ysize'] = superboxViewParam['$pcnt'].height()
    $('.superbox-current-img').Jcrop({
      onChange: @updatePreview
      onSelect: @updateCoords
      boxWidth: 600
    }, ->
      bounds = @getBounds()
      superboxViewParam['boundx'] = bounds[0]
      superboxViewParam['boundy'] = bounds[1]

      # Store the API in the jcrop_api variable
      superboxViewParam['jcrop_api'] = this

      # Move the preview into the jcrop container for css positioning
      superboxViewParam['$preview'].appendTo this.ui.holder
      return
    )
    this

  updatePreview: (c) ->
    if parseInt(c.w) > 0
      rx = superboxViewParam['xsize'] / c.w;
      ry = superboxViewParam['ysize'] / c.h;
      superboxViewParam['$pimg'].css({
        width: Math.round(rx * superboxViewParam['boundx']) + 'px',
        height: Math.round(ry * superboxViewParam['boundy']) + 'px',
        marginLeft: '-' + Math.round(rx * c.x) + 'px',
        marginTop: '-' + Math.round(ry * c.y) + 'px'
      })

  updateCoords: (c) ->
    image = new Image
    image.src = $('.superbox-current-img').attr('src')
    rx = image.naturalWidth / $('.superbox-current-img').width();
    ry = image.naturalHeight / $('.superbox-current-img').height();

    $('#media_crop_x', @$el).val(Math.round(rx * c.x));
    $('#media_crop_y', @$el).val(Math.round(ry * c.y));
    $('#media_crop_w', @$el).val(Math.round(rx * c.w));
    $('#media_crop_h', @$el).val(Math.round(ry * c.h));

  setupCropForm: ->
    currentView = this
    $(".media-override-format-form", @$el).hide()
    displayLoader('#selector-loader')
    $.ajax
      url: @media.get('links')._self_crop
      method: 'GET'
      success: (response) ->
        if isLoginForm(response)
          redirectToLogin()
        else
          $('#selector-loader-container').hide()
          $('.media_crop_form', currentView.$el).html response
          currentView.addEventOnCropForm()

  setupMetaForm: ->
    currentView = this
    displayLoader('.media_meta_form')
    $.ajax
      url: @media.get('links')._self_meta
      method: 'GET'
      success: (response) ->
        if isLoginForm(response)
          redirectToLogin()
        else
          $('.media_meta_form', currentView.$el).html response
          currentView.addEventOnMetaForm()
          currentView.addSelect2OnForm()

  changeView: (e) ->
    $('#crop-group').hide()
    $(".media-override-format-form", @$el).hide()
    superboxViewParam['jcrop_api'].destroy() if superboxViewParam['jcrop_api'] != undefined
    $('.media_crop_preview img', @$el).hide()
    format = e.currentTarget.value
    $('.superbox-current-img', @$el).append('<div id="preview-pane" style="display: none">
          <div class="preview-container">
              <img  class="jcrop-preview" alt="Preview" />
          </div>
      </div>')
    $('#preview-pane .preview-container', @$el).height($('.media_crop_' + format, @$el).height())
    $('#preview-pane .preview-container', @$el).width($('.media_crop_' + format, @$el).width())
    @showPreview(format)

  showPreview: (format) ->
    if format != ''
      $('.media_crop_' + format, @$el).show()
      $('.media_format_actions').show()
    else
      $('.media_crop_original', @$el).show()
      $('.media_format_actions').hide()

  addPreview: ->
    $('.media_crop_preview', @$el).append('<img class="media_crop_original" src="' + @media.get('displayed_image') + '" style="max-width:600px;">')
    for thumbnail of @media.get('thumbnails')
      $('.media_crop_preview', @$el).append('<img class="media_crop_' + thumbnail + '" src="' + @media.get('thumbnails')[thumbnail] + '" style="display: none;">')
    displayLoader('#image-loader')

  addEventOnCropForm: ->
    currentView = this
    $(".media_crop_form form", @$el).on "submit", (e) ->
      $('#crop-group').hide()
      $('.media_crop_preview img', @$el).hide()
      $('#image-loader').show()
      e.preventDefault() # prevent native submit
      $(this).ajaxSubmit
        statusCode:
          200: (response) ->
            superboxViewParam['jcrop_api'].destroy()
            currentView.refreshImages()
            currentView.addEventOnCropForm()
    return

  addEventOnOverrideForm: ->
    currentView = this
    $(".media-override-format-form form", @$el).on "submit", (e) ->
      $('.media-override-format-form').hide()
      $('.media_crop_preview img', @$el).hide()
      $('#image-loader').show()
      e.preventDefault() # prevent native submit
      $(this).ajaxSubmit
        statusCode:
          200: (response) ->
            currentView.refreshImages()
            currentView.addEventOnOverrideForm()
    return

  addEventOnMetaForm: ->
    currentView = this
    $(".media_meta_form form", @$el).on "submit", (e) ->
      e.preventDefault() # prevent native submit
      $(this).ajaxSubmit
        statusCode:
          200: (response) ->
            $('.media_meta_form', currentView.$el).html response
            currentView.addEventOnMetaForm()
            currentView.addSelect2OnForm()
          400: (response) ->
            $('.media_meta_form', currentView.$el).html response
            currentView.addEventOnMetaForm()
            currentView.addSelect2OnForm()
    return

  refreshImages: ->
    format = $('#media_crop_format').val()
    $('.media_crop_' + format).attr 'src', $('.media_crop_' + format).attr('src') + '?' + Math.random()
    $(".media-override-format-form").hide()
    $('#image-loader').hide()
    @showPreview(format)

  addSelect2OnForm: ->
    if $(".select2", @$el).length > 0
      activateSelect2($(".select2", @$el))

  setupOverrideForm: () ->
    $('#crop-group').hide()
    $(".media-override-format-form").hide()
    $('#alternative-loader-container').show()
    format = $('#media_crop_format').val()
    currentView = this
    linkFormat = '_self_format_' + format
    $.ajax
      url: @media.get('links')[linkFormat]
      method: 'GET'
      success: (response) ->
        if isLoginForm(response)
          redirectToLogin()
        else
          $('.media-override-format-form').html response
          $('#alternative-loader-container').hide()
          $(".media-override-format-form").show()
          currentView.addEventOnOverrideForm()

  cropImage: ->
    $("#media_crop").submit()
)
