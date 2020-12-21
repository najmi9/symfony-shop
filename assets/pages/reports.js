import '../styles/reports.css'
import ApexCharts from 'apexcharts'

const div = document.querySelector('div#data-admin');

const users = JSON.parse(div.dataset.users);
const months = JSON.parse(div.dataset.months);
const categories = JSON.parse(div.dataset.categories);
const orderPrices = JSON.parse(div.dataset.orderPrices);
const orderMonths = JSON.parse(div.dataset.orderMonths);


const options1 = {
  chart: {
    type: 'area'
  },
  stroke: {
  curve: 'smooth',
},
fill: {
  type: 'gradient'
},
legend: {
    show: true,
    position: 'top'
  },

  dataLabels: {
    enabled: true,
  style: {
    fontSize: '12px',
    fontWeight: 'bold',
  },
  formatter: function(val) {
      return val + ' ' + 'users';
  }
},

  series: [{
    name: 'Users',
    data: users
  }],
  xaxis: {
    categories: months
  }
}

const chart1 = new ApexCharts(document.querySelector("div#chart-users"), options1);
chart1.render();

const names = []; const prods = [];

categories.forEach(e => {names.push(e.category); prods.push(e.prods);})

const options2 = {
  chart: {
    type: 'donut'
  },
   dataLabels: {
    enabled: true,
    formatter: function (val) {
      return Math.round(val) + "%"
    },
  },

  series: prods,
  labels: names,
}
const chart2 = new ApexCharts(document.querySelector("div#chart-categories"), options2);
chart2.render();

const options3 = {
  chart: {
    type: 'bar'
  },
   dataLabels: {
    enabled: true,
    formatter: function (val) {
      return Math.round(val) + ' ' + "$"
    },
  },
  legend:{
position: 'top',
show: true
  },
  series: [{
    name: 'Ordes',
    data: orderPrices
  }],
  xaxis: {
    categories: orderMonths
  }
}

const chart3 = new ApexCharts(document.querySelector("div#chart-orders"), options3);
chart3.render();