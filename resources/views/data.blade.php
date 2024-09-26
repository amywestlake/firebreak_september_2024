<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Firebreak: Sight and Sound Critics Results</title>
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="container mx-auto px-4 py-8">
        <!-- Main Header -->
        <h1 class="text-4xl font-bold text-center mb-8">2022 Sight and Sound Critics Results</h1>
        <nav class="bg-gray-200 p-4 rounded-md mb-8">
            <ul class="flex space-x-4 justify-center">
                <li>
                    <a href="/firebreak_week_poll_data" class="text-gray-800 hover:text-gray-600 font-semibold active">Film Data</a>
                </li>
                <li>
                    <a href="/firebreak_week_poll_data/display" class="text-gray-800 hover:text-gray-600 font-semibold active">Film Display</a>
                </li>
                <li>
                    <a href="/firebreak_week_poll_data/directors" class="text-gray-800 hover:text-gray-600 font-semibold">Directors</a>
                </li>
                <li>
                    <a href="/firebreak_week_poll_data/actors" class="text-gray-800 hover:text-gray-600 font-semibold">Actors</a>
                </li>
            </ul>
        </nav>
        <section class="mb-8">
            <div id="decadeChart" class="w-full h-96 bg-white shadow-md rounded-md"></div>
        </section>

        <section class="mb-8">
            <div id="mapChart" class="w-full h-96 bg-white shadow-md rounded-md"></div>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-4">Top 10 films by votes</h2>
            <div class="table_component" role="region" tabindex="0" class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300"id="myTable">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-6 text-left text-gray-700 font-semibold uppercase text-sm border-b">Film</th>
                            <th class="py-3 px-6 text-left text-gray-700 font-semibold uppercase text-sm border-b">Total critics votes</th>
                            <th class="py-3 px-6 text-left text-gray-700 font-semibold uppercase text-sm border-b">Critics Vote Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($filmsByVote->slice(0, 10) as $film)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6 border-b">{{$film->name}}</td>
                            <td class="py-4 px-6 border-b">{{$film->critic_votes}}</td>
                            <td class="py-4 px-6 border-b">{{ number_format($film->critic_votes / $totalCriticVotes * 100, 2) }}%</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mb-8">
            <div id="scatterChart" class="w-full h-96 bg-white shadow-md rounded-md"></div>
        </section>

       
    </div>
    <script src="https://code.highcharts.com/maps/highmaps.js"></script>
    <script src="https://code.highcharts.com/maps/modules/map.js"></script>
    <script src="https://code.highcharts.com/maps/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/maps/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/maps/modules/accessibility.js"></script>
    
    <script>
        // Films by country chart
        var filmsByCountry = {!! json_encode($filmsByCountry) !!};

        // Function to transform filmsByCountry data into Highcharts format
        function transformData(data) {
            return data.map(function (item) {
                return {
                    code: item.name,            // Country name
                    value: item.total_films     // Number of films
                };
            });
        }

        (async () => {
            // Fetch the world map topology data
            const topology = await fetch(
                'https://code.highcharts.com/mapdata/custom/world.topo.json'
            ).then(response => response.json());

            // Transform the films data for Highcharts
            const transformedData = transformData(filmsByCountry);

            Highcharts.mapChart('mapChart', {
                chart: {
                    map: topology
                },

                title: {
                    text: 'Number of films per country',
                    align: 'left'
                },

                mapNavigation: {
                    enabled: true,
                    buttonOptions: {
                        verticalAlign: 'bottom'
                    }
                },

                colorAxis: {
                    min: 0,
                    minColor: '#FFFFFF',
                    maxColor: Highcharts.getOptions().colors[0] 
                },

                tooltip: {
                    headerFormat: '',
                    pointFormat: '<b>{point.name}</b>: {point.value} films'
                },

                series: [{
                    name: 'Number of films',
                    joinBy: ['name', 'code'], 
                    data: transformedData,
                    dataLabels: {
                        enabled: true,
                        format: '{point.value:.0f}'
                    }
                }]
            });

        })();

        // Films by decade chart
        var decadesData = {!! json_encode($filmsByDecade) !!};

        // Extracting the decades and the corresponding total films
        var categories = decadesData.map(function(item) {
            return item.decade; 
        });

        // Get the total films for that decade
        var data = decadesData.map(function(item) {
            return item.total_films; 
        });

        // Creating the Highcharts bar chart
        Highcharts.chart('decadeChart', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Number of films by decade',
                align: 'left'
            },
            xAxis: {
                categories: categories,
                title: {
                    text: null
                },
                gridLineWidth: 1,
                lineWidth: 0
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Number of Films',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                },
                gridLineWidth: 0
            },
            tooltip: {
                valueSuffix: ' films' 
            },
            plotOptions: {
                bar: {
                    borderRadius: '50%',
                    dataLabels: {
                        enabled: true
                    },
                    groupPadding: 0.1
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Films', 
                data: data 
            }]
        });

        // Films by vote chart
        var filmsByVote = {!! json_encode($filmsByVote) !!};

        // Transforming data for Highcharts scatter plot
        var criticData = filmsByVote.map(function(film) {
            return {
                x: film.critic_votes,
                y: film.director_votes,
                name: film.name,
                marker: { fillColor: 'rgba(119, 152, 191, 1)' } 
            };
        });


        Highcharts.chart('scatterChart', {
            chart: {
                type: 'scatter',
                zoomType: 'xy'
            },
            title: {
                text: 'Film Votes Critics & Directors'
            },
            xAxis: {
                title: {
                    text: 'Critic Votes'
                },
                gridLineWidth: 1
            },
            yAxis: {
                title: {
                    text: 'Director Votes'
                }
            },
            tooltip: {
                formatter: function() {
                    return `<b>${this.point.name}</b><br>Critic Votes: ${this.x}<br>Director Votes: ${this.y}`;
                }
            },
            series: [{
                name: 'Films',
                data: criticData
            }]
        });
    </script>
   

</body>
</html>
