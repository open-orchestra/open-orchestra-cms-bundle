###*
 * @namespace OpenOrchestra:FormBehavior
###
window.OpenOrchestra or= {}
window.OpenOrchestra.FormBehavior or= {}

###*
 * @class ValidateHidden
###
class OpenOrchestra.FormBehavior.ValidateHidden extends OpenOrchestra.FormBehavior.AbstractFormBehavior

  ###*
   * activateBehaviorOnElements
   * @param {Array} elements
   * @param {Object} view
   * @param {Object} form
  ###
  activateBehaviorOnElements: (elements, view, form) ->
    elements.addClass('focusable').attr('type', 'text')

jQuery ->
  OpenOrchestra.FormBehavior.formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.ValidateHidden("input[type='hidden'][required='required']"))
<<<<<<< HEAD
  OpenOrchestra.FormBehavior.formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.ValidateHidden("input:hidden[type='text'][required='required']"))
=======
  OpenOrchestra.FormBehavior.formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.ValidateHidden("input.select-boolean[type='text'][required='required']"))
>>>>>>> 5fd53d6e9c1a4d8b163c6a3de1e356c83f760e73
