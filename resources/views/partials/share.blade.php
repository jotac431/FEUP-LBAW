<section id="socialsPopup" class="d-block d-none fullPopup">
    <div id="backdrop" onclick="hideSocials()"></div>
    <div id="socialsPopupContainer" class="d-flex flex-column justify-content-center align-items-center popupContainer">
        <div id="socialsPopupInside" class="d-flex flex-column align-items-center popupInsideContainer position-relative">
            <div class="popupHeader">
                <h3 class="my-0">Share</h3>
                <button class="btn p-0 m-0 transparentButton"  onclick="hideSocials()">
                    <i class="fa fa-times fa-3x text-primary" id="closeIcon"></i>
                </button>
            </div>
            <div class="popupBody">
                <div id="socialsRow" class="row">
                    <div class="col-3 text-center">
                        <a id="fbIcon" target="_blank" class="btn btn-light-hover fab fa-facebook fa-3x"></a>
                        <p>Facebook</p>
                    </div>
                    <div class="col-3 text-center">
                        <a id="twitterIcon" target="_blank" class="btn btn-light-hover fab fa-twitter fa-3x"></a>
                        <p>Twitter</p>
                    </div>
                    <div class="col-3 text-center">
                        <a id="linkedInIcon" target="_blank" class="btn btn-light-hover fa fa-linkedin fa-3x"></a>
                        <p>LinkedIn</p>
                    </div>
                    <div class="col-3 text-center">
                        <a id="redditIcon" target="_blank" class="btn btn-light-hover fab fa-reddit fa-3x"></a>
                        <p>Reddit</p>
                    </div>
                </div>
            </div>
            <div class="popupFooter">
                <h5 class="mt-3 mb-2">Page Link</h5>
                <div class="container-fluid position-relative"> 
                    <input class="text-light" type="url" id="shareLinkInput" aria-describedby="inputGroup-sizing-default" disabled> 
                </div>
            </div>
        </div>
    </div>
</section>
