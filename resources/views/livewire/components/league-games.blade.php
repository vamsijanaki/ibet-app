 @php
     $userFavorites = getUserFavorites(auth()->id());
 @endphp

 <div class="container">
     <div class="row">
         <hr style="color:grey; margin-top: 20px">
         <div class="col-12">
             <div class="rule-conatiner my-1">
                 <div class="rule">
                     <ul>
                         <li>Help center</li>
                         <li>How to play</li>
                         <li>Scoring chart</li>
                     </ul>
                 </div>
                 <div class="">
                     <div class="row">
                         <div class="col-sm-12 col-md-12">
                             <div class="search-container">
                                 <input type="search" name="search" id="search" x-ref="searchField"
                                     placeholder="Search" autocomplete="off" wire:model.live.debounce.500ms="search">
                                 <button type="submit" class=""><img
                                         src="https://staging.ibetnetworks.com/assets/templates/basic/images/icons/magnifying-glass-16.png"
                                         alt="icon">
                                 </button>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <div class="cards-container" wire:loading.remove>
         <div class="row">

             @foreach ($games as $game)
                 @includeIf('livewire.cards.game-card-type-' . $filters['gameType'], ['game' => $game])
             @endforeach

             @if ($games->isEmpty())
                 <div class="col-12">
                     <div class="no-games">
                         <img src="{{ asset('assets/templates/basic/images/cards.webp') }}" alt="No games">
                         <p class="h4">No projections found for this league. Please try another league.</p>
                     </div>
                 </div>
             @endif

         </div>
     </div>

     <div class="cards-skeleton" wire:loading.block>
         @include('livewire.components.league-games-loading-v2')
     </div>
 </div>
