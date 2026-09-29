<?php

namespace backend\config;

use Yii;

class Constant
{
    const FISHERMAN = 1;
    const FI = 2;
    const AD = 3;
    const DG = 4;
    const DO = 5;
    const DM = 6;
    const MEA = 8;
    const DFI = 9;
    const ITD = 15;
    const ITU = 16;
    const SPECIAL_LICENCE = 17;
    const HARBOUR_OFFICER = 18;
    const EXPORT_COMPANY = 200;
    const ADMINISTRATION = 20;
    const PRINT_OFFICER = 224;
    const EXPORTER = 225;
    const QUALITY_EXPORT_OFFICER = 226;
    const VMS_PAYMENT_OFFICER_MAIN = 227;
    const VMS_PAYMENT_OFFICER_SECONDARY = 228;
    const EXPORT_FISH_DATA_COLLECTOR_MAIN = 229;
    const EXPORT_FISH_DATA_COLLECTOR_SECONDARY = 230;
    const AD_Highseas = 253;
    const REPORT_OFFICER = 297;
    const DEVELOPMENT_DIVISION = 22;


    // ── Leave-hierarchy roles (Colombo) ──
    const DIRECTOR                   = 999;
    const ICT_OFFICER                = 7;
    const ICT_ASSISTANT              = 12;
    const FISHERIES_OFFICER          = 13;
    const DEVELOPMENT_OFFICER        = 14;
    const MANAGEMENT_SERVICE_OFFICER = 19;
    const CC                         = 10;
    const KKS                        = 21;

    /**
     * Roles that SKIP the CC (supervising officer) review stage.
     * Their leave goes: draft -> acting officer agrees -> DG.
     */
    const CC_EXEMPT_TYPES = [self::AD, self::ITD];            // 3, 15

    /**
     * Roles whose leave the Director General personally approves.
     * Directors still pass through CC first; AD / ITD do not.
     */
    const DG_APPROVAL_TYPES = [self::DIRECTOR, self::AD, self::ITD];  // 999, 3, 15

    //const LEAVE_SUBMISSION_OFFICER = 101;
    //const LEAVE_ACTING_OFFICER = 102;
    //const LEAVE_CC_OFFICER = 103;

    const ALTER = 555;
    const MANAGEMENT = 111;
    const PRINT = 222;
    const CALL_SIGN = 223;

    const ADMIN_PERMISSION = 888;
    const EDIT_PERMISSION = 1;
    const VIEW_PERMISSION = 2;
    const ADMIN = 11;
    const THIRD_PARTY = 1000;

