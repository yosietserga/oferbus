var formSteps;

$(document).ready(function () {
    var btnPrev = $('#btn-prev-form'),
        btnNext = $('#btn-next-form'),
        btnsComprar = $('.btn-comprar'),
        lastStep = false;
        

    formSteps = $('#content-steps').smartWizard({
        selected: 0,
        keyNavigation: false,
        useURLhash: false,
        showStepURLhash: false,
        transitionEffect: 'slide',
        toolbarSettings: {
            toolbarPosition: 'none'
        },
        anchorSettings: {
            removeDoneStepOnNavigateBack: true
        }
    });

    formSteps.on("leaveStep", function(e, anchorObject, stepNumber, stepDirection) {
        var validate = true;

        if (stepDirection == 'forward') {
            validate = validateSteps(stepNumber);

            if (validate) {
                scrollTop();
            }
        }

        return validate;
    });

    formSteps.on("showStep", function(e, anchorObject, stepNumber, stepDirection, stepPosition) {
        btnNext.html('Confirmar');
        lastStep = false;

        if (!btnsComprar.first().hasClass('disabled')) {
            btnsComprar.first().addClass('disabled')
            btnsComprar.last().addClass('disabled')
        }

        if (stepPosition === 'first') {
            btnPrev.addClass('disabled');
        } else if (stepPosition === 'final') {
            lastStep = true;
            if (btnsComprar.first().hasClass('disabled')) {
                btnsComprar.first().removeClass('disabled')
                btnsComprar.last().removeClass('disabled')
            }
            btnNext.html('Comprar');
        } else {
            btnPrev.removeClass('disabled');
            btnNext.removeClass('disabled');
        }
    });

    btnPrev.on("click", function() {
        formSteps.smartWizard("prev");
        scrollTop();
        return true;
    });

    btnNext.on("click", function() {
        if (lastStep) {
            var validate = validateThirdStep();
            if (validate) {
                comprarPaquete();
            }
        } else {
            formSteps.smartWizard("next");
        }
        return true;
    });

    btnsComprar.on('click', function() {
        var validate = validateThirdStep();
        if (validate) {
            comprarPaquete();
        }
    });
});

function scrollTop() {
    var body = $("html, body");
    body.stop().animate({scrollTop: $('#content-steps').offset().top}, 500, 'swing');
}

function validateSteps(number) {
    var validate = false;
    if (number === 0) {
        validate = this.validateFirstStep();
    } else if (number === 1) {
        validate = this.validateSecondStep();
    }

    return validate;
}

function validateFirstStep() {
    var validate = true;
    var error = 0;

    if (data_selected.origen.id == 0) {
        validate = false;
        error = 1;
    } else if (data_selected.linea == 0) {
        validate = false;
        error = 2;
    }

    if (!validate) {
        var textError = '';

        if (error == 1) {
            textError = 'Seleccione un origen';
        } else if (error == 2) {
            textError = 'Seleccione una salida';
        }

        showError(1, textError);
    }

    return validate;
}

function validateSecondStep() {
    var validate = true;
    if (data_selected.amount_lines == 0) {
        validate = false;
        error = 1;
    } else if (data_selected.package_rooms.length < data_selected.hotels.amount) {
        validate = false;
        error = 1;
    } else if (data_selected.package_rooms.length == 0) {
        validate = false;
        error = 1;
    }

    if (!validate) {
        var textError = '';

        if (error == 1) {
            textError = 'Seleccione una habitación';
        }

        showError(2, textError);
    }

    return validate;
}

function validateThirdStep() {
    var validate = true;

    $('#alertbuy').hide();
    $('#alertbuy2').hide();
    arregloPasajeros = [];
    $('.pasajeros_formularios form').each(function() {
        addForm(this);
    });

    var valForms = validateForms();

    if (valForms !== true) {
        validate = false;
        showError(3, valForms);

        var containerError = $('.form-error-3');
        scrollContent(containerError, 120);
    }

    if (validate) {
        comprarPaquete();
    }
}

function showError(step, message, type = 'danger') {
    var containerError = $('.form-error-' + step);
    var errorContent = $('<div class="alert alert-' + type + '">'+ message +'</div>');
    
    containerError.html(errorContent);

    setTimeout(function () {
        if (errorContent) {
            errorContent.remove();
        }
    }, 100000);
}