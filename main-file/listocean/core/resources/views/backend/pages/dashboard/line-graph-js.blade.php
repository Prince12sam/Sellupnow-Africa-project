<script>
        $(document).ready(function () {
         function cssvar(name) {
          return getComputedStyle(document.documentElement).getPropertyValue(name);
         }

         cssvar();

         let lineChartCustomer;
         let lineChartListings;
         let ctxCustomer = document.getElementById('lineChartCustomer').getContext('2d');
         let ctxListings = document.getElementById('lineChartListings').getContext('2d');

         function createChartCustomer(data) {
          if (lineChartCustomer) {
           lineChartCustomer.destroy();
          }

          lineChartCustomer = new Chart(ctxCustomer, {
           type: 'line',
           data: {
            labels: data.labels,
            datasets: [{
             label: 'Total Users',
             data: data.data,
             borderColor: cssvar('--blue'),
             borderWidth: 2,
             fill: true,
             backgroundColor: 'rgba(0, 128, 255, .05)',
             pointBorderWidth: 2,
             pointBackgroundColor: cssvar('--white'),
             pointRadius: 2,
             pointHoverRadius: 5,
             pointHoverBackgroundColor: cssvar('--blue'),
             lineTension: .5,
            }]
           },
           options: {
            responsive: true,
            plugins: {
             legend: {
              display: true,
              labels: {
               color: cssvar('--body-color'),
              },
             }
            },
            scales: {
             x: {
              ticks: {
               beginAtZero: true,
               color: cssvar('--body-color'),
              },
              grid: {
               borderColor: cssvar('--border-color'),
               lineWidth: 1,
              }
             },
             y: {
              ticks: {
               beginAtZero: true,
               color: cssvar('--body-color'),
              },
              grid: {
               borderColor: cssvar('--border-color'),
               lineWidth: 1,
              }
             }
            },
           },
          });
         }

         function createChartListings(data) {
          if (lineChartListings) {
           lineChartListings.destroy();
          }

          lineChartListings = new Chart(ctxListings, {
           type: 'line',
           data: {
            labels: data.labels,
            datasets: [{
             label: 'Total Listings',
             data: data.data,
             borderColor: cssvar('--blue'),
             borderWidth: 2,
             fill: true,
             backgroundColor: 'rgba(0, 128, 255, .05)',
             pointBorderWidth: 2,
             pointBackgroundColor: cssvar('--white'),
             pointRadius: 2,
             pointHoverRadius: 5,
             pointHoverBackgroundColor: cssvar('--blue'),
             lineTension: .5,
            }]
           },
           options: {
            responsive: true,
            plugins: {
             legend: {
              display: true,
              labels: {
               color: cssvar('--body-color'),
              },
             }
            },
            scales: {
             x: {
              ticks: {
               beginAtZero: true,
               color: cssvar('--body-color'),
              },
              grid: {
               borderColor: cssvar('--border-color'),
               lineWidth: 1,
              }
             },
             y: {
              ticks: {
               beginAtZero: true,
               color: cssvar('--body-color'),
              },
              grid: {
               borderColor: cssvar('--border-color'),
               lineWidth: 1,
              }
             }
            },
           },
          });
         }

         function fetchDataCustomer(interval) {
          $.ajax({
           url: "{{ route('admin.get.user.graph.data') }}",
           method: 'GET',
           data: { interval: interval },
           success: function(response) {
            let labels = [];
            let data = [];
            if (interval === '0') {
             labels = ["{{__('Week 1')}}", "{{__('Week 2')}}", "{{__('Week 3')}}", "{{__('Week 4')}}"];
            } else if (interval === '1') {
             labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            } else if (interval === '2') {
             labels = ['2024'];
            } else if (interval === '3') {
             labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            } else if (interval === '4') {
             labels = ['1 AM', '2 AM', '3 AM', '4 AM', '5 AM', '6 AM', '7 AM', '8 AM', '9 AM', '10 AM', '11 AM', '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM', '6 PM', '7 PM', '8 PM', '9 PM', '10 PM', '11 PM', '12 AM'];
            }

            for (const [key, value] of Object.entries(response)) {
             data.push(value);
            }

            createChartCustomer({ labels: labels, data: data });
           },
           error: function(error) {
            console.error('Error fetching data:', error);
           }
          });
         }

         function fetchDataListings(interval) {
          $.ajax({
           url: "{{ route('admin.get.listing.graph.data') }}",
           method: 'GET',
           data: { interval: interval },
           success: function(response) {
            let labels = [];
            let data = [];
            if (interval === '0') {
             labels = ["{{__('Week 1')}}", "{{__('Week 2')}}", "{{__('Week 3')}}", "{{__('Week 4')}}"];
            } else if (interval === '1') {
             labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            } else if (interval === '2') {
             labels = ['2024'];
            } else if (interval === '3') {
             labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            } else if (interval === '4') {
             labels = ['1 AM', '2 AM', '3 AM', '4 AM', '5 AM', '6 AM', '7 AM', '8 AM', '9 AM', '10 AM', '11 AM', '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM', '6 PM', '7 PM', '8 PM', '9 PM', '10 PM', '11 PM', '12 AM'];
            }

            for (const [key, value] of Object.entries(response)) {
             data.push(value);
            }

            createChartListings({ labels: labels, data: data });
           },
           error: function(error) {
            console.error('Error fetching data:', error);
           }
          });
         }

         $('#timeIntervalSelect').on('change', function() {
          let interval = $(this).val();
          fetchDataCustomer(interval);
         });

         $('#listingTimeIntervalSelect').on('change', function() {
          let interval = $(this).val();
          fetchDataListings(interval);
         });

         fetchDataCustomer('0');
         fetchDataListings('0');

        });
 </script>
