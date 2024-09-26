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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="container mx-auto px-4 py-8">
        <!-- Main Header -->
        <h1 class="text-4xl font-bold text-center mb-8">Top 100 Critics Results</h1>
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
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center">
                <label for="decadeSelect" class="mr-2">Filter:</label>
                <select id="decadeSelect" class="border p-2">
                    <option value="all">1920-2000</option>
                    <option value="1920">1920</option>
                    <option value="1930">1930</option>
                    <option value="1940">1940</option>
                    <option value="1950">1950</option>
                    <option value="1960">1960</option>
                    <option value="1970">1970</option>
                    <option value="1980">1980</option>
                    <option value="1990">1990</option>
                    <option value="2000">2000</option>
                </select>
                @php
                    // Get all unique country names from the films collection
                    $uniqueCountries = $films->pluck('country_name')->unique()->sort();
                @endphp
                <select id="countrySelect" class="border p-2 ml-2">
                    <option value="all">All countries</option>
                    @foreach($uniqueCountries as $country)
                        <option value="{{ $country }}">{{ $country }}</option>
                    @endforeach
                </select>
            </div>
            <button class="filter-button border p-2" data-filter="all">Reset</button>
        </div>
        <section class="mb-8">
            <div class="grid grid-cols-10 gap-2">
                @foreach($films as $film)
                <div class="w-full film-container" data-director="{{$film->director_votes}}" data-critics="{{$film->critic_votes}}" data-dec="{{$film->decade}}" data-country="{{$film->country_name}}" data-director="{{$film->director_name}}">
                    <div class="image-container">

                    <img src="https://core-cms.bfi.org.uk/sites/default/files/styles/responsive/public{{$film->image_main}}" loading="lazy" class="w-full h-auto  cursor-pointer modal-trigger" data-film-id="{{$film->id}}" />
                    </div>
                    <!-- Hidden div containing film info -->
                    <div id="modal-{{$film->id}}" class="hidden fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.7);">
                        <div class="bg-white p-6 rounded-lg w-3/4 max-w-lg relative">
                            <button class="absolute top-2 right-2 text-gray-500 close-modal">✖</button>
                            <div style="display: flex">
                            <img src="https://core-cms.bfi.org.uk/sites/default/files/styles/responsive/public{{$film->image_main}}" loading="lazy" style="max-width: 200px; margin-right: 10px; min-width: 50%; margin-bottom: 10px;" />
                            <div>
                            <h2 class="text-2xl font-semibold mb-2">{{$film->name}}</h2>
                            <p><strong>Director:</strong> {{$film->director_name}}</p>
                            <p><strong>Country:</strong> {{$film->country_name}}</p>
                            <p><strong>Year:</strong> {{$film->year}}</p>
                            <p><strong>Ranked:</strong> {{ $loop->iteration }}</p>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
    </div>
    <script>
$(document).ready(function() {
    // Function to filter films based on selected criteria
    function filterFilms() {
        var selectedDecade = $('#decadeSelect').val();
        var selectedCountry = $('#countrySelect').val();

        // Iterate over each film item
        $('.film-container').each(function() {
            var decade = $(this).data('dec'); // Get the decade data attribute
            var country = $(this).data('country'); // Get the country data attribute

            // Determine if the film matches the selected filters
            var matchesDecade = (selectedDecade === 'all' || selectedDecade == decade);
            var matchesCountry = (selectedCountry === 'all' || selectedCountry == country);

            // Show or fade out films based on filter matches
            if (matchesDecade && matchesCountry) {
                $(this).find('.image-container').css('opacity', 1); // Show matching films 
            } else {
                $(this).find('.image-container').css('opacity', 0.3); // Fade out non-matching films
            }
        });
    }

    // Event listeners for filter changes
    $('#decadeSelect').on('change', filterFilms);
    $('#countrySelect').on('change', filterFilms);

    // Reset button to show all films
    $('.filter-button').on('click', function() {
        $('#decadeSelect').val('all');
        $('#countrySelect').val('all');
        filterFilms(); // Reset filter
    });

    // Modal functionality

    // Event for showing modal when image is clicked
    $(document).on('click', '.modal-trigger', function() {
        var filmId = $(this).data('film-id'); // Get the film ID from the data attribute
        $('#modal-' + filmId).removeClass('hidden'); // Show the corresponding modal
    });

    // Event for closing the modal when close button is clicked
    $(document).on('click', '.close-modal', function() {
        $(this).closest('.fixed').addClass('hidden'); // Hide the modal
    });

    // Close modal when clicking outside of modal content
    $(document).on('click', '.fixed', function(e) {
        if ($(e.target).is('.fixed')) {
            $(this).addClass('hidden'); 
        }
    });
});

        </script>
</body>
</html>