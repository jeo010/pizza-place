<template>
    <div class="grid md:grid-cols-2 gap-4">
        <div class="w-full h-64 md:h-96 my-4">
            <h2>Monthly Order Counts</h2>
            <canvas id="monthlyOrdersChart"></canvas>
        </div>
        <div class="w-full h-64 md:h-96">
            <h2>Daily Order Counts (Last 30 Days)</h2>
            <canvas id="dailyOrdersChart"></canvas>
        </div>
        <div class="w-full h-64 md:h-96">
            <h2>Monthly Pizza Prices & Sizes</h2>
            <canvas id="pizzaStatsChart"></canvas>
        </div>
    </div>
</template>

<script>
    import { Chart, registerables } from "chart.js";
    import axios from "axios";
    import moment from "moment";

    Chart.register(...registerables);

    export default {
        name: "orders",
        data() {
            return {
                orders: [],
                latestDate: null, // Latest date in orders
            };
        },
        computed: {
            ordersPerMonth() {
                const counts = {};
                this.orders.forEach((order) => {
                    if (order.created_at) {
                        const month = order.created_at.slice(0, 7); // Extract YYYY-MM
                        counts[month] = (counts[month] || 0) + 1;
                    }
                

                });
                return counts;
            },
            ordersPerDay() {
                if (!this.latestDate) return { dates: [], counts: {} };

                const counts = {};
                const last30Days = [];

            // Generate last 30 days based on latest order date
                for (let i = 29; i >= 0; i--) {
                    const date = moment(this.latestDate).subtract(i, "days").format("YYYY-MM-DD");
                    last30Days.push(date);
                    counts[date] = 0;
                }

            // Count orders per day
                this.orders.forEach((order) => {
                    const date = order.created_at ? order.created_at.slice(0, 10) : null;
                    if (date && counts.hasOwnProperty(date)) {
                        counts[date] += 1;
                    }
                });

                return { dates: last30Days, counts };
            },
        },
        mounted() {
            this.getOrders();
        },
        methods: {
            getOrders() {
                axios.get("http://pizza-place-api.test/api/orders")
                .then(response => {
                        this.orders = response.data[0];
                       
                        console.log("Orders fetched: ", response.data[0]);
                       
                        
                        this.latestDate = this.getLatestOrderDate(); // Find latest order date
                        this.renderCharts();
                })
                .catch(error => {
                    console.error("Error fetching orders: ", error);
                });
            },
            getLatestOrderDate() {
                return this.orders.length 
                ? this.orders.reduce((max, order) => order.created_at > max ? order.created_at.slice(0, 10) : max, "0000-00-00")
                : null;
            },
            renderCharts() {
                if (!this.latestDate) return;

      // Monthly Orders Chart
                const monthlyCtx = document.getElementById("monthlyOrdersChart").getContext("2d");
                new Chart(monthlyCtx, {
                    type: "line",
                    data: {
                        labels: Object.keys(this.ordersPerMonth),
                        datasets: [{
                            label: "Orders per Month",
                            data: Object.values(this.ordersPerMonth),
                            borderColor: "blue",
                            borderWidth: 2,
                            fill: false,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    },
                });

      // Daily Orders Chart
                const dailyCtx = document.getElementById("dailyOrdersChart").getContext("2d");
                new Chart(dailyCtx, {
                    type: "line",
                    data: {
                        labels: this.ordersPerDay.dates,
                        datasets: [{
                            label: "Orders per Day",
                            data: Object.values(this.ordersPerDay.counts),
                            borderColor: "green",
                            borderWidth: 2,
                            fill: false,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    },
                });

      // Pizza Prices & Sizes Chart

                
                /*const pizzaCtx = document.getElementById("pizzaStatsChart").getContext("2d");
                new Chart(pizzaCtx, {
                    type: "bar",
                    data: {
                        labels: Object.keys(this.pizzaStatsPerMonth.prices),
                        datasets: [
                            {
                                label: "Total Pizza Price per Month",
                                data: Object.values(this.pizzaStatsPerMonth.prices),
                                backgroundColor: "orange",
                            },
                            {
                                label: "Pizza Size Distribution",
                                data: Object.values(this.pizzaStatsPerMonth.sizes).map(sizeData => Object.values(sizeData).reduce((a, b) => a + b, 0)),
                                backgroundColor: "purple",
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { title: { display: true, text: "Month" } },
                            y: { title: { display: true, text: "Total Price / Number of Pizzas" } },
                        },
                    },
                });*/
            }
        }
    };
</script>
