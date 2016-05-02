###*
 * @namespace OpenOrchestra:FormBehavior
###
window.OpenOrchestra or= {}
window.OpenOrchestra.FormBehavior or= {}

###*
 * @class AbstractFormBehavior
###
class OpenOrchestra.FormBehavior.AbstractFormBehavior

  ###*
   * constructor
   * @param {String} selector
  ###
  constructor: (selector) ->
    @selector = selector

  ###*
   * activateBehavior
   * @param {Object} view
   * @param {Object} form
  ###
  activateBehavior: (view, form) ->
    elements = $(@selector, form)
    if elements and elements.length > 0
      @activateBehaviorOnElements(elements, view, form)

  ###*
   * deactivateBehavior
   * @param {Object} view
   * @param {Object} form
  ###
  deactivateBehavior: (view, form) ->
    elements = $(@selector, form)
    if elements and elements.length > 0 and @deactivateBehaviorOnElements
      @deactivateBehaviorOnElements(elements, view, form)
