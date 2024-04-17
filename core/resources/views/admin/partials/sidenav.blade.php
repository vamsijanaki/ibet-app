<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a class="sidebar__main-logo" href="{{ route('admin.dashboard') }}"><img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="@lang('image')"></a>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">

                <li class="sidebar-menu-item {{ menuActive('admin.dashboard') }}">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">@lang('Dashboard')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.users*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon las la-users"></i>
                        <span class="menu-title">@lang('Manage Bettors')</span>

                        @if ($bannedUsersCount > 0 || $emailUnverifiedUsersCount > 0 || $mobileUnverifiedUsersCount > 0 || $kycUnverifiedUsersCount > 0 || $kycPendingUsersCount > 0)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.users*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.users.active') }}">
                                <a class="nav-link" href="{{ route('admin.users.active') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Active Bettors')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.users.banned') }}">
                                <a class="nav-link" href="{{ route('admin.users.banned') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Banned Bettors')</span>
                                    @if ($bannedUsersCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $bannedUsersCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.users.email.unverified') }}">
                                <a class="nav-link" href="{{ route('admin.users.email.unverified') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Email Unverified')</span>

                                    @if ($emailUnverifiedUsersCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $emailUnverifiedUsersCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.users.mobile.unverified') }}">
                                <a class="nav-link" href="{{ route('admin.users.mobile.unverified') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Mobile Unverified')</span>
                                    @if ($mobileUnverifiedUsersCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $mobileUnverifiedUsersCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.users.kyc.unverified') }}">
                                <a class="nav-link" href="{{ route('admin.users.kyc.unverified') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('KYC Unverified')</span>
                                    @if ($kycUnverifiedUsersCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $kycUnverifiedUsersCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.users.kyc.pending') }}">
                                <a class="nav-link" href="{{ route('admin.users.kyc.pending') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('KYC Pending')</span>
                                    @if ($kycPendingUsersCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $kycPendingUsersCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.users.with.balance') }}">
                                <a class="nav-link" href="{{ route('admin.users.with.balance') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('With Balance')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.users.all') }}">
                                <a class="nav-link" href="{{ route('admin.users.all') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('All Bettors')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.users.notification.all') }}">
                                <a class="nav-link" href="{{ route('admin.users.notification.all') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Notification to All')</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="sidebar__menu-header">@lang('Bet Setup')</li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive(['admin.category*', 'admin.league*', 'admin.team*'], 3) }}" href="javascript:void(0)">
                        <i class="menu-icon la la-spotify"></i>
                        <span class="menu-title">@lang('Sports Config')</span>
                        @if ($bannedUsersCount > 0 || $emailUnverifiedUsersCount > 0 || $mobileUnverifiedUsersCount > 0 || $kycUnverifiedUsersCount > 0 || $kycPendingUsersCount > 0)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>

                    <div class="sidebar-submenu {{ menuActive(['admin.category*', 'admin.league*', 'admin.team*', 'admin.player*', 'admin.nflteam*', 'admin.schedules*', 'admin.stats*'], 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.category*') }}">
                                <a class="nav-link" href="{{ route('admin.category.index') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Manage Categories')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.league*') }}">
                                <a class="nav-link" href="{{ route('admin.league.index') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Manage Leagues')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item sidebar-dropdown">
                                <a class="{{ menuActive(['admin.stats*'], 3) }}" href="javascript:void(0)">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Manage Stats')</span>
                                </a>
                                <div class="sidebar-submenu {{ menuActive(['admin.stats*'], 2) }}">
                                    <ul>
                                        @foreach(\App\Models\League::where('status', 1)->get() as $league)
                                            <li class="sidebar-menu-item {{ menuActive('admin.stats*', 2) }}">
                                                <a class="nav-link" href="{{ route('admin.stats.index', ['league' => $league->slug]) }}">
                                                    <i class="menu-icon las la-dot-circle"></i>
                                                    <span class="menu-title">{{ $league->name }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>

                            <li class="sidebar-menu-item sidebar-dropdown">
                                <a class="{{ menuActive(['admin.team*'], 3) }}" href="javascript:void(0)">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Manage Teams')</span>
                                </a>
                                <div class="sidebar-submenu {{ menuActive(['admin.team*'], 2) }}">
                                    <ul>
                                        @foreach(\App\Models\League::where('status', 1)->get() as $league)
                                            <li class="sidebar-menu-item {{ menuActive('admin.team*', 2) }}">
                                                <a class="nav-link" href="{{ route('admin.team.index', ['league' => $league->slug]) }}">
                                                    <i class="menu-icon las la-dot-circle"></i>
                                                    <span class="menu-title">{{ $league->name }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>

                            <li class="sidebar-menu-item sidebar-dropdown">
                                <a class="{{ menuActive(['admin.player*'], 3) }}" href="javascript:void(0)">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Manage Players')</span>
                                </a>
                                <div class="sidebar-submenu {{ menuActive(['admin.player*'], 2) }}">
                                    <ul>
                                        @foreach(\App\Models\League::where('status', 1)->get() as $league)
                                            <li class="sidebar-menu-item {{ menuActive('admin.player*', 2) }}">
                                                <a class="nav-link" href="{{ route('admin.player.index', ['league' => $league->slug]) }}">
                                                    <i class="menu-icon las la-dot-circle"></i>
                                                    <span class="menu-title">{{ $league->name }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                            <li class="sidebar-menu-item sidebar-dropdown">
                                <a class="{{ menuActive(['admin.schedules*'], 3) }}" href="javascript:void(0)">
                                    <i class="menu-icon la la-clock"></i>
                                    <span class="menu-title">@lang('Manage Schedules')</span>
                                    @if ($bannedUsersCount > 0 || $emailUnverifiedUsersCount > 0 || $mobileUnverifiedUsersCount > 0 || $kycUnverifiedUsersCount > 0 || $kycPendingUsersCount > 0)
                                        <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                                    @endif
                                </a>

                                <div class="sidebar-submenu {{ menuActive(['admin.schedule*'], 2) }}">
                                    <ul>
                                        @foreach(\App\Models\League::where('status', 1)->get() as $league)
                                            <li class="sidebar-menu-item {{ menuActive('admin.schedules*', 2) }}">
                                                <a class="nav-link" href="{{ route('admin.schedules.index', ['league' => $league->slug]) }}">
                                                    <i class="menu-icon las la-dot-circle"></i>
                                                    <span class="menu-title">{{ $league->name }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive(['admin.game*', 'admin.question*', 'admin.option*'], 3) }}" href="javascript:void(0)">
                        <i class="menu-icon las la-gamepad"></i>
                        <span class="menu-title">@lang('Manage Games') </span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive(['admin.game*', 'admin.question*', 'admin.option*'], 2) }}">
                        <ul>

                            <li class="sidebar-menu-item {{ menuActive('admin.game.running') }}">
                                <a class="nav-link" href="{{ route('admin.game.running') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Running')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.game.upcoming') }}">
                                <a class="nav-link" href="{{ route('admin.game.upcoming') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Upcoming')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.game.ended') }}">
                                <a class="nav-link" href="{{ route('admin.game.ended') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Ended')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.game.index') }}">
                                <a class="nav-link" href="{{ route('admin.game.index') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('All')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar__menu-header">@lang('Manage Bets')</li>
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.bet*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon las la-clipboard-list"></i>
                        <span class="menu-title">@lang('Bet Placed') </span>
                        @if ($pendingBetCount > 0)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.bet*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.bet.pending') }}">
                                <a class="nav-link" href="{{ route('admin.bet.pending') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Pending')</span>
                                    @if ($pendingBetCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $pendingBetCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.bet.won') }}">
                                <a class="nav-link" href="{{ route('admin.bet.won') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Won')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.bet.lose') }}">
                                <a class="nav-link" href="{{ route('admin.bet.lose') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Lose')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.bet.refunded') }}">
                                <a class="nav-link" href="{{ route('admin.bet.refunded') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Refunded')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.bet.index') }}">
                                <a class="nav-link" href="{{ route('admin.bet.index') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('All')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.outcomes.*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon las la-clipboard-list"></i>
                        <span class="menu-title">@lang('Declare Outcomes') </span>
                        @if ($pendingBetCount > 0)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.outcomes*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.outcomes.declare.pending') }}">
                                <a class="nav-link" href="{{ route('admin.outcomes.declare.pending') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Pending Outcomes')</span>
                                    @if ($pendingBetCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $pendingBetCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.outcomes.declare.declared') }}">
                                <a class="nav-link" href="{{ route('admin.outcomes.declare.declared') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Declared Outcomes')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar__menu-header">@lang('Manage Finance')</li>
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.gateway*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon las la-credit-card"></i>
                        <span class="menu-title">@lang('Payment Gateways')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.gateway*', 2) }}">
                        <ul>

                            <li class="sidebar-menu-item {{ menuActive('admin.gateway.automatic.*') }}">
                                <a class="nav-link" href="{{ route('admin.gateway.automatic.index') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Automatic Gateways')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.gateway.manual.*') }}">
                                <a class="nav-link" href="{{ route('admin.gateway.manual.index') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Manual Gateways')</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.deposit*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon las la-file-invoice-dollar"></i>
                        <span class="menu-title">@lang('Deposits')</span>
                        @if (0 < $pendingDepositsCount)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.deposit*', 2) }}">
                        <ul>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.pending') }}">
                                <a class="nav-link" href="{{ route('admin.deposit.pending') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Pending Deposits')</span>
                                    @if ($pendingDepositsCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $pendingDepositsCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.approved') }}">
                                <a class="nav-link" href="{{ route('admin.deposit.approved') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Approved Deposits')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.successful') }}">
                                <a class="nav-link" href="{{ route('admin.deposit.successful') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Successful Deposits')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.rejected') }}">
                                <a class="nav-link" href="{{ route('admin.deposit.rejected') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Rejected Deposits')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.initiated') }}">

                                <a class="nav-link" href="{{ route('admin.deposit.initiated') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Initiated Deposits')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.list') }}">
                                <a class="nav-link" href="{{ route('admin.deposit.list') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('All Deposits')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.withdraw*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon la la-bank"></i>
                        <span class="menu-title">@lang('Withdrawals') </span>
                        @if (0 < $pendingWithdrawCount)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.withdraw*', 2) }}">
                        <ul>

                            <li class="sidebar-menu-item {{ menuActive('admin.withdraw.method.*') }}">
                                <a class="nav-link" href="{{ route('admin.withdraw.method.index') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Withdrawal Methods')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.withdraw.pending') }}">
                                <a class="nav-link" href="{{ route('admin.withdraw.pending') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Pending Withdrawals')</span>

                                    @if ($pendingWithdrawCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $pendingWithdrawCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.withdraw.approved') }}">
                                <a class="nav-link" href="{{ route('admin.withdraw.approved') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Approved Withdrawals')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.withdraw.rejected') }}">
                                <a class="nav-link" href="{{ route('admin.withdraw.rejected') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Rejected Withdrawals')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.withdraw.log') }}">
                                <a class="nav-link" href="{{ route('admin.withdraw.log') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('All Withdrawals')</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="sidebar__menu-header">@lang('Support & Report')</li>
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.ticket*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon la la-ticket"></i>
                        <span class="menu-title">@lang('Support Ticket') </span>
                        @if (0 < $pendingTicketCount)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.ticket*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.pending') }}">
                                <a class="nav-link" href="{{ route('admin.ticket.pending') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Pending Ticket')</span>
                                    @if ($pendingTicketCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $pendingTicketCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.closed') }}">
                                <a class="nav-link" href="{{ route('admin.ticket.closed') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Closed Ticket')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.answered') }}">
                                <a class="nav-link" href="{{ route('admin.ticket.answered') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Answered Ticket')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.index') }}">
                                <a class="nav-link" href="{{ route('admin.ticket.index') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('All Ticket')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.report*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon la la-list"></i>
                        <span class="menu-title">@lang('Report') </span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.report*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive(['admin.report.transaction', 'admin.report.transaction.search']) }}">
                                <a class="nav-link" href="{{ route('admin.report.transaction') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Transaction Log')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive(['admin.report.login.history', 'admin.report.login.ipHistory']) }}">
                                <a class="nav-link" href="{{ route('admin.report.login.history') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Login History')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.report.notification.history') }}">
                                <a class="nav-link" href="{{ route('admin.report.notification.history') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Notification History')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.report.referral.commissions') }}">
                                <a class="nav-link" href="{{ route('admin.report.referral.commissions') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Referral Commissions')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar__menu-header">@lang('Settings')</li>

                <li class="sidebar-menu-item {{ menuActive('admin.referral*') }}">
                    <a class="nav-link" href="{{ route('admin.referral.index') }}">
                        <i class="menu-icon las la-sitemap"></i>
                        <span class="menu-title">@lang('Referral Setting')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.setting.index') }}">
                    <a class="nav-link" href="{{ route('admin.setting.index') }}">
                        <i class="menu-icon las la-life-ring"></i>
                        <span class="menu-title">@lang('General Setting')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.setting.system.configuration') }}">
                    <a class="nav-link" href="{{ route('admin.setting.system.configuration') }}">
                        <i class="menu-icon las la-cog"></i>
                        <span class="menu-title">@lang('System Configuration')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive(['admin.setting.api.index*'], 3) }}" href="javascript:void(0)">
                        <i class="menu-icon las la-dot-circle"></i>
                        <span class="menu-title">@lang('API Settings')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive(['admin.setting.api.index*'], 2) }}">
                        <ul>
                            @foreach(\App\Models\League::where('status', 1)->get() as $league)
                                <li class="sidebar-menu-item {{ menuActive('admin.setting.api.index*', 2) }}">
                                    <a class="nav-link" href="{{ route('admin.setting.api.index', ['league' => $league->slug]) }}">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">{{ $league->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.setting.logo.icon') }}">
                    <a class="nav-link" href="{{ route('admin.setting.logo.icon') }}">
                        <i class="menu-icon las la-images"></i>
                        <span class="menu-title">@lang('Logo & Favicon')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.extensions.index') }}">
                    <a class="nav-link" href="{{ route('admin.extensions.index') }}">
                        <i class="menu-icon las la-cogs"></i>
                        <span class="menu-title">@lang('Extensions')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive(['admin.language.manage', 'admin.language.key']) }}">
                    <a class="nav-link" data-default-url="{{ route('admin.language.manage') }}" href="{{ route('admin.language.manage') }}">
                        <i class="menu-icon las la-language"></i>
                        <span class="menu-title">@lang('Language') </span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.seo') }}">
                    <a class="nav-link" href="{{ route('admin.seo') }}">
                        <i class="menu-icon las la-globe"></i>
                        <span class="menu-title">@lang('SEO Manager')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.kyc.setting') }}">
                    <a class="nav-link" href="{{ route('admin.kyc.setting') }}">
                        <i class="menu-icon las la-user-check"></i>
                        <span class="menu-title">@lang('KYC Setting')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.setting.notification*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon las la-bell"></i>
                        <span class="menu-title">@lang('Notification Setting')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.setting.notification*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.global') }}">
                                <a class="nav-link" href="{{ route('admin.setting.notification.global') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Global Template')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.email') }}">
                                <a class="nav-link" href="{{ route('admin.setting.notification.email') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Email Setting')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.sms') }}">
                                <a class="nav-link" href="{{ route('admin.setting.notification.sms') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('SMS Setting')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.templates') }}">
                                <a class="nav-link" href="{{ route('admin.setting.notification.templates') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Notification Templates')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar__menu-header">@lang('Frontend Manager')</li>

                <li class="sidebar-menu-item {{ menuActive('admin.frontend.templates') }}">
                    <a class="nav-link" href="{{ route('admin.frontend.templates') }}">
                        <i class="menu-icon la la-html5"></i>
                        <span class="menu-title">@lang('Manage Templates')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.frontend.sections*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon la la-puzzle-piece"></i>
                        <span class="menu-title">@lang('Manage Section')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.frontend.sections*', 2) }}">
                        <ul>
                            @php
                                $lastSegment = collect(request()->segments())->last();
                            @endphp
                            @foreach (getPageSections(true) as $k => $secs)
                                @if ($secs['builder'])
                                    <li class="sidebar-menu-item @if ($lastSegment == $k) active @endif">
                                        <a class="nav-link" href="{{ route('admin.frontend.sections', $k) }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">{{ __($secs['name']) }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </li>

                <li class="sidebar__menu-header">@lang('Extra')</li>

                <li class="sidebar-menu-item {{ menuActive('admin.maintenance.mode') }}">
                    <a class="nav-link" href="{{ route('admin.maintenance.mode') }}">
                        <i class="menu-icon las la-robot"></i>
                        <span class="menu-title">@lang('Maintenance Mode')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.setting.cookie') }}">
                    <a class="nav-link" href="{{ route('admin.setting.cookie') }}">
                        <i class="menu-icon las la-cookie-bite"></i>
                        <span class="menu-title">@lang('GDPR Cookie')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.system*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon la la-server"></i>
                        <span class="menu-title">@lang('System')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.system*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.system.info') }}">
                                <a class="nav-link" href="{{ route('admin.system.info') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Application')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.system.server.info') }}">
                                <a class="nav-link" href="{{ route('admin.system.server.info') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Server')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.system.optimize') }}">
                                <a class="nav-link" href="{{ route('admin.system.optimize') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Cache')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.system.update') }}">
                                <a class="nav-link" href="{{ route('admin.system.update') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Update')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.setting.custom.css') }}">
                    <a class="nav-link" href="{{ route('admin.setting.custom.css') }}">
                        <i class="menu-icon lab la-css3-alt"></i>
                        <span class="menu-title">@lang('Custom CSS')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.request.report') }}">
                    <a class="nav-link" data-default-url="{{ route('admin.request.report') }}" href="{{ route('admin.request.report') }}">
                        <i class="menu-icon las la-bug"></i>
                        <span class="menu-title">@lang('Report & Request') </span>
                    </a>
                </li>
            </ul>
            <div class="text-uppercase mb-3 text-center">
                <span class="text--primary">{{ __(systemDetails()['name']) }}</span>
                <span class="text--success">@lang('V'){{ systemDetails()['version'] }} </span>
            </div>
        </div>
    </div>
</div>
<!-- sidebar end -->

@push('script')
    <script>
        if ($('li').hasClass('active')) {
            $('#sidebar__menuWrapper').animate({
                scrollTop: eval($(".active").offset().top - 320)
            }, 500);
        }
    </script>
@endpush
