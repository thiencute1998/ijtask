<template>
  <div id="sample">
    <div class="d-lg-flex" style="width: 100%;">
      <div id="myPaletteDiv" style="height: 100%; width: 150px;"></div>
      <div style="vertical-align: top; flex: 1 1 auto"><div id="myDiagramDiv" style="height: 100%"></div></div>
    </div>
    <button @click="save">save</button>
    <button @click="load">load</button>
    <button @click="makeSVG">make SVG</button>
    <div style="display: none;">
      Diagram Model saved in JSON format:
      <textarea id="mySavedModel" style="width:100%;height:300px">
        {
          "nodeDataArray": [],
          "linkDataArray": []
        }
      </textarea>
    </div>
    <div>
      <svg width="357px" height="135px" QK="0 0 357 135" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g transform="matrix(1, 0, 0, 1, 0, 0)" clip-path="url(#mainClip21434)"><rect x="0" y="0" width="357" height="135" fill="white" stroke="none" transform="matrix(1, 0, 0, 1, 0, 0)"/><g transform="matrix(1, 0, 0, 1, 322.49713134765625, 254.2189743041992)"><g transform="matrix(1, 0, 0, 1, -321.49713134765625, -253.2189743041992)"><g transform="matrix(1, 0, 0, 1, 0, 0)"><path d="M 0,0 L 59.9942626953125,0 L 59.9942626953125,31.875448608398436 L 0,31.875448608398436 z" fill="#00A9C9" stroke="none" transform="matrix(1, 0, 0, 1, 0.5, 0.5)"/><text x="0" y="11.906586456298829" style="font: bold 11pt Helvetica, Arial, sans-serif" text-anchor="start" fill="whitesmoke" stroke="none" transform="matrix(1, 0, 0, 1, 8.5, 8.5)">Step 1</text></g><path d="M 4,0 C 6.209138999323175,0 8,1.7908610006768257 8,4 C 8,6.209138999323175 6.209138999323175,8 4,8 C 1.7908610006768257,8 0,6.209138999323175 0,4 C 0,1.7908610006768257 1.7908610006768257,0 4,0 z" fill="transparent" stroke="none" transform="matrix(1, 0, 0, 1, 26.49713134765625, 0.5)"/><path d="M 4,0 C 6.209138999323175,0 8,1.7908610006768257 8,4 C 8,6.209138999323175 6.209138999323175,8 4,8 C 1.7908610006768257,8 0,6.209138999323175 0,4 C 0,1.7908610006768257 1.7908610006768257,0 4,0 z" fill="transparent" stroke="none" transform="matrix(1, 0, 0, 1, 0.5, 12.437724304199218)"/><path d="M 4,0 C 6.209138999323175,0 8,1.7908610006768257 8,4 C 8,6.209138999323175 6.209138999323175,8 4,8 C 1.7908610006768257,8 0,6.209138999323175 0,4 C 0,1.7908610006768257 1.7908610006768257,0 4,0 z" fill="transparent" stroke="none" transform="matrix(1, 0, 0, 1, 52.4942626953125, 12.437724304199218)"/><path d="M 4,0 C 6.209138999323175,0 8,1.7908610006768257 8,4 C 8,6.209138999323175 6.209138999323175,8 4,8 C 1.7908610006768257,8 0,6.209138999323175 0,4 C 0,1.7908610006768257 1.7908610006768257,0 4,0 z" fill="transparent" stroke="none" transform="matrix(1, 0, 0, 1, 26.49713134765625, 24.375448608398436)"/></g><g transform="matrix(1, 0, 0, 1, -156.49713134765625, -153.2189743041992)"><g transform="matrix(1, 0, 0, 1, 0, 0)"><path d="M 0,0 L 59.9942626953125,0 L 59.9942626953125,31.875448608398436 L 0,31.875448608398436 z" fill="#00A9C9" stroke="none" transform="matrix(1, 0, 0, 1, 0.5, 0.5)"/><text x="0" y="11.906586456298829" style="font: bold 11pt Helvetica, Arial, sans-serif" text-anchor="start" fill="whitesmoke" stroke="none" transform="matrix(1, 0, 0, 1, 8.5, 8.5)">Step 2</text></g><path d="M 4,0 C 6.209138999323175,0 8,1.7908610006768257 8,4 C 8,6.209138999323175 6.209138999323175,8 4,8 C 1.7908610006768257,8 0,6.209138999323175 0,4 C 0,1.7908610006768257 1.7908610006768257,0 4,0 z" fill="transparent" stroke="none" transform="matrix(1, 0, 0, 1, 26.49713134765625, 0.5)"/><path d="M 4,0 C 6.209138999323175,0 8,1.7908610006768257 8,4 C 8,6.209138999323175 6.209138999323175,8 4,8 C 1.7908610006768257,8 0,6.209138999323175 0,4 C 0,1.7908610006768257 1.7908610006768257,0 4,0 z" fill="transparent" stroke="none" transform="matrix(1, 0, 0, 1, 0.5, 12.437724304199218)"/><path d="M 4,0 C 6.209138999323175,0 8,1.7908610006768257 8,4 C 8,6.209138999323175 6.209138999323175,8 4,8 C 1.7908610006768257,8 0,6.209138999323175 0,4 C 0,1.7908610006768257 1.7908610006768257,0 4,0 z" fill="transparent" stroke="none" transform="matrix(1, 0, 0, 1, 52.4942626953125, 12.437724304199218)"/><path d="M 4,0 C 6.209138999323175,0 8,1.7908610006768257 8,4 C 8,6.209138999323175 6.209138999323175,8 4,8 C 1.7908610006768257,8 0,6.209138999323175 0,4 C 0,1.7908610006768257 1.7908610006768257,0 4,0 z" fill="transparent" stroke="none" transform="matrix(1, 0, 0, 1, 26.49713134765625, 24.375448608398436)"/></g><g transform="matrix(1, 0, 0, 1, -27.49713134765625, -251.2189743041992)"><g transform="matrix(1, 0, 0, 1, 0, 0)"><path d="M 0,0 L 59.9942626953125,0 L 59.9942626953125,31.875448608398436 L 0,31.875448608398436 z" fill="#00A9C9" stroke="none" transform="matrix(1, 0, 0, 1, 0.5, 0.5)"/><text x="0" y="11.906586456298829" style="font: bold 11pt Helvetica, Arial, sans-serif" text-anchor="start" fill="whitesmoke" stroke="none" transform="matrix(1, 0, 0, 1, 8.5, 8.5)">Step 3</text></g><path d="M 4,0 C 6.209138999323175,0 8,1.7908610006768257 8,4 C 8,6.209138999323175 6.209138999323175,8 4,8 C 1.7908610006768257,8 0,6.209138999323175 0,4 C 0,1.7908610006768257 1.7908610006768257,0 4,0 z" fill="transparent" stroke="none" transform="matrix(1, 0, 0, 1, 26.49713134765625, 0.5)"/><path d="M 4,0 C 6.209138999323175,0 8,1.7908610006768257 8,4 C 8,6.209138999323175 6.209138999323175,8 4,8 C 1.7908610006768257,8 0,6.209138999323175 0,4 C 0,1.7908610006768257 1.7908610006768257,0 4,0 z" fill="transparent" stroke="none" transform="matrix(1, 0, 0, 1, 0.5, 12.437724304199218)"/><path d="M 4,0 C 6.209138999323175,0 8,1.7908610006768257 8,4 C 8,6.209138999323175 6.209138999323175,8 4,8 C 1.7908610006768257,8 0,6.209138999323175 0,4 C 0,1.7908610006768257 1.7908610006768257,0 4,0 z" fill="transparent" stroke="none" transform="matrix(1, 0, 0, 1, 52.4942626953125, 12.437724304199218)"/><path d="M 4,0 C 6.209138999323175,0 8,1.7908610006768257 8,4 C 8,6.209138999323175 6.209138999323175,8 4,8 C 1.7908610006768257,8 0,6.209138999323175 0,4 C 0,1.7908610006768257 1.7908610006768257,0 4,0 z" fill="transparent" stroke="none" transform="matrix(1, 0, 0, 1, 26.49713134765625, 24.375448608398436)"/></g><g transform="matrix(1, 0, 0, 1, -264.50286865234375, -246.2602663700036)"><path d="M 0,0 L 115.50286865234375,0 Q 116.50286865234375,0 116.50286865234375,1 L 116.50286865234375,1.5 Q 116.50286865234375,2 117.00286865234375,2 L 229.0057373046875,2" fill="none" stroke="transparent" stroke-width="8" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" transform="matrix(1, 0, 0, 1, 4, 9.479016370003578)"/><path d="M 0,0 L 115.50286865234375,0 Q 116.50286865234375,0 116.50286865234375,1 L 116.50286865234375,1.5 Q 116.50286865234375,2 117.00286865234375,2 L 229.0057373046875,2" fill="none" stroke="gray" stroke-width="2" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" transform="matrix(1, 0, 0, 1, 4, 9.479016370003578)"/><path d="M 0,0 L 8,4 L 0,8 L 2,4 z" fill="gray" stroke="none" transform="matrix(1, 0, 0, 1, 228.5057373046875, 7.479016370003578)"/></g><g transform="matrix(1, 0, 0, 1, -264.50286865234375, -240.78125)"><path d="M 0,0 L 47.00286865234375,0 Q 52.00286865234375,0 52.00286865234375,5 L 52.00286865234375,95 Q 52.00286865234375,100 57.00286865234375,100 L 100.0057373046875,100" fill="none" stroke="transparent" stroke-width="8" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" transform="matrix(1, 0, 0, 1, 4, 4)"/><path d="M 0,0 L 47.00286865234375,0 Q 52.00286865234375,0 52.00286865234375,5 L 52.00286865234375,95 Q 52.00286865234375,100 57.00286865234375,100 L 100.0057373046875,100" fill="none" stroke="gray" stroke-width="2" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" transform="matrix(1, 0, 0, 1, 4, 4)"/><path d="M 0,0 L 8,4 L 0,8 L 2,4 z" fill="gray" stroke="none" transform="matrix(1, 0, 0, 1, 99.5057373046875, 100)"/></g></g></g><clipPath id="mainClip21434"><rect x="0" y="0" width="357" height="135"/></clipPath></svg>
    </div>
  </div>
