<?php
namespace Arii\CoreBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class AriiFlowchart
{
    private $container;
    
    public function __construct( ContainerInterface $container, \Arii\CoreBundle\Service\AriiPortal $portal )
    {   
        $this->container = $container;
    }

    public function sequence( $graph, $Options=array() ) {
print '<html>
<head>  
<link href="https://unpkg.com/mermaid@7.0.8/dist/mermaid.min.css" rel="stylesheet" type="text/css" />
<style type="text/css">
body {
    background: white;
}
.actor {
    stroke: #CCCCFF;
    fill: #ECECFF;
}
text.actor {
    fill:black;
    stroke:none;
    font-family: Helvetica;
}
.actor-line {
    stroke:grey;
}

.messageLine0 {
    stroke-width:1.5;
    stroke-dasharray: "2 2";
    marker-end:"url(#arrowhead)";
    stroke:black;
}

.messageLine1 {
    stroke-width:1.5;
    stroke-dasharray: "2 2";
    stroke:black;
}

#arrowhead {
    fill:black;

}

.messageText {
    fill:black;
    stroke:none;
    font-family: "trebuchet ms", verdana, arial;
    font-size:14px;
}

.labelBox {
    stroke: #CCCCFF;
    fill: #ECECFF;
}

.labelText {
    fill:black;
    stroke:none;
    font-family: "trebuchet ms", verdana, arial;
}

.loopText {
    fill:black;
    stroke:none;
    font-family: "trebuchet ms", verdana, arial;
}

.loopLine {
    stroke-width:2;
    stroke-dasharray: "2 2";
    marker-end:"url(#arrowhead)";
    stroke: #CCCCFF;
}

.note {
    stroke: #decc93;
    stroke: #CCCCFF;
    fill: #fff5ad;
}

.noteText {
    fill:black;
    stroke:none;
    font-family: "trebuchet ms", verdana, arial;
    font-size:14px;
}</style>
</head>
<body>
  <div class="mermaid">'.$graph.'</div>
  <script src="https://unpkg.com/mermaid@7.0.8/dist/mermaid.min.js"></script>
  <script>mermaid.initialize({startOnLoad:true});</script>
</body>
</html>';
exit();
    }
    
    public function gantt( $graph, $Options=array() ) {
print '<html>
<head>  
<link href="https://unpkg.com/mermaid@7.0.8/dist/mermaid.min.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.grid .tick {
    stroke: lightgrey;
    opacity: 0.3;
    shape-rendering: crispEdges;
}
.grid path {
    stroke-width: 0;
}

#tag {
    color: white;
    background: #FA283D;
    width: 150px;
    position: absolute;
    display: none;
    padding:3px 6px;
    margin-left: -80px;
    font-size: 11px;
}

#tag:before {
    border: solid transparent;
    content: " ";
    height: 0;
    left: 50%;
    margin-left: -5px;
    position: absolute;
    width: 0;
    border-width: 10px;
    border-bottom-color: #FA283D;
    top: -20px;
}
.taskText {
    font-family: "trebuchet ms", verdana, arial;
    fill:white;
    text-anchor:middle;
}
.taskTextOutsideRight {
    font-family: "trebuchet ms", verdana, arial;
    fill:black;
    text-anchor:start;
}
.taskTextOutsideLeft {
    font-family: "trebuchet ms", verdana, arial;
    fill:black;
    text-anchor:end;
}
</style>
</head>
<body>
  <div class="mermaid">'.$graph.'</div>
  <script src="https://unpkg.com/mermaid@7.0.8/dist/mermaid.min.js"></script>
  <script>        
  mermaid.initialize({
                gantt: {
                    titleTopMargin:25,
                    barHeight:20,
                    barGap:4,
                    topPadding:50,
                    leftPadding:75,
                    gridLineStartPadding:5,
                    fontSize:11,
                    numberSectionStyles:3,
                    axisFormatter: [
                        // Within an hour
                        ["E %I:%M", function (d) {
                            return d.getMinutes();
                        }],                        // Within a day
                        ["X %I:%M", function (d) {
                            return d.getHours();
                        }],
                        // Monday a week
                        ["w. %U", function (d) {
                            return d.getDay() == 1;
                        }],
                        // Day within a week (not monday)
                        ["%a %d", function (d) {
                            return d.getDay() && d.getDate() != 1;
                        }],
                        // within a month
                        ["%b %d", function (d) {
                            return d.getDate() != 1;
                        }],
                        // Month
                        ["%m-%y", function (d) {
                            return d.getMonth();
                        }] 
                    ]
                }
            
        });
</script>
</body>
</html>';
exit();
    }

    public function flowchart( $graph, $Options=array() ) {
print '<html>
<head>  
<link href="https://unpkg.com/mermaid@7.0.8/dist/mermaid.min.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.grid .tick {
    stroke: lightgrey;
    opacity: 0.3;
    shape-rendering: crispEdges;
}
.grid path {
    stroke-width: 0;
}

#tag {
    color: white;
    background: #FA283D;
    width: 150px;
    position: absolute;
    display: none;
    padding:3px 6px;
    margin-left: -80px;
    font-size: 11px;
}

#tag:before {
    border: solid transparent;
    content: " ";
    height: 0;
    left: 50%;
    margin-left: -5px;
    position: absolute;
    width: 0;
    border-width: 10px;
    border-bottom-color: #FA283D;
    top: -20px;
}
.taskText {
    font-family: "trebuchet ms", verdana, arial;
    fill:white;
    text-anchor:middle;
}
.taskTextOutsideRight {
    font-family: "trebuchet ms", verdana, arial;
    fill:black;
    text-anchor:start;
}
.taskTextOutsideLeft {
    font-family: "trebuchet ms", verdana, arial;
    fill:black;
    text-anchor:end;
}
</style>
</head>
<body>
  <div class="mermaid">'.$graph.'</div>
  <script src="https://unpkg.com/mermaid@7.0.8/dist/mermaid.min.js"></script>
  <script>     
  mermaid.flowchartConfig = {
    width: 100%
  }
  mermaid.initialize({startOnLoad:true});
</script>
</body>
</html>';
exit();
    }
    
}
