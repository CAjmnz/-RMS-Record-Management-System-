(function () {

    $(document).ready(function () {

        // ─────────────────────────────────────────────
        // LOGS DATATABLE
        // ─────────────────────────────────────────────
        $('#logsTable').DataTable({
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50],
            order: [],
            columnDefs: [{ orderable: false, targets: 0 }],
            language: {
                search: 'Search:',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty: 'Showing 0 to 0 of 0 entries',
                infoFiltered: '(filtered from _MAX_ total)',
                paginate: {
                    first: 'First',
                    last: 'Last',
                    next: '&raquo;',
                    previous: '&laquo;'
                }
            }
        });


        // ─────────────────────────────────────────────
        // CHART HELPERS
        // ─────────────────────────────────────────────
        function createDonutChart(id, labels, data, colors) {
            const el = document.getElementById(id);
            if (!el) return;

            new Chart(el.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 12, font: { size: 11 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const total = context.dataset.data
                                        .reduce((a, b) => a + b, 0);

                                    const pct = total > 0
                                        ? Math.round((context.parsed / total) * 100)
                                        : 0;

                                    return ` ${context.label}: ${context.parsed} (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function createBarChart(id, labels, data) {
            const el = document.getElementById(id);
            if (!el) return;

            new Chart(el.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Logs',
                        data: data,
                        backgroundColor: 'rgba(99, 102, 241, 0.7)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }


        // ─────────────────────────────────────────────
        // STATUS DONUT
        // ─────────────────────────────────────────────
        createDonutChart(
            'statusDonutChart',
            ['Active', 'Inactive'],
            [27, 1],
            ['#16c784', '#f87171']
        );


        // ─────────────────────────────────────────────
        // ROLE DONUT
        // ─────────────────────────────────────────────
        createDonutChart(
            'roleDonutChart',
            ['Admins', 'Regular'],
            [10, 18],
            ['#6366f1', '#f59e0b']
        );


        // ─────────────────────────────────────────────
        // LOGS BAR CHART
        // ─────────────────────────────────────────────
        createBarChart(
            'logsBarChart',
            ["May 26","May 27","May 28","May 29","May 30","May 31","Jun 01"],
            [5,0,0,0,0,0,0]
        );

    });

})();