@extends("models.app")

@section("content")

<!--=================================
page-title-->

<section   class="page-title faq-page-title bg-overlay-black-60 jarallax pt-5" data-speed="0.6"
    data-img-src="{{ asset('assets/images/bg/dgradbg.jpg') }}">
    <div class="container" style="margin-top: 300px">
        <div class="row">
            <div class="text-center col-lg-12">
                <div class="d-flex justify-content-center" >
                    <img src="{{ asset('assets/images/logo.png') }}" class="d-none" alt="" width="200">
                </div>
                <span class="text-white">Recherchez une référence</span>
                <div class="pl-20 pr-20 row justify-content-center form pb-60 xs-mt-20">
                    <div class="col-12 mt-50">
                        <form id="formSearchRef" class="row justify-content-center">
                            <div class="col-sm-5">
                                <input type="text" id="reference" class="not-click form-control" name="reference"
                                    placeholder="Tapez la référence de l'inffraction" value="">
                            </div>
                            <div class="col-sm-3 xs-mt-10 d-grid">
                                <button id="btnSearchRef" class="button" type="submit"> Trouvez </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!--=================================
  page-title -->

<!--=================================
   search-result -->

<section class="section-transparent page-section-pb mt-10">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 position-relative">
                <div class="clearfix login-bg d-none" id="interfacePaiement">
                    <div class="login-title">
                        <h2 class="mb-0 text-white">Infraction à payer</h2>
                    </div>
                    <form id="FormPaiment">
                        <div class="login-form">
                            <div class="mb-20 section-field">
                                <div>
                                    <p id="identite">

                                    </p>
                                    <p id="infraction">

                                    </p>
                                </div>
                                <hr>
                                <h4>Choisissez un mode de paiement</h4>
                                <div class="d-none">
                                    <input id="contrevention" class="web form-control" type="text" name="contrevention">
                                    <input id="ref" class="web form-control" type="text" name="reference">
                                    <input id="prix" class="web form-control" type="text" name="prix">
                                    <input id="monaie" class="web form-control" type="text" name="monaie">

                                </div>
                            </div>
                            <div class="mb-4 box">
                                <label>
                                    <input type="radio" name="toggleOption" value="mobile"> Mobile Money
                                </label>
                                <label>
                                    <input type="radio" name="toggleOption" value="carte"> Carte bancaire
                                </label>
                                <label>
                                    <input type="radio" name="toggleOption" value="cash"> Cash
                                </label>
                            </div>
                            <div class="mb-20 section-field d-none" id="mobileMoneyField">
                                <label class="mb-10" for="Password">Numéro de téléphone :</label>
                                <input id="toggleField" class="web form-control" type="text" placeholder="Ex :24382700000"
                                    name="number">
                            </div>
                            <div id="cashField" class="d-none">
                                <div class="mb-20 section-field ">
                                    <label class="mb-10" for="Password">Numéro de téléphone de l'agent :</label>
                                    <input id="phoneAgent" class="web form-control" type="text"
                                        placeholder="Ex :24382700000" name="phoneAgent">
                                </div>
                                <div class="mb-20 section-field ">
                                    <label class="mb-10" for="Password">Mot de passe :</label>
                                    <input id="password" class="web form-control" type="password" placeholder=""
                                        name="password">
                                </div>
                            </div>
                            <div class="section-field">
                                <button type="submit" id="focus" class="button">
                                    <span>Payer</span>
                                    <i class="fa fa-check"></i>
                                </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!--=================================
   search-result -->

