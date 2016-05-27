###*
 * @namespace OpenOrchestra:FormBehavior
###
window.OpenOrchestra or= {}
window.OpenOrchestra.FormBehavior or= {}

###*
 * @class RefreshForm
###
class OpenOrchestra.FormBehavior.RefreshForm extends OpenOrchestra.FormBehavior.AbstractFormBehavior

  ###*
   * activateBehaviorOnElements
   * @param {Array} elements
   * @param {Object} view
   * @param {Object} form
  ###
  activateBehaviorOnElements: (elements, view, form) ->
    elements.on 'click', ->
      form = $('form', view.$el)

      window.OpenOrchestra.FormBehavior.channel.trigger 'deactivate', view, $('form', view.$el)
      form.replaceWith('<h1 class="spin"><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>')

      form.ajaxSubmit
        url: form.data('action')
        method: 'PATCH'
        context: view
        success: (response) ->
          newForm = $(response);
          $('input[name="_method"]', newForm).remove()
          $('.spin', @$el).replaceWith(newForm)
          window.OpenOrchestra.FormBehavior.channel.trigger 'activate', @, $('form', @$el)

jQuery ->
  OpenOrchestra.FormBehavior.formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.RefreshForm(".refresh-form"))
