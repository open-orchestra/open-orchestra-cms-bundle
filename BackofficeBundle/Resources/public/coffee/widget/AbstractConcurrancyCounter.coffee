###*
 * @class AbstractConcurrancyCounter
###
class AbstractConcurrancyCounter extends OrchestraView

  addConcurrency: ->
    if typeof @currentRankView == 'undefined'
      @oldRender = @render
      @currentRankView = 0
    else
      @currentRankView = @currentRankView + 1
    @render = () ->
      prototype = Object.getPrototypeOf(@)
      if @currentRankView == prototype.currentRankView
        @oldRender.apply this, arguments
      return
    return
