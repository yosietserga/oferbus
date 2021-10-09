/* Cambios realizados por Carlos Espinoza */
var ViajeroRoomSelection = {
    PEOPLE_AMOUNT: 0,
    MAX_NUM_CHILDREN_PER_ROOM: 10,
    MAX_NUM_PEOPLE_PER_ROOM: 3,
    MIN_NUM_ADULTS_PER_ROOM: 1,

    filter: [],

    onlyDigits: function() {
        $(".viajero_only_digits_field").each(function() {
        	this.value= this.value.replace(/[^\d]+/g,'');
        });
    },

    totalOfRooms: function() {
        return $('#viajero_rooms .viajero_room_item').length;
    },

    totalOfPassengers: function() {
        var total = 0;
        $(
            '#viajero_rooms .viajero_room_item .viajero_adults, #viajero_rooms .viajero_room_item .viajero_children'
        ).each(function() {
            if(!isNaN(this.value)) { 
                total += (this.value*1);
            }
        });

        return total;
    },

    setTotalOfPassagers: function(element) {
        if(element.value>ViajeroRoomSelection.MAX_NUM_PEOPLE_PER_ROOM) {
            element.value = ViajeroRoomSelection.MAX_NUM_PEOPLE_PER_ROOM;
            $('#viajero_alert_message').attr('class', 'alert alert-danger');
            $('#viajero_alert_message')
                .html('El máximo de adultos por Habitación es '+ViajeroRoomSelection.MAX_NUM_PEOPLE_PER_ROOM);
            $('#viajero_alert_message').show();
            $('#viajero_alert_message').fadeOut(5000);
            return;
        }
        //create
        // for (var i = 0; i<element.value; i++) {
        //     ViajeroRoomSelection.PEOPLE_AMOUNT++;
        //     createForm(ViajeroRoomSelection.PEOPLE_AMOUNT);
        // }
        $('#viajero_numbero_of_passengers_badge').html(ViajeroRoomSelection.totalOfPassengers());
    },

    hideOrShowAgesTitle: function() {
        var total = 0;
        $('#viajero_rooms .viajero_room_item .viajero_children').each(function() {
            if(this.value <= 0 || this.value == '') {
                $(this).parent().parent().find('.viajero_ages_title').hide();
	        } else {
                $(this).parent().parent().find('.viajero_ages_title').show();
	        }
        });
        return total;
    },

    setListenerIntoAddRomButton: function() {
        $('#viajero_add_room').click(function() {
            ViajeroRoomSelection.addRoom();
            ViajeroRoomSelection.deleteRoom();
            ViajeroRoomSelection.addInputQuantityControls();

            ViajeroRoomSelection.PEOPLE_AMOUNT++;
            createForm(ViajeroRoomSelection.PEOPLE_AMOUNT);
        });
    },

    addAgeField: function(element) {
    	if(element.value>ViajeroRoomSelection.MAX_NUM_CHILDREN_PER_ROOM) {
            element.value = ViajeroRoomSelection.MAX_NUM_CHILDREN_PER_ROOM;
            $('#viajero_alert_message').attr('class', 'alert alert-danger');
            $('#viajero_alert_message')
                .html('El máximo de niños por Habitación es '+ViajeroRoomSelection.MAX_NUM_CHILDREN_PER_ROOM);
            $('#viajero_alert_message').show();
            $('#viajero_alert_message').fadeOut(5000);
            return;
    	}
        var childrenAgeFields = $(element).parent().parent().parent()
            .find('.viajero_children_ages .viajero_children_ages_field');

        ViajeroRoomSelection.hideOrShowAgesTitle();

        //delete
        if(childrenAgeFields.length>parseInt(element.value)) {
            for (var i = 0; i<(childrenAgeFields.length-element.value); i++) {
                deleteForm(ViajeroRoomSelection.PEOPLE_AMOUNT);
                ViajeroRoomSelection.PEOPLE_AMOUNT--;
                $(element).parent().parent().find('.viajero_children_ages .viajero_children_ages_field').last()
                    .parent().remove();
            }
        }
        //create
        for (var i = 0; i<(element.value-childrenAgeFields.length); i++) {
            ViajeroRoomSelection.PEOPLE_AMOUNT++;
            $(element).parent().parent().parent().find('.viajero_children_ages').append(`
                <div class="col-sm-3" style="padding-bottom: 5px; padding-top:5px;">
                    <input type="number" style="width: 60px; border: solid 1px #c3c3c3; height: 25px; padding: 5px;"
                        title="Edad del niño `+ViajeroRoomSelection.PEOPLE_AMOUNT+`"
                        class="viajero_children_ages_field viajero_only_digits_field"
                        name="viajero_children_row_1[]" min="1" step="1"
                        placeholder="Niño `+ViajeroRoomSelection.PEOPLE_AMOUNT+`">
                    <span class="text-danger" onclick="ViajeroRoomSelection.deleteAgeField(this)">X</span>
                </div>
            `);
            createForm(ViajeroRoomSelection.PEOPLE_AMOUNT);
        }
        $('#viajero_numbero_of_passengers_badge').html(ViajeroRoomSelection.totalOfPassengers());
        ViajeroRoomSelection.onlyDigits();
    },

    deleteAgeField: function(element) {
        deleteForm(ViajeroRoomSelection.PEOPLE_AMOUNT);
        $(element).parent().parent().parent().parent().find('.viajero_children').each(function(){
            this.value = (this.value-1);
        });
        ViajeroRoomSelection.hideOrShowAgesTitle();
        $(element).parent().remove();
        $('#viajero_numbero_of_passengers_badge').html(ViajeroRoomSelection.totalOfPassengers());        
        ViajeroRoomSelection.PEOPLE_AMOUNT--;
    },

    addRoom: function() {
        $('#viajero_rooms').append(`
            <div class="viajero_room_item">
                <label class=" padding-left-30 viajero_room_number_label">
                    HABITACIÓN <span class="viajero_room_number">`+(ViajeroRoomSelection.totalOfRooms()+1)+`</span>
                </label>
                <div class="row padding-left-30">
                    <div class="col-sm-2">
                        <strong style="color:#505050;">Adultos</strong>
                        <input type="number" style="width: 60px; border: solid 1px #c3c3c3; height: 25px; padding: 5px;"
                        	class="viajero_adults viajero_only_digits_field" min="1" step="1" 
                        	onblur="ViajeroRoomSelection.setTotalOfPassagers(this)" placeholder="Adultos" value="1" 
                        	onclick="ViajeroRoomSelection.setTotalOfPassagers(this)">
                    </div>
                    <div class="col-sm-2">
                        <strong style="color:#505050;">Niños</strong>
                        <input type="number" style="width: 60px; border: solid 1px #c3c3c3; height: 25px; padding: 5px;"
                        	class="viajero_children viajero_only_digits_field" 
                        	onchange="ViajeroRoomSelection.addAgeField(this)" min="0" step="1" placeholder="Niños" 
                            value="0">
                    </div>
                    <div class="col-sm-7">
                        <strong class="viajero_ages_title" style="color:#505050;">
                        ¿Que edad tienen los niños con los que viaja?
                        </strong>
                        <div class="row viajero_children_ages"><!-- ages --></div>
                    </div>
                    <div class="col-sm-1">
                        <button type="button" class="btn btn-link text-danger pull-right viajero_room_remover_button">
                            <i style="margin-top: 10px" class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        `);
        $('#viajero_numbero_of_room_badge').html(ViajeroRoomSelection.totalOfRooms());
        $('#viajero_numbero_of_passengers_badge').html(ViajeroRoomSelection.totalOfPassengers());

		//ViajeroRoomSelection.deleteRoom();
        ViajeroRoomSelection.hideOrShowAgesTitle();
		ViajeroRoomSelection.addInputQuantityControls();
        ViajeroRoomSelection.onlyDigits();
        
        ViajeroRoomSelection.PEOPLE_AMOUNT++;
        createForm(ViajeroRoomSelection.PEOPLE_AMOUNT);
    },

    deleteRoom: function() {
        $('.viajero_room_item .viajero_room_remover_button').click(function(){
            $(this).parent().parent().parent().remove();
            $('#viajero_numbero_of_passengers_badge').html(ViajeroRoomSelection.totalOfPassengers());
            $('#viajero_numbero_of_room_badge').html(ViajeroRoomSelection.totalOfRooms());
            deleteForm(ViajeroRoomSelection.PEOPLE_AMOUNT);
            ViajeroRoomSelection.PEOPLE_AMOUNT--;
        });
    },

    addInputQuantityControls: function() {
        $(`
            <div class="quantity-nav">
                <div class="quantity-button quantity-up">
                    <i class="fa fa-angle-up"></i>
                </div>
                <div class="quantity-button quantity-down">
                    <i class="fa fa-angle-down"></i>
                </div>
            </div>
        `).insertAfter('#viajero_rooms div.quantity input');

        $('#viajero_rooms .quantity').each(function() {
            var spinner = $(this);
            var input = spinner.find('input[type="number"]');
            var btnUp = spinner.find('.quantity-up');
            var btnDown = spinner.find('.quantity-down');
            var min = input.attr('min');
            var max = input.attr('max');
            btnUp.click(function() {
            $("#ejecutarActualizar").trigger("click");
                var oldValue = parseFloat(input.val());
                if (oldValue >= max) {
                    var newVal = oldValue;
                } else {
                    var newVal = oldValue + 1;
                }
                spinner.find("input").val(newVal);
                //SelectorSlide.val(newVal);.    aquí pone el selector del slide
                spinner.find("input").trigger("change");
            });

            btnDown.click(function() {
                var oldValue = parseFloat(input.val());
                if (oldValue <= min) {
                    var newVal = oldValue;
                } else {
                    var newVal = oldValue - 1;
                }
                spinner.find("input").val(newVal);
                //SelectorSlide.val(newVal);        aquí pone el selector del slide
                spinner.find("input").trigger("change");
            });

            $(input).on('change', function() {
                var cantAdultos = parseInt($(this).val());
                var cantNinos = parseInt($(this).val());
                var total = cantAdultos + cantNinos;
                $('#viajero_numbero_of_passengers_badge').html(total);
                $( function() {
                    $( ".datepicker" ).datepicker();
                });
                if (inc < total) {
                    inc = inc+ 1;
                    createForm(inc);
                }
                if (inc > total) {
                    deleteForm(inc);
                    inc = inc - 1;
                }
            });
        });
    },

    consoleQuery: function() {
        ViajeroRoomSelection.filter = [];
        $(
            '#viajero_rooms .viajero_room_item .viajero_adults'
        ).each(function() {
            if(!isNaN(this.value)) { 
                ViajeroRoomSelection.filter.push({"room_type": this.value})
            }
        });
        console.log(ViajeroRoomSelection.filter);
        ViajeroRoomSelection.filter = JSON.stringify(ViajeroRoomSelection.filter);    }
};

ViajeroRoomSelection.addRoom();
ViajeroRoomSelection.setListenerIntoAddRomButton();