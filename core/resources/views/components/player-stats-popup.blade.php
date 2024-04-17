<div class="ib_player_stats">
<a class="arrow arrow-left ib_stat_navigation" data-action="ib_stat_navigation" data-trigger="previous" data-game-id="{{$game_id}}"><i class="fa-solid fa-chevron-left"></i></a>
  <div class="row ib_player_stat_wrap" data-index="1">
    <div class="col-12 col-md-6">
      <div class="player-card">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-4">
              <img src="{{ $image }}" alt="Player Image" class="player-image" />
            </div>
            <div class="col-8">
              <h5 class="player_name">{{ $name }}</h5>
              <p class="player_details">{{$position}} <br>{{$date}}, vs {{$versus}}</p>
            </div>
          </div>
          <div class="chart-container mt-3">
            <canvas id="statChart"></canvas>
        </div>
          <div class="row mt-3">
            <div class="col-12">
              <div class="average-box">
                Avg. Last {{$total_logs}}<br>
                {{$stat['average']}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="stat-card">
        <div class="card-body">
          <div class="stat-box mb-3">
          <p class="stat-value">{{$stat['value']}}</p>
          <p class="stat-name">{{$stat['name']}}</p>
          </div>
          <div class="table-container">
                <table class="ib_table_stats">
                    <thead>
                        <tr>
                            <th scope="col">Day</th>
                            <th scope="col">Opp</th>
                            <th scope="col">{{$stat['name']}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        @php
                        // If $log is is_dnp, set $log['value'] to 'DNP'
                        if($log['is_dnp'] && $log['value'] == 0) {
                            $log['value'] = 'DNP';
                        }
                        @endphp
                        <tr>
                            <td>{{ $log['date'] }}</td>
                            <td>{{ $log['opp'] }}</td>
                            <td>{{ $log['value'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>
</div>
<a class="arrow arrow-right ib_stat_navigation" data-action="ib_stat_navigation" data-trigger="next" data-game-id="{{$game_id}}"><i class="fa-solid fa-chevron-right"></i></a>
</div>