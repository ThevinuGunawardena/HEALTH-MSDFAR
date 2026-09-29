let scientific = {};

/*
 * ==============================================================
 * SCIENTIFIC WIZARD SECURITY / ROUTING HELPERS
 * ==============================================================
 * - Persistent DB records use server-side SecurityHelper tokens.
 * - Unsaved crafts do not have DB records yet, so their URL key is
 *   an opaque client-generated key (c_<32 hex>) instead of an array
 *   index such as ?craft=0.
 * - Yii-generated URLs and the server-authenticated profile storage
 *   key are supplied by backend/views/scientific/_js_config.php.
 */

const scientificRequestCache = new Map();
const scientificRequestInFlight = new Map();

function getScientificConfig() {
    return window.scientificConfig || {routes: {}};
}

function scientificRoute(name, fallback) {
    const config = getScientificConfig();
    if (config.routes && typeof config.routes[name] === "string" && config.routes[name] !== "") {
        return config.routes[name];
    }
    return fallback || "";
}

function buildScientificUrl(baseUrl, params) {
    const query = $.param(params || {});
    if (!query) {
        return baseUrl;
    }
    return baseUrl + (baseUrl.indexOf("?") === -1 ? "?" : "&") + query;
}

function getScientificStorageKey() {
    const config = getScientificConfig();
    if (typeof config.storageKey === "string" && config.storageKey !== "") {
        return config.storageKey;
    }

    /* Backward-compatible fallback only. */
    return "sss" + (localStorage.getItem("profile_id") || "");
}

function loadScientificState() {
    const storageKey = getScientificStorageKey();
    const raw = localStorage.getItem(storageKey);

    if (!raw) {
        return {};
    }

    try {
        const parsed = JSON.parse(raw);
        return parsed && typeof parsed === "object" ? parsed : {};
    } catch (error) {
        console.error("Invalid scientific draft data. Resetting local draft.", error);
        localStorage.removeItem(storageKey);
        return {};
    }
}

function saveScientificState() {
    localStorage.setItem(
        getScientificStorageKey(),
        JSON.stringify(scientific || {})
    );
}

function createClientCraftKey() {
    const bytes = new Uint8Array(16);

    if (window.crypto && typeof window.crypto.getRandomValues === "function") {
        window.crypto.getRandomValues(bytes);
    } else {
        for (let i = 0; i < bytes.length; i++) {
            bytes[i] = Math.floor(Math.random() * 256);
        }
    }

    return "c_" + Array.from(bytes)
        .map((value) => value.toString(16).padStart(2, "0"))
        .join("");
}

function isClientCraftKey(value) {
    return /^c_[a-f0-9]{32}$/.test(String(value || ""));
}

function ensureCraftKeys() {
    if (!Array.isArray(scientific.craft)) {
        scientific.craft = [];
        return;
    }

    let changed = false;

    scientific.craft.forEach(function (craft) {
        if (!craft || typeof craft !== "object") {
            return;
        }

        if (!isClientCraftKey(craft.clientKey)) {
            craft.clientKey = createClientCraftKey();
            changed = true;
        }
    });

    if (changed) {
        saveScientificState();
    }
}

function findCraftIndexByKey(clientKey) {
    if (!Array.isArray(scientific.craft) || !isClientCraftKey(clientKey)) {
        return -1;
    }

    return scientific.craft.findIndex(function (craft) {
        return craft && craft.clientKey === clientKey;
    });
}

function getCurrentCraftKey() {
    const params = new URLSearchParams(window.location.search);
    return params.get("craft") || "";
}

function getCraftIndexFromUrl() {
    return findCraftIndexByKey(getCurrentCraftKey());
}

function requireCraftIndex() {
    const craft = getCraftIndexFromUrl();

    if (craft >= 0) {
        return craft;
    }

    const message = "The draft craft reference is invalid or no longer available.";
    console.warn(message);

    if (typeof swal === "function") {
        swal("Draft Craft Not Found", message, "error");
    }

    window.location.replace(
        scientificRoute("create", "create")
    );

    return null;
}

function escapeHtml(value) {
    return $("<div>").text(value == null ? "" : String(value)).html();
}

function setSelectOptions(selector, data, textBuilder, includePrompt = true) {
    const $select = $(selector);
    $select.empty();

    if (includePrompt) {
        $select.append(
            $("<option>", {
                value: "",
                text: "Please Choose..."
            })
        );
    }

    $.each(data || [], function (key, value) {
        $select.append(
            $("<option>", {
                value: value.id,
                text: textBuilder(value)
            })
        );
    });
}

function requestScientificJson(routeName, fallbackUrl, data, options) {
    options = options || {};
    data = data || {};

    const url = scientificRoute(routeName, fallbackUrl);
    const cacheKey = routeName + "|" + JSON.stringify(data);
    const cacheMs = typeof options.cacheMs === "number" ? options.cacheMs : 30000;
    const now = Date.now();

    if (cacheMs > 0 && scientificRequestCache.has(cacheKey)) {
        const cached = scientificRequestCache.get(cacheKey);
        if (now - cached.time < cacheMs) {
            return $.Deferred().resolve(cached.data).promise();
        }
        scientificRequestCache.delete(cacheKey);
    }

    if (scientificRequestInFlight.has(cacheKey)) {
        return scientificRequestInFlight.get(cacheKey);
    }

    const request = $.ajax({
        url: url,
        type: options.type || "GET",
        data: data,
        dataType: "json",
        timeout: 15000,
        headers: options.headers || {}
    });

    scientificRequestInFlight.set(cacheKey, request);

    request.done(function (response) {
        if (cacheMs > 0) {
            scientificRequestCache.set(cacheKey, {
                time: Date.now(),
                data: response
            });
        }
    });

    request.always(function () {
        scientificRequestInFlight.delete(cacheKey);
    });

    return request;
}

function logScientificAjaxError(label, xhr, textStatus, errorThrown) {
    console.error(label, {
        status: xhr && xhr.status,
        response: xhr && xhr.responseText,
        textStatus: textStatus,
        error: errorThrown
    });
}


function validateInputs(input) {
    let validationTypeString = ($(input).data("validation"))
    if (typeof validationTypeString == "undefined") {
        return true;
    }
    let isValid = true;
    let value = $(input).val()
    if (validationTypeString.includes("NULL") && value === null) {
        isValid = false;
    }
    if (validationTypeString.includes("EMPTY") && value === "") {
        isValid = false;
    }
    if (validationTypeString.includes("NUMBER") && value !== "" && !$.isNumeric(value)) {
        isValid = false;
    }
    if (isValid) {
        return true;
    }
    $(input).addClass("is-invalid")
    $(input).focus()
    return false
}

function loadFleet() {
    let fleetData;
    let fleetHtml;
    if (typeof scientific.fleet !== "undefined") {
        fleetHtml = ""
        const fleet = scientific.fleet;
        for (const i in fleet) {
            fleetData = fleet[i]
            fleetHtml += '<tr>\n' + '<td>' + (1 + parseInt(i)) + '</td>\n' + '<td>' + escapeHtml(fleetData.boat_type.value) + '</td>\n' + '<td>' + escapeHtml(fleetData.sub_category.value) + '</td>' + '<td>' + escapeHtml(fleetData.gear_type.value) + '</td>' + '<td>' + escapeHtml(fleetData.number) + '</td>' + '<td><button type="button" class="btn btn-danger remove-fleet btn-sm"  onclick="removeFleet(' + i + ')" data-id="' + i + '"><i class="fas fa-trash-alt"></i></button></td>\n' + '</tr>'
        }

        $(".fleet-table_body").html(fleetHtml)
    }
}

function boatGearAvailable(i) {
    if (
        typeof scientific.sampling === "undefined" ||
        typeof scientific.sampling[i] === "undefined" ||
        scientific.sampling[i] === null
    ) {
        return "disabled";
    }

    return "";
}

function lengthAvailable(i) {
    if (
        typeof scientific.catch === "undefined" ||
        typeof scientific.catch[i] === "undefined" ||
        scientific.catch[i] === null
    ) {
        return "disabled";
    }

    return "";
}

