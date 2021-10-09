
// Declare globals variables

var dataRooms = [],
    dataFormUser = 0,
    maxAdults = 8,
    rooms = 0,
    users = 0,
    totalAdults = 0,
    totalChildren = 0,
    firstLoad = true,
    isValidApi = false;

var ajaxValidateApi = null;


// Set events
$(document).ready(function () {

    // Event to add room
    $('#viajero_add_room').click(function(e) {
        e.preventDefault();
        addRoom();
    });

    // Evento to remove room
    $('#viajero_rooms').on('click', '.viajero_room_remover_button', function(e) {
        e.preventDefault();
        var button = $(this),
            containerRoom = button.parents('.viajero_room_item'),
            key = containerRoom.attr('data-key');

        containerRoom.parent().remove();
        updateRoomNumber();
        updateRoomKey();
        validateUsersAmount();
        deleteRoom(key);
    });

    // Event to change number of adults in the room
    $('#viajero_rooms').on('change', '.viajero_adults', function(e) {
        e.preventDefault();
        updateAmountUsers();
    });

    // Event to change the numerical value of an input
    $('#viajero_rooms').on('click', '.add_number_input', function(e) {
        e.preventDefault();
        var btn = $(this),
            input = btn.parent().parent().find('.input_number'),
            amount_adults = parseInt(input.val()),
            next_value = amount_adults + 1;

        if (next_value <= input.attr('max')) {
            input.val(amount_adults + 1);
            input.trigger('change');
        }
    });
    
    // Event to change the numerical value of an input
    $('#viajero_rooms').on('click', '.subs_number_input', function(e) {
        e.preventDefault();
        var btn = $(this),
            input = btn.parent().parent().find('.input_number'),
            amount_adults = parseInt(input.val()),
            next_value = amount_adults - 1;

        if (next_value >= input.attr('min')) {
            input.val(amount_adults - 1);
            input.trigger('change');
        }
    });

    // Event to change the number of children in the room
    $('#viajero_rooms').on('change', '.viajero_children', function(e) {
        e.preventDefault();
        var input = $(this),
            amounChildren = input.val(),
            containerRoom = input.parents('.viajero_room_item'),
            containerTitle = containerRoom.find('.viajero_ages_title');

        if (amounChildren > 0) {
            if (containerTitle.hasClass('hide')) {
                containerTitle.removeClass('hide');
            }
        } else {
            if (!containerTitle.hasClass('hide')) {
                containerTitle.addClass('hide');
            }
        }

        validateInputChildren(containerRoom, amounChildren);
        updateAmountUsers();
        updateTotalChildrens();
    });

    // Event to change the number of children in the room
    $('#viajero_rooms').on('change', '.age-childrens', function(e) {
        e.preventDefault();
        updateAmountUsers();
        updateTotalChildrens();
    });

    // Event to search rooms
    $('#form-pasajeros').submit(function(e) {
        e.preventDefault();

        searchRooms();
    });

    /**
     * If the container exists, it means that it is calling from
     * the purchase page and it is not necessary to add a room
     */
    if ($('#container-frame-buy').length == 0) {
        addRoom();

        if (typeof is_api !== 'undefined' && is_api !== '0' && typeof id_api !== 'undefined' && id_api !== '0') {
            if (is_api && id_api) {
                //validatePackageApi();

                /*setTimeout(function() {
                    validateFinishPackageApi();
                }, 5000);*/
            }
        }
    }

    window.onbeforeunload = reloadPage;
});

function reloadPage() {
    if (typeof is_api !== 'undefined' && is_api !== '0' && typeof id_api !== 'undefined' && id_api !== '0') {
        ajaxValidateApi.abort();
    }
};

function validateFinishPackageApi() {
    if (!isValidApi) {
        ajaxValidateApi.abort();

        alert('Lo sentimos, pero el paquete seleccionado no se encuentra disponible');
        window.history.back();
    }
}