    static $userTypes = [
        
        self::FISHERMAN => [
            'name' => 'Fisherman',
            'category' => 'external'
        ],
        self::FI => [
            'name' => 'F.I.',
            'category' => 'main'
        ],
        self::AD => [
            'name' => 'A.D.',
            'category' => 'main'
        ],
        self::DG => [
            'name' => 'D.G.',
            'category' => 'main'
        ],
        self::DO => [
            'name' => 'D.S.O.',
            'category' => 'main'
        ],
        self::DM => [
            'name' => 'Director Management',
            'category' => 'main'
        ],
        self::MEA => [
            'name' => 'MEA',
            'category' => 'main'
        ],
        self::DFI => [
            'name' => 'DFI',
            'category' => 'main'
        ],
        self::ITD => [
            'name' => 'IT Director',
            'category' => 'main'
        ],
        self::ITU => [
            'name' => 'IT User',
            'category' => 'main'
        ],
        self::PRINT_OFFICER => [
            'name' => 'PRINT OFFICER',
            'category' => 'main'
        ],
         self::EXPORTER => [
            'name' => 'EXPORTER',
            'category' => 'main'
        ],
        self::QUALITY_EXPORT_OFFICER => [
            'name' => 'QUALITY_EXPORT_OFFICER',
            'category' => 'main'
        ],

        self::VMS_PAYMENT_OFFICER_MAIN => [
            'name' => 'VMS_PAYMENT_OFFICER',
            'category' => 'main'
        ],

        self::VMS_PAYMENT_OFFICER_SECONDARY => [
            'name' => 'VMS_PAYMENT_OFFICER',
            'category' => 'secondary'
        ],

         self::EXPORT_FISH_DATA_COLLECTOR_MAIN => [
            'name' => 'EXPORT_FISH_DATA_COLLECTOR',
            'category' => 'main'
        ],

        self::EXPORT_FISH_DATA_COLLECTOR_SECONDARY => [
            'name' => 'EXPORT_FISH_DATA_COLLECTOR',
            'category' => 'secondary'
        ],

        self::DIRECTOR => [
            'name' => 'Director',
            'category' => 'main'
        ],

        // ── Leave-hierarchy roles (Colombo) ──
        self::ICT_OFFICER => [
            'name' => 'ICT Officer',
            'category' => 'main'
        ],
        self::ICT_ASSISTANT => [
            'name' => 'ICT Assistant',
            'category' => 'main'
        ],
        self::FISHERIES_OFFICER => [
            'name' => 'Fisheries Officer',
            'category' => 'main'
        ],
        self::DEVELOPMENT_OFFICER => [
            'name' => 'Development Officer',
            'category' => 'main'
        ],
        self::MANAGEMENT_SERVICE_OFFICER => [
            'name' => 'Management Service Officer',
            'category' => 'main'
        ],
        self::CC => [
            'name' => 'CC',
            'category' => 'main'
        ],
        self::KKS => [
            'name' => 'KKS',
            'category' => 'main'
        ],
        
        self::ALTER => [
            'name' => 'ALTER',
            'category' => 'secondary'
        ],
        self::MANAGEMENT => [
            'name' => 'MANAGEMENT',
            'category' => 'main'
        ],
        self::PRINT => [
            'name' => 'PRINT',
            'category' => 'secondary'
        ],
        self::CALL_SIGN => [
            'name' => 'CALL_SIGN',
            'category' => 'secondary'
        ],
        self::SPECIAL_LICENCE => [
            'name' => 'SPECIAL_LICENCE',
            'category' => 'secondary'
        ],
        self::HARBOUR_OFFICER => [
            'name' => 'HARBOUR_OFFICER',
            'category' => 'secondary'
        ],
        self::EXPORT_COMPANY => [
            'name' => 'EXPORT_COMPANY',
            'category' => 'external'
        ],
        self::ADMINISTRATION => [
            'name' => 'ADMINISTRATION',
            'category' => 'main'
        ],
        self::THIRD_PARTY => [
            'name' => 'THIRD_PARTY',
            'category' => 'main'
        ],
        self::AD_Highseas => [
            'name' => 'AD_Highseas',
            'category' => 'secondary'
        ],
         self::REPORT_OFFICER => [
            'name' => 'REPORT_OFFICER',
            'category' => 'main'
        ],
        self::DEVELOPMENT_DIVISION => [
            'name' => 'DEVELOPMENT_DIVISION',
            'category' => 'main'
        ]
    ];

    public static function getUserTypesByCategory($category)
    {
        // Validate category
        if (!in_array($category, ['main', 'secondary', 'external'])) {
            Yii::error("Invalid category: $category", 'userTypesCategory');
            return [];
        }

        // Filter user types by category
        $filtered = array_filter(self::$userTypes, function ($item) use ($category) {
            return $item['category'] === $category;
        });

        // Manually map to value => label format
        $mapped = [];
        foreach ($filtered as $key => $item) {
            $mapped[$key] = $item['name'];
        }

        return $mapped;
    }

    static $userPermissions = [
        self::ADMIN_PERMISSION => "Admin",
        self::EDIT_PERMISSION => "Edit",
        self::VIEW_PERMISSION => "View Only"

    ];

    const Open = 'Open';
    const In_Progress = 'In Progress';
    const Completed = 'Completed';

    static $Inquiry_Status = [
        self::Open => "Open",
        self::In_Progress => "In Progress",
        self::Completed => "Completed",
    ];
    ///////////////////////////////////////////////////////////////////
    static $gearSettingTime = [
        1 => "Day",
        2 => "Night",
        3 => "Both",
    ];
    ///////////////////////////////////////////////////////////////////////
    static $yesNo = [
        1 => "Yes",
        2 => "No",
    ];
    ///////////////////////////////////////////////////////////////////////
    static $actDeact = [
        "Active" => "Active",
        "Deactivate" => "Deactivate",
        "Pending" => "Pending",
    ];
    static $actDeactInt = [
        1 => "Active",
        0 => "Deactivate",
    ];
    ////////////////////////////////////////////////////////////////////////
    const InProgress = 888;
    const Pending = 1;
    const PaymentPending = 99;
    const FinalApprovalPending = 100;
    const Active = 101;
    const Inactive = 404;
    const RequestCompleted = 200;
    const Cancelled = 400;
    const Expired = 403;
    const Transferred = 500;
    static $licenseStatus = [
        self::InProgress => "In progress",
        self::Pending => "Pending",
        self::PaymentPending => "Waiting for the Payment",
        self::FinalApprovalPending => "Waiting for final Approval",
        self::RequestCompleted => "Request Completed",
        self::Cancelled => "Cancelled",
        self::Expired => "Expired",
        self::Transferred => "Transferred",
        self::Active => "Active",
        self::Inactive => "Inactive",

    ];