function loadSampling() {
    let craftHtml = "";

    if (!Array.isArray(scientific.craft)) {
        $(".sampling-table").html("");
        return;
    }

    ensureCraftKeys();

    scientific.craft.forEach(function (craftData, i) {
        if (!craftData || !isClientCraftKey(craftData.clientKey)) {
            return;
        }

        const clientKey = craftData.clientKey;
        const boatNumber = escapeHtml(craftData.boatNumber || "");

        const boatGearUrl = buildScientificUrl(
            scientificRoute("addSampleCraft", "addsamplecraft"),
            {craft: clientKey}
        );
        const catchUrl = buildScientificUrl(
            scientificRoute("catchData", "catchdata"),
            {craft: clientKey}
        );
        const lengthUrl = buildScientificUrl(
            scientificRoute("addLengthWeight", "addlengthweight"),
            {craft: clientKey}
        );
        const costUrl = buildScientificUrl(
            scientificRoute("addOperationCost", "addoperationcost"),
            {craft: clientKey}
        );

        craftHtml +=
            '<div class="row mb-5">' +
            '<div class="col-xl-3 col-lg-12 col-md-12">' +
            '<p>Boat number : <strong>' + boatNumber + '</strong></p>' +
            '</div>' +
            '<div class="col-xl-9 col-lg-12 col-md-12"><div class="row">' +
            '<div class="col-lg-3 mb-1">' +
            '<a href="' + escapeHtml(boatGearUrl) + '" class="btn btn-primary btn-block">Boat & Gear</a>' +
            '</div>' +
            '<div class="col-lg-4 mb-1">' +
            '<a href="' + escapeHtml(catchUrl) + '" class="btn btn-primary btn-block ' + boatGearAvailable(i) + '">Catch</a>' +
            '</div>' +
            '<div class="col-lg-4 mb-1">' +
            '<a href="' + escapeHtml(lengthUrl) + '" class="btn btn-primary btn-block ' + boatGearAvailable(i) + '">Length & Weight</a>' +
            '</div>' +
            '<div class="col-lg-3 mb-1">' +
            '<a href="' + escapeHtml(costUrl) + '" class="btn btn-primary btn-block ' + lengthAvailable(i) + '">Economic</a>' +
            '</div>' +
            '<div class="col-lg-2 mb-1">' +
            '<button type="button" class="btn btn-danger btn-block remove-sampling-craft btn-sm" ' +
            'data-craft-key="' + escapeHtml(clientKey) + '">' +
            '<i class="fas fa-trash-alt"></i></button>' +
            '</div>' +
            '</div></div></div>';
    });

    $(".sampling-table").html(craftHtml);

    $(".remove-sampling-craft")
        .off("click.scientific")
        .on("click.scientific", function () {
            removeSamplingCraft($(this).data("craft-key"));
        });
}

function loadCatch() {
    const craft = getCraftIndexFromUrl();
    let catchData;
    let catchHtml;
    if (typeof scientific.catch !== "undefined" && typeof scientific.catch[craft] !== "undefined" && typeof scientific.catch[craft]["gear_used_" + $("#gear_used").val()] !== "undefined") {

        $("#no_species_fish").val(scientific['catch'][craft]["gear_used_" + $("#gear_used").val()]['no_species_fish'])
        $("#weight_code").val(scientific['catch'][craft]["gear_used_" + $("#gear_used").val()]['weight_code'])
        $("#weight").val(scientific['catch'][craft]["gear_used_" + $("#gear_used").val()]['weight'])


        if (typeof scientific.catch[craft]["gear_used_" + $("#gear_used").val()]['details'] !== "undefined") {
            catchHtml = ""
            const catchDatalist = scientific.catch[craft]["gear_used_" + $("#gear_used").val()]['details'];
            for (const i in catchDatalist) {
                catchData = catchDatalist[i]
                catchHtml += ' <tr>' + ' <td>' + (1 + parseInt(i)) + '</td>' + '<td>' + escapeHtml(catchData.catch_species_name) + '</td>' + '<td>' + escapeHtml(catchData.catch_weight) + '</td><td><button type="button" class="btn btn-danger btn-sm"  onclick="removeCatch(' + i + ')" data-id="' + i + '"><i class="fas fa-trash-alt"></i></button></td></td>' + '</tr>'
            }

            $(".lengthWeight-table").html(catchHtml)
        } else {
            $(".lengthWeight-table").html("")
        }
    } else {
        $(".lengthWeight-table").html("")
    }
}

function loadLengthWeight() {
    const craft = getCraftIndexFromUrl();
    let lengthWeightData;
    let lengthWeightHtml;
    if (typeof scientific.lengthWeight !== "undefined" && typeof scientific.lengthWeight[craft] !== "undefined" && typeof scientific.lengthWeight[craft]["gear_used_" + $("#gear_used").val()] !== "undefined") {

        $("#no_species_fish").val(scientific['lengthWeight'][craft]["gear_used_" + $("#gear_used").val()]['no_species_fish'])
        $("#weight_code").val(scientific['lengthWeight'][craft]["gear_used_" + $("#gear_used").val()]['weight_code'])
        $("#weight").val(scientific['lengthWeight'][craft]["gear_used_" + $("#gear_used").val()]['weight'])


        if (typeof scientific.lengthWeight[craft]["gear_used_" + $("#gear_used").val()]['details'] !== "undefined") {
            lengthWeightHtml = ""
            const lengthWeightDatalist = scientific.lengthWeight[craft]["gear_used_" + $("#gear_used").val()]['details'];
            for (const i in lengthWeightDatalist) {
                lengthWeightData = lengthWeightDatalist[i]
                lengthWeightHtml += ' <tr>' + ' <td>' + (1 + parseInt(i)) + '</td>' + '<td>' + escapeHtml(lengthWeightData.lw_species_name) + '</td>' + '<td>' + escapeHtml(lengthWeightData.lw_weight) + '</td>' + '<td>' + escapeHtml(lengthWeightData.lw_length) + '</td><td><button type="button" class="btn btn-danger btn-sm"  onclick="removeLengthWeight(' + i + ')" data-id="' + i + '"><i class="fas fa-trash-alt"></i></button></td></td>' + '</tr>'
            }

            $(".lengthWeight-table").html(lengthWeightHtml)
        } else {
            $(".lengthWeight-table").html("")
        }
    } else {
        $(".lengthWeight-table").html("")
    }
}

function loadOperationCostCatch() {
    const craft = getCraftIndexFromUrl();
    if (typeof scientific.operationCost !== "undefined" && typeof scientific.operationCost[craft] !== "undefined" && typeof scientific.operationCost[craft]['catch'] !== "undefined") {
        let operationCostCatchHtml = ""
        const catchData = scientific.operationCost[craft]['catch'];
        for (const i in catchData) {
            const operationCostCatch = catchData[i]
            operationCostCatchHtml += ' <tr>' +
                ' <td>' + (1 + parseInt(i)) + '</td>' +
                '<td>' + (operationCostCatch.trash === true ? "Trash" : " ") + " - " + escapeHtml(operationCostCatch.species_name) + '</td>' +
                '<td>' + escapeHtml(operationCostCatch.export_qty) + 'Kg / LKR ' + escapeHtml(operationCostCatch.export_val) + '</td>' +
                '<td>' + escapeHtml(operationCostCatch.local_qty) + 'Kg / LKR' + escapeHtml(operationCostCatch.local_val) + '</td>' +
                '<td> ' + escapeHtml(operationCostCatch.discard_qty) + 'Kg / LKR' + escapeHtml(operationCostCatch.discard_val) + '</td>' +
                '<td><button type="button" class="btn btn-danger btn-sm"  onclick="removeOperationCostCatch(' + i + ')" data-id="' + i + '"><i class="fas fa-trash-alt"></i></button></td>' +
                '</tr>'
        }

        $(".catch-table").html(operationCostCatchHtml)
    }
}