function validatePackageApi() {
    ajaxValidateApi = $.ajax({
        method: 'post',
        dataType: 'json',
        url: urlAjaxViajero,
        timeout: 6000,
        data: {
            action: 'getPackages',
        },
        success: function(packages) {
            isValidApi = true;
            var issetPackage = false;
            $.each(packages, function(key, packageApi) {
                if (packageApi.ProductoID == id_api) {
                    issetPackage = true;
                }
            });

            if (!issetPackage) {
                alert('Lo sentimos, pero el paquete seleccionado no se encuentra disponible');
                window.history.back();
            }
        },
        error: function(response) {
            isValidApi = true;

            alert('Lo sentimos, pero el paquete seleccionado no se encuentra disponible');
            window.history.back();
        }
    });
}

// Update the number of each room
function updateRoomNumber() {
    var containerRooms = $('#viajero_rooms .viajero_room_item'),
        numberRoom = 1;

    containerRooms.each(function(index, room) {
        var containerRoom = $(room),
            containerNumber = containerRoom.find('.viajero_room_number');

        containerNumber.html(numberRoom);
        numberRoom++;
    });
}

// Update the key of each room
function updateRoomKey() {
    var containerRooms = $('#viajero_rooms .viajero_room_item'),
        keyRoom = 0;

    containerRooms.each(function(index, room) {
        var containerRoom = $(room);

        containerRoom.attr('data-key', keyRoom);
        keyRoom++;
    });
}

/**
 * It varies if the children's inputs have the correct amount per room.
 * If they are needed then add the missing inputs, and if they are left
 * over then remove them
 * @param { jqueryObject } containerRoom 
 * @param { int }  amount 
 */
function validateInputChildren(containerRoom, amount) {
    var containerChildren = containerRoom.find('.viajero_children_ages'),
        inputAgeChildren = containerChildren.find('.viajero_children_ages_field'),
        key = containerRoom.attr('data-key');

    if (amount > inputAgeChildren.length) {
        var diffCreate = amount - inputAgeChildren.length,
            numberChild = inputAgeChildren.length + 1,
            content = '';
        for (var i = 0; i < diffCreate; i++) {
            content =  `
                <div class="col-6">
                    <div class="form-group">
                        <label>Edad del niño ${numberChild}</label>

                        <div class="input-change-number">
                            <div class="button-prepend">
                                <button type="button" class="subs_number_input">-</button>
                            </div>
                            <input type="text" class="viajero_children_ages_field age-childrens input_number" value="0" readonly max="${data_resumen.max_age_childrens}" min="0">
                            <div class="button-append">
                                <button type="button" class="add_number_input">+</button>
                            </div>
                        </div>
                    </div>
                </div>`;

            containerChildren.append(content);
            numberChild++;

            dataRooms[key].ages.push(0);
        }
    } else {
        var diffCreate = inputAgeChildren.length - amount;
        inputAgeChildren = inputAgeChildren.get().reverse();
        for (var i = 0; i < diffCreate; i++) {
            var lastInput = $(inputAgeChildren[i]);
            var keyLast = dataRooms[key].ages.length - 1;

            console.log(lastInput);
            lastInput.parent().parent().parent().remove();
            dataRooms[key].ages.splice(keyLast, 1);
        }
    }

}

/**
 * It varies if the adult inputs have the correct amount per room.
 * If they are needed then add the missing inputs, and if they are left
 * over then remove them.
 */
function validateUsersAmount() {
    var currentUsers = 0,
        totalUsers = 0,
        containerRooms = $('#viajero_rooms .viajero_room_item');

    for (var i = 0; i < dataRooms.length; i++) {
        var dataRoom = dataRooms[i];
        currentUsers += (parseInt(dataRoom.adults) + parseInt(dataRoom.childs));
    }

    containerRooms.each(function(index, room) {
        var containerRoom = $(room),
            inputAdult = containerRoom.find('.viajero_adults'),
            inputChild = containerRoom.find('.viajero_children'),
            amountAdult = inputAdult.val(),
            amountChild = inputChild.val();

        totalUsers += (parseInt(amountAdult) + parseInt(amountChild));
    });


    if (totalUsers > currentUsers) {
        var diffCreate = totalUsers - currentUsers;
        for (var i = 0; i < diffCreate; i++) {
            dataFormUser++;
            createForm(dataFormUser);
        }
    } else {
        var diffCreate = currentUsers - totalUsers;
        for (var i = 0; i < diffCreate; i++) {
            deleteForm(dataFormUser);
            dataFormUser--;
        }
    }
}

