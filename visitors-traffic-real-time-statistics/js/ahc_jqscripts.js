var colors = [
  "#DB6946",
  "#C14543",
  "#445060",
  "#395953",
  "#6C8C80",
  "#829AB5",
  "#BF807A",
  "#BF0000",
  "#006BB7",
  "#EC732C",
  "#BF3D27",
  "#A6375F",
  "#8C6D46",
  "#326149",
  "#802B35",
  "#8A3842",
  "#366D73",
  "#4D6173",
  "#4A4659",
  "#C9D65B",
  "#F45552",
  "#F3CC5E",
  "#F29B88",
  "#D96941",
  "#484F73",
  "#C9AB81",
  "#F5655C",
  "#F0C480",
];

var $ = jQuery;
var save_method; //for save method string
var host = window.location.hostname;
var fullpath = window.location.pathname;
var fullparam = window.location.search.split("&");

var firstparam = fullparam[0];
var secoundparam = fullparam[1];

function checkTime(i) {
  if (i < 10) {
    i = "0" + i;
  } // add zero in front of numbers < 10
  return i;
}

//------------------------------------------------------------------------------
function convertToNumeric(data) {
  if (data instanceof Array) {
    for (var index in data) {
      data[index] = Number(data[index]);
    }
  } else {
    data = Number(data);
  }
  return data;
}
//------------------------------------------------------------------------------
function getRandomElementFromArray(array) {
  var ranIndex = Math.floor(Math.random() * array.length);
  return array[ranIndex];
}
//------------------------------------------------------------------------------
// Global chart instance for managing updates
let visitsChart = null;

function drawVisitsLineChart(
  start_date,
  end_date,
  interval,
  visitors,
  visits,
  duration
) {
  loadChartJS(function () {
    drawChart();
  });

  function drawChart() {
    const ctx = document.getElementById("visitscount");

    // Destroy existing chart if it exists
    if (visitsChart) {
      visitsChart.destroy();
    }

    // Prepare data
    const labels = [];
    const visitorsData = [];
    const visitsData = [];

    for (let i = 0; i < visitors.length; i++) {
      labels.push(visitors[i][0]);
      visitorsData.push(parseFloat(visitors[i][1]));
      visitsData.push(parseFloat(visits[i][1]));
    }

    // Create chart
    visitsChart = new Chart(ctx, {
      type: "line",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Visitors",
            data: visitorsData,
            borderColor: "#3366CC",
            backgroundColor: "rgba(51, 102, 204, 0.1)",
            tension: 0, // Straight lines (equivalent to curveType: 'none')
            fill: false,
            pointRadius: 3,
            pointHoverRadius: 5,
          },
          {
            label: "Page Views",
            data: visitsData,
            borderColor: "#DC3912",
            backgroundColor: "rgba(220, 57, 18, 0.1)",
            tension: 0,
            fill: false,
            pointRadius: 3,
            pointHoverRadius: 5,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "top",
            labels: {
              color: "blue",
              font: {
                size: 16,
              },
              usePointStyle: true,
              padding: 20,
            },
          },
          tooltip: {
            mode: "index",
            intersect: false,
            backgroundColor: "rgba(0, 0, 0, 0.8)",
            titleColor: "white",
            bodyColor: "white",
            borderColor: "rgba(255, 255, 255, 0.2)",
            borderWidth: 1,
          },
        },
        scales: {
          x: {
            display: true,
            title: {
              display: true,
              text: "Date",
            },
            grid: {
              display: true,
              color: "rgba(0, 0, 0, 0.1)",
            },
          },
          y: {
            display: true,
            beginAtZero: true,
            title: {
              display: true,
              text: "Count",
            },
            grid: {
              display: true,
              color: "rgba(0, 0, 0, 0.1)",
            },
          },
        },
        interaction: {
          mode: "nearest",
          axis: "x",
          intersect: false,
        },
        elements: {
          point: {
            hoverRadius: 8,
          },
        },
      },
    });
  }
}

//------------------------------------------------------------------------------

