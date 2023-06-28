@extends('layouts.app')

<script type="text/javascript" src="{{ asset('js/tags.js') }}"></script>

@section('title', "- Manage Tags")

@section('content')

<div class="text-center container">
    <h1 class="text-center mt-5">Manage Tags</h1>
    <div class="mb-5 row">

        <div class="px-2 mt-5 col-12 col-lg-4">
            <div class="border bg-dark statusContainer" id="acceptedTagsContainer">
                <h3 class="mt-5">Accepted Tags</h3>
                @foreach ($tags_accepted as $tag)
                    <div class="mt-5 pb-3 pt-5 bg-dark mb-5 manageTagContainer">
                        <div id="stateButton" class="d-flex align-items-center">
                            <h5 class="mx-3 my-0 py-0 w-75">{{ $tag['name'] }}</h5>
                            <button type="button" onclick="removeTag(this, {{ $tag['id'] }})" class="my-0 py-0 btn btn-lg btn-transparent">
                                <i class="fas fa-trash fa-2x mb-2 text-danger"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="px-2 mt-5 col-12 col-lg-4">
            <div class="border bg-dark statusContainer" id="pendingTagsContainer">
                <h3 class="mt-5">Pending Tags</h3>
                @foreach ($tags_pending as $tag)
                    <div class="mt-5 pb-3 pt-5 bg-dark mb-5 manageTagContainer">
                        <div id="stateButton" class="d-flex align-items-center">
                            <h5 class="mx-3 my-0 py-0 w-75">{{ $tag['name'] }}</h5>
                            <button type="button" onclick="acceptTag(this, {{ $tag['id'] }})" class="my-0 py-0 btn btn-lg btn-transparent">
                                <i class="fas fa-check fa-2x mb-2 text-success"></i>
                            </button>
                            <button type="button" onclick="rejectTag(this, {{ $tag['id'] }})" class="my-0 mx-1 py-0 btn btn-lg btn-tranparent">
                                <i class="fas fa-times fa-2x mb-2 text-danger"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="px-2 mt-5 col-12 col-lg-4">
            <div class="border bg-dark statusContainer" id="rejectTagsContainer">
                <h3 class="mt-5">Rejected Tags</h3>
                @foreach ($tags_rejected as $tag)
                    <div class="mt-5 pb-3 pt-5 bg-dark mb-5 manageTagContainer">
                        <div id="stateButton" class="d-flex align-items-center">
                            <h5 class="mx-3 my-0 py-0 w-75">{{ $tag['name'] }}</h5>
                            <button type="button" onclick="acceptTag(this, {{ $tag['id'] }})" class="my-0 py-0 btn btn-lg btn-transparent">
                                <i class="fas fa-check fa-2x mb-2 text-success"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection