@extends($activeTemplate . 'layouts.board')
@section('board')
    <div class="m-0 pt-4" style="background:#0c0e2c">
        <div class="ib_league_filters">
            @livewire('components.league-filter')
        </div>
        <div class="ib_league_games">
            @livewire('components.league-games')
        </div>
        <div class="modal fade login-modal" id="statsGraph" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
            aria-labelledby="modalTitleId" aria-hidden="true" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered ib_stats-popup" role="document">
                <div class="modal-content">
                    <div class="modal-body p-3 p-sm-4">
                        <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                            <i class="las la-times"></i>
                        </span>
                        <div class="d-flex flex-column justify-content-center align-items-center mb-2 content w-100">
                            <div class="ib_stats w-100">
                            </div>
                            <div class="ib_spinner">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
