chai = require ("chai")
chai.should()

{Area} = require ("../../../../Resources/public/coffee/area")
{Block} = require ("../../../../Resources/public/coffee/block")

describe "Area Instance", ->
  area = null
  areaResponse =
    area_id: "main"
    blocks: [
      {
        component: "Sample"
        method: "generate"
        attributes:
          author: ""
          title: "Accueil"
          news: "Bienvenue"
        ui_model:
          html: "<span>datetime : 1407753067 </span>"
          label: "Sample"
      }
      {
        component: "Sample"
        method: "generate"
        attributes:
          author: ""
          title: "News"
          news: "News"
        ui_model:
          html: "<span>datetime : 1407753067 </span>"
          label: "Sample"
      }
    ]
    ui_model:
      label: "main"
  it 'should contain a areaResponse object', ->
    area = new Area areaResponse
    area.areaResponse.should.equal areaResponse
  it 'should add a block', ->
    blockResponse =
      component: "Sample"
      method: "generate"
      attributes:
        author: ""
        title: "Accueil"
        news: "Bienvenue"
      ui_model:
        html: "<span>datetime : 1407753067 </span>"
        label: "Sample"
    area.blocks.length.should.equal 2
    area.addBlock blockResponse
    area.blocks.length.should.equal 3
    area.blocks[0].should.instanceof Block
    area.blocks[1].should.instanceof Block
    area.blocks[2].should.instanceof Block
  it 'shound display the title', ->
    area.renderTitle().should.equal '<span class="title">main</span>'
  it 'should display the preview', ->
    area.renderPreview().should.equal '<span class="preview"></span>'
  it 'should render the action', ->
    area.renderActionButton().should.equal '<span class="action"><i class="fa fa-cog"></i></span>'
  it 'should print html', ->
    area.printHtml().should.contain '<div>'
    area.printHtml().should.contain '<span class="title">'
    area.printHtml().should.contain '<span class="preview">'
    area.printHtml().should.contain '<span class="action">'
    area.printHtml().should.contain '<ul class="ui-model-blocks">'
    area.printHtml().should.contain '<li class="ui-model-blocks block" style="height: 33.333333333333336%;">'


describe "Area with subarea", ->
  area = null
  areaResponse =
    area_id: "main"
    areas: [
      {
        area_id: "left_menu"
        blocks: [
          {
            component: "Sample"
            method: "generate"
            attributes:
              author: ""
              title: "Accueil"
              news: "Bienvenue"
            ui_model:
              html: "<span>datetime : 1407753067 </span>"
              label: "Sample"
          }
          {
            component: "Sample"
            method: "generate"
            attributes:
              author: ""
              title: "News"
              news: "News"
            ui_model:
              html: "<span>datetime : 1407753067 </span>"
              label: "Sample"
          }
        ]
        ui_model:
          label: "Left Menu"
      },
      {
        area_id: "right_menu"
        blocks: [
          {
            component: "Sample"
            method: "generate"
            attributes:
              author: ""
              title: "Accueil"
              news: "Bienvenue"
            ui_model:
              html: "<span>datetime : 1407753067 </span>"
              label: "Sample"
          }
          {
            component: "Sample"
            method: "generate"
            attributes:
              author: ""
              title: "News"
              news: "News"
            ui_model:
              html: "<span>datetime : 1407753067 </span>"
              label: "Sample"
          }
        ]
        ui_model:
          label: "Left Menu"
      }
    ]
    ui_model:
      label: "main"
  it 'should contain a areaResponse object', ->
    area = new Area areaResponse
    area.areaResponse.should.equal areaResponse
    area.subAreas[0].should.instanceof Area
    area.subAreas[0].blocks[0].should.instanceof Block
  it 'shound display the title', ->
    area.renderTitle().should.equal '<span class="title">main</span>'
  it 'should display the preview', ->
    area.renderPreview().should.equal '<span class="preview"></span>'
  it 'should render the action', ->
    area.renderActionButton().should.equal '<span class="action"><i class="fa fa-cog"></i></span>'
  it 'should print html', ->
    area.printHtml().should.contain '<div>'
    area.printHtml().should.contain '<span class="title">'
    area.printHtml().should.contain '<span class="preview">'
    area.printHtml().should.contain '<span class="action">'
    area.printHtml().should.contain '<ul class="ui-model-areas">'
    area.printHtml().should.contain '<ul class="ui-model-blocks">'
    area.printHtml().should.contain '<li class="ui-model-blocks block" style="height: 50%;">'
    area.printHtml().should.contain '<li class="ui-model-areas inline" style="width: 50%;">'