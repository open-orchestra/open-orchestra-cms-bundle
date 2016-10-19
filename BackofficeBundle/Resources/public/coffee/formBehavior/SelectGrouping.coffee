###*
 * @namespace OpenOrchestra:FormBehavior
###
window.OpenOrchestra or= {}
window.OpenOrchestra.FormBehavior or= {}

###*
 * @class SelectGrouping
###
class OpenOrchestra.FormBehavior.SelectGrouping extends OpenOrchestra.FormBehavior.AbstractFormBehavior

  ###*
   * activateBehaviorOnElements
   * @param {Array} elements
   * @param {Object} view
   * @param {Object} form
  ###
  activateBehaviorOnElements: (elements, view, form) ->
    for element in elements
      element = $(element)
      master = $('.select-grouping-master', element)
      slave = $('.select-grouping-slave', element)
      source = slave.clone().removeAttr('id').removeAttr('name').removeAttr('class').addClass('.select-grouping-source')
      source.hide().insertAfter(slave)
      refreshSlave = ((formBehavior, master, slave, source) ->
        ->
          slave.empty()
          $('optgroup[label="' + master.val() + '"] option', source).clone().appendTo(slave)
          return
      )(@, master, slave, source)
      refreshSlave()
      master.change ->
        refreshSlave()
  
jQuery ->
  OpenOrchestra.FormBehavior.formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.SelectGrouping(".select-grouping"))
