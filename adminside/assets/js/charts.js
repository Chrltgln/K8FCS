function updateAnalytics() {
    var duration = document.getElementById('analytics-duration').value;
    var url = 'analytics.php?duration=' + duration;
    if (duration === 'custom') {
        Swal.fire({
            title: 'Select Date Range',
            html: 'Start : <input type="date" id="start-date" class="swal2-input" placeholder="Start Date">' +
                  '<br>End : <input type="date" id="end-date" class="swal2-input" placeholder="End Date">',
            focusConfirm: false,
            preConfirm: () => {
                var startDate = document.getElementById('start-date').value;
                var endDate = document.getElementById('end-date').value;
                if (!startDate || !endDate) {
                    Swal.showValidationMessage('Please enter both start and end dates');
                    return false;
                }
                url += '&start_date=' + startDate + '&end_date=' + endDate;
                window.location.href = url;
            }
        });
    } else {
        window.location.href = url;
    }
}

// SWAL FOR LOGOUT
function confirmLogout() {
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to logout!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, logout!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '../php/logout.php'; // Redirect to logout
        }
    });
}

function displayAll() {
    window.location.href = 'analytics.php?duration=all';
}

function fetchDataPerYear() {
    var selectedYear = document.getElementById('appointments-data').value;
    var url = 'analytics.php?year=' + selectedYear;

    // Fetch data via AJAX
    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Update charts with new data
            updateCharts(data);
        })
        .catch(error => console.error('Error fetching data:', error));
}
// ---------- CHARTS ----------

// Bar chart for total data of clients, employees, approved, and declined
document.addEventListener('DOMContentLoaded', function() {
    console.log(totalClient, totalEmployee, totalApproved, totalDeclined); 
    const optionsClients = {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: true,
                tools: {
                    download: false // Disable the apexcharts-menu-icon
                }
            }
        },
        series: [{
            name: 'Total',
            data: [totalClient, totalEmployee, totalApproved, totalDeclined]
        }],
        xaxis: {
            categories: ['Clients', 'Employees', 'Approved', 'Declined'],
            labels: {
                style: {
                    colors: '#FFFFFF' 
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#FFFFFF' 
                }
            }
        },
        dataLabels: {
            style: {
                colors: ['#FFFFFF'] 
            }
        }
    };
  
    const chartClients = new ApexCharts(document.querySelector("#bar-chart"), optionsClients);
    chartClients.render();
});

// Pie chart for total data of clients, employees, admin
document.addEventListener('DOMContentLoaded', function() {
    console.log(totalClient, totalEmployee, totalAdmin); 

    // Function to get legend options based on screen width
    function getLegendOptions() {
        if (window.innerWidth <= 768) {
            return {
                position: 'bottom', // Position the legend at the bottom
                horizontalAlign: 'center', // Align the legend horizontally in the center
                labels: {
                    colors: '#FFFFFF'
                }
            };
        } else {
            return {
                position: 'right', // Default position for larger screens
                horizontalAlign: 'center', // Align the legend horizontally in the center
                labels: {
                    colors: '#FFFFFF'
                }
            };
        }
    }

    const optionsUsers = {
        chart: {
            type: 'pie',
            height: 270,
            toolbar: {
                show: true,
                tools: {
                    download: false // Disable the apexcharts-menu-icon
                }
            }
        },
        series: [totalClient, totalEmployee, totalAdmin],
        labels: ['Client', 'Employee', 'Admin'],
        dataLabels: {
            style: {
                colors: ['#FFFFFF'] 
            }
        },
        legend: getLegendOptions()
    };
  
    const chartUsers = new ApexCharts(document.querySelector("#pie-chart"), optionsUsers);
    chartUsers.render();

    // Update chart on window resize
    window.addEventListener('resize', function() {
        chartUsers.updateOptions({
            legend: getLegendOptions()
        });
    });
});

// Line chart for total data of appointments
document.addEventListener('DOMContentLoaded', function() {
    console.log(totalProcessing, totalAccepted, totalDeclined, totalApproved, totalNotContinued); 
    const optionsAppointments = {
        chart: {
            type: 'line',
            height: 310,
            toolbar: {
                show: true,
                tools: {
                    download: false // Disable the apexcharts-menu-icon
                }
            }
        },
        series: [{
            name: 'Total',
            data: [totalProcessing, totalAccepted, totalDeclined, totalApproved, totalNotContinued]
        }],
        xaxis: {
            categories: ['Processing', 'Accepted', 'Declined', 'Approved', 'Approve but not Continued'],
            labels: {
                style: {
                    colors: '#FFFFFF' 
                }
            }
        },
        yaxis: {
            tickAmount: 2,
            labels: {
                style: {
                    colors: '#FFFFFF' 
                }
            }
        },
        dataLabels: {
            style: {
                colors: ['#FFFFFF'] 
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val;
                }
            }
        }
    };
  
    const chartAppointments = new ApexCharts(document.querySelector("#appointments-chart"), optionsAppointments);
    chartAppointments.render();
});