function loadOperationCost() {
    console.log("loadOperationCost")
    const craft = getCraftIndexFromUrl();
    if (typeof scientific.operationCost !== "undefined" && typeof scientific.operationCost[craft] !== "undefined") {
        const operationCost = scientific.operationCost[craft];
        console.log({operationCost})
        $("#fuel_qty").val(operationCost.fuel_qty)
        $("#fuel_val").val(operationCost.fuel_val)
        $("#ice_qty").val(operationCost.ice_qty)
        $("#ice_val").val(operationCost.ice_val)
        $("#bait_qty").val(operationCost.bait_qty)
        $("#bait_val").val(operationCost.bait_val)
        $("#salt_qty").val(operationCost.salt_qty)
        $("#salt_val").val(operationCost.salt_val)
        $("#labour_cost").val(operationCost.labour_cost)
        $("#food_water").val(operationCost.food_water)
        $("#others").val(operationCost.others)
        $("#remarks").val(operationCost.remarks)

    }
}

function addSamplingData() {

    if (!validateInputs($("#fishery_type")) || !validateInputs($("#sub_cat2")) || !validateInputs($("#hp"))
        || !validateInputs($("#no_crew")) || !validateInputs($("#gear_set_time")) || !validateInputs($("#days"))
        || !validateInputs($("#hours")) || !validateInputs($("#dep_date")) || !validateInputs($("#dep_time"))
        || !validateInputs($("#dep_fi_district")) || !validateInputs($("#dep_fi_division")) || !validateInputs($("#arrival_date"))
        || !validateInputs($("#dep_fi_division")) || !validateInputs($("#remarks")) || !validateInputs($("#main_gear"))
        || !validateInputs($("#main_target_species_main")) || !validateInputs($("#main_operatoin_no"))
        || !validateInputs($("#main_days")) || !validateInputs($("#main_hours")) || !validateInputs($("#main_fishing_depth"))
        || !validateInputs($("#main_g_code"))

    ) {
        return false;
    }

    if ($("#Second_gear").val() !== "") {
        if (!validateInputs($("#Second_operatoin_no")) || !validateInputs($("#Second_target_species_main")) || !validateInputs($("#Second_fishing_depth")) || !validateInputs($("#Second_g_code"))) {
            return false;
        }
        if ($("#Second_days").val() === "0" && $("#Second_hours").val() === "0") {
            $("#Second_days").focus()
            swal("Validation Error!", "Days and hours both cannot be zero", "error");
            return false
        }
    }


    if ($("#Third_gear").val() !== "") {
        if (!validateInputs($("#Third_operatoin_no")) || !validateInputs($("#Third_target_species_main")) || !validateInputs($("#Third_fishing_depth")) || !validateInputs($("#Third_g_code"))) {
            return false;
        }
        if ($("#Third_days").val() === "0" && $("#Third_hours").val() === "0") {
            $("#Third_days").focus()
            swal("Validation Error!", "Days and hours both cannot be zero", "error");
            return false
        }
    }

    const craft = getCraftIndexFromUrl();
    if (craft < 0) {
        return false;
    }

    const sampling = {};
    sampling["craft"] = craft
    sampling["fishery_type"] = $("#fishery_type").val()
    sampling["sub_cat2"] = $("#sub_cat2").val()
    sampling["hp"] = $("#hp").val()
    sampling["no_crew"] = $("#no_crew").val()
    sampling["unloading-type"] = $('input[name="unloading-type"]:checked').val() !== "All" ? $("#event").val() : "All";
    sampling["gear_set_time"] = $("#gear_set_time").val()
    sampling["days"] = $("#days").val()
    sampling["hours"] = $("#hours").val()
    sampling["dep_date"] = $("#dep_date").val()
    sampling["dep_time"] = $("#dep_time").val()
    sampling["dep_fi_district"] = {
        "Id": $("#dep_fi_district").val(), 'value': $("#dep_fi_district option:selected").text()
    }
    sampling["dep_fi_division"] = {
        "Id": $("#dep_fi_division").val(), 'value': $("#dep_fi_division option:selected").text()
    }
    sampling["dep_landing_place"] = {
        "Id": $("#dep_landing_place").val(), 'value': $("#dep_landing_place option:selected").text()
    }
    // wether
    sampling["arrival_date"] = $("#arrival_date").val()
    sampling["remarks"] = $("#remarks").val()

    var weather = [];
    $('#weather input:checked').each(function () {
        weather.push(this.value);
    });
    sampling["weather"] = weather
    const gears = {}
    const mainGear = {}
    const secondGear = {}
    const thirdGear = {}
    mainGear["gear"] = {"Id": $("#main_gear").val(), 'value': $("#main_gear option:selected").text()}
    mainGear["target_species_main"] = {
        "Id": $("#main_target_species_main").val(), 'value': $("#main_target_species_main option:selected").text()
    }
    mainGear["operatoin_no"] = $("#main_operatoin_no").val()
    mainGear["days"] = $("#main_days").val()
    mainGear["hours"] = $("#main_hours").val()
    mainGear["fishing_depth"] = $("#main_fishing_depth").val()
    mainGear["g_code"] = $("#main_g_code").val()
    const extraMain = {}
    $(".extra-data-main :input").each(function () {
        const fieldName = this.name;
        extraMain[fieldName] = this.value;
    });
    mainGear["extra"] = extraMain

    secondGear["gear"] = {"Id": $("#Second_gear").val(), 'value': $("#Second_gear option:selected").text()}
    secondGear["target_species_main"] = {
        "Id": $("#Second_target_species_main").val(), 'value': $("#Second_target_species_main option:selected").text()
    }
    secondGear["operatoin_no"] = $("#Second_operatoin_no").val()
    secondGear["days"] = $("#Second_days").val()
    secondGear["hours"] = $("#Second_hours").val()
    secondGear["fishing_depth"] = $("#Second_fishing_depth").val()
    secondGear["g_code"] = $("#Second_g_code").val()
    const extraSecond = {}
    $(".extra-data-second :input").each(function () {
        const fieldName = this.name;
        extraSecond[fieldName] = this.value;
    });
    secondGear["extra"] = extraSecond
    thirdGear["gear"] = {"Id": $("#Third_gear").val(), 'value': $("#Third_gear option:selected").text()}
    thirdGear["target_species_main"] = {
        "Id": $("#Third_target_species_main").val(), 'value': $("#Third_target_species_main option:selected").text()
    }
    thirdGear["operatoin_no"] = $("#Third_operatoin_no").val()
    thirdGear["days"] = $("#Third_days").val()
    thirdGear["hours"] = $("#Third_hours").val()
    thirdGear["fishing_depth"] = $("#Third_fishing_depth").val()
    thirdGear["g_code"] = $("#Third_g_code").val()
    const extraThird = {}
    $(".extra-data-third :input").each(function () {
        const fieldName = this.name;
        extraThird[fieldName] = this.value;
    });
    thirdGear["extra"] = extraThird
    gears["mainGear"] = mainGear
    gears["secondGear"] = secondGear
    gears["thirdGear"] = thirdGear
    sampling["gears"] = gears

    if (typeof scientific["sampling"] == "undefined") {
        scientific['sampling'] = []
    }
    scientific['sampling'][craft] = sampling
    saveScientificState();

    return true
}

function addCatch() {
    const craft = getCraftIndexFromUrl();
    if (craft < 0) {
        return false;
    }

    if (
        !validateInputs($("#gear_used")) ||
        !validateInputs($("#catch_species_code")) ||
        !validateInputs($("#catch_weight_code")) ||
        !validateInputs($("#catch_weight"))
    ) {
        return false;
    }

    const lengthWeight = {};
    lengthWeight["craft"] = craft;
    lengthWeight["catch_species_code"] = $("#catch_species_code").val();
    lengthWeight["catch_species_name"] = $("#catch_species_code option:selected").text();
    lengthWeight["catch_weight_code"] = $("#catch_weight_code").val();
    lengthWeight["catch_weight"] = $("#catch_weight").val();

    if (typeof scientific["catch"] === "undefined") {
        scientific["catch"] = [];
    }
    if (typeof scientific["catch"][craft] === "undefined") {
        scientific["catch"][craft] = {};
    }

    scientific["catch"][craft]["gear"] = $("#gear_used").val();

    const gearKey = "gear_used_" + $("#gear_used").val();

    if (typeof scientific["catch"][craft][gearKey] === "undefined") {
        scientific["catch"][craft][gearKey] = {};
    }
    if (typeof scientific["catch"][craft][gearKey]["details"] === "undefined") {
        scientific["catch"][craft][gearKey]["details"] = [];
    }

    scientific["catch"][craft][gearKey]["details"].push(lengthWeight);
    saveScientificState();
    loadCatch();

    $("#catch_species_code").val("");
    $("#catch_weight_code").val("");
    $("#catch_weight").val("");

    return true;
}