    ////////////////////////////////////////////////////////////////////////
    const FISHERMAN_REG = "FISHERMAN-REG";
    static $processTypes = [
        self::FISHERMAN_REG => self::FISHERMAN_REG,
    ];

    ///////////////////////////////////////////////////////////////////////////
    static $hullMaterials = [
        1 => "Fiber",
        2 => "Steel",
        3 => "Timber",
    ];
    ///////////////////////////////////////////////////////////////////////////
    static $engineTypes = [
        1 => "Inboard Motor",
        2 => "Outboard Motor",
        3 => "Oars",
        4 => "Sail",
    ];
    ///////////////////////////////////////////////////////////////////////////
    static $engineMake = [
        1 => "Isuzu",
        2 => "Susuki",
        3 => "Yamaha",
        4 => "Yanmar",
        5 => "Hyhundai",
        6 => "Weichai",
        9999 => "Other",
        8888 => "Not Applicable"
    ];
    ///////////////////////////////////////////////////////////////////////////
    static $communicationEquipment = [
        "SSB" => "SSB",
        "Test VMS" => "Test VMS",
        "VHF" => "VHF",
        "Not Applicable" => "Not Applicable",
    ];
    ///////////////////////////////////////////////////////////////////////////
    static $fishingEquipment = [
        "Fish Finder" => "Fish Finder",
        "Line Hauler" => "Line Hauler",
        "Net Hauler" => "Net Hauler",
        "Not Applicable" => "Not Applicable",
    ];
    ///////////////////////////////////////////////////////////////////////////
    static $navigationEquipment = [
        "Depth Sounder" => "Depth Sounder",
        "Rader" => "Rader",
        "Satellite Navigation" => "Satellite Navigation",
        "Not Applicable" => "Not Applicable",
    ];
    ///////////////////////////////////////////////////////////////////////////
    static $FishingAreas = [
        1 => "Coastal",
    ];
    ///////////////////////////////////////////////////////////////////////////
    static $howPropelled = [
        "Oar" => "Oar",
        "Sail" => "Sail",
        "Engine" => "Engine",
        "Other" => "Other",
    ];
    ///////////////////////////////////////////////////////////////////////////
    static $engineType = [
        "Inboard Motor" => "Inboard Motor",
        "Outboard Motor" => "Outboard Motor",
        "Oars" => "Oars",
        "Sail" => "Sail",
        "Not Applicable" => "Not Applicable",
    ];
    ///////////////////////////////////////////////////////////////////////////
    static $fuelType = [
        "Petrol" => "Petrol",
        "Diesel" => "Diesel",
        "Kerosene" => "Kerosene",
        "NA" => "NA",
    ];
    ///////////////////////////////////////////////////////////////////////////
    static $FishingTimes = [
        "Entire Year" => "Entire Year",
        "January" => "January",
        "February" => "February",
        "March" => "March",
        "April" => "April",
        "May" => "May",
        "June" => "June",
        "July" => "July",
        "August" => "August",
        "September" => "September",
        "October" => "October",
        "November" => "November",
        "December" => "December",
    ];
    ///////////////////////////////////////////////////////////////////////////
    static $FishingDuration = [
        1 => "4AM to 10AM",
        2 => "10AM to 4PM",
        3 => "4PM to 8PM",
        4 => "8PM to 4AM",
        5 => "Full Day",
        6 => "12AM to 6AM",
        7 => "6AM to 6PM",

    ];
    ///////////////////////////////////////////////////////////////////////////
    static $MEAInspection = [
        1 => "Yes, Satisfactory",
        2 => "Yes, Not Satisfactory",
        3 => "No",
        4 => "Not Available",
        5 => "Not Applicable",

    ];
    ///////////////////////////////////////////////////////////////////////////
    static $MEAEngineModel = [
        1 => "Ashok Laylend",
        2 => "BROWNS GRE",
        3 => "Buke",
        4 => "CHUNG HUNG",
        5 => "DAEDON",
        6 => "DAEWOO",
        7 => "DOOSAN",
        8 => "HINO",
        9 => "HIRO",
        10 => "Hiyundai",
        11 => "ISUZU",
        12 => "JOHN DEER",
        13 => "JTec",
        14 => "MITSHUBISHI",
        15 => "suzuki",
        16 => "WEIFANG",
        17 => "YAHAMA",
        18 => "YANMAR",
        19 => "YUCHAI",
        20 => "NISSAN",
        21 => "Weichai"


    ];
    ///////////////////////////////////////////////////////////////////////////
    static $MEAVesselTypes = [
        1 => "IMUL",
        2 => "OFRP",
        3 => "MTRB",
        4 => "NTRB",

    ];
    ///////////////////////////////////////////////////////////////////////////
    static $MEADeclaration = [
        1 => "Sri Lankan Waters Only",
        2 => "International Waters Only",
        3 => "Both Sri Lankan  and  International Waters",
        4 => "NA",

    ];

