<div class="card user-card d-flex flex-row flex-wrap mb-3 bg-secondary">
    <div class="user-card-avatar card-block px-4 py-4 text-center">
        <a href="/user/{{ $user['id'] }}">
        <img src="{{
                isset($user['avatar']) ?
                asset('storage/avatars/'.$user['avatar']) : $userImgPHolder
            }}"
            onerror="this.src='{{ $userImgPHolder }}'"
            style="border-radius: 50%;"
        /> </a>
    </div>

    <div class="card-block user-card-body d-flex flex-column justify-content-center px-4 py-4">
        <h4 class="card-title mb-0">
            <a href="/user/{{ $user['id'] }}" class="purpleLink" >{{ $user['name'] }}</a>
        </h4>

        <p class="user-card-location py-1 m-0">
            @if (isset($user['country']))
                &#{{ $user['country']['flag'][0] }}&#{{ $user['country']['flag'][1] }}
            @endif
            @if (isset($user['city']))
                {{ $user['city'] }}, {{ $user['country']['name'] }}
            @else
                {{ $user['country']['name'] }}
            @endif
        </p>

        @if (!Auth::guest() && $user['id'] != Auth::id())
            @if ($user['followed'])
                <div class="w-25 mb-2">
                    <button type="button" class="btn btn-primary my-0 py-0 me-3" id="followBtn"
                        onclick="shortcutUnfollowUser(this, {{ $user['id'] }})">Following</button>
                </div>
            @else
                <div class="w-25 mb-2">
                    <button type="button" class="btn btn-primary my-0 py-0 me-3" id="followBtn"
                        onclick="shortcutFollowUser(this, {{ $user['id'] }})">Follow</button>
                </div>
            @endif
        @endif

        @if (isset($user['description']))
            <p class="user-card-description">{{ mb_strimwidth($user['description'], 0, 300, "...") }} </p>
        @endif
    </div>

    <div class="card-block user-card-right d-flex flex-column align-items-center justify-content-start py-4">
        @if ($user['isAdmin'])
            <span class="badge rounded-pill bg-custom mt-4 mb-4"> Admin </span>
        @endif

        <p class="user-card-description mt-4 mb-4">Reputation: {{ $user['reputation'] }}</p>

        @foreach ($user['topAreasExpertise'] as $area)
            <span class="badge bg-primary rounded mb-3">
                {{ $area['tag_name'] }}
            </span>
        @endforeach
    </div>
    
</div>