function addLengthWeight() {
    const craft = getCraftIndexFromUrl();
    if (craft < 0) {
        return false;
    }

    if (
        !validateInputs($("#gear_used")) ||
        !validateInputs($("#lw_species_code")) ||
        !validateInputs($("#lw_weight_code")) ||
        !validateInputs($("#lw_weight")) ||
        !validateInputs($("#lw_length_type")) ||
        !validateInputs($("#lw_length_code")) ||
        !validateInputs($("#lw_length"))
    ) {
        return false;
    }

    const lengthWeight = {};
    lengthWeight["craft"] = craft;
    lengthWeight["lw_species_code"] = $("#lw_species_code").val();
    lengthWeight["lw_species_name"] = $("#lw_species_code option:selected").text();
    lengthWeight["lw_weight_code"] = $("#lw_weight_code").val();
    lengthWeight["lw_weight"] = $("#lw_weight").val();
    lengthWeight["lw_length_type"] = $("#lw_length_type").val();
    lengthWeight["lw_length_code"] = $("#lw_length_code").val();
    lengthWeight["lw_length"] = $("#lw_length").val();

    if (typeof scientific["lengthWeight"] === "undefined") {
        scientific["lengthWeight"] = [];
    }
    if (typeof scientific["lengthWeight"][craft] === "undefined") {
        scientific["lengthWeight"][craft] = {};
    }

    scientific["lengthWeight"][craft]["gear"] = $("#gear_used").val();

    const gearKey = "gear_used_" + $("#gear_used").val();

    if (typeof scientific["lengthWeight"][craft][gearKey] === "undefined") {
        scientific["lengthWeight"][craft][gearKey] = {};
    }
    if (typeof scientific["lengthWeight"][craft][gearKey]["details"] === "undefined") {
        scientific["lengthWeight"][craft][gearKey]["details"] = [];
    }

    scientific["lengthWeight"][craft][gearKey]["details"].push(lengthWeight);
    saveScientificState();
    loadLengthWeight();

    $("#lw_species_code").val("");
    $("#lw_weight_code").val("");
    $("#lw_weight").val("");
    $("#lw_length_type").val("");
    $("#lw_length_code").val("");
    $("#lw_length").val("");

    return true;
}

function addWeight() {
    const craft = getCraftIndexFromUrl();
    if (craft < 0) {
        return false;
    }
    if (!validateInputs($("#gear_used")) || !validateInputs($("#no_species_fish")) || !validateInputs($("#weight_code")) || !validateInputs($("#weight_code")) || !validateInputs($("#weight"))

    ) {
        return false;
    }


    if (typeof scientific["lengthWeight"] == "undefined") {
        scientific['lengthWeight'] = []
    }
    if (typeof scientific["lengthWeight"][craft] == "undefined") {
        scientific['lengthWeight'][craft] = {}
    }
    if (typeof scientific["lengthWeight"][craft] == "undefined") {
        scientific['lengthWeight'][craft]["gear_used_" + $("#gear_used").val()] = {}
    }

    // scientific['lengthWeight'][craft]["gear_used_"+$("#gear_used").val()]['no_species_fish'] = $("#no_species_fish").val()
    // scientific['lengthWeight'][craft]["gear_used_"+$("#gear_used").val()]['weight_code'] = $("#weight_code").val()
    // scientific['lengthWeight'][craft]["gear_used_"+$("#gear_used").val()]['weight'] = $("#weight").val()
    saveScientificState();
    loadCatch()
    swal("Data saved Successfully!");
}

function setSamplingData() {
    let radioValue;
    if (typeof scientific["sampling"] != "undefined") {

        const craft = getCraftIndexFromUrl();
        if (craft < 0) {
            return;
        }
        const sampling = scientific['sampling'][craft]
        if (typeof sampling != "undefined") {
            $("#fishery_type").val(sampling["fishery_type"])
            $("#sub_cat2").val(sampling["sub_cat2"])
            $("#hp").val(sampling["hp"])
            $("#no_crew").val(sampling["no_crew"])
            //unloading type
            $("#gear_set_time").val(sampling["gear_set_time"])
            $("#days").val(sampling["days"])
            $("#hours").val(sampling["hours"])
            $("#dep_date").val(sampling["dep_date"])
            $("#dep_time").val(sampling["dep_time"])
            $.each(sampling["weather"], function (key, value) {
                $('input.Weather[value="' + value + '"]').prop('checked', true);
            });

            var $radios = $('input:radio[name=unloading-type]');
            if ($radios.is(':checked') === false) {
                radioValue = sampling["unloading-type"] !== "All" ? "Partial" : "All"
                $radios.filter('[value=' + radioValue + ']').prop('checked', true);
                $("#event").val(sampling["unloading-type"])
            }
            $(".unloading_type").click()
            $('input.Weather[value="6"]').prop('checked', true);

            $("#arrival_date").val(sampling["arrival_date"])
            $("#remarks").val(sampling["remarks"])

            const gears = sampling["gears"]
            const mainGear = gears["mainGear"]
            const secondGear = gears["secondGear"]
            const thirdGear = gears["thirdGear"]
            $("#main_operatoin_no").val(mainGear["operatoin_no"])
            $("#main_days").val(mainGear["days"])
            $("#main_hours").val(mainGear["hours"])
            $("#main_fishing_depth").val(mainGear["fishing_depth"])
            $("#main_g_code").val(mainGear["g_code"])

            $("#Second_operatoin_no").val(secondGear["operatoin_no"])
            $("#Second_days").val(secondGear["days"])
            $("#Second_hours").val(secondGear["hours"])
            $("#Second_fishing_depth").val(secondGear["fishing_depth"])
            $("#Second_g_code").val(secondGear["g_code"])

            $("#Third_operatoin_no").val(thirdGear["operatoin_no"])
            $("#Third_days").val(thirdGear["days"])
            $("#Third_hours").val(thirdGear["hours"])
            $("#Third_fishing_depth").val(thirdGear["fishing_depth"])
            $("#Third_g_code").val(thirdGear["g_code"])


            if (typeof scientific["sampling"] == "undefined") {
                scientific['sampling'] = []
            }
            scientific['sampling'][craft] = sampling
            saveScientificState();
        }
    }
}

function removeSamplingCraft(clientKey) {
    const id = findCraftIndexByKey(clientKey);

    if (id < 0) {
        return;
    }

    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Yes, delete it!',
        closeOnConfirm: true
    }, function () {
        if (Array.isArray(scientific.craft)) {
            scientific.craft.splice(id, 1);
        }

        ["lengthWeight", "catch", "operationCost", "sampling"].forEach(function (section) {
            if (Array.isArray(scientific[section])) {
                scientific[section].splice(id, 1);
            }
        });

        saveScientificState();
        loadSampling();
    });
}

function removeOperationCostCatch(id) {
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Yes, delete it!',
        closeOnConfirm: true, //closeOnCancel: false
    }, function () {
        const craft = getCraftIndexFromUrl();
        const operationCostCatch = scientific.operationCost[craft]['catch'];
        operationCostCatch.splice(id, 1)
        scientific.operationCost[craft]['catch'] = operationCostCatch
        saveScientificState();
        loadOperationCostCatch()
    })
}

