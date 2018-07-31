<?php
$dbName = "symptom_cleaners_nl";
return array(
	"SYSTEM_TITLE_NAME"	=>"explanation of the word",
    //&#30331;&#24405;&#39029;&#38754;&#30340;&#25991;&#26412;
    "login"             =>array(
        "MODEL_TITLE"   	=>  "Registreer",
        "USERNAME"      	=>  "Gebruikersnaam",
        "PASSWORD"       	=>  "Paswoord",
        "SIGNIN_BUTTON"  	=>  "Registreer",
        "CAN_NOT_LOGIN_IN"	=>  "Het ingevoerde paswoord is niet juist, voer nogmaals in a.u.b.!"
        ),
    //&#20027;&#39029;&#30340;&#25991;&#26412;
    "index"             =>array(
    	"LABEL_FOUND_SYMPTOM" 			=> "Gevonden symptomen:",
    	"LABEL_NOT_FOUND_SYMPTOM" 		=> "Niet gevonden symptomen:",
    	"DISEASE" 						=> "Ziektebeeld:",
    	"SELECT_MORE_WORD"				=> "Kies a.u.b. mnimaal een woord !",
    	"SELECT_MORE_WORD_WARNING_A"	=> "Note: Realiseer dat resultaat = ziekte {",
    	"SELECT_MORE_WORD_WARNING_B"	=> "} OF ziekte {",
    	
    	
        //&#29992;&#25143;&#20449;&#24687;&#37096;&#20998;&#30340;&#25991;&#26412;
        "userInformation"=>array(            
            "MODEL_BUTTON"                  =>  "Klant gegevens",
            "MODEL_TITLE"                   =>  "Om u beter te kunnen helpen, vul onderstaande informatie aan:",   
            "SAVE_BUTTON"                   =>  "Bewaar",
            "CLEAR_BUTTON"                  =>  "Verwijder alle invoer",          
            //&#29992;&#25143;&#20449;&#24687;&#37096;&#20998;
            "AGE"                           =>  "Leeftijd:",
            "AGE_TOOLTIP"                   =>  "Geef hier uw leeftijd.",
            "WEIGHT"                        =>  "Gewicht:",
            "WEIGHT_TOOLTIP"                =>  "Geef hier uw lichaamsgewicht (kg).",
            "HIGH"                          =>  "Lengte:",
            "HIGH_TOOLTIP"                  =>  "Geef hier uw lichaamslengte(cm).",
            "SEX"                           =>  "Geslacht:",
            "SEX_TOOLTIP"                   =>  "Bent u man of een vrouw.",
            "HEREDITARY"                    =>  "Erfelijkheid:",
            "HEREDITARY_TOOLTIP"            =>  "Komt een erfelijke ziekte in <br>uw familie voor?",           
            "INJURY_HISTORY"                =>  "Ongeluk in verleden:",
            "INJURY_HISTORY_TOOLTIP"        =>  "Heeft u ooit een ongeluk gehad?",
            "REPETITIVE"                    =>  "Repeterend werk:",
            "REPETITIVE_TOOLTIP"            =>  "Repeterend betekent éénzijdig of <br>repeterend werk.",
            "LIFESTYLE"                     =>  "Lifestyle",
            "LACK_OF_EXCERSIZE"             =>  "Gebrek aan beweging:",
            "LACK_OF_EXCERSIZE_TOOLTIP"     =>  "Beweegt u regelmatig ?",
            "OVERWEIGHT"                    =>  "Overgewicht:",
            "UNHEALTHY_FOOD"                =>  "Allergie voedsel:",
            "UNHEALTHY_FOOD_TOOLTIP"        =>  "Vertoont u alergische reacties <br>op voedsel?",
            "EXTERNAL_FACTORS"              =>  "External Factors",
            "AICOHOL"                       =>  "Alcohol:",
            "AICOHOL_TOOLTIP"               =>  "Drinkt u veel alcohol?",
            "POLLUTANTS"                    =>  "Verontreiniging:",
            "POLLUTANTS_TOOLTIP"            =>  "Verblijft of werk u in een <br>verontreinigde omgeving?",
            "DRUGS"                         =>  "Drugs:",
            "DRUGS_TOOLTIP"                 =>  "Gebruikt u drugs?",
            "SMOKING"                       =>  "Roken:",
            "SMOKING_TOOLTIP"               =>  "Rookt u?",
            "MALE_OPTION"                   =>  "Man",
            "FEMALE_OPTION"                 =>  "Vrouw",
            "YES_OPTION"                    =>  "Ja",
            "NO_OPTION"                     =>  "Nee",
            //&#20581;&#24247;&#35780;&#27979;&#27169;&#22359;
            "HEALTH_AWARENESS_TITLE"        =>  "Bewustwording Gezondheid",
            "HEALTH_AWARENESS_GRADE"        =>  "BMI factor is:",
            "HEALTH_AWARENESS_LEVEL_1"      =>  "<strong>Ondergewicht</strong>",
            "HEALTH_AWARENESS_LEVEL_2"      =>  "<strong>Normaal gewicht</strong>",
            "HEALTH_AWARENESS_LEVEL_3"      =>  "<strong>Licht overgewicht</strong>",
            "HEALTH_AWARENESS_LEVEL_4"      =>  "<strong>Middelmatig overgewicht</strong>",
            "HEALTH_AWARENESS_LEVEL_5"      =>  "<strong>Obesitas</strong>",
            "HEALTH_AWARENESS_LEVEL_6"      =>  "<strong>Morbide Obesitas</strong>",
            "HEALTH_AWARENESS_DELETED"      =>  "Verwijderd:",
            "HEALTH_AWARENESS_DISEASE"      =>  "ziektebeelden:",
            //&#29992;&#25143;&#19981;&#20860;&#23481;&#25552;&#31034;
            "NOT_SUPPORT"					=>	"Your browser does not support the canvas element."
        ),
        //&#25512;&#29702;&#32467;&#26524;&#30340;&#35686;&#21578;&#25991;&#26412;
        "resultWarning" =>array(
                //&#22810;&#30142;&#30149;&#20132;&#38598;&#32467;&#26524;
                "MULTI_MODEL_BODY"      =>  "Mogelijk multi-morbiditeit !",
                //&#36817;&#20041;&#35789;&#36873;&#25321;&#26694;
                "SYNONYM_MODEL_TITLE"     =>  "Kies een synoniem",
                "SYNONYM_MODEL_RETUR"     =>  "Return",
                "SYNONYM_MODEL_SAVE"      =>  "Bewaar keuze",
                //&#26080;&#30142;&#30149;&#32467;&#26524;&#25552;&#31034;
                "NOT_DISEASE_MODEL_BODY"=>"<b>Geen ziektebeeld gevonden </b> voor deze set symptomen!",
                //&#32467;&#26524;&#35686;&#21578;&#27169;&#22359;&#20849;&#29992;
                "MODEL_TIPS"      =>    "Tips",
                "MODEL_CLOSE"     =>    "Close"
            ),
        //&#29992;&#25143;&#21453;&#39304;&#27169;&#22359;&#30340;&#25991;&#26412;
        "useful"=>array(
                "MODEL_TITLE"       		=>  "Rapport",
                "MODEL_BUTTON"      		=>  "Registreer",
                "MODEL_USER_NAME"   		=>  "Uw naam:",
                "MODEL_SAVE"        		=>  "Bewaar",
                "MODEL_CONCEPT"     		=>  "Concepts:",
            	"MODEL_UNRELATED_CONCEPT"	=>	"UnrelatedConcept:",
            	"SYSTEM_RESULT"				=>	"The result of system:",
            	"DISEASES"					=>	"Diseases:",
                "MODEL_LOADING"     		=>  "Loading"
            ),
        //&#23545;&#25512;&#29702;&#32467;&#26524;&#30142;&#30149;&#31867;&#21035;&#20998;&#26512;&#27169;&#22359;&#30340;&#25991;&#26412;
        "control"=>array(
        	"MODEL_CHART_TITLE"				=>	"Visuele Controle",
        	"MODEL_CHART_ALL_DISEASES"		=>	"alle ziektes",
        	"MODEL_CHART_FOUND_DISEASES"	=>	"gevonden ziektes",
            "MODEL_TITLE"                   =>  "Figuur met horizontaal de ziektebeelden en vertikaal ICPC class.",
            "MODEL_BUTTON"                  =>  "controle",
            "ICPC_CLASS_NUMBER_TITLE"       =>  "ICPC class nummer",
            "ICPC_CLASS_NAME_TITLE"         =>  "ICPC class naam",
            "MODEL_CLOSE"                   =>  "Sluiten",
            //ICPC&#21015;&#34920;&#21517;&#31216;
            "ICPC_CLASS_NAME_A"             =>  "Alg",
            "ICPC_CLASS_NAME_B"             =>  "Bloed",
            "ICPC_CLASS_NAME_D"             =>  "Spijs",
            "ICPC_CLASS_NAME_F"             =>  "Oog",
            "ICPC_CLASS_NAME_H"             =>  "Oor",
            "ICPC_CLASS_NAME_K"             =>  "Cardio",
            "ICPC_CLASS_NAME_L"             =>  "Beweg",
            "ICPC_CLASS_NAME_N"             =>  "Zenuw",
            "ICPC_CLASS_NAME_P"             =>  "Psych",
            "ICPC_CLASS_NAME_R"             =>  "Lucht",
            "ICPC_CLASS_NAME_S"             =>  "Huid",
            "ICPC_CLASS_NAME_T"             =>  "Stofw",
            "ICPC_CLASS_NAME_U"             =>  "Urine",
            "ICPC_CLASS_NAME_W"             =>  "Zwang",
            "ICPC_CLASS_NAME_X"             =>  "Vrouw",
            "ICPC_CLASS_NAME_Y"             =>  "Man",
            "ICPC_CLASS_NAME_Z" 			=>	"Soc"
            )
        ),
    "word_concept"       =>array(
        "MAIN_TITLE"            		=> "Concept invoer",
        "WORD_TITLE"                    => "Ik heb de volgende symptomen:",
        "WORD_TITLE_PLACEHOLDER"        => "bijvoorbeeld: ik heb buikpijn, hoofdpijn", 
        "NOT_WORD_TITLE"                => "Ik heb deze symptomen niet:",
        "NOT_WORD_TITLE_PLACEHOLDER"    => "bijvoorbeeld: koorts, koud",
        "SYNONYM_CONJUGATE"				=> "Na vervangen synoniem/vervoeging:",
        "INCREMENT_CONCEPT"				=> "Verbeterd concept:",
        "ADVISED_CONCEPT"				=> "Nieuw Concept:",
        "WORD"							=> "word",
        "SYNONYM_CONJUGATE_TITLE"		=> "vervoeging/synoniem",
        "SELECT_SYNONYM_WORD"			=> "Selecteer a.u.b. synoniem",
        "SAVE_SELECTION"				=> "Bewaar keuze"
        ),
    "concept_disease"   =>array(
    	"PAGE_TITLE" 				=> "Concept -> Ziekte",
    	"USER_RESULT_LABEL" 		=> "Resultaten:",	
    	"RELATED_SYPTOM_NUMBER"		=> "Aantal symptomen:",
    	"UNRELATED_SYPTOM_NUMBER"	=> "Aantal symptomen NIET:",
    	"DISEASE_NUMBER"			=> "Aantal ziektebeelden:",	
    	"FINAL_DISEASE"				=> "Mogelijke ziektebeelden:",
    	"LOADING_WAIT"				=> "Laden,even geduld",
    	"SUBMIT"					=> "start",
    	"comb"=>array(
    		"COMB_SETTINGS"		=> "verhouding P : R",
    		"COMB_PARAMETER_A" 	=> "invloedsfaktor P (prevalentie):",
	    	"COMB_PARAMETER_B" 	=> "invloedsfaktor R (betrouwbaarheid):",	
		    "CLOSE"				=> "Sluit",
		    "SAVE"				=> "Bewaar instelling"
		    ),
		 "SORT_PARAMETER_R"			=> "sort R",
		 "SORT_PARAMETER_P"			=> "sort P",
		 "SORT_COMB"				=> "sort comb",
		 "WITH_SAME_CLASS_DISEASE"	=> "Disease with same ICPC class:",
		 "INCREMENTAL_HAVE" 		=> "wel",
    	 "INCREMENTAL_NOT_HAVE" 	=> "niet",
    	 "INPUT_WARNING"			=> "Please input number !",
    	 "TOTAL"					=> "total"
	    			),
    "disease_symptom"   =>array(
    	"PAGE_TITLE" 	=> "Ziekte -> Symptoom",
    	"DISEASE_NAME" 	=> "Naam ziektebeeld:",	
    	"SYMPTOM_LIST"	=> "Symptomen lijst:",
    	"AGE"			=> "Leeftijd"
    )
);
?>