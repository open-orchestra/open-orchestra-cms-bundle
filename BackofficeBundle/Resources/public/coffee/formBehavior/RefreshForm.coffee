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
      $('form', view.$el).replaceWith('<h1 class="spin"><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>')
      form.ajaxSubmit
        method: 'POST'
        context: view
        success: (response) ->
          $('.spin', @$el).replaceWith(response)
          activateForm(@, $('form', @$el))

formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.RefreshForm(".refresh-form"))
