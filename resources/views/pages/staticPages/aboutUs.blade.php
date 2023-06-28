@extends('layouts.app')

@section('title', "- About Us")

@section('content')

    <div class="container text-center my-5 p-5 bg-secondary">
        <h1>About us</h1>
        <div class="horizontalDivider mb-5"></div>

        <p>
            We are in an era of technology growth, but with it, the amount of wrong information on the internet and
            media algo grows. It becomes increasingly complicated to filter the information that is correct, and knowledge proof
            is often required for our disclosures to be legitimate, proofs that aren't always easy to get.
        </p>

        <p>
            This is the reason why our team of four 3rd year L.EIC students decided to create Newtify!
        </p>

        <p>
            On this application, users are the experts which will evaluate the news being published, by giving 
            their feedbacks and suggestions. Newtify brings a new reality where one's practical knowledge takes the place of
            theoretical knowledge and where your opinion is used to evaluate the authors' reliability! Newtify is a way to decrease the 
            amount of biased news we have nowadays.
        </p>

        <p>
            Help us create a new and honest world where information is legit and we can learn with each other by 
            joining us!
        </p>

        <p>
            If you need help or found a problem, please contact one of the following team element and we'll reply as soon as possible!
        </p>
        
        <div class="row py-3" id="contactsSection">
            
            <div class="col mb-3 d-flex justify-content-center">
                <div class="card">
                    <img class="card-img-top" src="storage/up201905853.jpg" alt="Rui Alves">
                    <div class="card-body">
                        <h4>Rui Alves</h4>
                        <a href="">up201905853@up.pt</a>
                    </div>
                </div>
            </div>

            <div class="col mb-3 d-flex justify-content-center">
                <div class="card">
                    <img class="card-img-top" src="storage/up201906334.jpg" alt="Bruno Rosendo">
                    <div class="card-body">
                        <h4>Bruno Rosendo</h4>
                        <a href="">up201906334@up.pt</a>
                    </div>
                </div>
            </div>

            <div class="col mb-3 d-flex justify-content-center">
                <div class="card">
                    <img class="card-img-top" src="storage/up201906682.jpg" alt="João Mesquita">
                    <div class="card-body">
                        <h4>João Mesquita</h4>
                        <a href="">up201906682@up.pt</a>
                    </div>
                </div>
            </div>

            <div class="col mb-3 d-flex justify-content-center">
                <div class="card">
                    <img class="card-img-top" src="storage/up201706518.jpg" alt="Jorge Costa">
                    <div class="card-body">
                        <h4>Jorge Costa</h4>
                        <a href="">up201706518@up.pt</a>
                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection
