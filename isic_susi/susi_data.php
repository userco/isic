<?php
//function getSUSIdata($egn){
//var_dump("wjfdwrf");
require_once './confs/conn.php';	
$year = date('Y');
$month = date('n');
if(($month)<7)
$year = $year -1;
var_dump($year);
	$querystr = "SELECT
			  pd.FullName Name, 
			pd.PersonalNumber EGN, 
			convert(varchar(10), pd.BirthDate, 121) BirthDate, 
			faccat.CategoryName FAC, 
			faccat.Abrv Faculty,
			 
			 
			ci.CityName as cityname, 
			s.FacultyNumber FacultyNumber, 
			eduplan.EducationPlanName, 
			com1.Number GSM, 
			com2.Number Email,
			ad.PostCode  postcode,
	    	ad.AddressText AddressStreet,	
	    	CASE WHEN pd.Sex>0 THEN 'F' ELSE 'M' END GenderName,
	    	eduplan.BeginYear FirstYear 

	FROM       [dbo].[PersonData]         AS pd  
	INNER JOIN [dbo].[Students]           AS s    ON pd.[PersonData_ID]            = s.[PersonData_ID]
	
	INNER JOIN [dbo].[EducationPlans]     AS eduplan    ON eduplan.[EducationPlan_ID]    = s.[EducationPlan_ID]
	INNER JOIN [dbo].[EducationalDegrees] AS edudeg     ON edudeg.[EducationalDegree_ID] = eduplan.[EducationalDegree_ID]
	INNER JOIN [dbo].[EducationalForms]   AS eduform    ON eduform.[EducationalForm_ID]  = eduplan.[EducationalForm_ID]
	INNER JOIN [dbo].[StudentsCategories] AS stucat1    ON stucat1.[Student_ID]      = s.[Student_ID]
	INNER JOIN [dbo].[Categories]         AS faccat     ON faccat.[Category_ID]      = stucat1.[Category_ID] AND faccat.[CategoryType_ID] = 2
	INNER JOIN [dbo].[StudentsCategories] AS stucat3    ON stucat3.[Student_ID]      = s.[Student_ID]
	INNER JOIN [dbo].[Categories]         AS grupcat    ON grupcat.[Category_ID]     = stucat3.[Category_ID] AND grupcat.[CategoryType_ID] = 8
	INNER JOIN [dbo].[CategoryLinks]      AS potcl      ON potcl.[ChildCategory_ID]  = grupcat.[Category_ID]
	INNER JOIN [dbo].[Categories]         AS potcat     ON potcat.[Category_ID]      = potcl.[ParentCategory_ID] AND potcat.[CategoryType_ID] = 9
	INNER JOIN [dbo].[CategoryLinks]      AS kurscl     ON kurscl.[ChildCategory_ID] = potcat.[Category_ID]
	INNER JOIN [dbo].[Categories]         AS kurscat    ON kurscat.[Category_ID]     = kurscl.[ParentCategory_ID] AND kurscat.[CategoryType_ID] = 7
	INNER JOIN [dbo].[CategoryLinks]      AS yearcl     ON yearcl.[ChildCategory_ID] = kurscat.[Category_ID]
	INNER JOIN [dbo].[Categories]         AS yearcat    ON yearcat.[Category_ID]     = yearcl.[ParentCategory_ID] AND yearcat.[CategoryType_ID] = 6 
	LEFT JOIN Communications com1   ON   com1.PersonData_ID=s.PersonData_ID AND com1.CommunicationType_ID = 2
	LEFT JOIN Communications com2   ON   com2.PersonData_ID=s.PersonData_ID AND com2.CommunicationType_ID = 3
	LEFT JOIN Addresses                  ad      ON ad.PersonData_ID = pd.PersonData_ID
	LEFT join [dbo].[Cities]             AS ci         ON ci.[City_ID] = ad.[City_ID]

	WHERE
		yearcat.[Year] =?";
	$susi_info = array();
	$query2 = $link->prepare($querystr);
	$query2->execute(array($year));
	 if ($query2->rowCount()==0) {
	 	echo "no rows found";
	 }
	 else {
		$susi_info = $query2->fetchAll(PDO::FETCH_ASSOC);
 //		return $susi_info;
 	}
// //}
//var_dump($susi_info);

foreach($susi_info as $row){
	
		//, `faculty`, `faculty_number`, `email`, `phone_number`, 	
	 	   // `address_city`, `address_street`, `egn`, `gender_name`)
	 $statement = $dbh->prepare("INSERT INTO `susi`(`name`, `faculty`,`faculty_number`, `email`, `phone_number`, 	
	 	    `address_city`, `address_street`, `egn`, `gender_name`, `post_code`, birth_date)

	  VALUES(:name, :faculty,  :facultyNumber, :email, :phoneNumber, :addressCity,  :addressStreet, :egn, :genderName, :postCode, :birthDate)");
    // $name = $row['EGN'];
    // var_dump($name);
    // $statement->bindParam(':name1', $name);
	$statement->execute(array(
   "name" => $row['Name'],
    "egn" => $row['EGN'],
    "birthDate" => $row['BirthDate'],
     "faculty" => $row['Faculty'],
    "facultyNumber" => $row['FacultyNumber'],
    "phoneNumber" => $row['GSM'],
    "email" => $row['Email'],
    "genderName" => $row['GenderName'],
    "addressCity" =>$row['cityname'],
    "addressStreet" => $row['AddressStreet'],
    "postCode" => $row['postcode'],
    //"fac" => $row['FAC'],
    ));
echo "\nPDOStatement::errorCode(): ";
var_dump($statement->errorInfo());

//));
}/*
//var_dump(getSUSIdata('8810290702'));
//var_dump(getSUSIdata('9608083801'));
   //9711083225    9608083801
$sql = "select distinct(CategoryName), CategoryType_ID
from Categories
WHERE CategoryType_ID = 28

";
$query2 = $link->prepare($sql);
$query2->execute(array());//$egn));
$susi_info = $query2->fetchAll(PDO::FETCH_ASSOC);
var_dump($susi_info);
//return $susi_info;
*/
// 	}
// //}

?>
