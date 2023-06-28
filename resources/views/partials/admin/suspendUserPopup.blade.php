<section id="reportElement" class="d-block d-none fullPopup">

    <div id="backdrop" onclick="toggleReportPopup()"></div>

    <div id="reportContainer" class="d-flex flex-column align-items-center justify-content-center">

        <div id="reportInsideContainer" class="pb-4 d-flex flex-column align-items-center justify-content-evenly">

            <h3 class="mt-5">Suspension reason</h3>
            <div class="text-danger d-flex d-none py-0 my-0 align-items-center text-center px-5" id="reportError">
                <i class="fa fa-exclamation me-3 fa-1x"></i>
                <h5 class="py-0 my-0" id="reportErrorText"></h5>
            </div>
            <textarea id="reason" class="w-75 text-white" rows="10" placeholder="Insert suspension reason here"></textarea>

            <label for="suspensionEndTime"> Suspension End Time:</label>
            <input class="text-white w-50" placeholder="Insert Date" type="text" name="suspensionPicker">
            <input name="suspensionEndTime" type="hidden" id="suspensionEndTime">
            <div class="mb-2 text-center">
                <button class="btn btn-purple btn-lg customBtn" id="suspendBtn">
                    SUSPEND
                </button>
            </div>

            <button class="btn p-0 m-0 transparentButton" id="closePopupBtn" onclick="toggleReportPopup()">
                <i class="fa fa-times fa-3x purpleLink" id="closeIcon"></i>
            </button>

        </div>

    </div>

</section>