</template>

<script>
import * as mylib from '@/libraries/flowchart';
let myDiagram = {}, myPalette = {};
export default {
  name: "Flowchart",
  props: {
    diagramConfig: [Object, Array]
  },
  data: function() {
    return {};
  },
  mounted() {
    this.init();
  },
  computed: {

  },
  methods: {
    init() {
      let self = this;
      if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this
      let $ = go.GraphObject.make;  // for conciseness in defining templates

      myDiagram =
        $(go.Diagram, "myDiagramDiv",  // must name or refer to the DIV HTML element
          {
            initialContentAlignment: go.Spot.Center,
            allowDrop: true,  // must be true to accept drops from the Palette
            // allowTextEdit: false,
            "LinkDrawn": showLinkLabel,  // this DiagramEvent listener is defined below
            "LinkRelinked": showLinkLabel,
            "animationManager.duration": 800, // slightly longer than default (600ms) animation
            "undoManager.isEnabled": true  // enable undo & redo,
          });

      // when the document is modified, add a "*" to the title and enable the "Save" button
      myDiagram.addDiagramListener("Modified", function(e) {
        let button = document.getElementById("SaveButton");
        if (button) button.disabled = !myDiagram.isModified;
        let idx = document.title.indexOf("*");
        if (myDiagram.isModified) {
          if (idx < 0) document.title += "*";
        } else {
          if (idx >= 0) document.title = document.title.substr(0, idx);
        }
      });

      // helper definitions for node templates
      function nodeStyle() {
        return [
          // The Node.location comes from the "loc" property of the node data,
          // converted by the Point.parse static method.
          // If the Node.location is changed, it updates the "loc" property of the node data,
          // converting back using the Point.stringify static method.
          new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
          {
            // the Node.location is at the center of each node
            locationSpot: go.Spot.Center,
            //isShadowed: true,
            //shadowColor: "#888",
            // handle mouse enter/leave events to show/hide the ports
            mouseEnter: function (e, obj) { self.showPorts(obj.part, true); },
            mouseLeave: function (e, obj) { self.showPorts(obj.part, false); }
          }
        ];
      }
      // Define a function for creating a "port" that is normally transparent.
      // The "name" is used as the GraphObject.portId, the "spot" is used to control how links connect
      // and where the port is positioned on the node, and the boolean "output" and "input" arguments
      // control whether the user can draw links from or to the port.
      function makePort(name, spot, output, input) {
        // the port is basically just a small circle that has a white stroke when it is made visible
        return $(go.Shape, "Circle",
          {
            fill: "transparent",
            stroke: null,  // this is changed to "white" in the showPorts function
            desiredSize: new go.Size(8, 8),
            alignment: spot, alignmentFocus: spot,  // align the port on the main Shape
            portId: name,  // declare this object to be a "port"
            fromSpot: spot, toSpot: spot,  // declare where links may connect at this port
            fromLinkable: output, toLinkable: input,  // declare whether the user may draw links to/from here
            cursor: "pointer"  // show a different cursor to indicate potential link point
          });
      }
      // define the Node templates for regular nodes
      var lightText = 'whitesmoke';
      // myDiagram.nodeTemplate =
      //   $(go.Node, "Vertical", nodeStyle(),
      //     $(go.Panel, "Auto",
      //       $(go.Shape, "Rectangle",
      //         { fill: "#00A9C9", stroke: null},
      //         new go.Binding("figure", "figure"),
      //       ),
      //       $(go.TextBlock,
      //         { margin: new go.Margin(3, 0, 0, 0),
      //           maxSize: new go.Size(100, 30),
      //           isMultiline: false,
      //           font: "bold 10pt sans-serif" },
      //         new go.Binding("text", "head")),
      //       $(go.Picture,
      //         { maxSize: new go.Size(100, 100) },
      //         new go.Binding("source", "img")),
      //       $(go.TextBlock,
      //         { margin: new go.Margin(3, 0, 0, 0),
      //           maxSize: new go.Size(100, 30),
      //           isMultiline: false },
      //         new go.Binding("text", "foot")
      //       )
      //     ),
      //   );
      myDiagram.nodeTemplateMap.add("",  // the default category
        $(go.Node, "Spot", nodeStyle(),
          // the main object is a Panel that surrounds a TextBlock with a rectangular Shape
          $(go.Panel, "Auto",
            $(go.Shape, "Rectangle",
              { fill: "#00a2e8", stroke: null},
              new go.Binding("figure", "figure"),
            ),
            $(go.TextBlock,
              {
                font: "bold 11pt Helvetica, Arial, sans-serif",
                stroke: lightText,
                margin: 8,
                maxSize: new go.Size(160, NaN),
                wrap: go.TextBlock.WrapFit,
                editable: true},
              new go.Binding("text").makeTwoWay()),
            {
              cursor: "pointer",
              click: function(e, obj) {
                console.log(e);
                console.log(obj);
                // console.log(obj.part);
                // console.log(obj.part.data);
                let link = (obj.part.data.href) ? obj.part.data.href : '';
                if (link !== '') window.open(link);
              }
            },
          ),

          // four named ports, one on each side:
          makePort("T", go.Spot.Top, false, true),
          makePort("L", go.Spot.Left, true, true),
          makePort("R", go.Spot.Right, true, true),
          makePort("B", go.Spot.Bottom, true, false)
        ));
      myDiagram.nodeTemplateMap.add("Start",
        $(go.Node, "Spot", nodeStyle(),
          $(go.Panel, "Auto",
            $(go.Shape, "Circle",
              { minSize: new go.Size(40, 40), fill: "#79C900", stroke: null }),
            $(go.TextBlock, "Start",
              { font: "bold 11pt Helvetica, Arial, sans-serif", stroke: lightText },
              new go.Binding("text"))
          ),
          // three named ports, one on each side except the top, all output only:
          makePort("L", go.Spot.Left, true, false),
          makePort("R", go.Spot.Right, true, false),
          makePort("B", go.Spot.Bottom, true, false)
        ));
      myDiagram.nodeTemplateMap.add("End",
        $(go.Node, "Spot", nodeStyle(),
          $(go.Panel, "Auto",
            $(go.Shape, "Circle",
              { minSize: new go.Size(40, 40), fill: "#DC3C00", stroke: null }),
            $(go.TextBlock, "End",
              { font: "bold 11pt Helvetica, Arial, sans-serif", stroke: lightText },
              new go.Binding("text"))
          ),
          // three named ports, one on each side except the bottom, all input only:
          makePort("T", go.Spot.Top, false, true),
          makePort("L", go.Spot.Left, false, true),
          makePort("R", go.Spot.Right, false, true)
        ));
      myDiagram.nodeTemplateMap.add("Comment",
        $(go.Node, "Auto", nodeStyle(),
          $(go.Shape, "File",
            { fill: "#EFFAB4", stroke: null }),
          $(go.TextBlock,
            {
              margin: 5,
              maxSize: new go.Size(200, NaN),
              wrap: go.TextBlock.WrapFit,
              textAlign: "center",
              editable: true,
              font: "bold 12pt Helvetica, Arial, sans-serif",
              stroke: '#454545'
            },
            new go.Binding("text").makeTwoWay())
          // no ports, because no links are allowed to connect with a comment
        ));
      // replace the default Link template in the linkTemplateMap

      myDiagram.linkTemplate =
        $(go.Link,  // the whole link panel
          {
            routing: go.Link.AvoidsNodes,
            curve: go.Link.JumpOver,
            corner: 5, toShortLength: 4,
            relinkableFrom: true,
            relinkableTo: true,
            reshapable: true,
            resegmentable: true,
            // mouse-overs subtly highlight links:
            mouseEnter: function(e, link) { link.findObject("HIGHLIGHT").stroke = "rgba(30,144,255,0.2)"; },
            mouseLeave: function(e, link) { link.findObject("HIGHLIGHT").stroke = "transparent"; }
          },
          new go.Binding("points").makeTwoWay(),
          $(go.Shape,  // the highlight shape, normally transparent
            { isPanelMain: true, strokeWidth: 8, stroke: "transparent", name: "HIGHLIGHT" }),
          $(go.Shape,  // the link path shape
            { isPanelMain: true, stroke: "gray", strokeWidth: 2 }),
          $(go.Shape,  // the arrowhead
            { toArrow: "standard", stroke: null, fill: "gray"}),
          $(go.Panel, "Auto",  // the link label, normally not visible
            { visible: false, name: "LABEL", segmentIndex: 2, segmentFraction: 0.5},
            new go.Binding("visible", "visible").makeTwoWay(),
            $(go.Shape, "RoundedRectangle",  // the label shape
              { fill: "#F8F8F8", stroke: null }),
            $(go.TextBlock, "Yes",  // the label
              {
                textAlign: "center",
                font: "10pt helvetica, arial, sans-serif",
                stroke: "#333333",
                editable: true
              },
              new go.Binding("text").makeTwoWay())
          )
        );
      // Make link labels visible if coming out of a "conditional" node.
      // This listener is called by the "LinkDrawn" and "LinkRelinked" DiagramEvents.
      function showLinkLabel(e) {
        var label = e.subject.findObject("LABEL");
        if (label !== null) label.visible = (e.subject.fromNode.data.figure === "Diamond");
      }
      // temporary links used by LinkingTool and RelinkingTool are also orthogonal:
      myDiagram.toolManager.linkingTool.temporaryLink.routing = go.Link.Orthogonal;
      myDiagram.toolManager.relinkingTool.temporaryLink.routing = go.Link.Orthogonal;
      this.load();  // load an initial diagram from some JSON text
      // initialize the Palette that is on the left side of the page
      myPalette =
        $(go.Palette, "myPaletteDiv",  // must name or refer to the DIV HTML element
          {
            "animationManager.duration": 800, // slightly longer than default (600ms) animation
            nodeTemplateMap: myDiagram.nodeTemplateMap,  // share the templates used by myDiagram
            model: new go.GraphLinksModel([  // specify the contents of the Palette
              //{ category: "Start", text: "Start" },
              { text: "Step 1", name: "step-1", href: '#/sysadmin/users', version: 'beta'},
              { text: "Step 2" , href: '#/sysadmin/group-user'},
              { text: "Step 3" },
              { text: "Step 4" },
              { text: "Step 5" },
              { text: "???", figure: "Diamond" },
              //{ category: "End", text: "End" },
              { category: "Comment", text: "Comment" }
            ])
          });

    },

    showPorts(node, show){
      var diagram = node.diagram;
      if (!diagram || diagram.isReadOnly || !diagram.allowLink) return;
      node.ports.each(function(port) {
        port.stroke = (show ? "white" : null);
      });
    },
    save() {
      myDiagram.isModified = false;
      myDiagram.isReadOnly = true;
      myDiagram.isModelReadOnly = true;
      document.getElementById("mySavedModel").value = myDiagram.model.toJson();
      console.log(myDiagram.model.toJson());
    },
    load() {
      myDiagram.model = go.Model.fromJson(document.getElementById("mySavedModel").value);
    },
    makeSVG() {
      let svg = myDiagram.makeSvg({ scale: 1, background: "white" });
      let svgStr = new XMLSerializer().serializeToString(svg);
      let blob = new Blob([svgStr], { type: "image/svg+xml" });
    }
  }
};
</script>

<style>
</style>