function removeLengthWeight(id) {
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Yes, delete it!',
        closeOnConfirm: true, //closeOnCancel: false
    }, function () {
        const craft = getCraftIndexFromUrl();
        const lengthWeightData = scientific.lengthWeight[craft]["gear_used_" + $("#gear_used").val()]['details']
        lengthWeightData.splice(id, 1)
        scientific.lengthWeight[craft]["gear_used_" + $("#gear_used").val()]["details"] = lengthWeightData
        saveScientificState();
        loadLengthWeight()
    })
}

function removeCatch(id) {
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Yes, delete it!',
        closeOnConfirm: true, //closeOnCancel: false
    }, function () {
        const craft = getCraftIndexFromUrl();
        const catchData = scientific.catch[craft]["gear_used_" + $("#gear_used").val()]['details']
        catchData.splice(id, 1)
        scientific.catch[craft]["gear_used_" + $("#gear_used").val()]["details"] = catchData
        saveScientificState();
        loadCatch()
    })
}

function removeFleet(id) {
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Yes, delete it!',
        closeOnConfirm: true
    }, function () {
        const fleet = Array.isArray(scientific.fleet)
            ? scientific.fleet
            : [];

        const index = parseInt(id, 10);
        if (!Number.isInteger(index) || index < 0 || index >= fleet.length) {
            return;
        }

        fleet.splice(index, 1);
        scientific.fleet = fleet;
        saveScientificState();
        loadFleet();
    });
}

function loadSamplingGearDropDown(craft) {
    const sampling = scientific.sampling && scientific.sampling[craft]
        ? scientific.sampling[craft]
        : null;

    const samplingGears = sampling && sampling.gears
        ? sampling.gears
        : {};

    const $select = $("#gear_used").empty().append(
        $("<option>", {
            value: "",
            text: "Please Choose..."
        })
    );

    for (const i in samplingGears) {
        const gear = samplingGears[i];
        if (gear && gear.gear && gear.gear.Id) {
            $select.append(
                $("<option>", {
                    value: gear.gear.Id,
                    text: gear.gear.value || ""
                })
            );
        }
    }
}

function addOperationCostCatch() {
    const craft = getCraftIndexFromUrl();
    if (craft < 0) {
        return false;
    }
    const catchData = {};
    catchData["craft"] = craft

    if (!validateInputs($("#export_qty")) || !validateInputs($("#export_val")) || !validateInputs($("#local_qty")) || !validateInputs($("#local_val")) || !validateInputs($("#dry_qty")) || !validateInputs($("#dry_val")) || !validateInputs($("#discard_qty")) || !validateInputs($("#discard_val"))

    ) {
        return false;
    }

    catchData["species_code"] = $("#species_code").val()
    catchData["species_name"] = $("#species_code option:selected").text()
    catchData["export_qty"] = $("#export_qty").val()
    catchData["export_val"] = $("#export_val").val()
    catchData["local_qty"] = $("#local_qty").val()
    catchData["local_val"] = $("#local_val").val()
    catchData["dry_qty"] = $("#dry_qty").val()
    catchData["dry_val"] = $("#dry_val").val()
    catchData["discard_qty"] = $("#discard_qty").val()
    catchData["discard_val"] = $("#discard_val").val()
    catchData["trash"] = $("#trash").prop('checked') === true

    if (typeof scientific["operationCost"] == "undefined") {
        scientific['operationCost'] = []
    }

    if (typeof scientific["operationCost"][craft] == "undefined") {
        scientific['operationCost'][craft] = {}
    }
    if (typeof scientific["operationCost"][craft]['catch'] == "undefined") {
        scientific['operationCost'][craft]['catch'] = []
    }
    scientific['operationCost'][craft]['catch'].push(catchData)
    saveScientificState();
    loadOperationCostCatch()
    return true
}

function addOperatoinCost() {
    const craft = getCraftIndexFromUrl();
    if (craft < 0) {
        return false;
    }

    if (!validateInputs($("#fuel_qty")) || !validateInputs($("#fuel_val")) || !validateInputs($("#ice_qty")) || !validateInputs($("#ice_val")) || !validateInputs($("#bait_qty")) || !validateInputs($("#bait_val")) || !validateInputs($("#salt_qty")) || !validateInputs($("#salt_val")) || !validateInputs($("#labour_cost")) || !validateInputs($("#food_water")) || !validateInputs($("#others"))

    ) {
        return false;
    }

    if (typeof scientific["operationCost"] == "undefined") {
        scientific['operationCost'] = []
    }

    if (typeof scientific["operationCost"][craft] == "undefined") {
        scientific['operationCost'][craft] = {}
    }
    scientific['operationCost'][craft]['fuel_qty'] = $("#fuel_qty").val()
    scientific['operationCost'][craft]['fuel_val'] = $("#fuel_val").val()
    scientific['operationCost'][craft]['ice_qty'] = $("#ice_qty").val()
    scientific['operationCost'][craft]['ice_val'] = $("#ice_val").val()
    scientific['operationCost'][craft]['bait_qty'] = $("#bait_qty").val()
    scientific['operationCost'][craft]['bait_val'] = $("#bait_val").val()
    scientific['operationCost'][craft]['salt_qty'] = $("#salt_qty").val()
    scientific['operationCost'][craft]['salt_val'] = $("#salt_val").val()
    scientific['operationCost'][craft]['labour_cost'] = $("#labour_cost").val()
    scientific['operationCost'][craft]['food_water'] = $("#food_water").val()
    scientific['operationCost'][craft]['others'] = $("#others").val()
    scientific['operationCost'][craft]['remarks'] = $("#remarks").val()
    saveScientificState();
    return true
}

function loadGearTypesAjax(callback) {
    requestScientificJson(
        "gearTypeList",
        "../gear-type/list",
        {},
        {cacheMs: 60000}
    ).done(function (data) {
        setSelectOptions(
            "#gear_type",
            data,
            function (value) {
                return value.description || "";
            }
        );

        if ($('.sampling').length) {
            ["#main_gear", "#Second_gear", "#Third_gear"].forEach(function (selector) {
                setSelectOptions(
                    selector,
                    data,
                    function (value) {
                        return value.description || "";
                    }
                );
            });

            if (typeof callback === "function") {
                callback();
            }
        }
    }).fail(function (xhr, textStatus, errorThrown) {
        logScientificAjaxError("Unable to load gear types.", xhr, textStatus, errorThrown);
    });
}

function loadFishTypesAjax(callback) {
    requestScientificJson(
        "fishTypeList",
        "../fish-types/list",
        {},
        {cacheMs: 60000}
    ).done(function (data) {
        const targets = [];

        if ($('.sampling').length) {
            targets.push(
                "#main_target_species_main",
                "#Second_target_species_main",
                "#Third_target_species_main"
            );
        }

        if ($('.Catch-data').length) {
            targets.push("#catch_species_code");
        }

        targets.forEach(function (selector) {
            setSelectOptions(
                selector,
                data,
                function (value) {
                    return value.name || "";
                }
            );
        });

        if (typeof callback === "function") {
            callback();
        }
    }).fail(function (xhr, textStatus, errorThrown) {
        logScientificAjaxError("Unable to load fish types.", xhr, textStatus, errorThrown);
    });
}

function loadDistrictsAjax(callback = setFiDistrict) {
    requestScientificJson(
        "districtList",
        "../fi-district/list",
        {},
        {cacheMs: 60000}
    ).done(function (data) {
        if ($('#scientific-page').length) {
            setSelectOptions(
                "#fiDistrict",
                data,
                function (value) {
                    return value.name || "";
                }
            );

            if (typeof callback === "function") {
                callback(loadDivisionsAjax);
            }
        }

        if ($('.sampling').length) {
            setSelectOptions(
                "#dep_fi_district",
                data,
                function (value) {
                    return value.name || "";
                }
            );

            if (typeof callback === "function") {
                callback();
            }
        }
    }).fail(function (xhr, textStatus, errorThrown) {
        logScientificAjaxError("Unable to load districts.", xhr, textStatus, errorThrown);
    });
}

