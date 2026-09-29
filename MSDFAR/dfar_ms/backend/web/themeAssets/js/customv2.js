$('.userform').on('beforeSubmit', function (e) {

    $('.btn-success').attr('disabled', 'disabled');
    return true;

});

$("#dependant-add-btn").on("click", function () {

    let count = parseInt($("#dependant_count").val()) + 1;
    $("#dependantList").append('\n' +
        '                            <tr class="row_"' + count + '>\n' +
        '                                \n' +
        '                                <td>\n' +
        '                                    <div class="form-group field-fishermandependant-type' + count + ' required has-error">\n' +
        '                                        <input type="text" id="fishermandependant-type' + count + '" class="form-control"\n' +
        '                                               name="FishermanDependant[type][]">\n' +
        '                                    </div>\n' +
        '                                </td>\n' +
        '                                <td>\n' +
        '                                    <div class="form-group field-fishermandependant-name' + count + ' required">\n' +
        '                                        <input type="text" id="fishermandependant-name' + count + '" class="form-control"\n' +
        '                                               name="FishermanDependant[name][]" maxlength="500">\n' +
        '                                    </div>\n' +
        '                                </td>\n' +
        '                                <td>\n' +
        '                                    <div class="form-group field-fishermandependant-nic' + count + ' required">\n' +
        '                                        <input type="text" id="fishermandependant-nic' + count + '" class="form-control"\n' +
        '                                               name="FishermanDependant[nic][]" maxlength="50">\n' +
        '                                    </div>\n' +
        '                                </td>\n' +
        '                                <td>\n' +
        '                                    <div class="form-group field-fishermandependant-birthday' + count + ' required has-error">\n' +
        '                                        <input type="text" id="fishermandependant-birthday' + count + '" class="form-control"\n' +
        '                                               name="FishermanDependant[birthday][]">\n' +
        '                                    </div>\n' +
        '                                </td>\n' +
        '                            </tr>');

    $("#dependant_count").val(count);

});

function setFiDivision() {
    $("#fi_division").val(scientific["fi_division"]).change();
}

function setLandingSite() {
    $("#landing_place").val(scientific["landing_place"]);
}

function loadFIdistricts(value) {
    loadDivisionsAjax(value)

}