    ///////////////////////////////////////////////////////////////////////////
    static $MEAWhereWasVessel = [
        1 => "Afloat",
        2 => "Beached",
        3 => "Slipway",
        4 => "Dry-dock",
        5 => "NA",

    ];

    static $ScintificEnumReason = [
        "Bad weather" => "Bad weather",
        "No boat operations" => "No boat operations",
        "Religious festival" => "Religious festival",
        "Conflict / protest" => "Conflict / protest",
        "Fuel issues" => "Fuel issues",
        "Other" => "Other",

    ];
    static $ScoolingSystem = [
        "RSW" => "RSW",
        "CSW" => "CSW",
        "Mechanical Cooling System" => "Mechanical Cooling System",
        "Other" => "Other",

    ];

    const Black = "Black";
    const Bronze = "Bronze";
    const Silver = "Silver";
    const Gold = "Gold";
    const Platinum = "Platinum";
    static $USER_RANKS = [
        Self::Black => "Basic",
        Self::Bronze => "Bronze",
        Self::Silver => "Silver",
        Self::Gold => "Gold",
        Self::Platinum => "Platinum"
    ];
    static $USER_RANKS_LABEL = [
        Self::Black => '<span class="badge" style="background-color: black;color: white" >Basic</span>',
        Self::Bronze => '<span class="badge" style="background-color: #CE8946;color: black" >Bronze</span>',
        Self::Silver => '<span class="badge" style="background-color: silver;color: black" >Silver</span>',
        Self::Gold => '<span class="badge" style="background-color: goldenrod;color: white" >Gold</span>',
        Self::Platinum => '<span class="badge" style="background-color: #E5E4E2;color: darkgoldenrod" >Platinum</span>',
    ];

    static $countries =

