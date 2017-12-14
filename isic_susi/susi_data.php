<?php
//function getSUSIdata($egn){
	try{
		$link = new PDO("sqlsrv:Server=62.44.109.144;Database=SU_STUDENTDATABASE", 'maria', '123456');
	} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
			exit();
	}
	try{
	$dbh = new PDO('mysql:dbname=isic;host=localhost;charset=utf8', 'root', 'strongly');
	} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
			exit();
	}
	$querystr = "SELECT
			  pd.FullName Name, 
			pd.PersonalNumber EGN, 
			convert(varchar(10), pd.BirthDate, 121) BirthDate, 
			faccat.CategoryName, 
			faccat.Abrv Faculty,
			 
			 
			ci.CityName as cityname, 
			s.FacultyNumber FacultyNumber, 
			eduplan.EducationPlanName, 
			com1.Number GSM, 
			com2.Number Email,
	    	ad.AddressText AddressStreet,	
	    	CASE WHEN pd.Sex>0 THEN 'F' ELSE 'M' END GenderName,
	    	eduplan.BeginYear FirstYear 

	FROM       [dbo].[PersonData]         AS pd  
	INNER JOIN [dbo].[Students]           AS s    ON pd.[PersonData_ID]            = s.[PersonData_ID]
	INNER JOIN Addresses                  ad      ON ad.PersonData_ID = pd.PersonData_ID
	inner join [dbo].[Cities]             AS ci         ON ci.[City_ID] = ad.[City_ID]
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
	INNER JOIN [dbo].[CategoryLinks]      AS edpcl      ON edpcl.[ChildCategory_ID]  = yearcat.[Category_ID]
	INNER JOIN [dbo].[Categories]         AS edpcat     ON edpcat.[Category_ID]      = edpcl.[ParentCategory_ID] AND edpcat.[CategoryType_ID] = 28
	INNER JOIN [dbo].[StudentsCategories] AS stucat4    ON stucat4.[Student_ID]      = s.[Student_ID]
	INNER JOIN [dbo].[Categories]         AS spec    ON spec.[Category_ID]     = stucat4.[Category_ID] AND spec.[CategoryType_ID] = 5
	LEFT JOIN Communications com1   ON   com1.PersonData_ID=s.PersonData_ID AND com1.CommunicationType_ID = 2
	LEFT JOIN Communications com2   ON   com2.PersonData_ID=s.PersonData_ID AND com2.CommunicationType_ID = 3

	WHERE
		yearcat.[CategoryName] = 2017
	--AND pd.PersonalNumber IN ( ? )";
	$susi_info = array();
	$query2 = $link->prepare($querystr);
	$query2->execute(array());//$egn));
	// if ($query2->rowCount()==0) {
	// 	return false;
	// }
	// else {
		$susi_info = $query2->fetchAll(PDO::FETCH_ASSOC);
// 		return $susi_info;
// 	}
// //}
foreach($susi_info as $row){
	//, `faculty`, `faculty_number`, `email`, `phone_number`, 	
	 	   // `address_city`, `address_street`, `egn`, `gender_name`)
	 $statement = $dbh->prepare("INSERT INTO `susi`(`name`, `faculty`,`faculty_number`, `email`, `phone_number`, 	
	 	    `address_city`, `address_street`, `egn`, `gender_name`)

	  VALUES(:name, :faculty, :facultyNumber, :email, :phoneNumber, :addressCity,  :addressStreet, :egn, :genderName)");
    // $name = $row['EGN'];
    // var_dump($name);
    // $statement->bindParam(':name1', $name);
	$statement->execute(array(
   "name" => $row['Name'],
    "egn" => $row['EGN'],
    // //"birth_date" => $row['BirthDate'],
     "faculty" => $row['Faculty'],
    "facultyNumber" => $row['FacultyNumber'],
    "phoneNumber" => $row['GSM'],
    "email" => $row['Email'],
    "genderName" => $row['GenderName'],
    "addressCity" =>$row['AddressStreet'],
    "addressStreet" => $row['AddressStreet']
    ));
echo "\nPDOStatement::errorCode(): ";
print $statement->errorCode();

//));
}
//var_dump(getSUSIdata('8810290702'));
//var_dump(getSUSIdata('9608083801'));    //9711083225    9608083801
?>