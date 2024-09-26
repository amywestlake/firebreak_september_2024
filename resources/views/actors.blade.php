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
    <style>
        .tablesorter-default .header,
        .tablesorter-default .tablesorter-header {
            background-image: url(data:image/gif;base64,R0lGODlhFQAJAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAkAAAIXjI+AywnaYnhUMoqt3gZXPmVg94yJVQAAOw==);
            background-position: center right;
            background-repeat: no-repeat;
            cursor: pointer;
            white-space: normal;
        }
        .tablesorter-default thead .headerSortUp,
        .tablesorter-default thead .tablesorter-headerAsc,
        .tablesorter-default thead .tablesorter-headerSortUp {
            background-image: url(data:image/gif;base64,R0lGODlhFQAEAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAQAAAINjI8Bya2wnINUMopZAQA7);
            border-bottom: #000 2px solid;
        }
        .tablesorter-default thead .headerSortDown,
        .tablesorter-default thead .tablesorter-headerDesc,
        .tablesorter-default thead .tablesorter-headerSortDown {
            background-image: url(data:image/gif;base64,R0lGODlhFQAEAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAQAAAINjB+gC+jP2ptn0WskLQA7);
            border-bottom: #000 2px solid;
        }
    </style>
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
            <h2 class="text-2xl font-semibold mb-4">Actors</h2>
            <div class="table_component" role="region" tabindex="0" class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300"id="myTable">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-6 text-left text-gray-700 font-semibold uppercase text-sm border-b">Actor</th>
                            <th class="py-3 px-6 text-left text-gray-700 font-semibold uppercase text-sm border-b">Films</th>
                            <th class="py-3 px-6 text-left text-gray-700 font-semibold uppercase text-sm border-b">Critics votes</th>
                        </tr>
                    </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($actorsWithFilmCount as $actor)
                @if($actor->total_films > 1)
                    <tr class="hover:bg-gray-50">
                        <td class="py-4 px-6 border-b">{{$actor->name}}</td>
                        <td class="py-4 px-6 border-b"><strong>{{$actor->total_films}}</strong>: {{$actor->film_titles}}</td>
                        <td class="py-4 px-6 border-b">{{ number_format($actor->total_critic_votes / $totalCriticVotes * 100, 2) }}%<br/>({{$actor->total_critic_votes}})</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
        </section>

    </div>
   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.32.0/js/jquery.tablesorter.min.js" integrity="sha512-O/JP2r8BG27p5NOtVhwqsSokAwEP5RwYgvEzU9G6AfNjLYqyt2QT8jqU1XrXCiezS50Qp1i3ZtCQWkHZIRulGA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>

jQuery(function() {
$("#myTable").tablesorter();
});
        </script>
</body>
</html>
