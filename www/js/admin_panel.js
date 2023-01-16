
const chartShowedDate = new Date();
let currentMonth = chartShowedDate.getMonth();
let currentYear = chartShowedDate.getFullYear();

const monthsArray = ["styczeń", "luty", "marzec", "kwiecień", "maj", "czerwiec", "lipiec", "sierpień", "wrzesień", "październik", "listopad", "grudzień"];

$(document).ready(() => {

    reloadChart(currentMonth, currentYear);

    reloadYearChart(currentYear);

    const buttonLeft = document.querySelector(".adminpanel__activityStatsLeft");
    const buttonRight = document.querySelector(".adminpanel__activityStatsRight");

    $(buttonLeft).on("click", () => {
        
        currentMonth -= 1;
        if(currentMonth < 0) {
            currentMonth = 11;
            currentYear -= 1;
            reloadYearChart(currentYear);
        }

        reloadChart(currentMonth, currentYear);

    });

    $(buttonRight).on("click", () => {
        
        currentMonth += 1;
        if(currentMonth > 11) {
            currentMonth = 0;
            currentYear += 1;
            reloadYearChart(currentYear);
        }

        reloadChart(currentMonth, currentYear);

    });

});

function reloadChart(month, year) {

    data = {
        "fetchMonthlyActivity": true,
        "month": month + 1,
        "year": year
    };

    $.ajax({
        type: "POST",
        url: "/site_statistics.php",
        data: data,
        success: (msg) => {
            
            const messageText = new String(msg);

            const chartCanvas = document.querySelector(".adminpanel__activityStatsCanvas");

            let xValues = [];
            let yValues = [];

            messageText.split("\n").forEach((chunk) => {
                const dayCount = chunk.split(" ");

                if(dayCount.length == 2) {
                    xValues.push(dayCount[0]);
                    yValues.push(dayCount[1]);
                }
            });

            // handle empty result
            if(xValues.length == 0 && yValues.length == 0) {
                chartCanvas.innerHTML = '<h3 class="adminpanel__emptyActivityHeading">Brak aktywności w podanym miesiącu</h3>';
                chartCanvas.style.height = "400px";
                return;
            }


            const mTr = 201;
            const mTg = 181;
            const mTb = 181;

            const r = 201 - 121;
            const g = 181 - 99;
            const b = 181 - 99;

            let barColors = [];

            const maxValue = Math.max(...yValues);

            yValues.forEach((count) => {
                const f = count / maxValue;
                barColors.push('rgb(' + (mTr - f*r)  + "," + (mTg - f*g)  + "," + (mTb - f*b)  + ")");
            });

            
            chartCanvas.innerHTML = "<canvas style=\"width: 100%; height: 400px\"></canvas>";

            const chartBanner = document.querySelector(".adminpanel__activityStatsBannerContent");
            chartBanner.innerHTML = "Aktywność strony na " + monthsArray[month] + " " + year;

            const chart = new Chart(chartCanvas.querySelector("canvas"), {
                type: "bar",
                data: {
                    labels: xValues,
                    datasets: [{
                        label: "Ilość postów utworzonych lub zmodyfikowanych",
                        backgroundColor: barColors,
                        data: yValues,
                        barPercentage: 1.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                              }
                        }
                    }
                }
            });
        }
      });

}

function reloadYearChart(year) {

    data = {
        "fetchYearlyActivity": true,
        "year": year
    };

    $.ajax({
        type: "POST",
        url: "/site_statistics.php",
        data: data,
        success: (msg) => {
            
            const messageText = new String(msg);

            let xValues = [];
            let yValues = [];

            messageText.split("\n").forEach((chunk) => {
                const weekCount = chunk.split(" ");

                if(weekCount.length == 2) {
                    xValues.push(weekCount[0]);
                    yValues.push(weekCount[1]);
                }
            });

            const chartCanvas = document.querySelector(".adminpanel__yearStatsCanvas");
            chartCanvas.innerHTML = "<canvas style=\"width: 100%; height: 400px\"></canvas>";

            const chartBanner = document.querySelector(".adminpanel__yearStatsBannerContent");
            chartBanner.innerHTML = "Aktywność strony na rok " + year;

            const chart = new Chart(chartCanvas.querySelector("canvas"), {
                type: "line",
                data: {
                    labels: xValues,
                    datasets: [{
                        label: "Ilość postów utworzonych lub zmodyfikowanych",
                        data: yValues,
                        borderColor: '#b39191',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                              }
                        }
                    },
                    elements: {
                        point: {
                          pointRadius: 0
                        }
                      }
                }
            });


        }
      });

    data = {
        fetchYearlyNumbers: true,
        year: year
    };

    $.ajax({
        type: "POST",
        url: "/site_statistics.php",
        data: data,
        success: (msg) => {

            const messageString = new String(msg);
            const counts = messageString.split("\n");

            if(counts.length < 4) {
                return;
            }

            // collecting stats
            const nPosts = counts[0];
            const nComments = counts[1];
            const nUsers = counts[2];
            const nReports = counts[3];

            // stats containers
            const postsItem = document.querySelector(".adminpanel__yearStatsPosts span");
            const commentsItem = document.querySelector(".adminpanel__yearStatsComments span");
            const usersItem = document.querySelector(".adminpanel__yearStatsUsers span");
            const reportsItem = document.querySelector(".adminpanel__yearStatsReports span");

            // assigning 
            postsItem.innerHTML = nPosts;
            commentsItem.innerHTML = nComments;
            usersItem.innerHTML = nUsers;
            reportsItem.innerHTML = nReports;
            
        }
    });

}