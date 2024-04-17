<div>
    <div id="myDiv" class="container mt-2">
        <div id="cards-row" class="row cards-row" data-sticky-container>
            <div class="row">
                <div class="col-12">
                    <div class="rule-conatiner my-1 mt-4">
                        <div class="rule">
                            <ul>
                                <li>Help center</li>
                                <li>How to play</li>
                                <li>Scoring chart</li>
                            </ul>
                        </div>

                        <div class="">
                            <div class="row">
                                <div class="col-sm-11 col-md-11">
                                    <div class="search-container">
                                        <input type="search" name="search" id="search" x-ref="searchField"
                                            placeholder='Search' autocomplete="off"
                                            wire:model.live.debounce.500ms="search" />
                                        <button type="submit" class=""><img
                                                src="{{ asset('assets/templates/basic/images/icons/magnifying-glass-16.png') }}"
                                                alt="@lang('icon')"></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cards-container">
                <div class="row">
                    @if (session()->has('message'))
                        <div class="alert alert-danger">
                            {{ session('message') }}
                        </div>
                    @endif


                    @if ($favorites && count($favorites) > 0)
                        @foreach ($favorites as $key => $games)
                            @if (strpos($key, 'type_2') !== false)
                                @include('livewire.league.favorite_std')
                            @else
                                @foreach ($games as $game)
                                    @include('livewire.league.favorite_h2h')
                                @endforeach
                            @endif
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="alert alert-info">
                                You haven't added any favorites yet. To add a favorite, simply click on the star icon in
                                the right-hand corner of a player. Your favorites will appear here for quick and easy
                                access.
                            </div>
                        </div>
                    @endif


                </div>

            </div>
        </div>
    </div>
</div>
