<?php
return [

    'adminEmail' => 'admin@example.com',
    'leaveBaseUrl' => 'https://msdfar.com',
    'languages'=>[
        'si'=>'Sinhala',
        'tm'=>"Tamil",
        'en'=>'English'
    ],
    'blood_groups'=>[
        "A+"=>"A+",
        "A-"=>"A-",
        "B+"=>"B+",
        "AB+"=>"AB+",
        "AB-"=>"AB-",
        "O+"=>"O+",
        "O-"=>"O-"

    ],
    'gender'=>[
        "Male"=>"Male",
        "Female"=>"Female"
    ],
    'yesNo'=>[
        "1"=>"Yes",
        "0"=>"No"
    ],
    'civil'=>[
        "Married"=>"Married",
        "Un_Married"=>"Un Married"
    ],
    'weightCode'=>[
        1=>"SWHO (Whole weight)",
        2=>"AGGT (Gilled and gutted)",
        3=>"SPDD (Peduncle off and headed)",
        4=>"SDRY (Dried)",
        5=>"SGIL (Gilled)",
        6=>"SHDD (Headed)",
        7=>"SLOI (Fish loins)",
        8=>"SRTA (Tail off)",
        9=>"SFIN (Fins off)",
        10=>"SSKF (Shark fins)",
        11=>"STAL (Tailed)"
    ],

    'lengthCode'=>[
        1=>"SL (Standard Length)",
        2=>"CF (Cleithrum - fork of the tail)",
        3=>"CK (Cleithrum - keel)",
        4=>"EF (Eye - fork of the tail)",
        5=>"DF (Base first dorsal fin - fork of the tail)",
        6=>"TL (Total Length)",
        7=>"SF (Tip of snout - base first dorsal fin)",
        8=>"PA (Base pectoral fin - base anal fin)",
        9=>"PC (Base pectoral fin -fork of the tail) -for headless fish",
        10=>"FL (Tip of snout -fork of the tail) -for whole fish",
        11=>"JF (Lower jaw -fork of the tail) -for Bil fish",
        12=>"CW (Carapace width) -for Crabs",
        13=>"CL (Carapace length) -for Lobster"
    ],
    'gearSettingTime'=>[
        1=>"Day",
        2=>"Night",
        3=>"Both",
    ],

   
 'turnstile' => [
      'siteKey' => '1x00000000000000000000AA',
    'secretKey' => '1x0000000000000000000000000000000AA',
], 
    
     'subscriptionId' => 293, // Your real subscription ID

    // Existing initial tokens
    'personalApiBearerToken' => 'eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJZY1JXTEhwZFViekRDSkFzOWduVTNRWW8yNlF2Y01vZjhjbFVJU0s3SFVVIn0.eyJleHAiOjE3ODMzMjc3NjcsImlhdCI6MTc4MzMyNTk2NywianRpIjoiMDYwNzViNTMtYTMwMi00MzkwLTkyNzItM2IwYmUwNDBkODdiIiwiaXNzIjoiaHR0cHM6Ly8xOTIuMTY4Ljc3LjczOjg0NDMvcmVhbG1zL2RhdGEtc2hhcmluZyIsImF1ZCI6ImFjY291bnQiLCJzdWIiOiIwNzExZDNjYy1lM2MxLTQ2ZjgtYTM4OS1hYWEzODVhMTMyMmMiLCJ0eXAiOiJCZWFyZXIiLCJhenAiOiJkcnAtYWRtaW4tYXBpcyIsInNlc3Npb25fc3RhdGUiOiI2YWY3M2VlYy02MDdmLTQ4NmQtYjY1Yi00MGMzYWU5Yjk1NDAiLCJyZWFsbV9hY2Nlc3MiOnsicm9sZXMiOlsiZGVmYXVsdC1yb2xlcy1kYXRhLXNoYXJpbmciLCJvZmZsaW5lX2FjY2VzcyIsInVtYV9hdXRob3JpemF0aW9uIl19LCJyZXNvdXJjZV9hY2Nlc3MiOnsiZHJwLWFkbWluLWFwaXMiOnsicm9sZXMiOlsiSW5zdGl0dXRlIC0gQWNjb3VudHMiLCJJbnN0aXR1dGUgVXNlciJdfSwiYWNjb3VudCI6eyJyb2xlcyI6WyJtYW5hZ2UtYWNjb3VudCIsIm1hbmFnZS1hY2NvdW50LWxpbmtzIiwidmlldy1wcm9maWxlIl19fSwic2NvcGUiOiJlbWFpbCBwcm9maWxlIiwic2lkIjoiNmFmNzNlZWMtNjA3Zi00ODZkLWI2NWItNDBjM2FlOWI5NTQwIiwiZW1haWxfdmVyaWZpZWQiOnRydWUsIm5hbWUiOiIyNDciLCJwcmVmZXJyZWRfdXNlcm5hbWUiOiIxOTgzMjcyMDIwNzMiLCJnaXZlbl9uYW1lIjoiMjQ3IiwiZW1haWwiOiJkZmFyLmljdG9AZ21haWwuY29tIn0.gWSTqzkIDKZDzq8b5hk0eMjWNcPSJuF4YRMvUW-BCk1vQV0Ee5LtPxEFUfs-ORypo2lNKO8YgGguo5PK2j876ldFEXWgOXVpE4ih5lZgd5BD74XcK2Zk_S-wrF9TuZRx43AwIM4BbXbF1GNtHMLpy9n4nNAXZuWDr3Vvh-nYhA2iU4ej2DntAfDN2BCbOurHZkmFIpud_NjEiF3SWvjpZM0uCgiupghqpmtx-5rKVzUujUCsxRLKXy9sMbsT9qiR6eFD_JVbLsDeZ25EYMjzkuA2SUp6s9QSy1dEo44JxUesiRG3EQUO56b9giCAnDD49SMdXOpzGk8buV0wW0wc9w',
    'personalApiRefreshToken' => 'eyJhbGciOiJIUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJlYTY4ZDNkMS02NzA3LTQyMWItYTllNS01YTM4YzllZTRmMDQifQ.eyJleHAiOjE3ODMzMzMxNjcsImlhdCI6MTc4MzMyNTk2NywianRpIjoiMWFiNDNkNTktZGZjMC00NTBhLWEwYzEtZjk2YThiMzBjYzlmIiwiaXNzIjoiaHR0cHM6Ly8xOTIuMTY4Ljc3LjczOjg0NDMvcmVhbG1zL2RhdGEtc2hhcmluZyIsImF1ZCI6Imh0dHBzOi8vMTkyLjE2OC43Ny43Mzo4NDQzL3JlYWxtcy9kYXRhLXNoYXJpbmciLCJzdWIiOiIwNzExZDNjYy1lM2MxLTQ2ZjgtYTM4OS1hYWEzODVhMTMyMmMiLCJ0eXAiOiJSZWZyZXNoIiwiYXpwIjoiZHJwLWFkbWluLWFwaXMiLCJzZXNzaW9uX3N0YXRlIjoiNmFmNzNlZWMtNjA3Zi00ODZkLWI2NWItNDBjM2FlOWI5NTQwIiwic2NvcGUiOiJlbWFpbCBwcm9maWxlIiwic2lkIjoiNmFmNzNlZWMtNjA3Zi00ODZkLWI2NWItNDBjM2FlOWI5NTQwIn0._pz8U-WZG05rX9OL9b7VVHTjMBxnGdH5qI_40DfFIbU',

     'personalApiBaseUrl' =>
        'https://eservices.drp.gov.lk/datashare/api/v1',
    // Existing Refresh Token API
    'personalApiRefreshTokenUrl' =>
        'https://eservices.drp.gov.lk/datashare/api/v1/public/oauth/refresh-token',

    // New: Generate Access Token API
    'personalApiTokenUrl' =>
        'https://eservices.drp.gov.lk/datashare/api/v1/public/oauth/org/token',

    // Credentials supplied by DRP
    'personalApiUsername' => '198327202073',
    'personalApiPassword' => 'V0@vishwa',
    'personalApiClientId' => 'ORG00056',
    'personalApiClientSecret' => 'AV5KMPEVKdfpoXMdR972g5c6UydJtU',

        'personalApiVerifySsl' => false,


    'apiSystemUserId' => 1,

   'secureIdKey' => 'bra0rsQxVilcKGwph2ZcRUF4nsDx/tQL2LT18yVjt+w=',



];