@extends('layouts.admin')

@section('title')
    TITLE
@endsection

@section('active-users')
    active
@endsection

@section('content')
    @section('navigation')
        <div class="p-2">
            <a class="btn btn-primary" href="{{ route('admin-edit-user', $user->id) }}">Edit</a>
        </div>

        <div class="p-2">
            <a class="btn btn-danger" href="">Ban</a>
        </div>
    @endsection

    <div class="row">
        <div class="col-12 col-md-2 mb-2">
            <div class="p-4 bg-admin-dark">
                <div class="text-center">
                    <img
                        src="https://www.osrsbox.com/osrsbox-db/items-icons/{{ $user->icon_id }}.png"
                        class="pixel icon"
                        alt="Profile icon"
                        style="width: 7.5rem; height: 7.5rem;">
                    <h1>{{ $user->name }}</h1>
                </div>

                {{--<p><strong>rank</strong></p>--}}

                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Icon ID:</strong> {{ (is_null($user->icon_id)) ? 'None' : $user->icon_id }}</p>
                <p><strong>Private:</strong> {{ ($user->private === 0 ? 'False' : 'True') }}</p>
                <p><strong>User ID:</strong> {{ $user->id }}</p>
                <p><strong>Joined:</strong> {{ \Carbon\Carbon::parse($user->created_at)->format('d. M Y H:i') }}</p>
                <p><strong>Updated:</strong> {{ \Carbon\Carbon::parse($user->updated_at)->format('d. M Y H:i') }}</p>
            </div>
        </div>

        <div class="col-12 col-md-5">
            <div class="p-4 bg-admin-dark">
                <h1>Accounts</h1>

                @foreach ($accounts as $key => $account)
                    <div class="row mb-4">
                        <div class="col-md-5 mb-4">
                            <div class="p-4 bg-admin-info">
                                <h2>@if (sizeof($user->account) > 1) {{ ++$key }} - @endif{{ $account->username }}</h2>
                                <p>
                                    <span><strong>Rank: </strong>{{ number_format($account->rank) }}</span>
                                    <br>
                                    <span><strong>Level: </strong>{{ $account->level }}</span>
                                    <br>
                                    <span><strong>Total XP:</strong> {{ number_format($account->xp) }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="p-4 bg-admin-info">
                                <accounthiscore :account="{{ $account }}"></accounthiscore>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