        [
            "AF" => "Afghanistan", "AL" => "Albania", "DZ" => "Algeria", "AS" => "American Samoa", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Antarctica", "AG" => "Antigua and Barbuda", "AR" => "Argentina", "AM" => "Armenia", "AW" => "Aruba", "AU" => "Australia", "AT" => "Austria", "AZ" => "Azerbaijan", "BS" => "Bahamas", "BH" => "Bahrain",
            "BD" => "Bangladesh", "BB" => "Barbados", "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BT" => "Bhutan", "BO" => "Bolivia", "BA" => "Bosnia and Herzegovina", "BW" => "Botswana", "BV" => "Bouvet Island", "BR" => "Brazil", "IO" => "British Indian Ocean Territory", "BN" => "Brunei Darussalam", "BG" => "Bulgaria", "BF" => "Burkina Faso", "BI" => "Burundi", "KH" => "Cambodia",
            "CM" => "Cameroon", "CA" => "Canada", "CV" => "Cape Verde", "KY" => "Cayman Islands", "CF" => "Central African Republic", "TD" => "Chad", "CL" => "Chile", "CN" => "China", "CX" => "Christmas Island", "CC" => "Cocos (Keeling) Islands", "CO" => "Colombia", "KM" => "Comoros", "CG" => "Congo", "CD" => "Congo, the Democratic Republic of the", "CK" => "Cook Islands", "CR" => "Costa Rica", "CI" => "Cote D'Ivoire", "HR" => "Croatia", "CU" => "Cuba", "CY" => "Cyprus", "CZ" => "Czech Republic", "DK" => "Denmark", "DJ" => "Djibouti",
            "DM" => "Dominica", "DO" => "Dominican Republic", "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea", "EE" => "Estonia", "ET" => "Ethiopia", "FK" => "Falkland Islands (Malvinas)", "FO" => "Faroe Islands", "FJ" => "Fiji", "FI" => "Finland", "FR" => "France", "GF" => "French Guiana", "PF" => "French Polynesia", "TF" => "French Southern Territories", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia", "DE" => "Germany", "GH" => "Ghana", "GI" => "Gibraltar", "GR" => "Greece", "GL" => "Greenland", "GD" => "Grenada", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GN" => "Guinea", "GW" => "Guinea-Bissau",
            "GY" => "Guyana", "HT" => "Haiti", "HM" => "Heard Island and Mcdonald Islands", "VA" => "Holy See (Vatican City State)", "HN" => "Honduras", "HK" => "Hong Kong", "HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IR" => "Iran, Islamic Republic of", "IQ" => "Iraq", "IE" => "Ireland", "IL" => "Israel", "IT" => "Italy", "JM" => "Jamaica", "JP" => "Japan", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KI" => "Kiribati", "KP" => "Korea, Democratic People's Republic of", "KR" => "Korea, Republic of", "KW" => "Kuwait",
            "KG" => "Kyrgyzstan", "LA" => "Lao People's Democratic Republic", "LV" => "Latvia", "LB" => "Lebanon", "LS" => "Lesotho", "LR" => "Liberia", "LY" => "Libyan Arab Jamahiriya", "LI" => "Liechtenstein", "LT" => "Lithuania", "LU" => "Luxembourg", "MO" => "Macao", "MK" => "Macedonia, the Former Yugoslav Republic of", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives", "ML" => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MQ" => "Martinique", "MR" => "Mauritania", "MU" => "Mauritius", "YT" => "Mayotte", "MX" => "Mexico", "FM" => "Micronesia, Federated States of", "MD" => "Moldova, Republic of", "MC" => "Monaco",
            "MN" => "Mongolia", "MS" => "Montserrat", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar", "NA" => "Namibia", "NR" => "Nauru", "NP" => "Nepal", "NL" => "Netherlands", "AN" => "Netherlands Antilles", "NC" => "New Caledonia", "NZ" => "New Zealand", "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "NU" => "Niue", "NF" => "Norfolk Island", "MP" => "Northern Mariana Islands", "NO" => "Norway", "OM" => "Oman", "PK" => "Pakistan", "PW" => "Palau", "PS" => "Palestinian Territory, Occupied", "PA" => "Panama", "PG" => "Papua New Guinea", "PY" => "Paraguay", "PE" => "Peru", "PH" => "Philippines", "PN" => "Pitcairn", "PL" => "Poland", "PT" => "Portugal", "PR" => "Puerto Rico", "QA" => "Qatar", "RE" => "Reunion", "RO" => "Romania", "RU" => "Russian Federation", "RW" => "Rwanda",
            "SH" => "Saint Helena", "KN" => "Saint Kitts and Nevis", "LC" => "Saint Lucia", "PM" => "Saint Pierre and Miquelon", "VC" => "Saint Vincent and the Grenadines", "WS" => "Samoa", "SM" => "San Marino", "ST" => "Sao Tome and Principe", "SA" => "Saudi Arabia", "SN" => "Senegal", "CS" => "Serbia and Montenegro", "SC" => "Seychelles", "SL" => "Sierra Leone", "SG" => "Singapore", "SK" => "Slovakia", "SI" => "Slovenia", "SB" => "Solomon Islands", "SO" => "Somalia", "ZA" => "South Africa", "GS" => "South Georgia and the South Sandwich Islands", "ES" => "Spain", "LK" => "Sri Lanka", "SD" => "Sudan", "SR" => "Suriname", "SJ" => "Svalbard and Jan Mayen", "SZ" => "Swaziland", "SE" => "Sweden", "CH" => "Switzerland", "SY" => "Syrian Arab Republic", "TW" => "Taiwan, Province of China", "TJ" => "Tajikistan", "TZ" => "Tanzania, United Republic of", "TH" => "Thailand", "TL" => "Timor-Leste", "TG" => "Togo", "TK" => "Tokelau", "TO" => "Tonga", "TT" => "Trinidad and Tobago", "TN" => "Tunisia", "TR" => "Turkey", "TM" => "Turkmenistan", "TC" => "Turks and Caicos Islands", "TV" => "Tuvalu", "UG" => "Uganda", "UA" => "Ukraine", "AE" => "United Arab Emirates", "GB" => "United Kingdom", "US" => "United States", "UM" => "United States Minor Outlying Islands", "UY" => "Uruguay", "UZ" => "Uzbekistan", "VU" => "Vanuatu", "VE" => "Venezuela", "VN" => "Viet Nam",
            "VG" => "Virgin Islands, British",
            "VI" => "Virgin Islands, U.s.",
            "WF" => "Wallis and Futuna",
            "EH" => "Western Sahara",
            "YE" => "Yemen",
            "ZM" => "Zambia",
            "ZW" => "Zimbabwe"
        ];
    static $FISHERMAN_NUMBER_FORMAT = "FM{number}{district_code}";
    static $SKIPPER_NUMBER_FORMAT = "SK{number}{district_code}";
    static $NATIONAL_LICENSE_FORMAT = "{year}EEZ{boatType}{number}{district_code}";
    static $MEA_FORMAT = "{year}/{boat}{district_code}{id}";
    static $HIGHSEAS_LICENSE_FORMAT = "{year}HSIMUL{number}{district_code}";
    static $BASEURL = "https://msdfar.com";
//    static $BASEURL = "http://dev.hynetz/DFAR_MS/backend/web";
//    static $BASEURL_LICENSE = "http://dev.hynetz/DFAR_MS/backend/web/license/";
    static $BASEURL_LICENSE = "https://files.msdfar.com/static/";
    static $FILE_UPLOAD_PATH = "../../../../../mountpoint/uploads/";
    // static $FILE_VIEW_PATH = "https://files.msdfar.com/";
    static $FILE_VIEW_PATH = "https://msdfar.com/files/";
//    static $FILE_UPLOAD_PATH = "../web/uploads/";
//    static $FILE_VIEW_PATH = "http://dev.hynetz/DFAR_MS/backend/web/uploads/";

