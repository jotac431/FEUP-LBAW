<section id="reportElement" class="d-block d-none fullPopup">
    <div id="backdrop" onclick="toggleReportPopup()"></div>
    <div id="reportContainer" class="d-flex flex-column align-items-center justify-content-center">
        <div id="reportInsideContainer" class="d-flex flex-column align-items-center justify-content-evenly">
            <h3 class="mt-4 mt-lg-0">Give us a reason to report this user</h3>
            <div class="text-danger d-flex d-none py-0 my-0 align-items-center text-center px-5" id="reportError">
                <i class="fa fa-exclamation me-3 fa-1x"></i>
                <h5 class="py-0 my-0" id="reportErrorText"></h5>
            </div>
            <textarea id="reason" rows="10" placeholder="Insert report reason here"></textarea>
            <button class="btn btn-purple btn-lg customBtn" onclick="reportUser({{ $id }})">SUBMIT</button>
            <button class="btn p-0 m-0 transparentButton" id="closePopupBtn" onclick="toggleReportPopup()">
                <i class="fa fa-times fa-3x purpleLink" id="closeIcon"></i>
            </button>
        </div>
    </div>
</section>