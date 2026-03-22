// data
const consultants = ["Dr. Aisha Khan", "Dr. Wei Zhang", "Dr. Carlos Gonzales", "Dr. John Doe", "Dr. Emily Davis", "Dr. Suresh Patel", 
                    "Dr. Fatima Al-Amin", "Dr. Laura White", "Dr. Rohan Mehta", "Dr. Nguyen Tran", "Dr. Mia Scott", "Dr. Benjamin Harris",
                    "Dr. Akira Yamamoto", "Dr. James Clark", "Dr. Fiona Baker", "Dr. Maria Santos", "Dr. Amara Okafor", "Dr. Daniel Walker",
                    "Dr. Grace Hall", "Dr. Ethan Taylor", "Dr. William Allen", "Dr. Priya Sharma", "Dr. Abdullah Youssef", "Dr. Isabella King",
                    "Dr. Jack Wright"];
const consultant_reviews = [2.6, 4.8, 4.0, 2.5, 2.5, 5.0, 4.0, 3.5, 3.0, 2.3, 2.0, 3.3, 4.0, 2.9, 3.9, 2.8, 3.1, 3.2, 2.1, 3.3, 4.4, 3.3, 2.2,
                            4.0, 1.8, 3.3, 3.1, 2.8, 4.2, 2.7, 2.7, 4.4];

// Create the chart
const ctx =  document.getElementById('myChart');
const myChart = new Chart(ctx, {
    type: "bar",
    data: {
        labels: consultants,
        datasets: [{
            label: 'Consultant Reviews',
            backgroundColor: 'rgba(173, 216, 230, 0.8)', 
            borderColor: 'rgba(173, 216, 230, 1)', 
            borderWidth: 1, 
            data: consultant_reviews
        }]
    },
    options: {
        datasets: {
            bar: {
                borderRadius: 5,
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                grid: {
                    display: false,
                }
            }
        }
    }
});