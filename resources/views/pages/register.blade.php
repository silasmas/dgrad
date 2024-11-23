@extends('models.app')


@section("content")

<section class="height-100vh d-flex align-items-center page-section-ptb login login-gradient">
    <div class="container">
        <div class="row g-0 justify-content-center position-relative">
            <div class="col-lg-4 col-md-6 login-fancy-bg bg-overlay-black-30"
                style="background: url(images/login/02.jpg);">
                <div class="login-fancy pos-r">
                    <h2 class="mb-20 text-white">Hello world!</h2>
                    <p class="mb-20 text-white">Create tailor-cut websites with the exclusive multi-purpose responsive
                        template along with powerful features.</p>
                    <ul class="list-unstyled list-inline-item pos-bot pb-30">
                        <li class="list-inline-item"><a class="text-white" href="#"> Terms of Use</a> </li>
                        <li class="list-inline-item"><a class="text-white" href="#"> Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 white-bg">
                <div class="clearfix pb-40 login-fancy">
                    <h4 id="txtInfo">Silas Masimango</h4>
                    <div>
                        <form id="formIdentite">
                            <div class="mb-20 section-field">
                                <label class="mb-10" for="name">Télephone* </label>
                                <input id="phone" class="web form-control" type="text"
                                    placeholder="Votre numéro de téléphone" name="phone">
                            </div>
                            <div class="hidden mb-20 section-field">
                                <label class="mb-10" for="Password">Mot de passe* </label>
                                <input id="Password" class="Password form-control" type="password"
                                    placeholder="Votre mot de passe" name="password">
                            </div>
                            <div class="section-field">
                                <button type="submit" class="button">
                                    <span>Identifiez-vous</span>
                                    <i class="fa fa-check"></i>
                                </button>

                            </div>
                        </form>

                    </div>
                    <div id="infoInfraction" class="d-none">
                        <form id="formMatricule">
                            <div class="mb-20 section-field">
                                <label class="mb-10" for="Password">Client</label>
                                <input id="user_id" class="form-control" type="texte" placeholder="ID"
                                    name="user_id">
                            </div>
                            <div class="mb-20 section-field">
                                <label class="mb-10" for="Password">Matricule</label>
                                <input id="matricule" class="form-control" type="texte" placeholder="Matricule"
                                    name="matricule">
                            </div>
                            <div class="mb-20 section-field">
                                <label class="mb-10" for="">Choisir une infraction* </label>
                                <div class="mb-4 box">
                                    <select class="wide fancyselect" name="contrevention_id">
                                        @forelse ($infractions as $inf)
                                        <option value="{{ $inf->id }}">{{$inf->name}}</option>
                                        @empty

                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="section-field">
                                <button type="submit" class="button">
                                    <span>Login</span>
                                    <i class="fa fa-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="section-field">
                        <p class="mt-20 mb-0">Aider à <a href="{{ route('home') }}"> Payé une infraction</a></p>
                    </div>
                    {{--
                    <div class="mb-20 section-field">
                        <label class="mb-10" for="Password">Payer par :</label>
                        <input id="payerPar" class="form-control" type="texte" placeholder="Nom et Prenom"
                            name="payerPar">
                    </div>
                    <div class="mb-20 section-field">
                        <label class="mb-10" for="Password">Téléphone</label>
                        <input id="phonepayant" class="form-control" type="texte" placeholder="Contact du payant"
                            name="phonepayant">
                    </div> --}}

                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')
<script>
    $(document).on("submit", "#formIdentite", function (e) {
    e.preventDefault();
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    Swal.fire({
        title: 'Merci de patienter...',
        icon: 'info'
    });

    $.ajax({
        url: 'authAgent',
        type: "POST",
        data: new FormData(this),
        processData: false, // Empêche jQuery de traiter les données
        contentType: false, // Empêche jQuery de définir un type de contenu incorrect
        headers: {
            'X-CSRF-TOKEN': csrfToken
                    },
        success: function (data) {
            if (!data.reponse) {
                Swal.fire({
                    title: data.msg,
                    icon: 'error'
                });
            } else {
                // Remplir les champs du formulaire avec les données reçues
                console.log(data.data);
                $('#txtInfo').text(data.data.firstname + ' - ' + data.data.name);
                $('#user_id').val(data.data.id);
                $('#formIdentite').addClass("d-none");
                $('#infoInfraction').removeClass("d-none");
                $("#formIdentite")[0].reset();
                Swal.fire({
                    title: data.msg,
                    icon: 'true'
                });
            }
        },
        error: function (xhr, status, error) {
            // Gérer les erreurs
            console.error("Erreur lors de la requête :", error);
            Swal.fire({
                title: 'Une erreur est survenue',
                icon: 'error'
            });
        }
    });
});
    $(document).on("submit", "#formMatricule", function (e) {
    e.preventDefault();
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    Swal.fire({
        title: 'Merci de patienter...',
        icon: 'info'
    });

    $.ajax({
        url: 'registerInfra',
        type: "POST",
        data: new FormData(this),
        processData: false, // Empêche jQuery de traiter les données
        contentType: false, // Empêche jQuery de définir un type de contenu incorrect
        headers: {
            'X-CSRF-TOKEN': csrfToken
                    },
        success: function (data) {
            if (!data.reponse) {
                Swal.fire({
                    title: data.msg,
                    icon: 'error'
                });
            } else {
                $('#txtInfo').text("Identifiez-vous");
                $('#formIdentite').removeClass("d-none");
                $('#infoInfraction').addClass("d-none");
                $("#formMatricule")[0].reset();

                Swal.fire({
                    title: data.msg,
                    icon: 'true'
                });
            }
        },
        error: function (xhr, status, error) {
            // Gérer les erreurs
            console.error("Erreur lors de la requête :", error);
            Swal.fire({
                title: 'Une erreur est survenue',
                icon: 'error'
            });
        }
    });
});

</script>
@endsection
