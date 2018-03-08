<?php
//function getSUSIdata($egn){
	try{
		$link = new PDO("dblib:version=7.0;charset=UTF-8;host=62.44.109.144;dbname=SU_STUDENTDATABASE", 'maria', '123456');
	} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
			exit();
	}
	try{
	$dbh = new PDO('mysql:dbname=isic8;host=localhost;charset=utf8', 'root', 'strongly');
	} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
			exit();
	}
	$querystr = "SELECT  
				pd.FullName Name,
                pd.PersonalNumber EGN,
                convert(varchar(10), pd.BirthDate, 121) BirthDate,
             	com1.Number GSM,
               com2.Number Email,
              ad.AddressText AddressStreet,
                CASE WHEN pd.Sex>0 THEN 'F' ELSE 'M' END GenderName,
               ad.PostCode postcode,
               c.CityName cityname,
               teachercat.Abrv Faculty

FROM [PersonData] as pd inner join [Teachers] as t on
pd.[PersonData_ID]=t.[PersonData_ID]
inner join TeachersCategories as tc on t.Teacher_ID = tc.Teacher_ID
inner join Categories as teachercat on tc.Category_ID = teachercat.Category_ID and teachercat.CategoryType_ID=2
--inner  join  [Staff] as s on s.PersonData_ID=t.PersonData_ID and s.ByMainContract=1
                                       left join  [Addresses] as ad on ad.PersonData_ID=t.PersonData_ID
                                      left join  [Cities] as c on c.City_ID=ad.City_ID
                                      -- left join  [AddressTypes] as adt on ad.AddressType_ID =
--adt.AddressType_ID and adt.Permanent=1
                                    
                                  left join  [Communications] as com1 on
com1.PersonData_ID=pd.PersonData_ID and com1.CommunicationType_ID=2
               left join  [Communications] as com2 on
com2.PersonData_ID=pd.PersonData_ID and com2.CommunicationType_ID=3";
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


		var_dump($susi_info);

foreach($susi_info as $row){
	//, `faculty`, `faculty_number`, `email`, `phone_number`, 	
	 	   // `address_city`, `address_street`, `egn`, `gender_name`)
	 $statement = $dbh->prepare("INSERT INTO `susi`(`name`, `faculty`,`faculty_number`, `email`, `phone_number`, 	
	 	    `address_city`, `address_street`, `egn`, `gender_name`, `post_code`, birth_date)

	  VALUES(:name, :faculty, :facultyNumber, :email, :phoneNumber, :addressCity,  :addressStreet, :egn, :genderName, :postCode, :birthDate)");
    // $name = $row['EGN'];
    // var_dump($name);
    // $statement->bindParam(':name1', $name);
	$statement->execute(array(
   "name" => $row['Name'],
    "egn" => $row['EGN'],
    "birthDate" => $row['BirthDate'],
     "faculty" => $row['Faculty'],
    "facultyNumber" => NULL, //$row['FacultyNumber'],
    "phoneNumber" => $row['GSM'],
    "email" => $row['Email'],
    "genderName" => $row['GenderName'],
    "addressCity" =>$row['cityname'],
    "addressStreet" => $row['AddressStreet'],
    "postCode" => $row['postcode'],
    ));
echo "\nPDOStatement::errorCode(): ";
print $statement->errorCode();
//("hegdewr");
//));
}
