superboxViewParam = []
SuperboxView = OrchestraView.extend(
  className: 'superbox-show'
  el: '#content'
  events:
    'change select#media_crop_format': 'changeView'

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
    @setupCropForm()
    @setUpCrop()

  setUpCrop: ->
    superboxViewParam['$preview'] = $('#preview-pane', @$el)
    superboxViewParam['$pimg'] = $('#preview-pane .preview-container img', @$el)
    superboxViewParam['$pcnt'] = $('#preview-pane .preview-container', @$el)
    superboxViewParam['xsize'] = superboxViewParam['$pcnt'].width()
    superboxViewParam['ysize'] = superboxViewParam['$pcnt'].height()
    $('.superbox-current-img').Jcrop({
      onChange: @updatePreview
      onSelect: @updateCoords
      aspectRatio: superboxViewParam['xsize'] / superboxViewParam['ysize']
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
    $('#media_crop_x', @$el).val(c.x);
    $('#media_crop_y', @$el).val(c.y);
    $('#media_crop_w', @$el).val(c.w);
    $('#media_crop_h', @$el).val(c.h);

  setupCropForm: ->
    currentView = this
    displayLoader('.media_crop_form')
    $.ajax
      url: @media.get('links')._self_crop
      method: 'GET'
      success: (response) ->
        $('.media_crop_form', currentView.$el).html response

  changeView: (e) ->
    superboxViewParam['jcrop_api'].destroy()
    format = e.currentTarget.value
    $('.superbox-current-img', @$el).append('<div id="preview-pane">
          <div class="preview-container">
              <img  class="jcrop-preview" alt="Preview" />
          </div>
      </div>')
    $('#preview-pane .preview-container', @$el).height(100)
    $('#preview-pane .preview-container', @$el).width(100)
    $('#preview-pane .preview-container img', @$el).attr('src', $('.superbox-current-img', @$el).attr('src'))
    @setUpCrop()
)