/**
 * The values ​​of the number of adults and rooms that have been created are
 * updated
 */
function updateAmountUsers() {
    validateUsersAmount();
    var containerRooms = $('#viajero_rooms .viajero_room_item');

    containerRooms.each(function(index, room) {
        var containerRoom = $(room),
            inputAdult = containerRoom.find('.viajero_adults'),
            inputChild = containerRoom.find('.viajero_children'),
            ageInputs = containerRoom.find('.age-childrens'),
            amountAdult = inputAdult.val(),
            amountChild = inputChild.val();

        ageInputs.each(function(indexAge, ageInput) {
            var containerAge = $(ageInput),
                age = containerAge.val();

            dataRooms[index].ages[indexAge] = parseInt(age);
        });

        dataRooms[index].adults = parseInt(amountAdult);
        dataRooms[index].childs = parseInt(amountChild);
    });

    updateIconValues();
}

/**
 * The icons of the number of adults and rooms that have been created
 * are updated
 */
function updateIconValues() {
    var containerRoom = $('#viajero_numbero_of_room_badge'),
        containerUsers = $('#viajero_numbero_of_passengers_badge'),
        amountAdults = 0,
        amountChildren = 0,
        amountUsers = 0;

    for (var i = 0; i < dataRooms.length; i++) {
        var room = dataRooms[i];
        amountAdults += room.adults;
        amountChildren += room.childs;
    }

    amountUsers = amountAdults + amountChildren;
    totalAdults = amountAdults;
    totalChildren = amountChildren;

    data_resumen.total_adultos = totalAdults;


    containerRoom.html(dataRooms.length);
    containerUsers.html(amountUsers);

    clearDestinies();
    updateTotalChildrens();
    calculatePrices();
}