    static $departureBoatStatusVMS = [
        "Departure Allowed" => "Departure Allowed",
        "Violation Detected" => "Violation Detected",
        "Compulsory Service Pending" => "Compulsory Service Pending",
        "Other" => "Other"
    ];
    static $departureBoatStatusAll = [
        "Departure Allowed" => [
            "name" => "Departure Allowed",
            "allowed" => ["Investigation", "DROPDOWN"],
            "Subs" => [
                "Investigation completed - First warning" => [
                    "name" => "Investigation completed - First warning",
                    "allowed" => ["Investigation"],
                ],
                "Investigation completed - Second warning" => [
                    "name" => "Investigation completed - Second warning",
                    "allowed" => ["Investigation"],
                ],
                "Investigation completed - Released" => [
                    "name" => "Investigation completed - Released",
                    "allowed" => ["Investigation"],
                ],
                "Suspension period expired" => [
                    "name" => "Suspension period expired",
                    "allowed" => ["Investigation"],
                ],
                "Conditions fullfilled" => [
                    "name" => "Conditions fullfilled",
                    "allowed" => ["Investigation"],
                ],
                "VMS payment done" => [
                    "name" => "VMS payment done",
                    "allowed" => ["VMS"],
                ],
                "VMS service done" => [
                    "name" => "VMS service done",
                    "allowed" => ["VMS"],
                ],
                "Court case finalized" => [
                    "name" => "Court case finalized",
                    "allowed" => ["Investigation"],
                ],
                "Admin penalty paid" => [
                    "name" => "Admin penalty paid",
                    "allowed" => ["Investigation"],
                ],
                "Other - Details given" => [
                    "name" => "Other - Details given",
                    "allowed" => ["VMS"],
                ],
            ]
        ],
        "Violation Detected" => [
            "name" => "Violation Detected",
            "allowed" => ["VMS", "Investigation", "DROPDOWN"],
            "Subs" => [
                "Detected - Suspicious navigation in other country''s waters" => [
                    "name" => "Detected - Suspicious navigation in other country''s waters",
                    "allowed" => ["Investigation", "VMS"],
                ],
                "Detected - Suspicious navigation" => [
                    "name" => "Detected - Suspicious navigation",
                    "allowed" => ["Investigation", "VMS"],
                ],
                "Detected - Fishing without license" => [
                    "name" => "Detected - Fishing without license",
                    "allowed" => ["Investigation"],
                ],
                "Detected - VMS Off" => [
                    "name" => "Detected - VMS Off",
                    "allowed" => ["Investigation", "VMS"],
                ],
                "Detected - Other fisheries law violation" => [
                    "name" => "Detected - Other fisheries law violation",
                    "allowed" => ["Investigation"],
                ],
                "VMS payment pending" => [
                    "name" => "VMS payment pending",
                    "allowed" => ["VMS"],
                ],
                "VMS service pending" => [
                    "name" => "VMS service pending",
                    "allowed" => ["VMS"],
                ],
                "Investigation ongoing" => [
                    "name" => "Investigation ongoing",
                    "allowed" => ["Investigation"],
                ],
                "Operation license / registration suspended" => [
                    "name" => "Operation license / registration suspended",
                    "allowed" => ["Investigation"],
                ],
                "Court case ongoing" => [
                    "name" => "Court case ongoing",
                    "allowed" => ["Investigation"],
                ],
                "Non compliance to conditions" => [
                    "name" => "Non compliance to conditions",
                    "allowed" => ["Investigation"],
                ],
                "Vessel confiscated" => [
                    "name" => "Vessel confiscated",
                    "allowed" => ["Investigation"],
                ],
            ]
        ],
        "Compulsory Service Pending" => [
            "name" => "Compulsory Service Pending",
            "allowed" => ["VMS"],
            "Subs" => [
                "Compulsory service for VMS unit should be done before the next departure" => [
                    "name" => "Compulsory service for VMS unit should be done before the next departure",
                    "allowed" => ["VMS"],
                ]
            ]
        ],
        "Compulsory Service Done" => [
            "name" => "Compulsory Service Done",
            "allowed" => ["VMS"],
            "Subs" => [
                "Compulsory service for VMS unit done" => [
                    "name" => "Compulsory service for VMS unit done.",
                    "allowed" => ["VMS"],
                ]
            ]
        ],

        "Temporary Service Allow" => [
            "name" => "Temporary Service Allow",
            "allowed" => ["VMS"],
            "Subs" => [
                "Temporarily allowed for a specified period of time. " => [
                    "name" => "Temporarily allowed for a specified period of time.",
                    "allowed" => ["VMS"],
                ]
            ]
        ],
        "Other" => [
            "name" => "Other",
            "allowed" => ["VMS", "Operation", "Investigation", "DROPDOWN"],
            "Subs" => [
                "Other Non compliances" => [
                    "name" => "Other Non compliances",
                    "allowed" => ["Investigation", "VMS", 'Operation'],
                ],
                "Other - Details given" => [
                    "name" => "Other - Details given",
                    "allowed" => ["Investigation", "VMS"],
                ],
            ]
        ]
    ];

