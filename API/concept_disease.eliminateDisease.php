<?php
	include("../logic/common.inc.php");
    $db = getDB();
    $pageResult = array();
    $finalDiseaseDetail = json_decode($_SESSION["finalDieseaseToPage"],true);
    // print_r($finalDiseaseDetail);
    // 针对PVW 字段进行降序排序
    $finalDiseaseDetail = arraySort($finalDiseaseDetail,'PVW',SORT_DESC);
    // 计算PVW最大值的疾病和其他疾病之间的相似度
    $finalDiseaseDetailWithSim = calculateSimilarityAmongDiseases($finalDiseaseDetail);
    // 针对相似度进行排序
    $finalDiseaseDetailWithSim = arraySort($finalDiseaseDetailWithSim,'similarity',SORT_DESC);
    // 获取相似度最低的疾病
    $minimumRelateSimDiseaseSiteId = $finalDiseaseDetailWithSim[count($finalDiseaseDetailWithSim)-1]['disease_site_id'];
    // 获取相似度最低疾病的症状列表
    $symptoms = getSymptomsFromDiseaseSiteId($minimumRelateSimDiseaseSiteId);
    // 获取相关症状的concept数据
    $symptoms = getRelatedConceptsBySymptoms($symptoms);
    $pageResult['mainDisease'] = $finalDiseaseDetailWithSim[0];
    $pageResult['lowestSimDisease'] = $finalDiseaseDetailWithSim[count($finalDiseaseDetailWithSim)-1];
    $pageResult['symptoms'] = $symptoms;
    echo json_encode($pageResult);


/**
 * 二维数组根据某个字段排序
 * @param array $array 要排序的数组
 * @param string $keys   要排序的键字段
 * @param string $sort  排序类型  SORT_ASC     SORT_DESC
 * @return array 排序后的数组
 */
function arraySort($array, $keys, $sort = SORT_DESC) {
    $keysValue = [];
    foreach ($array as $k => $v) {
        $keysValue[$k] = $v[$keys];
    }
    array_multisort($keysValue, $sort, $array);
    return $array;
}

/**
 * 获取与第一个疾病相似度最低的那个疾病id
 * @param array $finalDiseases 最终疾病的详细疾病信息
 */
function calculateSimilarityAmongDiseases($finalDiseases){
    foreach ($finalDiseases as $k => $disease){
        if($k == 0){
            // 第一个疾病跳过
            $finalDiseases[$k]['similarity'] = 100;
            continue;
        }
        // 主疾病
        $mainDisease = $finalDiseases[0]["disease_name"];
        $sideDisease = $disease["disease_name"];
        similar_text($mainDisease,$sideDisease,$similarity);
        $finalDiseases[$k]['similarity'] = round($similarity,2);
    }
    return $finalDiseases;
}


function getSymptomsFromDiseaseSiteId($diseaseSiteId){
    global $db;
    $concepts = [];
    $sql = "SELECT DISTINCT name_decode,site_id FROM symptom WHERE site_id in (SELECT symptom_site_id FROM dis_symp LEFT JOIN `symp_concept` ON symp_concept.site_id = dis_symp.symptom_site_id WHERE `disease_site_id` = '{$diseaseSiteId}')";
    $result = $db->query($sql);
    if($result){
        while ($row = $result->fetch_array()) {
            $temp = array();
            $temp["symptomName"] = $row['name_decode'];
            $temp["sympSiteId"] = $row['site_id'];
            $temp["selected"] = true;//默认选中状态
            $temp["show"] = false;
            $concepts[] = $temp;
        }
    }
    return $concepts;
}

function getRelatedConceptsBySymptoms($symptoms){
    global $db;
    $newSymptoms = array();
    foreach ($symptoms as $symptom){
        $symptom['concepts'] = array();
        $sympSiteId = $symptom['sympSiteId'];
        $sql = "SELECT DISTINCT concept.concept_id as concept_id,concept.keyword as keyword FROM symp_concept LEFT JOIN concept ON concept.concept_id = symp_concept.concept_id WHERE site_id = '{$sympSiteId}'";
        $result = $db->query($sql);
        if($result && $result->num_rows>0){
            while ($row = $result->fetch_array()) {
                $temp = array();
                $temp['conceptId'] = $row['concept_id'];
                $temp['concept'] = $row['keyword'];
                $symptom['concepts'][] = $temp;
            }
            $newSymptoms[] = $symptom;
        }
    }
    return $newSymptoms;
}


?>