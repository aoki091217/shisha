import Chart from "chart.js/auto";

const SALSE_STEP = 5000;
const CUSTOMER_STEP = 5;

$(window).on('load', function () {
    let salesData = createData(window.sales);
    let customersData = createData(window.customers);

    createSalesChart(salesData);
    createCustomersChart(customersData);
});

function createSalesChart(salesData)
{
    let labels = createMonthLabels();

    let digit = 10000;
    let max = getMaxValue(salesData, digit);

    new Chart($('#salesLineChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
            {
                label: '前年',
                data: salesData.prev,
                backgroundColor: '#0d6efd',
                borderColor: '#0d6efd'
            },
            {
                label: '今年',
                data: salesData.this,
                backgroundColor: '#F25B69',
                borderColor: '#F25B69'
            }
            ],
        },
        options: {
            scales: {
                x: {
                    grid: {
                        display: false,
                        zeroLineColor: '#6c757d',
                        borderColor: '#6c757d',
                    },
                    scaleLabel: {
                        display: true,
                    }
                },
                y: {
                    grid: {
                        display: false,
                        zeroLineColor: '#6c757d',
                        borderColor: '#6c757d',
                    },
                    max: max,
                    min: 0,
                    ticks: {
                        fontSize: 14,
                        stepSize: SALSE_STEP
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            }
        }
    });
}

function createCustomersChart(customersData)
{
    let labels = createMonthLabels();

    let digit = 10;
    let max = getMaxValue(customersData, digit);

    new Chart($('#customersLineChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: '前年',
                data: customersData.prev,
                backgroundColor: '#0d6efd',
                borderColor: '#0d6efd'
            },{
                label: '今年',
                data: customersData.this,
                backgroundColor: '#F25B69',
                borderColor: '#F25B69'
            }],
        },
        options: {
            scales: {
                x: {
                    grid: {
                        display: false,
                        zeroLineColor: '#6c757d',
                        borderColor: '#6c757d',
                    },
                    scaleLabel: {
                        display: true,
                    }
                },
                y: {
                    grid: {
                        display: false,
                        zeroLineColor: '#6c757d',
                        borderColor: '#6c757d',
                    },
                    max: max,
                    min: 0,
                    ticks: {
                        fontSize: 14,
                        stepSize: CUSTOMER_STEP
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            }
        }
    });
}

function createMonthLabels()
{
    let months = [];
    for (let i = 0; i < 12; i++) {
        let month = i;
        months[i] = `${month + 1}月`;
    }

    return months;
}

function createData(aggregation)
{
    let data = {};
    let prevMonth = [];
    let thisMonth = [];
    $.each(aggregation.month, function (i, item) {
        let nowMonth = new Date().getMonth() + 1;

        if (i + 1 <= nowMonth) {
            prevMonth[i] = item.prev;
            thisMonth[i] = item.this;
        } else {
            prevMonth[i] = item.prev;
        }

        data = {
            prev: prevMonth,
            this: thisMonth
        }
    });

    return data;
}

function getMaxValue(aggregation, digit)
{
    let prevMax = Math.max.apply(null, aggregation.prev);
    let thisMax = Math.max.apply(null, aggregation.this);

    if (prevMax > thisMax) {
        return Math.ceil(prevMax / digit) * digit;
    } else {
        return Math.ceil(thisMax / digit) * digit;
    }
}
