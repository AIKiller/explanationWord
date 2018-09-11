<?php
$dbName = "symptom_cleaners";
return array(
	"SYSTEM_TITLE_NAME"	=>"explanation of the word",
    //登录页面的文本
    "login"             =>array(
        "MODEL_TITLE"   	=>  "Sign in to your account",
        "USERNAME"      	=>  "Username",
        "PASSWORD"       	=>  "Password",
        "SIGNIN_BUTTON"  	=>  "Sign in",
        "CAN_NOT_LOGIN_IN"	=>  "The current password id incorrect,please re-enter !"
        ),
    //主页的文本
    "index"             =>array(
    	"LABEL_FOUND_SYMPTOM" 			=> "Found Symptoms:",
    	"LABEL_NOT_FOUND_SYMPTOM" 		=> "Not Found Symptoms:",
    	"DISEASE" 						=> "Disease:",
    	"SELECT_MORE_WORD"				=> "Please select at least one word !",
    	"SELECT_MORE_WORD_WARNING_A"	=> "Note: Be aware that Result = disease {",
    	"SELECT_MORE_WORD_WARNING_B"	=> "} OR disease {",
    	
    	
        //用户信息部分的文本
        "userInformation"=>array(
            "MODEL_BUTTON"                  =>  "Change User Information",
            "MODEL_TITLE"                   =>  "In order to better serve you,Please check in the following information:",   
            "SAVE_BUTTON"                   =>  "SAVE",
            "CLEAR_BUTTON"                  =>  "Clear All Conditions",
            //用户信息部分
            "AGE"                           =>  "Age:",
            "AGE_TOOLTIP"                   =>  "Input your age here.",
            "WEIGHT"                        =>  "Weight:",
            "WEIGHT_TOOLTIP"                =>  "Input your bodyweight here (kg).",
            "HIGH"                          =>  "High:",
            "HIGH_TOOLTIP"                  =>  "Input your height (cm).",
            "SEX"                           =>  "Sex:",
            "SEX_TOOLTIP"                   =>  "Choose man or woman.",
            "HEREDITARY"                    =>  "Hereditary:",
            "HEREDITARY_TOOLTIP"            =>  "If your family member has<br>heredity disease, check yes.",           
            "INJURY_HISTORY"                =>  "Injury history:",
            "INJURY_HISTORY_TOOLTIP"        =>  "Any injury?",
            "REPETITIVE"                    =>  "Repetitive:",
            "REPETITIVE_TOOLTIP"            =>  "Repetitive means one sided<br>or frequent body movements.",
            "LIFESTYLE"                     =>  "Lifestyle",
            "LACK_OF_EXCERSIZE"             =>  "Lack of excersize",
            "LACK_OF_EXCERSIZE_TOOLTIP"     =>  "Do you do exercise regularly?",
            "OVERWEIGHT"                    =>  "Overweight:",
            "UNHEALTHY_FOOD"                =>  "Unhealthy food:",
            "UNHEALTHY_FOOD_TOOLTIP"        =>  "Do you have an allergic <br>reactionof food?",
            "EXTERNAL_FACTORS"              =>  "External Factors",
            "AICOHOL"                       =>  "Alcohol:",
            "AICOHOL_TOOLTIP"               =>  "Do you drink much alcohol?",
            "POLLUTANTS"                    =>  "Pollutants:",
            "POLLUTANTS_TOOLTIP"            =>  "Is there any pollutant in<br>your environment?",
            "DRUGS"                         =>  "Drugs:",
            "DRUGS_TOOLTIP"                 =>  "Do you use drugs?",
            "SMOKING"                       =>  "Smoking:",
            "SMOKING_TOOLTIP"               =>  "Do you smoke?",
            "MALE_OPTION"                   =>  "Male",
            "FEMALE_OPTION"                 =>  "Female",
            "YES_OPTION"                    =>  "Yes",
            "NO_OPTION"                     =>  "No",
            //健康评测模块
            "HEALTH_AWARENESS_TITLE"        =>  "Health Awareness",
            "HEALTH_AWARENESS_GRADE"        =>  "Awareness is:",
            "HEALTH_AWARENESS_LEVEL_1"      =>  "<strong>Underweight</strong>",
            "HEALTH_AWARENESS_LEVEL_2"      =>  "<strong>Normal weight</strong>",
            "HEALTH_AWARENESS_LEVEL_3"      =>  "<strong>Slightly overweight</strong>",
            "HEALTH_AWARENESS_LEVEL_4"      =>  "<strong>Moderately overgeweicht</strong>",
            "HEALTH_AWARENESS_LEVEL_5"      =>  "<strong>Obesity</strong>",
            "HEALTH_AWARENESS_LEVEL_6"      =>  "<strong>Morbidly Overweight</strong>",
            "HEALTH_AWARENESS_DELETED"      =>  "Deleted:",
            "HEALTH_AWARENESS_DISEASE"      =>  "diseases.",
            //用户不兼容提示
            "NOT_SUPPORT"					=>	"Your browser does not support the canvas element."
        ),
        //推理结果的警告文本
        "resultWarning" =>array(
                //多疾病交集结果
                "MULTI_MODEL_BODY"      =>  "possible multi morbidity !",
                //近义词选择框
                "SYNONYM_MODEL_TITLE"     =>  "Please select synonym word",
                "SYNONYM_MODEL_RETUR"     =>  "Return",
                "SYNONYM_MODEL_SAVE"      =>  "Save selections",
                //无疾病结果提示
                "NOT_DISEASE_MODEL_BODY"=>"<b>No disease found for this set of symptoms!</b>",
                //结果警告模块共用
                "MODEL_TIPS"      =>    "Tips",
                "MODEL_CLOSE"     =>    "Close"
            ),
        //用户反馈模块的文本
        "useful"=>array(
                "MODEL_TITLE"       		=>  "Useful",
                "MODEL_BUTTON"      		=>  "useful",
                "MODEL_USER_NAME"   		=>  "User name:",
                "MODEL_SAVE"        		=>  "Save",
                "MODEL_CONCEPT"     		=>  "Concepts:",
            	"MODEL_UNRELATED_CONCEPT"	=>	"UnrelatedConcept:",
            	"SYSTEM_RESULT"				=>	"The result of system:",
            	"DISEASES"					=>	"Diseases:",
                "MODEL_LOADING"     		=>  "Loading"
            ),
        //对推理结果疾病类别分析模块的文本
        "control"=>array(
        	"MODEL_CHART_TITLE"				=>	"Visual Control",
        	"MODEL_CHART_ALL_DISEASES"		=>	"all diseases",
        	"MODEL_CHART_FOUND_DISEASES"	=>	"found diseases",
            "MODEL_TITLE"                   =>  "Plot with horizontal disease number and vertical ICPC class",
            "MODEL_BUTTON"                  =>  "control",
            "ICPC_CLASS_NUMBER_TITLE"       =>  "ICPC Class number",
            "ICPC_CLASS_NAME_TITLE"         =>  "ICPC Class name",
            "MODEL_CLOSE"                   =>  "Close",
            //ICPC列表名称
            "ICPC_CLASS_NAME_A"             =>  "Gen",
            "ICPC_CLASS_NAME_B"             =>  "Blood",
            "ICPC_CLASS_NAME_D"             =>  "Dig",
            "ICPC_CLASS_NAME_F"             =>  "Eye",
            "ICPC_CLASS_NAME_H"             =>  "Ear",
            "ICPC_CLASS_NAME_K"             =>  "Circ",
            "ICPC_CLASS_NAME_L"             =>  "Musc",
            "ICPC_CLASS_NAME_N"             =>  "Neuro",
            "ICPC_CLASS_NAME_P"             =>  "Psych",
            "ICPC_CLASS_NAME_R"             =>  "Resp",
            "ICPC_CLASS_NAME_S"             =>  "Skin",
            "ICPC_CLASS_NAME_T"             =>  "Endo",
            "ICPC_CLASS_NAME_U"             =>  "Urin",
            "ICPC_CLASS_NAME_W"             =>  "Pregn",
            "ICPC_CLASS_NAME_X"             =>  "Fem",
            "ICPC_CLASS_NAME_Y"             =>  "Male",
            "ICPC_CLASS_NAME_Z" 			=>	"Soc"
            )
        ),
    "word_concept"       =>array(
        "MAIN_TITLE"                    => "User Input",
        "WORD_TITLE"                    => "I have the following symptoms:",
        "WORD_TITLE_PLACEHOLDER"        => "for example: he feel abdomind pains,headache", 
        "NOT_WORD_TITLE"                => "I don't have these symptoms:",
        "NOT_WORD_TITLE_PLACEHOLDER"    => "for example: he feel abdomind pains,headache",
        "SYNONYM_CONJUGATE"				=> "After replace synonym/conjugate word:",
        "INCREMENT_CONCEPT"				=> "increment concept",
        "ADVISED_CONCEPT"				=> "Advised Concept:",
        "WORD"							=> "word",
        "SYNONYM_CONJUGATE_TITLE"		=> "conjugate/synonym",
        "SELECT_SYNONYM_WORD"			=> "Please select synonym word",
        "SAVE_SELECTION"				=> "Save selections"
        ),
    "concept_disease"   =>array(
    	"PAGE_TITLE" 				=> "Concept -> Disease",
    	"USER_RESULT_LABEL" 		=> "Please give us useful result:",	
    	"RELATED_SYPTOM_NUMBER"		=> "Group Related Symptom Number:",
    	"UNRELATED_SYPTOM_NUMBER"	=> "Group Unrelated Symptom Number:",
    	"DISEASE_NUMBER"			=> "Group Disease Number:",	
    	"FINAL_DISEASE"				=> "Final Disease:",
    	"LOADING_WAIT"				=> "Loading,please wait",
    	"SUBMIT"					=> "submit",
    	"comb"=>array(
    		"COMB_SETTINGS"		=> "comb settings:",
    		"COMB_PARAMETER_A" 	=> "a (have influence on P factor):",
	    	"COMB_PARAMETER_B" 	=> "b (have influence on R factor):",
		    "CLOSE"				=> "Close",
		    "SAVE"				=> "Save changes"
		    ),
		 "SORT_PARAMETER_R"			=> "sort R",
		 "SORT_PARAMETER_P"			=> "sort P",
        "SORT_PARAMETER_PVW"		=> "sort PPV",
        "SORT_PARAMETER_NVW"		=> "sort NPV",
		 "SORT_COMB"				=> "sort comb",
		 "WITH_SAME_CLASS_DISEASE"	=> "Disease with same ICPC class:",
		 "INCREMENTAL_HAVE" 		=> "have",
    	 "INCREMENTAL_NOT_HAVE" 	=> "don\'t have",
    	 "INPUT_WARNING"			=> "Please input number !",
    	 "TOTAL"					=> "total"
    			
	    			),
    "disease_symptom"   =>array(
    	"PAGE_TITLE" 	=> "Disease -> Symptom",
    	"DISEASE_NAME" 	=> "Disease name:",	
    	"SYMPTOM_LIST"	=> "Symptom List:",
    	"AGE"			=> "Age"
   	)
);
?>