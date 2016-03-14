###*
 * @namespace OpenOrchestra:FormBehavior
###
window.OpenOrchestra or= {}
window.OpenOrchestra.FormBehavior or= {}

###*
 * @class HelpText
###
class OpenOrchestra.FormBehavior.HelpText extends OpenOrchestra.FormBehavior.AbstractFormBehavior

  ###*
   * activateBehaviorOnElements
   * @param {Array} elements
   * @param {Object} view
   * @param {Object} form
  ###
  activateBehaviorOnElements: (elements, view, form) ->
    for element in elements
      element = $(element)
      title = element.data('original-title').split('\\n').join('\n')
      element.attr 'data-original-title', title
      element.tooltip()

jQuery ->
  OpenOrchestra.FormBehavior.formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.HelpText(".helper-block"))