function getSelectedDiscrictData(callback) {
    requestScientificJson(
        "selectedEnumerationData",
        "../enumeration/selected-data",
        {},
        {cacheMs: 5000}
    ).done(function (data) {
        scientific["fiDistrict"] = data.district;
        scientific["fi_division"] = data.division;
        scientific["landing_place"] = data.landing_site;
        scientific["sampling_date"] = data.request_date;
        saveScientificState();

        if (typeof callback === "function") {
            callback();
        }
    }).fail(function (xhr, textStatus, errorThrown) {
        logScientificAjaxError("Unable to load selected enumeration data.", xhr, textStatus, errorThrown);
    });
}

function loadDivisionsAjax(district, callback) {
    if (!district) {
        if ($('#scientific-page').length) {
            $("#fi_division").empty();
            $("#landing_place").empty();
        }
        if ($('.sampling').length) {
            $("#dep_fi_division").empty();
            $("#dep_landing_place").empty();
        }
        return;
    }

    requestScientificJson(
        "divisionByDistrict",
        "../division/list-by-district",
        {districtId: district},
        {cacheMs: 60000}
    ).done(function (data) {
        if ($('#scientific-page').length) {
            setSelectOptions(
                "#fi_division",
                data,
                function (value) {
                    return value.name || "";
                }
            );

            if (typeof callback === "function") {
                callback(loadLandingSiteAjax);
            }
        }

        if ($('.sampling').length) {
            setSelectOptions(
                "#dep_fi_division",
                data,
                function (value) {
                    return value.name || "";
                }
            );

            if (typeof callback === "function") {
                callback();
            }
        }
    }).fail(function (xhr, textStatus, errorThrown) {
        logScientificAjaxError("Unable to load divisions.", xhr, textStatus, errorThrown);
    });
}

function loadLandingSiteAjax(division, callback) {
    if (!division) {
        if ($('#scientific-page').length) {
            $("#landing_place").empty();
        }
        if ($('.sampling').length) {
            $("#dep_landing_place").empty();
        }
        return;
    }

    requestScientificJson(
        "landingSiteByDivision",
        "../landing-site/list-by-division",
        {divisionId: division},
        {cacheMs: 60000}
    ).done(function (data) {
        if ($('#scientific-page').length) {
            setSelectOptions(
                "#landing_place",
                data,
                function (value) {
                    return value.name || "";
                }
            );

            if (typeof callback === "function") {
                callback();
            }
        }

        if ($('.sampling').length) {
            setSelectOptions(
                "#dep_landing_place",
                data,
                function (value) {
                    return value.name || "";
                }
            );

            if (typeof callback === "function") {
                callback();
            }
        }
    }).fail(function (xhr, textStatus, errorThrown) {
        logScientificAjaxError("Unable to load landing sites.", xhr, textStatus, errorThrown);
    });
}

function loadBoatTypeAjax() {
    requestScientificJson(
        "boatTypeList",
        "../boat-types/list",
        {},
        {cacheMs: 60000}
    ).done(function (data) {
        ["#boat_type", "#main_cat"].forEach(function (selector) {
            setSelectOptions(
                selector,
                data,
                function (value) {
                    return value.code || "";
                }
            );
        });
    }).fail(function (xhr, textStatus, errorThrown) {
        logScientificAjaxError("Unable to load boat types.", xhr, textStatus, errorThrown);
    });
}

function loadBoatCateAjax(id, callback) {
    if (!id) {
        $("#sub_cat, #sub_cat2").empty();
        if (typeof callback === "function") {
            callback();
        }
        return;
    }

    requestScientificJson(
        "boatCategoryList",
        "../boat-category/list",
        {type: id},
        {cacheMs: 60000}
    ).done(function (data) {
        if ($('#sub_cat').length) {
            setSelectOptions(
                "#sub_cat",
                data,
                function (value) {
                    return value.code || "";
                },
                (data || []).length > 0
            );
        }

        if ($('#sub_cat2').length) {
            setSelectOptions(
                "#sub_cat2",
                data,
                function (value) {
                    return value.code || "";
                },
                (data || []).length > 0
            );
        }

        if (typeof callback === "function") {
            callback();
        }
    }).fail(function (xhr, textStatus, errorThrown) {
        logScientificAjaxError("Unable to load boat categories.", xhr, textStatus, errorThrown);
    });
}

function loadBoatSubCateAjax(id) {
    if (!id) {
        $("#sub_cat-sampling").empty();
        return;
    }

    requestScientificJson(
        "boatSubCategoryList",
        "../boat-sub-category/list",
        {type: id},
        {cacheMs: 60000}
    ).done(function (data) {
        setSelectOptions(
            "#sub_cat-sampling",
            data,
            function (value) {
                return value.code || "";
            },
            (data || []).length > 0
        );
    }).fail(function (xhr, textStatus, errorThrown) {
        logScientificAjaxError("Unable to load boat sub-categories.", xhr, textStatus, errorThrown);
    });
}

function loadGearExtraDataAjax(id, section, callback) {
    const selector = section === "main"
        ? ".extra-data-main"
        : section === "second"
            ? ".extra-data-second"
            : ".extra-data-third";

    if (!id) {
        $(selector).empty();
        if (typeof callback === "function") {
            callback();
        }
        return;
    }

    requestScientificJson(
        "gearExtraDataList",
        "../gear-type-extra-data/list-by-gear",
        {gear: id},
        {cacheMs: 60000}
    ).done(function (data) {
        const $container = $(selector).empty();

        $.each(data || [], function (key, value) {
            const slug = String(value.slug || "").replace(/[^A-Za-z0-9_-]/g, "");
            if (!slug) {
                return;
            }

            const $column = $("<div>", {class: "col-lg-6"});
            const $group = $("<div>", {class: "form-group"});

            $group.append(
                $("<label>").text(value.name || "")
            );
            $group.append(
                $("<input>", {
                    "data-validation": "EMPTY",
                    id: section + "_extra_" + slug,
                    name: slug,
                    class: "form-control"
                })
            );

            $column.append($group);
            $container.append($column);
        });

        if (typeof callback === "function") {
            callback();
        }
    }).fail(function (xhr, textStatus, errorThrown) {
        logScientificAjaxError("Unable to load gear extra data.", xhr, textStatus, errorThrown);
    });
}

function loadFisheryTypesAjax(callback) {
    requestScientificJson(
        "fisheryTypeList",
        "../fishery-types/list",
        {},
        {cacheMs: 60000}
    ).done(function (data) {
        setSelectOptions(
            "#fishery_type",
            data,
            function (value) {
                return value.name || "";
            }
        );

        if (typeof callback === "function") {
            callback();
        }
    }).fail(function (xhr, textStatus, errorThrown) {
        logScientificAjaxError("Unable to load fishery types.", xhr, textStatus, errorThrown);
    });
}

function setSubCategory() {

    if (typeof scientific["sampling"] === "undefined") {
        return;
    }

    const craft = getCraftIndexFromUrl();
        if (craft < 0) {
            return;
        }

    const sampling = scientific["sampling"][craft];

    if (
        typeof sampling !== "undefined" &&
        typeof sampling["sub_cat2"] !== "undefined"
    ) {
        $("#sub_cat2").val(sampling["sub_cat2"]);
    }
}

function loadBoatDistrictCodeAjax() {
    requestScientificJson(
        "districtCodeList",
        "../fi-district/list-code",
        {},
        {cacheMs: 60000}
    ).done(function (data) {
        setSelectOptions(
            "#boat-district",
            data,
            function (value) {
                return value.code || "";
            }
        );
    }).fail(function (xhr, textStatus, errorThrown) {
        logScientificAjaxError("Unable to load district codes.", xhr, textStatus, errorThrown);
    });
}