function drawBrowsersBieChart(browsersData) {
  if (browsersData.length > 0) {
    var brsBieChartData = [];
    var brsBieChartDataLables = [];
    brsBieChartDataLables[0] = "";
    brsBieChartDataLables[1] = "";
    brsBieChartDataLables[2] = "";
    brsBieChartDataLables[3] = "";
    brsBieChartDataLables[4] = "";

    for (var i = 0; i < browsersData.length; i++) {
      var color = getRandomElementFromArray(colors);
      var value = Number(browsersData[i].hits);
      brsBieChartData[i] = value;
      brsBieChartDataLables[i] = browsersData[i].bsr_name;
    }

    var config = {
      type: "pie",
      data: {
        datasets: [
          {
            data: [
              brsBieChartData[0],
              brsBieChartData[1],
              brsBieChartData[2],
              brsBieChartData[3],
              brsBieChartData[4],
            ],
            backgroundColor: [
              window.chartColors.red,
              window.chartColors.orange,
              window.chartColors.yellow,
              window.chartColors.green,
              window.chartColors.blue,
            ],
            label: "Browser",
          },
        ],
        labels: [
          brsBieChartDataLables[0],
          brsBieChartDataLables[1],
          brsBieChartDataLables[2],
          brsBieChartDataLables[3],
          brsBieChartDataLables[4],
        ],
      },
      options: {
        responsive: true,
      },
    };

    window.onload = function () {
      var ctx = document
        .getElementById("brsBiechartContainer")
        .getContext("2d");
      window.myPie = new Chart(ctx, config);
    };
  } else {
    document.getElementById("brsBiechartContainer_msg").innerHTML =
      "<center>No data to display</center>";
  }
}

//------------------------------------------------------------------------------
function drawSrhEngVstLineChart(srhEngVisitsData) {
  if (srhEngVisitsData.length > 0) {
    var srhBieChartData = [];
    var srhBieChartDataLables = [];
    srhBieChartDataLables[0] = "";
    srhBieChartDataLables[1] = "";
    srhBieChartDataLables[2] = "";
    srhBieChartDataLables[3] = "";
    srhBieChartDataLables[4] = "";

    for (var i = 0; i < srhEngVisitsData.length; i++) {
      var color = getRandomElementFromArray(colors);
      var value = Number(srhEngVisitsData[i].hits);
      srhBieChartData[i] = value;
      srhBieChartDataLables[i] = srhEngVisitsData[i].bsr_name;
    }

    var config = {
      type: "pie",
      data: {
        datasets: [
          {
            data: [
              srhBieChartData[0],
              srhBieChartData[1],
              srhBieChartData[2],
              srhBieChartData[3],
              srhBieChartData[4],
            ],
            backgroundColor: [
              window.chartColors.yellow,
              window.chartColors.green,
              window.chartColors.blue,
              window.chartColors.red,
              window.chartColors.orange,
            ],
            label: "Search Engine",
          },
        ],
        labels: [
          srhBieChartDataLables[0],
          srhBieChartDataLables[1],
          srhBieChartDataLables[2],
          srhBieChartDataLables[3],
          srhBieChartDataLables[4],
        ],
      },
      options: {
        responsive: true,
      },
    };

    var ctx2 = document
      .getElementById("srhEngBieChartContainer")
      .getContext("2d");
    window.myPie = new Chart(ctx2, config);
    window.myPie = new Chart(ctx2, config);
  } else {
    document.getElementById("srhEngBieChartContainer_msg").innerHTML =
      "<center>No data to display</center>";
  }
}

function isEmpty(val) {
  return val == null || val == 0 || val == "" || val == "0";
}

//------------------------------------------------------------------------------
function countVisits(arr) {
  var count = 0;
  for (var i = 0; i < arr.length; i++) {
    count += Number(arr[i]);
  }
  return count;
}
//------------------------------------------------------------------------------

jQuery(document).ready(function () {
  if (typeof browsersData !== "undefined") {
    //------------------------------------------
    //if(visitsData.success && typeof visitsData.data != 'undefined'){
    var duration = jQuery("#hits-duration").val();
    drawVisitsLineChart(
      mystart_date,
      myend_date,
      "1 day",
      visitors_data,
      visits_data,
      duration
    );
    //}
    //------------------------------------------
    if (
      browsersData.success &&
      typeof browsersData.data != "undefined" &&
      typeof drawBrowsersBieChart === "function"
    ) {
      drawBrowsersBieChart(browsersData.data);
    }
    //------------------------------------------
    if (
      srhEngVisitsData.success &&
      typeof srhEngVisitsData.data != "undefined" &&
      typeof drawSrhEngVstLineChart === "function"
    ) {
      drawSrhEngVstLineChart(srhEngVisitsData.data);
    }
    //------------------------------------------

    //------------------------------------------
  }
});