    public static function getMainTypesKeyValue(string $allowedValue): array
    {
        $result = [];

        foreach (self::$departureBoatStatusAll as $mainKey => $main) {
            if (in_array($allowedValue, $main['allowed'])) {
                $result[$mainKey] = $main['name'];
            }
        }

        return $result;
    }

    public static function getSubTypesByMainAndAllowed(string $mainType, string $allowedValue): array
    {
        $result = [];

        if (!isset(self::$departureBoatStatusAll[$mainType]['Subs'])) {
            return $result;
        }

        foreach (self::$departureBoatStatusAll[$mainType]['Subs'] as $subKey => $sub) {
            if (in_array($allowedValue, $sub['allowed'])
            ) {
                $result[$subKey] = $sub['name'];
            }
        }

        return $result;
    }


    static $departureBoatStatus = [
        "Departure Allowed" => "Departure Allowed",
        "Compulsory Service Pending" => "Compulsory Service Pending",
    ];
    static $departureStatus = [
        "Departure Allowed" => "Departure Allowed",
        "Departure Banned" => "Departure Banned"
    ];
    static $departureOffence = [
        "Ongoing investigation" => "Ongoing investigation",
        "Skipper license suspended for VMS violation" => "Skipper license suspended for VMS violation",
        "Skipper license suspended for illegal fishing in other waters" => "Skipper license suspended for illegal fishing in other waters",
        "Skipper license suspended for fishing without license" => "Skipper license suspended for fishing without license",
        "Other fisheries law violation" => "Other fisheries law violation",
        "Court order" => "Court order",
        "Warrent issued" => "Warrent issued",
        "Skipper license cancelled" => "Skipper license cancelled",
        "Other - Details given" => "Other - Details given",
        "Penalty period ove" => "Penalty period ove",
        "Investigation over" => "Investigation over",
        "Court case over" => "Court case over",
        "Other" => "Other"
    ];
    static $departureBoatOffence = [
        "First time VMS off" => "First time VMS off",
        "Detected - Suspicious navigation in other country''s waters" => "Detected - Suspicious navigation in other country''s waters",
        "Detected - Fishing without license" => "Detected - Fishing without license",
        "Detected - VMS Off" => "Detected - VMS Off",
        "Detected - Other fisheries law violation" => "Detected - Other fisheries law violation",
        "VMS payment pending" => "VMS payment pending",
        "VMS service pending" => "VMS service pending",
        "Investigation ongoing" => "Investigation ongoing",
        "Operation license / registration suspended" => "Operation license / registration suspended",
        "Operation license / registration cancelled" => "Operation license / registration cancelled",
        "Court case ongoing" => "Court case ongoing",
        "Non compliance to conditions" => "Non compliance to conditions",
        "Other - Details given" => "Other - Details given",
        "Vessel confiscated" => "Vessel confiscated",
        "First time allowed" => "First time allowed",
        "Investigation completed - First warning" => "Investigation completed - First warning",
        "Investigation completed - Second warning" => "Investigation completed - Second warning",
        "Investigation completed - Released" => "Investigation completed - Released",
        "Suspension period expired" => "Suspension period expired",
        "Conditions fullfilled" => "Conditions fullfilled",
        "VMS payment done" => "VMS payment done",
        "VMS service done" => "VMS service done",
        "Court case finalized" => "Court case finalized",
        "Admin penalty paid" => "Admin penalty paid",
        "Compulsory service for VMS unit should be done before the next departure" => "Compulsory service for VMS unit should be done before the next departure"
    ];
    static $departureHarbours = [
        "Ambalangoda" => "Ambalangoda",
        "Beruwala" => "Beruwala",
        "Chilaw" => "Chilaw",
        "Codbay" => "Codbay",
        "Dickowita" => "Dickowita",
        "Dikovitha South" => "Dikovitha South",
        "Dondra" => "Dondra",
        "Galle" => "Galle",
        "Gandera" => "Gandera",
        "Hambathota" => "Hambathota",
        "Hikkaduwa" => "Hikkaduwa",
        "Kalametiya" => "Kalametiya",
        "Kalpitiya" => "Kalpitiya",
        "Kapparathota" => "Kapparathota",
        "Kirinda" => "Kirinda",
        "Kudawella" => "Kudawella",
        "Miladdi" => "Miladdi",
        "Mirissa" => "Mirissa",
        "Negombo" => "Negombo",
        "Nilwella" => "Nilwella",
        "Oluwil" => "Oluwil",
        "Point_Pedro" => "Point_Pedro",
        "Poonochimunai" => "Poonochimunai",
        "Suduwella" => "Suduwella",
        "Tangalle" => "Tangalle",
        "Valachchenai" => "Valachchenai",
        "Wennappuwa" => "Wennappuwa"
    ];