function loadFishList(craft) {
    const catchForCraft =
        typeof scientific.catch !== "undefined"
        && typeof scientific.catch[craft] !== "undefined"
            ? scientific.catch[craft]
            : null;

    let dropDownHtml = '<option value="" selected hidden>Please Choose...</option>';

    if (!catchForCraft) {
        $("#lw_species_code").html(dropDownHtml);
        return;
    }

    for (const i in catchForCraft) {
        const list = catchForCraft[i];
        const details = list && Array.isArray(list.details) ? list.details : [];

        for (const j in details) {
            const detail = details[j];
            if (detail && detail.catch_species_code !== "") {
                dropDownHtml +=
                    '<option value="' + escapeHtml(detail.catch_species_code) + '">' +
                    escapeHtml(detail.catch_species_name) +
                    '</option>';
            }
        }
    }

    $("#lw_species_code").html(dropDownHtml);
}

function loadSampleFishList(craft) {
    const catchForCraft =
        typeof scientific.catch !== "undefined"
        && typeof scientific.catch[craft] !== "undefined"
            ? scientific.catch[craft]
            : null;

    let dropDownHtml = '<option value="" selected hidden>Please Choose...</option>';

    if (!catchForCraft) {
        $("#species_code").html(dropDownHtml);
        return;
    }

    for (const i in catchForCraft) {
        const list = catchForCraft[i];
        const details = list && Array.isArray(list.details) ? list.details : [];

        for (const j in details) {
            const detail = details[j];
            if (detail && detail.catch_species_code !== "") {
                dropDownHtml +=
                    '<option data-weight="' + escapeHtml(detail.catch_weight) + '" ' +
                    'value="' + escapeHtml(detail.catch_species_code) + '">' +
                    escapeHtml(detail.catch_species_name) +
                    '</option>';
            }
        }
    }

    $("#species_code").html(dropDownHtml);
}

function setFiDistrict() {
    if (typeof scientific["fiDistrict"] !== "undefined")
        $("#fiDistrict").val(scientific["fiDistrict"]).change();

}

function setFiDivision() {
    $("#fi_division").val(scientific["fi_division"]).change();
}

function setLandingSite() {
    $("#landing_place").val(scientific["landing_place"]);
}

function setDistrict() {
    if (typeof scientific["sampling"] != "undefined") {

        const craft = getCraftIndexFromUrl();
        if (craft < 0) {
            return;
        }
        const sampling = scientific['sampling'][craft];
        if (typeof sampling != "undefined" && typeof sampling["dep_fi_district"]["Id"] != "undefined") {
            $("#dep_fi_district").val(sampling["dep_fi_district"]["Id"]).change();
        }
    }

}

function setFisheryType() {

    if (typeof scientific["sampling"] === "undefined") {
        return;
    }

    const craft = getCraftIndexFromUrl();
    if (craft < 0) {
        return;
    }

    if (
        typeof scientific["sampling"][craft] === "undefined" ||
        scientific["sampling"][craft] === null
    ) {
        return;
    }

    const fisheryType =
        scientific["sampling"][craft]["fishery_type"];

    if (
        typeof fisheryType !== "undefined" &&
        fisheryType !== ""
    ) {
        $("#fishery_type").val(String(fisheryType));
    }
}



function setDivision() {

    if (typeof scientific["sampling"] != "undefined") {

        const craft = getCraftIndexFromUrl();
        if (craft < 0) {
            return;
        }
        const sampling = scientific['sampling'][craft]
        $("#dep_fi_division").val(sampling["dep_fi_division"]["Id"]).change()
    }
}

function setLanding_sample() {
    if (typeof scientific["sampling"] != "undefined") {

        const craft = getCraftIndexFromUrl();
        if (craft < 0) {
            return;
        }
        const sampling = scientific['sampling'][craft]
        $("#dep_landing_place").val(sampling["dep_landing_place"]["Id"]).change()
    }
}

function setGearType() {
    if (typeof scientific["sampling"] === "undefined") {
        return;
    }

    const craft = getCraftIndexFromUrl();
    if (craft < 0) {
        return;
    }

    const sampling = scientific["sampling"][craft];
    if (typeof sampling === "undefined" || !sampling.gears) {
        return;
    }

    const mainGear = sampling.gears.mainGear || {};
    const secondGear = sampling.gears.secondGear || {};
    const thirdGear = sampling.gears.thirdGear || {};

    function restoreGear(selector, section, gearData) {
        const gearId = gearData.gear && gearData.gear.Id
            ? gearData.gear.Id
            : "";

        $(selector).val(gearId);

        if (!gearId) {
            return;
        }

        loadGearExtraDataAjax(gearId, section, function () {
            $.each(gearData.extra || {}, function (key, value) {
                $("#" + section + "_extra_" + key).val(value);
            });
        });
    }

    restoreGear("#main_gear", "main", mainGear);
    restoreGear("#Second_gear", "second", secondGear);
    restoreGear("#Third_gear", "third", thirdGear);
}

function setFishType() {
    if (typeof scientific["sampling"] === "undefined") {
        return;
    }

    const craft = getCraftIndexFromUrl();
    if (craft < 0) {
        return;
    }

    const sampling = scientific["sampling"][craft];
    if (typeof sampling === "undefined" || !sampling.gears) {
        return;
    }

    const mainGear = sampling.gears.mainGear || {};
    const secondGear = sampling.gears.secondGear || {};
    const thirdGear = sampling.gears.thirdGear || {};

    $("#main_target_species_main").val(
        mainGear.target_species_main && mainGear.target_species_main.Id
            ? mainGear.target_species_main.Id
            : ""
    );
    $("#Second_target_species_main").val(
        secondGear.target_species_main && secondGear.target_species_main.Id
            ? secondGear.target_species_main.Id
            : ""
    );
    $("#Third_target_species_main").val(
        thirdGear.target_species_main && thirdGear.target_species_main.Id
            ? thirdGear.target_species_main.Id
            : ""
    );
}

function submitScientificData() {
    const config = getScientificConfig();

    /*
     * clientKey is only for local wizard navigation. Do not send it to
     * ScientificService; keep the original array indexes expected by the
     * existing service layer.
     */
    const payload = $.extend(true, {}, scientific);

    if (Array.isArray(payload.craft)) {
        payload.craft = payload.craft.map(function (craft) {
            const clean = $.extend({}, craft);
            delete clean.clientKey;
            return clean;
        });
    }

    $.ajax({
        url: scientificRoute("create", "../scientific/create"),
        type: "POST",
        data: payload,
        dataType: "json",
        timeout: 30000,
        headers: config.csrfToken
            ? {"X-CSRF-Token": config.csrfToken}
            : {},
        success: function (data) {
            if (!data || data.success !== true) {
                console.error("Scientific submission failed:", data);
                swal(
                    "Something went wrong!",
                    data && data.message ? data.message : "Unable to submit the scientific report.",
                    "error"
                );
                return;
            }

            swal({
                title: "Success!",
                text: "Scientific report has been submitted!"
            }, function () {
                scientific = {};
                saveScientificState();

                window.location.href =
                    data.viewUrl || scientificRoute("index", "index");
            });
        },
        error: function (xhr, textStatus, errorThrown) {
            logScientificAjaxError(
                "Scientific submission failed.",
                xhr,
                textStatus,
                errorThrown
            );

            let message = "Please contact your system administrator.";

            if (xhr && xhr.status === 429) {
                message = "Too many requests. Please wait and try again.";
            }

            swal("Something went wrong!", message, "error");
        }
    });

    return false;
}

function resetScientificData() {
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Yes, delete it!',
        closeOnConfirm: true, //closeOnCancel: false
    }, function () {
        scientific = {}
        saveScientificState();

        window.location.href = scientificRoute("index", "index");
    })
}

function setGcode() {
    const number = $(".gear-number").val();
    if (number === "1") {

        $("#main_g_code").val($('input[name="radio-gcode"]:checked').val());
    }
    if (number === "2") {
        $("#Second_g_code").val($('input[name="radio-gcode"]:checked').val());
    }
    if (number === "3") {
        $("#Third_g_code").val($('input[name="radio-gcode"]:checked').val());
    }
    $(".gear-number").val("");

    $("#gcodeMoal").modal("hide");
}

