// CHART SPLINE
// ----------------------------------- 
(function (window, document, $, undefined) {
  $(function () {

    getresult();

    $('.panel.panel-demo').on('panel.refresh', function (e, panel) {
      getresult();
      setTimeout(() => panel.removeSpinner(), 1000);
    });

    async function getresult() {
      try {
        const data = await getDataToAPI();
        updateUI(data, heighestData(data));
      } catch (err) {
        console.log(err);
      }
    }

    function getDataToAPI() {
      return new Promise((resolve, reject) => {
        fetch(`${baseurl}administrator/visitors?session=${session}&username=${username}`)
          .then(res => res.json())
          .then(res => resolve(res))
          .catch(err => reject(err))
      });
    }

    function heighestData(data) {
      let recurrent = data[0].data
      let uniques = data[1].data

      let heighest = 0;

      for (let i = 0; i < recurrent.length; i++) {
        let count = recurrent[i]
        let result = "";
        count.forEach(c => result = c);

        if (Number(result) > heighest) {
          heighest = result;
        }
      }
      for (let i = 0; i < uniques.length; i++) {
        let count = uniques[i]
        let result = "";
        count.forEach(c => result = c);

        if (Number(result) > heighest) {
          heighest = result;
        }
      }

      return Number(heighest) + 50;
    }

    function updateUI(data, heighest) {
      var chart = $('.chart-spline');
      var options = {
        series: {
          lines: {
            show: false
          },
          points: {
            show: true,
            radius: 4
          },
          splines: {
            show: true,
            tension: 0.4,
            lineWidth: 1,
            fill: 0.5
          }
        },
        grid: {
          borderColor: '#eee',
          borderWidth: 1,
          hoverable: true,
          backgroundColor: '#fcfcfc'
        },
        tooltip: true,
        tooltipOpts: {
          content: function (label, x, y) { return x + ' : ' + y; }
        },
        xaxis: {
          tickColor: '#fcfcfc',
          mode: 'categories'
        },
        yaxis: {
          min: 0,
          max: heighest, // optional: use it for a clear represetation
          tickColor: '#eee',
          //position: 'right' or 'left',
          tickFormatter: function (v) {
            return v/* + ' visitors'*/;
          }
        },
        shadowSize: 0
      };

      if (chart.length) $.plot(chart, data, options);
    }

  });
})(window, document, window.jQuery);
