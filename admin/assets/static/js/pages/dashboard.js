var mentalHealthReports = {
  series: [
      {
      name: "Normal",
      data: [100, 110, 100, 100, 100, 109, 100, 80, 100, 100, 90, 120]
    },
    {
      name: "Anxiety",
      data: [31, 40, 28, 51, 42, 109, 100, 80, 65, 70, 90, 120]
    },
    {
      name: "Stress",
      data: [11, 32, 45, 32, 34, 52, 41, 55, 60, 50, 65, 75]
    },
    {
      name: "Depression",
      data: [20, 25, 35, 40, 38, 60, 55, 45, 50, 62, 70, 85]
    }
  ],
  chart: {
    height: 350,
    type: "area"
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: "smooth"
  },
  xaxis: {
    categories: [
      "Jan", "Feb", "Mar", "Apr", "May", "Jun", 
      "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ]
  },
  tooltip: {
    x: {
      show: true
    }
  },
  colors: ["#19662cff","#752738", "#e67e22", "#2980b9"],
  legend: {
    position: "top",
    horizontalAlign: "left"
  }
};

var proneGender = {
  annotations: {
    position: "back",
  },
  dataLabels: {
    enabled: false,
  },
  chart: {
    type: "bar",
    height: 350,
  },
  fill: {
    opacity: 1,
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: "40%", // adjust bar width
      endingShape: "rounded",
    },
  },
  series: [
    {
      name: "Male",
      data: [15, 25, 20, 30, 22, 18, 27, 35, 28, 30, 40, 32],
    },
    {
      name: "Female",
      data: [12, 20, 18, 25, 30, 22, 29, 32, 24, 28, 35, 30],
    },
  ],
  colors: ["#752738", "#000"],
  xaxis: {
    categories: [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "May",
      "Jun",
      "Jul",
      "Aug",
      "Sep",
      "Oct",
      "Nov",
      "Dec",
    ],
  },
  legend: {
    position: "top",
    horizontalAlign: "left",
  },
};


let optionsVisitorsProfile = {
  series: [70, 30],
  labels: ["Male", "Female"],
  colors: ["#752738", "#000"],
  chart: {
    type: "donut",
    width: "100%",
    height: "350px",
  },
  legend: {
    position: "bottom",
  },
  plotOptions: {
    pie: {
      donut: {
        size: "30%",
      },
    },
  },
}

var optionsEurope = {
  series: [
    {
      name: "series1",
      data: [310, 800, 600, 430, 540, 340, 605, 805, 430, 540, 340, 605],
    },
  ],
  chart: {
    height: 80,
    type: "area",
    toolbar: {
      show: false,
    },
  },
  colors: ["#5350e9"],
  stroke: {
    width: 2,
  },
  grid: {
    show: false,
  },
  dataLabels: {
    enabled: false,
  },
  xaxis: {
    type: "datetime",
    categories: [
      "2018-09-19T00:00:00.000Z",
      "2018-09-19T01:30:00.000Z",
      "2018-09-19T02:30:00.000Z",
      "2018-09-19T03:30:00.000Z",
      "2018-09-19T04:30:00.000Z",
      "2018-09-19T05:30:00.000Z",
      "2018-09-19T06:30:00.000Z",
      "2018-09-19T07:30:00.000Z",
      "2018-09-19T08:30:00.000Z",
      "2018-09-19T09:30:00.000Z",
      "2018-09-19T10:30:00.000Z",
      "2018-09-19T11:30:00.000Z",
    ],
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
    labels: {
      show: false,
    },
  },
  show: false,
  yaxis: {
    labels: {
      show: false,
    },
  },
  tooltip: {
    x: {
      format: "dd/MM/yy HH:mm",
    },
  },
}

let optionsAmerica = {
  ...optionsEurope,
  colors: ["#008b75"],
}
let optionsIndonesia = {
  ...optionsEurope,
  colors: ["#dc3545"],
}

var chartMentalHealtReports = new ApexCharts(
  document.querySelector("#chart-mental-health-reports"),
  mentalHealthReports
)
var chartProneGender = new ApexCharts(
  document.querySelector("#chart-prone-gender"),
  proneGender
)
var chartVisitorsProfile = new ApexCharts(
  document.getElementById("chart-visitors-profile"),
  optionsVisitorsProfile
)
var chartEurope = new ApexCharts(
  document.querySelector("#chart-europe"),
  optionsEurope
)
var chartAmerica = new ApexCharts(
  document.querySelector("#chart-america"),
  optionsAmerica
)
var chartIndonesia = new ApexCharts(
  document.querySelector("#chart-indonesia"),
  optionsIndonesia
)

chartIndonesia.render()
chartAmerica.render()
chartEurope.render()
chartMentalHealtReports.render()
chartProneGender.render()
chartVisitorsProfile.render()
