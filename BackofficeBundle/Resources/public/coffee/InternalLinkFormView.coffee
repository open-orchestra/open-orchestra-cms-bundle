InternalLinkFormView = OrchestraView.extend(
  events:
    'click .modalClose': 'closeModal'
    'hidden.bs.modal': 'closedModal'
  initialize: (options) ->
    @options = @reduceOption(options, [
      'selector'
      'input'
    ])
    @options = $.extend(@options, $(options.selector).data())
    
    @loadTemplates [
        'OpenOrchestraBackofficeBundle:BackOffice:Underscore/internalLinkModalView'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/internalLinkModalView', 
      body: "<h1><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>"
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
        old = @$el
        @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/internalLinkModalView', 
          body: response
        )
        originalButton = $('.submit_form', response)
        button = originalButton.clone().attr('data-clone', originalButton.attr('id')).removeAttr('id')
        $('.modal-header', @$el).prepend(button)
        @$el.removeClass('fade') 
        old.replaceWith @$el
        @$el.modal "show"
        activateForm(@, $('form', @$el))
    return

  closeModal: (event) ->
    @$el.addClass('fade').modal 'hide'

  closedModal: (event) ->
    @$el.remove()
)


jQuery ->
  appConfigurationView.setConfiguration 'internalLink', 'showForm', InternalLinkFormView
