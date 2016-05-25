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
  OpenOrchestra.FormBehavior.formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.ValidateHidden("input:hidden[type='text'][required='required']"))