function loadDivisionsAjaxBoatNumber(district, selectedValue) {
    $.ajax({
        url: "../division/list-by-district?districtId=" + district, type: "GET", success: function (data) {
            data = JSON.parse(data);
            let options = ' <option value="" selected >Please Choose...</option>';
            $.each(data, function (key, value) {
                options += '<option value=' + value.id + '>' + value.name + '</option>';
            });
            $("#boatnumbers-fisheries_division").html(options)
            $("#boatnumbers-fisheries_division").val(selectedValue)

        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });

}

function loadDepartureBoatReasons(
    mainType,
    role,
    selectedValue
) {
    const $reasonDropdown =
        $("#departureboats-remarks");

    /*
     * Clear the Reason dropdown first.
     */
    $reasonDropdown
        .empty()
        .append(
            $('<option>', {
                value: '',
                text: 'Please select'
            })
        );

    if (
        mainType === null ||
        mainType === undefined ||
        String(mainType).trim() === '' ||
        role === null ||
        role === undefined ||
        String(role).trim() === ''
    ) {
        return;
    }

    $.ajax({
        url:
            "../departure/getdepartureboatdetails",

        type:
            "GET",

        dataType:
            "json",

        data: {
            main:
                mainType,

            role:
                role
        },

        success: function (data) {
            console.log(
                "Departure reasons:",
                data
            );

            /*
             * Do NOT JSON.parse(data).
             *
             * Because dataType: "json" tells jQuery
             * to return a JavaScript object.
             */
            if (
                !data ||
                typeof data !== "object"
            ) {
                console.error(
                    "Invalid departure reason response:",
                    data
                );

                return;
            }

            $reasonDropdown.empty();

            $reasonDropdown.append(
                $('<option>', {
                    value: '',
                    text: 'Please select'
                })
            );

            $.each(
                data,
                function (key, value) {
                    $reasonDropdown.append(
                        $('<option>', {
                            value: key,
                            text: value
                        })
                    );
                }
            );

            /*
             * Restore existing Reason when editing.
             */
            if (
                selectedValue !== null &&
                selectedValue !== undefined &&
                String(selectedValue) !== ''
            ) {
                $reasonDropdown.val(
                    String(selectedValue)
                );
            }

            /*
             * Trigger change for any other
             * dependent behaviour.
             */
            $reasonDropdown.trigger(
                'change'
            );
        },

        error: function (
            jqXHR,
            textStatus,
            errorThrown
        ) {
            console.error(
                "Unable to load Departure Reasons:",
                {
                    status:
                        jqXHR.status,

                    response:
                        jqXHR.responseText,

                    textStatus:
                        textStatus,

                    error:
                        errorThrown
                }
            );

            $reasonDropdown
                .empty()
                .append(
                    $('<option>', {
                        value: '',
                        text:
                            'Unable to load reasons'
                    })
                );
        }
    });
}

function loadSubGearTypesAjaxDivisionGearType(gear, selectedValue) {
    $.ajax({
        url: "../gear-type/list-by-gear?gear=" + gear, type: "GET", success: function (data) {
            data = JSON.parse(data);
            let options = ' <option value="" selected >Please Choose...</option>';
            $.each(data, function (key, value) {
                options += '<option value=' + value.id + '>' + value.description + '</option>';
            });
            $("#districtgeartypes-sub_gear").html(options)
            $("#districtgeartypes-sub_gear").val(selectedValue)
            $("#districtgeartypes-sub_gear").change()

        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });

}

function loadDivisionsAjaxYard(district, selectedValue) {
    $.ajax({
        url: "../division/list-by-district?districtId=" + district, type: "GET", success: function (data) {
            data = JSON.parse(data);
            let options = ' <option value="" selected >Please Choose...</option>';
            $.each(data, function (key, value) {
                options += '<option value=' + value.id + '>' + value.name + '</option>';
            });
            $("#profileyard-division").html(options)
            $("#profileyard-division").val(selectedValue)

        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });

}

function loadDivisionsAjaxFisherman(district, selectedValue) {
    $.ajax({
        url: "../division/list-by-district?districtId=" + district, type: "GET", success: function (data) {
            data = JSON.parse(data);
            let options = ' <option value="" selected >Please Choose...</option>';
            $.each(data, function (key, value) {
                options += '<option value=' + value.id + '>' + value.name + '</option>';
            });
            $("#profilefisherman-division").html(options)
            $("#profilefisherman-division").val(selectedValue)
            $("#profilefisherman-division").change()

        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });

}

function loadDivisionsAjaxScientific(district, selectedValue) {
    $.ajax({
        url: "../division/list-by-district?districtId=" + district, type: "GET", success: function (data) {
            data = JSON.parse(data);
            let options = ' <option value="" selected >Please Choose...</option>';
            $.each(data, function (key, value) {
                options += '<option value=' + value.id + '>' + value.name + '</option>';
            });
            $("#scientificenumerationrequest-division").html(options)
            $("#scientificenumerationrequest-division").val(selectedValue)
            $("#scientificenumerationrequest-division").change()

        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });

}

function loadDivisionsAjaxBatRegSpecial(district, selectedValue) {
    $.ajax({
        url: "../division/list-by-district?districtId=" + district, type: "GET", success: function (data) {
            data = JSON.parse(data);
            let options = ' <option value="" selected >Please Choose...</option>';
            $.each(data, function (key, value) {
                options += '<option value=' + value.id + '>' + value.name + '</option>';
            });
            $("#fishermanregisterdboatlicense-division").html(options)
            $("#fishermanregisterdboatlicense-division").val(selectedValue)
            $("#fishermanregisterdboatlicense-division").change()

        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });

}

function loadDivisionsAjaxOfficer(district, selectedValue) {
    $.ajax({
        url: "../division/list-by-district?districtId=" + district, type: "GET", success: function (data) {
            data = JSON.parse(data);
            let options = ' <option value="" selected >Please Choose...</option>';
            $.each(data, function (key, value) {
                options += '<option value=' + value.id + '>' + value.name + '</option>';
            });
            $("#profileofficers-division").html(options)
            $("#profileofficers-division").val(selectedValue)
            $("#profileofficer-division").html(options)
            $("#profileofficer-division").val(selectedValue)

        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });

}

function loadDivisionsAjaxSkipper(district, selectedValue) {
    $.ajax({
        url: "../division/list-by-district?districtId=" + district, type: "GET", success: function (data) {
            data = JSON.parse(data);
            let options = ' <option value="" selected >Please Choose...</option>';
            $.each(data, function (key, value) {
                options += '<option value=' + value.id + '>' + value.name + '</option>';
            });
            $("#skipper-fisheries_division").html(options)

        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });

}

function loadProgramsAjaxSkipper(institute, selectedValue) {
    $.ajax({
        url: "../traning-institute-programs/list-by-institute?institute=" + institute,
        type: "GET",
        success: function (data) {
            data = JSON.parse(data);
            let options = ' <option value="" selected >Please Choose...</option>';
            $.each(data, function (key, value) {
                options += '<option value=' + value.id + '>' + value.name + '</option>';
            });
            $("#skippertranings-program_name").html(options)

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });

}


function getDivisionAjax(division, selectedValue) {

}

function loadLandingSiteAjaxFisherman(division, selectedValue) {

    $.ajax({
        url: "../landing-site/list-by-division?divisionId=" + division, type: "GET", success: function (data) {
            data = JSON.parse(data);
            var options = ' <option value="" selected >Please Choose...</option>';
            $.each(data, function (key, value) {
                options += '<option value=' + value.id + '>' + value.name + '</option>';
            });

            if ($('.fisherman_profile').length) {
                $("#profilefisherman-landing_site").html(options)
                $("#profilefisherman-landing_site").val(selectedValue)
            }

        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });

}

function loadLandingSiteAjaxScientific(division, selectedValue) {

    $.ajax({
        url: "../landing-site/list-by-division?divisionId=" + division, type: "GET", success: function (data) {
            data = JSON.parse(data);
            var options = ' <option value="" selected >Please Choose...</option>';
            $.each(data, function (key, value) {
                options += '<option value=' + value.id + '>' + value.name + '</option>';
            });

            $("#scientificenumerationrequest-landing_site").html(options)
            $("#scientificenumerationrequest-landing_site").val(selectedValue)


        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });

}

function loadLandingSiteAjaxBatRegSpecial(division, selectedValue) {

    $.ajax({
        url: "../landing-site/list-by-division?divisionId=" + division, type: "GET", success: function (data) {
            data = JSON.parse(data);
            var options = ' <option value="" selected >Please Choose...</option>';
            $.each(data, function (key, value) {
                options += '<option value=' + value.id + '>' + value.name + '</option>';
            });

            $("#fishermanregisterdboatlicense-landing_site").html(options)
            $("#fishermanregisterdboatlicense-landing_site").val(selectedValue)


        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });

}

function loadLandingSiteAjaxNationalLicense(division, selectedValue) {

    $.ajax({
        url: "../landing-site/list-by-division?divisionId=" + division, type: "GET", success: function (data) {
            data = JSON.parse(data);
            var options = ' <option value="" selected >Please Choose...</option>';
            $.each(data, function (key, value) {
                options += '<option value=' + value.id + '>' + value.name + '</option>';
            });

            if ($('.fisherman_profile').length) {
                $("#profilefisherman-landing_site").html(options)
                $("#profilefisherman-landing_site").val(selectedValue)
            }

        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });

}

$('#status-approval-btn').on('click', function (e) {
    e.preventDefault();
    if ($("#status-approval").val() === "reject" && $("#remarks-approval").val() === "") {
        $("#remarks-approval").focus()
        swal("Validation Error!", "Remarks is mandatory while you are rejecting the application", "error");
        return false;
    }
    swal({
        title: "Are you sure?",
        text: "You will not be able to undo this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: true,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {

            $("#status-approval-btn").closest('form').submit()
        }
        return false;
    });

})

$(document).on('click', '#catch-request-submit-btn', function (e) {
    e.preventDefault();

    swal({
        title: 'Success!',
        text: 'You Submit Catch Data Successfully!',
        type: 'success'
    }, function () {
        window.location.href = 'index';    });
});

$(document).on('click', '#exporter-catch-request-submit-btn', function (e) {
    e.preventDefault();

    swal({
        title: 'Success!',
        text: 'You Submit Your Export Data Quota Successfully!',
        type: 'success'
    }, function () {
        window.location.href = 'exporter-view';    });
});

function loadBoatDesignByYard(yard, selectedValue) {
    $.ajax({
        url: "../boat-design/list-by-yard?yard=" + yard, type: "GET", success: function (data) {
            data = JSON.parse(data);
            var options = ' <option value="" selected >Please Choose...</option>';
            $.each(data, function (key, value) {
                options += '<option data-cat="' + value.boat_category + '" value=' + value.id + '>' + value.design_notation + '</option>';
            });

            $("#boatnumbers-boat_design").html(options)
            $("#boatnumbers-boat_design").val(selectedValue)

        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });
}

function loadBoatBoatData(id, callback) {
    $.ajax({
        url: "../boat-numbers/boatdetails?id=" + id, type: "GET", success: function (data) {
            data = JSON.parse(data);
            console.log(data)
            callback(data)
        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });
}

function loadOwnerData(data) {

    $(".ownerName").html("")
    $(".ownerName").html(blankForNull(data.name))
    $(".ownerNIC").html("")
    $(".ownerNIC").html(blankForNull(data.nic))
    $(".ownerImage").html("")
    $(".ownerImage").html(data.image)
    $(".ownerDistrict").html("")
    $(".ownerDistrict").html(data.district)
    $(".ownerDivision").html("")
    $(".ownerDivision").html(data.division)
    $(".currentOwner").show()

}

function blankForNull(s) {
    return s == null ? "" : s;
}

$("#boatnumbers-boat_design").on("change", function () {
    $("#boatnumbers-boat_category").val($("#boatnumbers-boat_design").find(':selected').data("cat"))

})

$("#boatnumbertransferrequest-boat_number").on("change", function () {
    $("#boatnumbers-boat_category").val($("#boatnumbers-boat_design").find(':selected').data("cat"))
    loadBoatBoatData($("#boatnumbertransferrequest-boat_number").val(), loadOwnerData)
})
$(".skipper-license-view").on("click", function () {
    $("#licence-view-modal").modal("show")
})
$("#add-training-programs").on("click", function () {
    newRow = '<tr>\n' +
        '                <th scope="row"><input type="hidden" name="Tranings[institute][]" value="' + $("#skippertranings-institute").val() + '"> ' + $("#skippertranings-institute option:selected").text() + ' </th>\n' +
        '                <td><input type="hidden" name="Tranings[program_name][]" value="' + $("#skippertranings-program_name").val() + '"> ' + $("#skippertranings-program_name option:selected").text() + ' </td>\n' +
        '                <td><input type="hidden" name="Tranings[training_period][]" value="' + $("#skippertranings-training_period").val() + '"> ' + $("#skippertranings-training_period").val() + ' </td>\n' +
        '                <td><input type="hidden" name="Tranings[date_certified][]" value="' + $("#skippertranings-date_certified").val() + '"> ' + $("#skippertranings-date_certified").val() + '</td>\n' +
        '                <td></td>\n' +
        '                <td><a href="#" class="btn btn-danger">Remove</a></td>\n' +
        '            </tr>'

    $(".programs").append(newRow)
})


$("#addnewMebmer").on("click", function () {
    newRow = '<div class="row mb-3">' +
        '        <div class="col-lg-1">' +
        '        M1' +
        '        </div>' +
        '    <div class="col-lg-5">' +
        '        <input type="text" class="form-control" name="crewName[]">' +
        '    </div>' +
        '    <div class="col-lg-5">' +
        '        <input type="text" class="form-control" name="crewNIC[]">' +
        '    </div>' +
        '    <div class="col-lg-1"></div>' +
        '</div>'

    $("#crewMemberContainer").append(newRow)
})

function loadExtraGearDataAjax(id) {
    if (id == null) {
        return
    }
    $.ajax({
        url: "../gear-type-extra-data/list-by-gear?gear=" + id, type: "GET", success: function (data) {
            data = JSON.parse(data);
            var options = '';
            $.each(data, function (key, value) {
                options += '<div class="col-lg-6">'
                    + '<div class="form-group">'
                    + '<label>' + value.name + '</label>'
                    + '<input   id="extra_' + value.slug + '"  name="extra[' + value.slug + ']" class="form-control">'

                    + '</div>'
                    + ' </div>';
            });
            $(".extra-data").html(options)

        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            // alert('Error Publish data');
        }
    });
}

$(document).ready(function () {
    if ($('.fish-checkbox').length) {
        const search = document.getElementsByClassName("searchbox");
        const labels = document.querySelectorAll("#chboxlist-fish > label");
        console.log({labels})

        $('.searchbox').on('keyup', function () {
            var query = this.value;
            console.log($('[class="ckbox"]'))
            $('.ckbox').each(function (i, elem) {
                console.log(elem)

                if (elem.class.contents(query) !== -1) {
                    elem.style.display = 'block';
                } else {
                    elem.style.display = 'none';
                }
            });
        });
    }
    if ($('.fisherman_profile').length) {
        $("#profilefisherman-district").change()


        $(".pay-boat-number").on("click", function () {
            $("#paynow-modal").modal("show");
            $("#boat-id").val($(this).data("boat"))
        })

        $(".pay-boat-register").on("click", function () {
            $("#paynow-boat-reg-modal").modal("show");
            $("#boat-id-reg").val($(this).data("boat"))
        })
        $(".pay-skipper-licence").on("click", function () {
            $("#paynow-skipper-licence-modal").modal("show");
            $("#skipper_id").val($(this).data("skipper"))
        })
        $(".pay-national-licence").on("click", function () {
            $("#paynow-national-licence-modal").modal("show");
            $("#national_id").val($(this).data("national"))
        })
        $(".pay-yard-licence").on("click", function () {
            $("#paynow-yard-modal").modal("show");
            $("#yard-id").val($(this).data("yard"))
        })
        $(".pay-highseas-licence").on("click", function () {
            $("#paynow-highseas-licence-modal").modal("show");
            $("#highseas_id").val($(this).data("highseas"))
        })
        $(".pay-boat-number-submit").on("click", function () {

            $.ajax({
                url: "../boat-numbers/payment?id=" + $("#boat-id").val() + "&ref=" + $("#ref").val(),
                type: "GET", success: function (data) {
                    data = JSON.parse(data);
                    if (data === true) {
                        $("#paynow-modal").modal("hide");
                        $.pjax.reload({container: "#boat-numbers", async: false});
                    }


                }, error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    alert('Error Publish data');
                }
            });
        })
        $(".pay-boat-register-submit").on("click", function () {

            $.ajax({
                url: "../boat-registration/payment?id=" + $("#boat-id-reg").val() + "&ref=" + $("#ref-reg").val(),
                type: "GET", success: function (data) {
                    data = JSON.parse(data);
                    if (data === true) {
                        $("#paynow-boat-reg-modal").modal("hide");
                        $.pjax.reload({container: "#boat-numbers", async: false});
                    }


                }, error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    alert('Error Publish data');
                }
            });
        })
        $(".pay-yard-submit").on("click", function () {

            $.ajax({
                url: "../yard/payment?id=" + $("#yard-id").val() + "&ref=" + $("#ref-yard").val(),
                type: "GET", success: function (data) {
                    data = JSON.parse(data);
                    if (data === true) {
                        $("#paynow-yard-modal").modal("hide");
                        $.pjax.reload({container: "#boat-numbers", async: false});
                    }


                }, error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    alert('Error Publish data');
                }
            });
        })
        $(".pay-skipper-submit").on("click", function () {

            $.ajax({
                url: "../skipper/payment?id=" + $("#skipper_id").val() + "&ref=" + $("#ref-skipper").val(),
                type: "GET", success: function (data) {
                    data = JSON.parse(data);
                    if (data === true) {
                        $("#paynow-skipper-licence-modal").modal("hide");
                        $.pjax.reload({container: "#boat-numbers", async: false});
                    }


                }, error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    alert('Error Publish data');
                }
            });
        })
        $(".pay-national-submit").on("click", function () {

            $.ajax({
                url: "../national-license/payment?id=" + $("#national_id").val() + "&ref=" + $("#ref-national").val(),
                type: "GET", success: function (data) {
                    data = JSON.parse(data);
                    if (data === true) {
                        $("#paynow-national-licence-modal").modal("hide");
                        $.pjax.reload({container: "#boat-numbers", async: false});
                    }


                }, error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    alert('Error Publish data');
                }
            });
        })
        $(".pay-highseas-submit").on("click", function () {

            $.ajax({
                url: "../highseas-license/payment?id=" + $("#highseas_id").val() + "&ref=" + $("#ref-highseas").val(),
                type: "GET", success: function (data) {
                    data = JSON.parse(data);
                    if (data === true) {
                        $("#paynow-national-licence-modal").modal("hide");
                        $.pjax.reload({container: "#boat-numbers", async: false});
                    }


                }, error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    alert('Error Publish data');
                }
            });
        })


    }


    $("#payment-approval-btn").on("click", function () {
        $.ajax({
            url: "../boat-numbers/payment-approve?id=" + $("#boat-id").val(),
            type: "GET", success: function (data) {
                data = JSON.parse(data);
                if (data === true) {
                    $.pjax.reload({container: "#boat-numbers-log", async: false});
                }


            }, error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                alert('Error Publish data');
            }
        });
    })

    $("#highseaslicense-fisherman_id").on("change", function () {
        // var url = document.location.href+"?success=yes";
        // return h + (h.indexOf('?') != -1 ? "&ajax=1" : "?ajax=1");

        window.history.replaceState(null, null, "?fisherman=" + $("#highseaslicense-fisherman_id").val());
        $.pjax.reload({container: "#boat_reg_no", async: false});
    })

    $("#reg-payment-approval-btn").on("click", function () {
        $.ajax({
            url: "../boat-registration/payment-approve?id=" + $("#reg-boat-id").val(),
            type: "GET", success: function (data) {
                data = JSON.parse(data);
                if (data === true) {
                    $.pjax.reload({container: "#boat-numbers-log", async: false});
                }


            }, error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                alert('Error Publish data');
            }
        });
    })
    $("#yard-payment-approval-btn").on("click", function () {
        $.ajax({
            url: "../yard/payment-approve?id=" + $("#reg-yard-id").val(),
            type: "GET", success: function (data) {
                data = JSON.parse(data);
                if (data === true) {
                    $.pjax.reload({container: "#boat-numbers-log", async: false});
                }


            }, error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                alert('Error Publish data');
            }
        });
    })
    $("#skipper-payment-approval-btn").on("click", function () {
        $.ajax({
            url: "../skipper/payment-approve?id=" + $("#skipper").val(),
            type: "GET", success: function (data) {
                data = JSON.parse(data);
                if (data === true) {
                    $.pjax.reload({container: "#boat-numbers-log", async: false});
                }


            }, error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                alert('Error Publish data');
            }
        });
    })
    $("#national-payment-approval-btn").on("click", function () {
        $.ajax({
            url: "../national-license/payment-approve?id=" + $("#national_license_id").val(),
            type: "GET", success: function (data) {
                data = JSON.parse(data);
                if (data === true) {
                    $.pjax.reload({container: "#boat-numbers-log", async: false});
                }


            }, error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                alert('Error Publish data');
            }
        });
    })
    $("#highseas-payment-approval-btn").on("click", function () {
        $.ajax({
            url: "../highseas-license/payment-approve?id=" + $("#highseas_license_id").val(),
            type: "GET", success: function (data) {
                data = JSON.parse(data);
                if (data === true) {
                    $.pjax.reload({container: "#boat-numbers-log", async: false});
                }


            }, error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                alert('Error Publish data');
            }
        });
    })
    $("#nationallicense-main_gear_type").on("change", function () {
        var oldURL = window.location.href;
        var type = "Active";
        var href = new URL(oldURL);
        href.searchParams.set('mainGear', $("#nationallicense-main_gear_type").val());
        console.log(href.toString()); // https://google.com/?q=dogs
        if (history.pushState) {
            //     if (oldURL)
            //     // var newUrl = oldURL + "&mainGear=" + $("#nationallicense-main_gear_type").val();
            window.history.pushState({path: href.toString()}, '', href.toString());
            //     window.history.replaceState(null, null, "?mainGear=" + $("#highseaslicense-fisherman_id").val());
            //
        }
        $.pjax.reload({container: "#boat-numbers-log", async: false});

        return false;
    })
    $("#highseaslicense-main_gear_type").on("change", function () {
        var oldURL = window.location.href;
        var type = "Active";
        var href = new URL(oldURL);
        href.searchParams.set('mainGear', $("#highseaslicense-main_gear_type").val());
        console.log(href.toString()); // https://google.com/?q=dogs
        if (history.pushState) {
            //     if (oldURL)
            //     // var newUrl = oldURL + "&mainGear=" + $("#nationallicense-main_gear_type").val();
            window.history.pushState({path: href.toString()}, '', href.toString());
            //     window.history.replaceState(null, null, "?mainGear=" + $("#highseaslicense-fisherman_id").val());
            //
        }
        $.pjax.reload({container: "#boat-numbers-log", async: false});

        return false;
    })


})
$(function () {
    $("#btnSave").click(function () {
        html2canvas(document.querySelector("#licensePrint")).then(canvas => {
            console.log(canvas);
            $("#licensePrint").html(canvas);
        });

    });
});

$("#departurerequests-boat_no").on("change", function () {
    loadBoatBoatDataForDeparture($("#departurerequests-boat_no").val())
})
$("#departurerequests-skipper_nic").on("change", function () {
    loadBoatSkipperDataForDeparture($("#departurerequests-skipper_nic").val())
})
$("#crewMemberAdd").off("click").on("click", function (event) {
    event.preventDefault();
    addCrewMember();
});

function addCrewMember() {
    let count = parseInt($("#memberCount").val(), 10);

    if (isNaN(count)) {
        count = $("#crewMemberContainer .crew-member-row").length + 1;
    }

    const options =
        '<div class="row mb-3 crew-member-row">' +

            '<div class="col-lg-1">' +
                count +
            '</div>' +

            '<div class="col-lg-3">' +
                '<input ' +
                    'type="text" ' +
                    'onfocusout="loadBoatCrewDataForDeparture($(this).val(), $(this).attr(\'id\'))" ' +
                    'class="form-control crewNic" ' +
                    'id="crewnic' + count + '" ' +
                    'name="crewNIC[]" ' +
                    'placeholder="NIC">' +
            '</div>' +

            '<div class="col-lg-4">' +
                '<input ' +
                    'type="text" ' +
                    'class="form-control crewName" ' +
                    'id="crewname' + count + '" ' +
                    'name="crewName[]" ' +
                    'placeholder="Name">' +
            '</div>' +

            '<div class="col-lg-4">' +
                '<input ' +
                    'type="tel" ' +
                    'class="form-control crewMobile" ' +
                    'id="crewmobile' + count + '" ' +
                    'name="crewMobile[]" ' +
                    'placeholder="Mobile Number" ' +
                    'maxlength="20">' +
            '</div>' +

        '</div>' +
        '<hr>';

    $("#crewMemberContainer").append(options);
    $("#memberCount").val(count + 1);
}
function loadSkipper(id) {
    addCrewMember();
}


function loadBoatBoatDataForDeparture(id) {
    $.ajax({
        url: "../departure/boatdetails?id=" + id, type: "GET", success: function (data) {
            data = JSON.parse(data);
            console.log(data)
            if (data.alters != "") {
                $("#boatAlterts").html("")
                $("#boatAlterts").html(data.alters)
                $("#boatAlterts").show()

            } else {
                $("#boatAlterts").html("")

                $("#boatAlterts").hide()
            }
            if (data.status != "") {
                $("#departurerequests-boat_no").val("")
                $("#departurerequests-boat_no").focus()
                swal(data.status, "", "error");
                return false;
            }

            $("#departurerequests-owner").val(data.ownerName);
            $("#departurerequests-contact_no").val(data.ownerMobile);
            $("#departurerequests-email").val(data.ownerEmail);
            $("#departurerequests-owner").focus();
            $("#departurerequests-contact_no").focus();
            $("#departurerequests-email").focus();
            $("#departurerequests-boat_name").focus();

            $("#departurerequests-national_license_no").val(data.nationalLicence);
            $("#departurerequests-hs_license_no").val(data.highseasLicence);
        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });
}

function loadBoatSkipperDataForDeparture(id) {
    console.log({id})
    if (id === "") {
        return false;
    }
    $.ajax({
        url: "../departure/getskipper?id=" + id, type: "GET", success: function (data) {
            data = JSON.parse(data);
            console.log(data)
            if (data.status != "") {
                $("#departurerequests-skipper_nic").val("")
                $("#departurerequests-skipper_nic").focus()
                swal(data.status, "", "error");
                return false;
            }
            // if (data.nic == "") {
            //     swal("This NIC is not available", "", "error");
            // }
            // $("#departurerequests-skipper_nic").val(data.nic);
            $("#departurerequests-skipper_no").val(data.id);
            $("#departurerequests-skipper").val(data.name);

        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });
}

function loadBoatCrewDataForDeparture(value, tbId) {
    if (value === "") {
        return false;
    }
    console.log({tbId})
    $.ajax({
        url: "../departure/getskipper?id=" + value, type: "GET", success: function (data) {
            data = JSON.parse(data);
            console.log(data)
            if (data.status != "") {
                $("#" + tbId).val("")
                $("#" + tbId).focus()
                swal(data.status, "", "error");
                return false;
            }
            tbName = tbId.replace('nic', 'name')
            checkDuplicates()
            if (data.name != "") {
                $("#" + tbName).val(data.name);
            }


        }, error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error Publish data');
        }
    });
}

function checkDuplicates() {
    let values = [];
    let isValid = true;
    let errorMessage = '';
    let dupthis
    // Collect all values from crewNic inputs
    $('.crewNic').each(function () {
        let value = $(this).val().trim();
        if (value !== '') {
            if (values.includes(value)) {
                isValid = false;
                dupthis = this;
                errorMessage = 'Duplicate NIC detected. This NIC has already been entered. ' + value;
            } else {
                values.push(value);
            }
        }
    });

    // Display error message if duplicates found
    if (!isValid) {
        swal(errorMessage, "", "error");
        $(dupthis).val("")
        tbName = dupthis.replace('nic', 'name')
        $("#" + tbName).val("");
    } else {
        $('#errorMessage').text('');
    }

    return isValid;
}

$('.crewNic').focusout(function () {
    var value = $(this).val();
    var id = $(this).attr('id');
    // console.log('ID: ' + id + ', Value: ' + value);
    loadBoatCrewDataForDeparture(value, id)
});

$(document).ready(function () {
    $('.toggle-password').click(function () {
        var passwordField = $(this).closest('.input-group').find('input');
        var icon = $(this).find('i');
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    // Function to validate dates
    function validateDates() {
        // Get the values of the two date inputs
        var startDate = $('#meaboatregistration-commenced_construction_date').val();
        var endDate = $('#meaboatregistration-completed_construction_date').val();

        // Clear previous error messages
        $('#dateError').text('');

        // Check if both dates are provided
        if (startDate === '' || endDate === '') {
            $('#dateError').text('Please fill in both dates.');
            swal("Please fill in both dates.");

            return false;
        }

        // Convert string dates to Date objects
        var start = new Date(startDate);
        var end = new Date(endDate);

        // Check if dates are valid
        if (isNaN(start.getTime()) || isNaN(end.getTime())) {
            $('#dateError').text('Please enter valid dates.');
            swal("Please enter valid dates.");
            return false;
        }

        // Check if end date is greater than start date
        if (end <= start) {
            $('#dateError').text('End date must be greater than start date.');
            swal("Construction Completed Date must be greater than start date.");
            return false;
        }

        // If all validations pass
        return true;
    }

    // Trigger validation on form submission
    // $('#dateForm').on('submit', function(e) {
    //     if (!validateDates()) {
    //         e.preventDefault(); // Prevent form submission if validation fails
    //     }
    // });

    // Optional: Trigger validation on date input change
    $('#meaboatregistration-completed_construction_date').on('change', validateDates);

    $('#meaboatregistration-beam').on('focusout', function () {
        var value1 = $(this).val();
        validateDatesvalues(value1)
    });
    $('#meaboatregistration-depth').on('focusout', function () {
        var value1 = $(this).val();
        validateDatesvalues(value1)
    });
    $('#meaboatregistration-draught').on('focusout', function () {
        var value1 = $(this).val();
        validateDatesvalues(value1)
    });

    $('#meaboatregistration-compass').on('focusout', function () {
        var value1 = $(this).val();
        calculateTotal(value1)
    });
    $('#meaboatregistration-radio').on('focusout', function () {
        var value1 = $(this).val();
        calculateTotal(value1)
    });
    $('#meaboatregistration-gps').on('focusout', function () {
        var value1 = $(this).val();
        calculateTotal(value1)
    });
    $('#meaboatregistration-radar').on('focusout', function () {
        var value1 = $(this).val();
        calculateTotal(value1)
    });

    $('#meaboatregistration-ais').on('focusout', function () {
        var value1 = $(this).val();
        calculateTotal(value1)
    });

    $('#meaboatregistration-winch').on('focusout', function () {
        var value1 = $(this).val();
        calculateTotal(value1)
    });
    $('#meaboatregistration-vms').on('focusout', function () {
        var value1 = $(this).val();
        calculateTotal(value1)
    });
    $('#meaboatregistration-gillnets').on('focusout', function () {
        var value1 = $(this).val();
        calculateTotal(value1)
    });
    $('#meaboatregistration-longlines').on('focusout', function () {
        var value1 = $(this).val();
        calculateTotal(value1)
    });
    $('#meaboatregistration-other').on('focusout', function () {
        var value1 = $(this).val();
        calculateTotal(value1)
    });

    function validateDatesvalues(value1) {
        // Get value from the other textbox
        var value2 = $('#meaboatregistration-overall_length').val();

        // Convert to numbers for comparison
        value1 = parseFloat(value1) || 0;
        value2 = parseFloat(value2) || 0;

        // Compare values and show alert
        if (value1 > value2) {
            swal("This length cannot be greeter than Overall Length");
        }
    }
    function calculateTotal(value1) {
        // Get value from the other textbox
        var value2 = $('#meaboatregistration-total').val();

        // Convert to numbers for comparison
        value1 = parseFloat(value1) || 0;
        value2 = parseFloat(value2) || 0;

        // Compare values and show alert
        $('#meaboatregistration-total').val(value1+value2)
    }

});


$(document).ready(function () {
    // Add new row
    $('#addRow').click(function () {
        var row = $('.template').clone().removeClass('template').show();
        $('#dynamicTable tbody').append(row);
    });

    // Remove row
    $(document).on('click', '.removeRow', function () {
        if ($('#dynamicTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            alert('At least one row is required.');
        }
    });
});

$(document).ready(function () {
    // Add new row
    $('#addRows').click(function () {
        var row = $('.templates').clone().removeClass('templates').show();
        $('#dynamicTables tbody').append(row);
    });

    // Remove row
    $(document).on('click', '.removeRows', function () {
        if ($('#dynamicTables tbody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            alert('At least one row is required.');
        }
    });
});
$(document).ready(function () {
    function calculateTotal() {
        if ($('.total-weight').length === 0) return;
        var total = 0;

        // Select ALL inputs inside any element with class "total-weight"
        $('.total-weight').each(function () {
            var val = parseFloat($(this).val());
            if (!isNaN(val)) {
                total += val;
            }
        });
        // Update the display (change #grand-total to your target element)
        $('#applicationexportbechedemer-charges').val(total * 20);
    }

    // Initial calculation on page load
    calculateTotal();

    // Recalculate whenever any relevant input loses focus
    $('.total-weight').on('change', calculateTotal);
    // $(document).on('change', '.total-weight', function() {
    //     calculateTotal();
    // });
});

$(document).ready(function () {
    // Debounce function for performance
    function debounce(func, wait) {
        let timeout;
        return function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, arguments), wait);
        };
    }

    // Search handler
    const handleSearch = debounce(function () {
        let searchTerm = $('#country-search').val().toLowerCase().trim();
        console.log('Search term:', searchTerm); // Debug

        $('.checkbox-list .checkbox').each(function () {
            // Find the label within the checkbox div
            let label = $(this).find('label').first();
            let labelText = label.length ? label.text().toLowerCase().trim() : '';
            console.log('Label text:', labelText); // Debug

            if (searchTerm === '' || labelText.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }, 300);

    // Bind search input
    $('#country-search').on('input', handleSearch);

    // Clear search
    $('#clear-search').on('click', function () {
        $('#country-search').val('');
        $('.checkbox-list .checkbox').show();
    });


    // function checkDuplicates() {
    //     let values = [];
    //     let isValid = true;
    //     let errorMessage = '';
    //     let dupthis
    //     // Collect all values from crewNic inputs
    //     $('.crewNic').each(function() {
    //         let value = $(this).val().trim();
    //         if (value !== '') {
    //             if (values.includes(value)) {
    //                 isValid = false;
    //                 dupthis=this;
    //                 errorMessage = 'Duplicate NIC value found: ' + value;
    //                 $(this).addClass('error');
    //             } else {
    //                 values.push(value);
    //                 $(this).removeClass('error');
    //             }
    //         }
    //     });
    //
    //     // Display error message if duplicates found
    //     if (!isValid) {
    //         $(dupthis).val("")
    //         alert(errorMessage);
    //     } else {
    //         $('#errorMessage').text('');
    //     }
    //
    //     return isValid;
    // }
    // // Validate on focus out
    // $('.crewNic').on('blur', function() {
    //     checkDuplicates();
    // });

    // $('#departure-submit').on('submit', function(e) {
    //     if (!validateCrewNic()) {
    //         e.preventDefault();
    //         alert('Please enter at least one crew NIC before submitting.');
    //     }
    // });
});

function validateCrewNic() {
    let hasAtLeastOneCrew = false;

    // Check if at least one crewNic input has a value
    $('.crewNic').each(function () {
        let value = $(this).val().trim();
        if (value !== '') {
            hasAtLeastOneCrew = true;
            $(this).removeClass('error');
        }
    });
    // Display error message if no crew NIC is entered
    if (!hasAtLeastOneCrew) {
        $('#errorMessage').text('Please enter at least one crew NIC.');
        alert('Please enter at least one crew NIC before submitting.');
        return false;
    } else {
        $('#errorMessage').text('');
        return true;
    }
}