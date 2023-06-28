@if (isset($author))

    <div class="d-flex flex-column px-2 px-lg-0 py-1" id="authorDetails">
        <h2 class="py-0 my-0 h-25">Author</h2>

        <div class="d-flex flex-row pt-3 h-75">
            <div class="w-25 me-3 text-center">
                <a href="{{ route('userProfile', ['id' => $author['id']]) }}">
                    <img id="authorAvatar" class="w-100" src="{{
                        isset($author['avatar']) ?
                        asset('storage/avatars/'.$author['avatar']) : $userImgPHolder 
                    }}" onerror="this.src='{{ $userImgPHolder }}'" alt="Author Avatar" />
                </a>
            </div>
            <div class="w-75 pb-0" id="authorHeader">
                <a href="{{ route('userProfile', ['id' => $author['id']]) }}">
                    <h4 class="m-3">{{ $author['name'] }}</h4>
                </a>
                
                <p class="m-3">
                    &#{{ $author['country']['flag'][0] }}&#{{ $author['country']['flag'][1] }}
                    @if (isset($author['city']))
                        {{ $author['city'] }}, {{ $author['country']['name'] }}
                    @else
                        {{ $author['country']['name'] }}
                    @endif
                </p>

                <div class="m-3">
                    @include('partials.user.reputationBar', [
                        'user' => $author,
                        'guest' => !Auth::check(),
                        'isOwner' => $isOwner,
                    ])
                </div>
            </div>
                        
        </div>

    </div>

    <div class="flex-row px-1 py-4 text-white overflow-auto" id="authorDescription">
        <p>{{ (isset($author['description']) ? $author['description'] : '') }}</p>
    </div>

    <div class="flex-row p-3" id="authorAreaExpertise">
        @include('partials.user.areasOfExpertiseGraph', ['topAreasExpertise' => $author['topAreasExpertise']])
    </div>
@else
    <div class="d-flex flex-row mb-3">
        <div class="flex-col w-25" style="margin-right: 1em;">
            <img id="authorAvatar" class="h-100" src={{ $userImgPHolder }} alt="Author Avatar">
        </div>
        <div class="flex-col w-75" id="author-header" style="padding-top: 1em;">
            <h4 class="mb-2"><i>Anonymous</i></h4>
        </div>
    </div>

    <div class="flex-row my-3" style="padding-top: 50%">
        <p>The author has deleted his account</p>
    </div>
@endif