@endsection
@section('script')
<script>
    $(document).ready(function() {
        initRadio();
        // Afficher la section correspondant au bouton radio sélectionné par défaut
        // $('input[name="toggleOption"]:checked').trigger('change');
        $('input[name="toggleOption"]').on('change', function() {

                const selectedValue = $(this).val();

                // Cacher tous les champs
                $('#mobileMoneyField, #cashField').addClass('d-none');

                // Afficher le champ correspondant
                if (selectedValue === 'mobile') {
                    $('#mobileMoneyField').removeClass('d-none');
                } else if (selectedValue === 'carte') {
                    // $('#cardField').removeClass('d-none');
                } else if (selectedValue === 'cash') {
                    $('#cashField').removeClass('d-none');
                }
            });
    });

    $(document).on("submit", "#formSearchRef", function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Merci de patienter...',
            icon: 'info'
        });

        var ref= $('#reference').val();
        $.ajax({
            url: '../findInfra',
            type: "GET",
            data: {'ref':ref},
            success: function (data) {
                if (!data.reponse) {
                    Swal.fire({
                        title: data.msg,
                        icon: 'warning'
                    });
                    $('#interfacePaiement').addClass("d-none");
                } else {
                    // Remplir les champs du formulaire avec les données reçues

                    $('#identite').text("Proprietaire : "+data.data.user.fisrtname+" "+data.data.user.name);
                    $('#infraction').text("Infraction commis : "+ data.data.contrevention.name+" "+"Prix : "+ data.data.contrevention.prix+data.data.contrevention.monaie);
                    $('#contrevention').val(data.data.reference.id);
                    $('#prix').val(data.data.contrevention.prix);
                    $('#monaie').val(data.data.contrevention.monaie);
                    $('#ref').val(data.data.reference.reference);

                    $('#interfacePaiement').removeClass("d-none");                    // $("#formIdentite")[0].reset();

                    Swal.fire({
                        title: data.msg,
                        icon: 'success'
                    });
                    scrol();
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

    function scrol(){
         // Cible l'élément à atteindre
         const targetElement = document.getElementById("focus");

        // Défile jusqu'à cet élément
        // targetElement.scrollIntoView({ behavior: "smooth" });
         // Bouton pour afficher le popup
    const showPopupBtn = document.getElementById("showPopup");

        // Variable pour sauvegarder la position de défilement
        // Défile jusqu'à la fin de la page
        window.scrollTo({ top: document.body.scrollHeight, behavior: "smooth" });


    }
    function initRadio(){
         // Sélectionne tous les boutons radio du formulaire et les désélectionne
         const radios = document.querySelectorAll('input[type="radio"]');
            radios.forEach(radio => {
                radio.checked = false;
            });

    }

    $(document).on("submit", "#FormPaiment", function (e) {
            e.preventDefault();
                var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                Swal.fire({
                    title: 'Merci de patienter...',
                    icon: 'info'
                });

            $.ajax({
                url: 'paieInfraction',
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

                        $("#FormPaiment")[0].reset();
                        $("#formSearchRef")[0].reset();
                        $('#identite').text("");
                        $('#infraction').text("");
                        $('#mobileMoneyField, #cashField').addClass('d-none');
                        $('#interfacePaiement').addClass("d-none");
                        Swal.fire({
                            title: data.msg,
                            icon: 'warning'
                        });
                        initRadio();
                        document.location=data.data.result_response.url;
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
$(document).ready(function () {
    // Récupère les paramètres de l'URL
    // const urlParams = new URLSearchParams(window.location.search);
 // Récupérer l'URL complète
 const fullUrl = window.location.href;

// Extraire le dernier segment après le dernier "/"
const lastSegment = fullUrl.split('/').pop();

// Afficher dans la console pour vérifier
console.log('Dernier paramètre :', lastSegment);
    // Vérifie si le paramètre "ref" existe
    if (lastSegment.includes('REF')) {
    // const ref = urlParams.get('ref'); // Obtenir la valeur du paramètre "ref"

    // Vérifier si le paramètre existe
    if (ref) {
        // Remplir le champ de recherche avec la valeur de 'ref'
        $('#reference').val(lastSegment);

        // Simuler un clic sur le bouton de recherche
        $('#btnSearchRef').trigger('click');
    }
    } else {
        console.log('Paramètre ref non trouvé.');
    }
});

</script>
@endsection