function openGcode(number) {
    let valueSelected = 0;
    if (number === "1") {
        valueSelected = $("#main_g_code").val();
    }
    if (number === "2") {
        valueSelected = $("#Second_g_code").val();
    }
    if (number === "3") {
        valueSelected = $("#Third_g_code").val();
    }
    const $radios = $('input:radio[name="radio-gcode"]');
    if ($radios.is(':checked') === true) {
        $radios.prop('checked', false);
    }
    if (valueSelected !== "" && $radios.is(':checked') === false) {
        $radios.filter('[value=' + valueSelected + ']').prop('checked', true);
    }
    $(".gear-number").val(number);
    $("#gcodeMoal").modal("show");
}

function closeGcode() {
    const number = $(".gear-number").val();
    if (number === "1") {
        $("#main_g_code").val("");
    }
    if (number === "2") {
        $("#Second_g_code").val("");
    }
    if (number === "3") {
        $("#Third_g_code").val("");
    }
    $(".gear-number").val("");

    $("#gcodeMoal").modal("hide");
}

$(document).ready(function () {
    scientific = loadScientificState();
    ensureCraftKeys();
    if (typeof scientific["fiDistrict"] !== "undefined") {
        $(".unsubmited").show()
    }
    $("#scientificenumerationrequest-reson").on("change", function () {
        if ($("#scientificenumerationrequest-reson").val() != "Other") {
            $(".sientificEnumeration-reason-other").hide();
        } else {
            $(".sientificEnumeration-reason-other").show();
        }
    })
    $("#scientificenumerationrequest-can_continue").on("change", function () {
        if ($("#scientificenumerationrequest-can_continue").val() == "yes") {
            $(".sientificEnumeration-reason").hide();
        } else {
            $(".sientificEnumeration-reason").show();
        }
    })
    $(".form-control").on("change", function () {
        $(this).removeClass("is-invalid")
        validateInputs(this)
    })
    if ($('#scientific-page').length) {
        getSelectedDiscrictData(function () {
            loadDistrictsAjax(setFiDistrict);
        });

        $("#fiDistrict").on("change", function () {
            loadDivisionsAjax(this.value, setFiDivision)
        })


        $("#fi_division").on("change", function () {
            loadLandingSiteAjax(this.value, setLandingSite)
        })
        loadBoatTypeAjax()
        // loadBoatSubCateAjax()
        loadBoatDistrictCodeAjax()
        loadGearTypesAjax()
        loadFleet()
        loadSampling()

        $(".scientific-drop-down").on("change", function () {
            scientific[this.name] = this.value;
            saveScientificState();
        })
        $("#boat_type").on("change", function () {
            loadBoatCateAjax($("#boat_type").val(), null)
        })

        $("#main_cat").on("change", function () {
            loadBoatSubCateAjax($("#main_cat").val())
        })

        $("#add-fleet").on("click", function () {
            if (!validateInputs($("#boat_type")) || !validateInputs($("#sub_cat")) || !validateInputs($("#gear_type")) || !validateInputs($("#no_of_boats"))

            ) {
                return false;
            }
            const fleet = {};
            fleet["boat_type"] = {"Id": $("#boat_type").val(), 'value': $("#boat_type option:selected").text()}
            fleet["sub_category"] = {"Id": $("#sub_cat").val(), 'value': $("#sub_cat option:selected").text()}
            fleet["gear_type"] = {"Id": $("#gear_type").val(), 'value': $("#gear_type option:selected").text()}
            fleet["number"] = $("#no_of_boats").val()
            if (typeof scientific["fleet"] == "undefined") {
                scientific['fleet'] = []
            }
            scientific['fleet'].push(fleet)
            saveScientificState();
            loadFleet()

            $("#boat_type").val("")
            $("#sub_cat").val("")
            $("#gear_type").val('')
            $("#no_of_boats").val("")
        })

        $(".add-sampling").on("click", function () {
            if (!validateInputs($("#main_cat")) || !validateInputs($("#sub_cat-sampling")) || !validateInputs($("#boat_no-sampling")) || !validateInputs($("#boat-district"))

            ) {
                return false;
            }
            if ($("#sub_cat-sampling option:selected").text() == "Please Choose...") {
                $("#sub_cat-sampling").addClass("is-invalid")
                $("#sub_cat-sampling").focus()
                return false
            }
            const boatNumber = $("#main_cat option:selected").text() + $("#sub_cat-sampling option:selected").text() + $("#boat_no-sampling").val() + $("#boat-district option:selected").text()

            if (typeof scientific["craft"] == "undefined") {
                scientific['craft'] = []
            }
            const craft = {
                "boatNumber": boatNumber,
                "type": $("#main_cat").val(),
                "clientKey": createClientCraftKey()
            };
            scientific['craft'].push(craft)
            saveScientificState();
            loadSampling()
            $("#main_cat").val("")
            $("#sub_cat-sampling").val("")
            $("#boat_no-sampling").val("")
            $("#boat-district").val("")
        })
        $("#submit-form").on("click", function () {
            submitScientificData();
        });
        $("#reset-form").on("click", function () {
            resetScientificData();
        });
    }


    if ($('.sampling').length) {
        const requiredCraft = requireCraftIndex();
        if (requiredCraft === null) {
            return;
        }
        const craft = requiredCraft;
        loadGearTypesAjax(setGearType)
        loadFishTypesAjax(setFishType)
        loadDistrictsAjax(setDistrict)
        loadFisheryTypesAjax(setFisheryType)
        loadBoatCateAjax(scientific.craft[craft]["type"], setSubCategory)
        $("#craft-number").text(scientific.craft[craft]["boatNumber"] || "")

        $(".unloading_type").on("click", function () {
            if ($('input[name="unloading-type"]:checked').val() !== "All") {
                $(".event_box").show()
            } else {
                $(".event_box").hide()
            }
        })

        setSamplingData();
        $("#dep_fi_district").on("change", function () {
            loadDivisionsAjax(this.value, setDivision)
        })

        $("#dep_fi_division").on("change", function () {
            loadLandingSiteAjax(this.value, setLanding_sample)
        })
        $("#main_gear").on("change", function () {
            loadGearExtraDataAjax(this.value, "main")
        })
        $("#Second_gear").on("change", function () {
            loadGearExtraDataAjax(this.value, "second")
        })
        $("#Third_gear").on("change", function () {
            loadGearExtraDataAjax(this.value, "third")
        })
    }

    if ($('.Catch-data').length) {
        const requiredCraft = requireCraftIndex();
        if (requiredCraft === null) {
            return;
        }
        const craft = requiredCraft;
        $("#craft-number").text(scientific.craft[craft]["boatNumber"] || "")
        loadSamplingGearDropDown(craft)
        loadFishTypesAjax(setFishType)

        if (typeof scientific["lengthWeight"] != "undefined") {
            const lengthWeight = scientific['lengthWeight'][craft]
            loadCatch()
        }

        $("#addCatch").on("click", function () {
            addCatch();

        })
        $("#gear_used").on("change", function () {
            loadCatch();

        })
    }
    if ($('.operational-cost').length) {
        const requiredCraft = requireCraftIndex();
        if (requiredCraft === null) {
            return;
        }
        const craft = requiredCraft;
        $("#craft-number").text(scientific.craft[craft]["boatNumber"] || "")

        loadSampleFishList(craft)
        loadOperationCost()
        loadOperationCostCatch()

        $("#add_catch").on("click", function () {
            addOperationCostCatch();
        })
        $("#species_code").on("change", function () {
            $(".qty-num").html($(this).find(':selected').data('weight') + "Kg")
            $(".available-qty").show()

        })
    }

    if ($('.lenght-weight').length) {
        const requiredCraft = requireCraftIndex();
        if (requiredCraft === null) {
            return;
        }
        const craft = requiredCraft;
        $("#craft-number").text(scientific.craft[craft]["boatNumber"] || "")
        loadSamplingGearDropDown(craft)


        loadFishList(craft)
        loadOperationCost()
        loadOperationCostCatch()

        $("#add_catch").on("click", function () {
            addOperationCostCatch();

        })
        $("#gear_used").on("change", function () {
            loadLengthWeight();

        })
        $("#add_lw").on("click", function () {
            addLengthWeight();

        })
    }

});