// A new room is added
function addRoom() {
    var containerRooms = $('#viajero_rooms'),
        key = dataRooms.length,
        numberRoom = dataRooms.length + 1,
        buttonDelete = `<button type="button" class="btn btn-link text-danger viajero_room_remover_button">
            Eliminar
        </button>`;

    if (rooms == 0) {
        buttonDelete = '';
    }

    content = `
            <div class="col-md-6 col-12">
                <div data-key="${key}" class="viajero_room_item">
                    <h4>
                        Habitación <span class="viajero_room_number">${ numberRoom }</span>
                    </h4>
                    ${buttonDelete}
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Adultos</label>

                                <div class="input-change-number">
                                    <div class="button-prepend">
                                        <button type="button" class="subs_number_input">-</button>
                                    </div>
                                    <input type="text" class="viajero_adults input_number" value="1" readonly max="8" min="1">
                                    <div class="button-append">
                                        <button type="button" class="add_number_input">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Niños</label>

                                <div class="input-change-number">
                                    <div class="button-prepend">
                                        <button type="button" class="subs_number_input">-</button>
                                    </div>
                                    <input type="text" class="viajero_children input_number" value="0" readonly max="5" min="0">
                                    <div class="button-append">
                                        <button type="button" class="add_number_input">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <strong class="viajero_ages_title hide">
                                ¿Que edad tienen los niños con los que viaja?
                            </strong>
                            <div class="row viajero_children_ages"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    
    containerRooms.append(content);

    rooms++;
    users++;

    validateUsersAmount();

    dataRooms.push({
        adults: 1,
        childs: 0,
        ages: []
    });

    updateIconValues();
}

// Delete room
function deleteRoom(key) {
    dataRooms.splice(key, 1);
    updateIconValues();
}

/**
 * The method that searches the lines is called but that meets the
 * parameters of the selected room
 */
function searchRooms() {
    var errors = false;
    for (var i = 0; i < dataRooms.length; i++) {
        var dataRoom = dataRooms[i];

        if (dataRoom.adults > maxAdults) {
            errors = true;
        }
    }

    if (errors) {
        var containerError = $('#viajero_alert_message')
        containerError.addClass('alert alert-danger')
            .html('El máximo de adultos por habitación es de ' + maxAdults)
            .show()
            .fadeOut(5000);
        return;
    }

    if (data_selected.linea == 0) {
        var containerError = $('#viajero_alert_message')
        containerError.addClass('alert alert-danger')
            .html('Por favor selecciona una salida')
            .show()
            .fadeOut(5000);
        return;
    }

    if (is_api) {
        var containerError = $('#viajero_alert_message');
        containerError.fadeOut();
        validateQuotas();
    } else {
        searchHotels();
    }
}

function searchHotels() {
    scrollHotels();
    loadDestinysLine(data_selected.linea);
}

function validateQuotas() {
    var containerError = $('#viajero_alert_message'),
        id_package = $("#viajeroProduct").attr("data-package"),
        textError = 'Espere un momento, buscando habitaciones...';
    
    containerError.addClass('alert alert-info')
        .html(textError)
        .show();

    $.ajax({
        method: 'post',
        dataType: 'json',
        url: urlAjaxViajero,
        data: {
            departure_id: data_selected.departure_id,
            package_id:id_package,
            action: 'validateQuotasLine',
        },
        success: function(departure) {
            var containerError = $('#viajero_alert_message');
            containerError.fadeOut();

            if (departure === null) {
                alert('Lo sentimos, pero el paquete seleccionado no se encuentra disponible');
                window.history.back();
            }

            var currentQuota = parseInt(departure.CupoAlojamiento) - data_resumen.total_adultos;
            var currentQuotaTranport = parseInt(departure.CupoTransporte )- data_resumen.total_adultos;

            if (disponibilidad == 'No') {
                if (currentQuota <= 0  || minQuota > currentQuota || currentQuotaTranport <= 0 || minQuota > currentQuotaTranport) {
                    containerError = $('#viajero_alert_message');
                    textError = 'Lo sentimos, no hay cupos suficientes para este paquete';
                    
                    containerError.addClass('alert alert-danger')
                        .html(textError)
                        .show()
						.delay(2500)
                        .fadeOut(4000);
                    return;
                }
            }

            searchHotels();
        },
        error: function(response) {
            alert('Lo sentimos, pero el paquete seleccionado no se encuentra disponible');
            window.history.back();
        }
    });

    /*$.ajax({
        method: 'post',
        dataType: 'json',
        url: urlAjaxViajero,
        data: {
            departure_id: data_selected.departure_id,
            action: 'validateQuotasLine',
        },
        success: function(departure) {
            var containerError = $('#viajero_alert_message');
            containerError.fadeOut();

            if (departure === null) {
                alert('Lo sentimos, pero el paquete seleccionado no se encuentra disponible');
                window.history.back();
            }

            var currentQuota = departure.CupoAlojamiento - data_resumen.total_adultos;
            var currentQuotaTranport = departure.CupoTransporte - data_resumen.total_adultos;

            if (disponibilidad == 'No') {
                if (currentQuota <= 0  || minQuota > currentQuota || currentQuotaTranport <= 0 || minQuota > currentQuotaTranport) {
                    containerError = $('#viajero_alert_message');
                    textError = 'Lo sentimos, no hay cupos suficientes para este paquete';

                    containerError.addClass('alert alert-danger')
                        .html(textError)
                        .show()
                        .fadeOut(5000);
                    return;
                }
            }

            searchHotels();
        },
        error: function(response) {
            alert('Lo sentimos, pero el paquete seleccionado no se encuentra disponible');
            window.history.back();
        }
    });*/
}

function scrollHotels() {
    var body = $("html, body");
    body.stop().animate({scrollTop: $('#container-destinations').offset().top - 65}, 500, 'swing');
}

function scrollContent(content, add = 0) {
    var body = $("html, body");
    body.stop().animate({scrollTop: content.offset().top - add}, 500, 'swing');
}