    static $fishingArea = [0 => "දේශීය මුහුද / தேசிய கடற்பரப்பு", 2 => "අන්තර්ජාතික 
                මුහුද / சர்வதேச கடற்பரப்பு"];

    static $exportLicenseTypes = [
        "ExportBedchamber" => "ExportBedchamber",
        "ExportChank" => "ExportChank",
        "ExportLiveFish" => "ExportLiveFish",
        "ExportLobster" => "ExportLobster",
        "ExportNakla" => "ExportNakla",
        "TransportBechedemer" => "TransportBechedemer",
        "TransportChank" => "TransportChank",
        "TransportLiveFish" => "TransportLiveFish",
        "TransportLobster" => "TransportLobster",
        "TransportNakla" => "TransportNakla"
    ];

     static $exportFishTypes = [
        1 => "Yellow Fin Tuna",
        2 => "Big Eye Tuna",
        3 => "Sward Fish",
        4 => "Skip Jack Tuna",
        5 => "Sail Fish",
        6 => "Black Marlin",
        7 => "Blue Marlin",
        8 => "Other"
     ];

     static $exportGearTypes = [
        1 => "Gill Net",
        2 => "Ring Net",
        3 => "Long Line",
        4 => "Hand Line",
        5 => "Troll Line"
     ];

    public static $BlueTrakerEventTypes = [
        0  => 'Navigation',
        3  => 'Tamper Activation',
        4  => 'Power Lost',
        6  => 'Enter EEZ',
        7  => 'Exit EEZ',
        8  => 'In Port',
        9  => 'Out of Port',
        10 => 'Power Restored',
        11 => 'Tamper Deactivation',
        12 => 'Fishing Ended',
        13 => 'Fishing Started',
        14 => 'Start Up',
        15 => 'Shutdown',
        16 => 'Antenna Blockage',
        19 => 'Brute Force Reset',
    ];


}

?>