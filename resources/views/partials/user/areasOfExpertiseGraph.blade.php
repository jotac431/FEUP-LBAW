@php
if (!function_exists('calculateExpertiseLevel')) {
    function calculateExpertiseLevel($reputation)
    {
        $step = 20;
        if ($reputation <= 0) {
            return 0;
        } elseif ($reputation < 5) {
            return $step * 1;
        } elseif ($reputation < 10) {
            return $step * 2;
        } elseif ($reputation < 20) {
            return $step * 3;
        } elseif ($reputation < 40) {
            return $step * 4;
        } else {
            return $step * 5;
        }
    }
}
@endphp

<div class="h-100 text-white" id="graphContainer">
    <h4 class="text-center pt-3 pb-0 my-0">Areas of Expertise</h4>
    <div class="d-flex flex-column h-100 justify-content-evenly pb-5">
        @foreach ($topAreasExpertise as $area)
            <div class="d-flex align-items-center ms-3 me-5">
                <p class="my-0 py-0 me-5 tagName text-truncate">{{ $area['tag_name'] }}</p>
                <div class="tagBar w-100 position-relative">
                    <div class="tagBarLevel" style="width: {{ calculateExpertiseLevel($area['reputation']) . '%' }}">
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
