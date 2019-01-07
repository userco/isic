<?php
require_once './confs/conn.php';	
$year = date('Y');
$month = date('n');
if(($month)<7){
	
	$year = $year -1;

}
var_dump($year);
	$query = "TRUNCATE TABLE `susi`";
	$queryPre = $link->prepare($query);
	$queryPre->execute();
	$querystr = "SELECT
			pd.FullName Name, 
			pd.PersonalNumber EGN, 
			convert(varchar(10), pd.BirthDate, 121) BirthDate, 
			faccat.CategoryName FAC, 
			faccat.Abrv Faculty,
			kurscat.CategoryName Course,
			eduform.EducationalFormName as EducationalFormName,
			ci.CityName as cityname, 
			s.FacultyNumber FacultyNumber, 
			eduplan.EducationPlanName Speciality, 
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
 
 	}

	$counter = 0;
	foreach($susi_info as $row){
	
		
	 $statement = $dbh->prepare("INSERT INTO `susi`(`name`, `faculty`,`faculty_number`, `email`, `phone_number`, 	
	 	    `address_city`, `address_street`, `egn`, `gender_name`, `post_code`, birth_date, `speciality`,`course`,`educational_type_name`)

	  VALUES(:name, :faculty,  :facultyNumber, :email, :phoneNumber, :addressCity,  :addressStreet, :egn, :genderName, :postCode, :birthDate, :speciality, :course, :educationalForm)");
    
	$statement->execute(array(
	   "name" => $row['Name'],
	    "egn" => $row['EGN'],
	    "birthDate" => $row['BirthDate'],
	     "faculty" => $row['Faculty'],
	    "facultyNumber" => $row['FacultyNumber'],
	    "speciality" => $row['Speciality'],
	    "course" => $row['Course'],
	    "educationalForm" => $row['EducationalFormName'],
	    "phoneNumber" => $row['GSM'],
	    "email" => $row['Email'],
	    "genderName" => $row['GenderName'],
	    "addressCity" =>$row['cityname'],
	    "addressStreet" => $row['AddressStreet'],
	    "postCode" => $row['postcode']
	    
    ));
	$counter++;
echo "\nPDOStatement::errorCode(): ";
}
if($counter < 5000){
mail('mpenelova@ucc.uni-sofia.bg', 'ISIC Cards -Error', 'SUSI Records are less than 5000');
mail('kneshev@ucc.uni-sofia.bg', 'ISIC Cards -Error', 'SUSI Records are less than 5000');
}
//var_dump($statement->errorInfo());




?>
