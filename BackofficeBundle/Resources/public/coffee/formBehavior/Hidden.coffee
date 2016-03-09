###*
 * @namespace OpenOrchestra:FormBehavior
###
window.OpenOrchestra or= {}
window.OpenOrchestra.FormBehavior or= {}

###*
 * @class Hidden
###
class OpenOrchestra.FormBehavior.Hidden extends OpenOrchestra.FormBehavior.AbstractFormBehavior

  ###*
   * activateBehaviorOnElements
   * @param {Array} elements
   * @param {Object} view
   * @param {Object} form
  ###
  activateBehaviorOnElements: (elements, view, form) ->
    elements.addClass('focusable').attr('type', 'text')

formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.Hidden("input[type='hidden'][required='required']"))
