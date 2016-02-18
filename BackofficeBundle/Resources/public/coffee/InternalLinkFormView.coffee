InternalLinkFormView = OrchestraView.extend(
  events:
    'click .modalClose': 'closeModal'
    'hidden.bs.modal': 'closedModal'
    'click button[data-clone]': 'sendToTiny'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'selector'
      'editor'
    ])
    @options = $.extend(@options, $(options.selector).data())
    
    @loadTemplates [
        'OpenOrchestraBackofficeBundle:BackOffice:Underscore/internalLinkModalView'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/internalLinkModalView',
      body: '<h1 class="spin"><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>'
    )
    $(@options.selector).html @$el
    @$el.detach().appendTo('body')
    @$el.modal "show"

    viewContext = @
    $.ajax
      url: @options.url
      context: this
      method: 'GET'
      success: (response) ->
        $('.spin', @$el).replaceWith(response)
        originalButton = $('.submit_form', response)
        button = originalButton.clone().attr('data-clone', originalButton.attr('id')).removeAttr('id')
        $('.modal-header', @$el).prepend(button)
        activateForm(@, $('form', @$el))
    return

  closeModal: (event) ->
    @$el.modal 'hide'

  closedModal: (event) ->
    @$el.remove()

  sendToTiny: (event) ->
    if $('.label-tinyMce', this.$el).val() != ''
      @closeModal()
      link = $('<a>').html($('.label-tinyMce', this.$el).val())
      _.each $('.to-tinyMce', this.$el), (element, key) ->
        element = $(element)
        link.attr('data-' + element.data('key'), element.val())
      div = $('<div>').append(link)
      tinymce.get(@options.editor.id).insertContent(div.html())
)

jQuery ->
  appConfigurationView.setConfiguration 'internalLink', 'showForm', InternalLinkFormView
