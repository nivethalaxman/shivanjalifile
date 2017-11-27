window.onload = function () {
    var chart = new CanvasJS.Chart("chartContainer",
    {
        title:{
            text: "Presidential Election Results"
        },
                animationEnabled: true,
        data: [
        {
            type: "doughnut",
            startAngle: 60,
            toolTipContent: "{legendText}: {y} - <strong>#percent% </strong>",
            showInLegend: true,
          explodeOnClick: false, //**Change it to true
            dataPoints: [
                {y: 65899660, indexLabel: "Barack Obama #percent%", legendText: "Barack Obama" },
                {y: 60929152, indexLabel: "Mitt Romney #percent%", legendText: "Mitt Romney" },
                {y: 2175850,  indexLabel: "Others #percent%", legendText: "Others" }
            ]
        }
        ]
    });
    chart.render();
    }
/*-----scroll nav----*/  