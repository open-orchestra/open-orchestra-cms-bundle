chai = require ("chai")
chai.should()

{Node} = require ("../../../../Resources/public/coffee/node")
{Area} = require ("../../../../Resources/public/coffee/area")

describe "Node Instance", ->
  node = null
  nodeResponse =
    name: "Fixture Home"
    alias: "-"
    areas: [
      {
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
      },
      {
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
      }
    ]
  it 'should contain a nodeResponse object', ->
    node = new Node nodeResponse
    node.nodeResponse.should.equal nodeResponse
    node.areas.length.should.equal 2
    node.areas[0].should.instanceof Area
  it 'should print the first div with the title', ->
    node.printHtml().should.contain '<div class="ui-model">'
    node.printHtml().should.contain '</div>'
    node.printHtml().should.contain '<span class="title">' + nodeResponse.name + '</span>'
  it 'should contains the title', ->
    node.renderTitle().should.equal '<span class="title">' + nodeResponse.name + '</span>'
  it 'should contains the action', ->
    node.renderActionButton().should.contain '<span class="action"><i class="fa fa-cog"></i></span>'
  it 'should contain the area', ->
    node.printHtml().should.contain '<span class="preview"></span>'
    node.printHtml().should.contain '<span class="action"><i class="fa fa-cog"></i></span>'
    node.printHtml().should.contain '<ul class="ui-model-areas">'
    node.printHtml().should.contain '<li class="ui-model-areas block" style="height: 50%;">'