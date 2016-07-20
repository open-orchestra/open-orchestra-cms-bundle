###*
 * @namespace OpenOrchestra:InternalLinkFormView
###
window.OpenOrchestra or= {}

###*
 * @class InternalLinkFormView
###
class OpenOrchestra.InternalLinkFormView extends OrchestraModalView

  events:
    'click .modalClose': 'closeModal'
    'hidden.bs.modal': 'closedModal'
    'click button[data-clone]': 'sendToTiny'

  ###*
   * @param {object} options
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'url'
      'editor'
      'fields'
    ])
    @formName = 'oo_internal_link'
    @loadTemplates [
        'OpenOrchestraBackofficeBundle:BackOffice:Underscore/internalLinkModalView'
    ]
    return

  ###*
   * Spin and render ajax call
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/internalLinkModalView',
      body: '<h1 class="spin"><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>'
    )
    @$el.appendTo('body')
    @$el.modal "show"
    window.OpenOrchestra.FormBehavior.channel.trigger 'deactivate', @, $('form', @$el)
    viewContext = @
    fields = {}
    for i of @options.fields
      fieldName = @formName + '[' + i.replace('_', '][') + ']'
      fields[fieldName] = @options.fields[i]
    $.ajax
      url: @options.url
      context: @
      method: 'PATCH'
      data: fields
      success: (response) ->
        $('.spin', @$el).replaceWith(response)
        originalButton = $('.submit_form', response)
        button = originalButton.clone().attr('data-clone', originalButton.attr('id')).removeAttr('id')
        $('.modal-header', @$el).prepend(button)
        window.OpenOrchestra.FormBehavior.channel.trigger 'activate', @, $('form', @$el)
    return

  ###*
   * @param {object} event
  ###
  closeModal: (event) ->
    @$el.modal 'hide'

  ###*
   * @param {object} event
  ###
  closedModal: (event) ->
    @$el.remove()

  ###*
   * @param {object} event
  ###
  sendToTiny: (event) ->
    inputText = $('#' + @formName + '_label', @$el)
    inputText.parent().removeClass 'has-error'
    if inputText.val() != ''
      @closeModal()
      serializeFields = $('form', @$el).serializeArray()
      fields = {}
      for i of serializeFields
        field = serializeFields[i]
        fieldName = field.name.replace(@formName, '').replace(/\]\[/g, '_').replace(/(\]|\[)/g, '')
        if fieldName != '_token'
          fields[fieldName] = field.value
      link = $('<a href="#">').html(inputText.val()).attr('data-options', JSON.stringify(fields))
      div = $('<div>').append(link)
      tinymce.get(@options.editor.id).insertContent div.html()
    else
      inputText.parent().addClass 'has-error'
      inputText.focus()

jQuery ->
  appConfigurationView.setConfiguration 'internalLink', 'showForm', OpenOrchestra.InternalLinkFormView