// Bar chart for monthly processing appointments
document.addEventListener('DOMContentLoaded', function() {

    console.log(totalMonthlyProcessing); 

    // Ensure all months are represented
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const abbreviatedMonths = months.map(month => month.slice(0, 3).toUpperCase());
    const values = months.map(month => totalMonthlyProcessing[month] || 0);

    // Calculate a flexible tickAmount based on the maximum value
    const maxValue = Math.max(...values);
    const tickAmount = Math.ceil(maxValue / 5); 

    const optionsMonthlyProcessing = {
        chart: {
            type: 'bar',
            height: 270,
            toolbar: {
                show: true,
                tools: {
                    download: false // Disable the apexcharts-menu-icon
                }
            }
        },
        series: [{
            name: 'Total',
            data: values
        }],
        xaxis: {
            categories: abbreviatedMonths,
            labels: {
                style: {
                    colors: '#FFFFFF' 
                }
            }
        },
        yaxis: {
            tickAmount: tickAmount,
            labels: {
                style: {
                    colors: '#FFFFFF' 
                }
            }
        }
    };

    const chartMonthlyProcessing = new ApexCharts(document.querySelector("#appointment-processing-chart-monthly"), optionsMonthlyProcessing);
    chartMonthlyProcessing.render();
});

// Bar chart for monthly accepted appointments
document.addEventListener('DOMContentLoaded', function() {

    console.log(totalMonthlyAccepted); 

    // Ensure all months are represented
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const abbreviatedMonths = months.map(month => month.slice(0, 3).toUpperCase());
    const values = months.map(month => totalMonthlyAccepted[month] || 0);

    // Calculate a flexible tickAmount based on the maximum value
    const maxValue = Math.max(...values);
    const tickAmount = Math.ceil(maxValue / 5); 

    const optionsMonthlyAccepted = {
        chart: {
            type: 'bar',
            height: 270,
            toolbar: {
                show: true,
                tools: {
                    download: false // Disable the apexcharts-menu-icon
                }
            }
        },
        series: [{
            name: 'Total',
            data: values
        }],
        xaxis: {
            categories: abbreviatedMonths,
            labels: {
                style: {
                    colors: '#FFFFFF' 
                }
            }
        },
        yaxis: {
            tickAmount: tickAmount,
            labels: {
                style: {
                    colors: '#FFFFFF' 
                }
            }
        }
    };

    const chartMonthlyAccepted = new ApexCharts(document.querySelector("#appointment-accepted-chart-monthly"), optionsMonthlyAccepted);
    chartMonthlyAccepted.render();
});

// Bar chart for monthly approved appointments
document.addEventListener('DOMContentLoaded', function() {

    console.log(totalMonthlyApprovedWithPayment); 

    // Ensure all months are represented
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const abbreviatedMonths = months.map(month => month.slice(0, 3).toUpperCase());
    const values = months.map(month => totalMonthlyApprovedWithPayment[month] || 0);

    // Calculate a flexible tickAmount based on the maximum value
    const maxValue = Math.max(...values);
    const tickAmount = Math.ceil(maxValue / 5); 

    const optionsMonthlyApprovedWithPayment = {
        chart: {
            type: 'bar',
            height: 230,
            toolbar: {
                show: true,
                tools: {
                    download: false // Disable the apexcharts-menu-icon
                }
            }
        },
        series: [{
            name: 'Total',
            data: values
        }],
        xaxis: {
            categories: abbreviatedMonths,
            labels: {
                style: {
                    colors: '#FFFFFF' 
                }
            }
        },
        yaxis: {
            tickAmount: tickAmount,
            labels: {
                style: {
                    colors: '#FFFFFF' 
                }
            }
        }
    };

    const chartMonthlyApprovedWithPayment = new ApexCharts(document.querySelector("#appointment-paid-chart-monthly"), optionsMonthlyApprovedWithPayment);
    chartMonthlyApprovedWithPayment.render();
});

// Bar chart for monthly approved appointments
document.addEventListener('DOMContentLoaded', function() {

    console.log(totalMonthlyNotAcceptedClient); 

    // Ensure all months are represented
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const abbreviatedMonths = months.map(month => month.slice(0, 3).toUpperCase());
    const values = months.map(month => totalMonthlyNotAcceptedClient[month] || 0);

    // Calculate a flexible tickAmount based on the maximum value
    const maxValue = Math.max(...values);
    const tickAmount = Math.ceil(maxValue / 5); 

    const optionsMonthlyNotAcceptedClient = {
        chart: {
            type: 'bar',
            height: 230,
            toolbar: {
                show: true,
                tools: {
                    download: false // Disable the apexcharts-menu-icon
                }
            }
        },
        series: [{
            name: 'Total',
            data: values
        }],
        xaxis: {
            categories: abbreviatedMonths,
            labels: {
                style: {
                    colors: '#FFFFFF' 
                }
            }
        },
        yaxis: {
            tickAmount: tickAmount,
            labels: {
                style: {
                    colors: '#FFFFFF' 
                }
            }
        }
    };

    const chartMonthlyNotAcceptedClient = new ApexCharts(document.querySelector("#appointment-notaccepted-chart-monthly"), optionsMonthlyNotAcceptedClient);
    chartMonthlyNotAcceptedClient.render();
});
