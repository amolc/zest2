<?php 
class adminhome_model extends CI_Model
{
	
	function updateFaq($data,$id)
		{
			$this->db->where('id',$id);		
			$this->db->update('faq',$data);		
		}
	
	function insertFaqCat($data)
	{
			$this->db->insert('helpcategory',$data);
	}
	function insertFaq($data)
		{
			$this->db->insert('faq',$data);
		}
	
	function getAllFaq($id)
		{
		if(!$id)
			$query=$this->db->query(" select * from sg_faq as F,sg_helpcategory as HP where pID = '$id' and HP.helpcatID = F.helpcatID order by F.helpcatID ");
		else
			$query=$this->db->query(" select * from sg_faq where pID = '$id' order by faqorder ");
		return $query->result_array();
		}
	
	function updateFaqCat($data,$id)
		{
			$this->db->where('helpcatID',$id);		
			$this->db->update('helpcategory',$data);		
		
		}
	
	
	
function getCategories($offset,$per_page)
	{
			$query=$this->db->query("Select * from categories");
			return $query->result_array();
	}

	function gUpdate($tableName,$where,$qData)
		{	
			foreach($where as $col=>$val)
				$this->db->where($col,$val);		
			$this->db->update($tableName,$qData);		
		}

	function gInsert($tableName,$data)
	{
	
			$this->db->insert($tableName, $data); 
			return $ID = $this->db->insert_id();	
		
	}

	function gInsertEID($tableName,$data,$emailID)
		{
			$this->db->insert($tableName, $data); 
			return $ID = $this->db->insert_id();	
		}


	function gDelete($tableName,$where)
		{
			foreach($where as $col=>$val)
				$this->db->where($col,$val);		
			$this->db->insert($tableName); 
		}

      function glogoUpdates($tableName,$bID,$data)
			{
				// $userID = $this->session->userData('userID');
				//$query=$this->db->query("update users  set userID='$userID'");
			    $this->db->where('cat_id',$cat_id);
				$this->db->update('mcategories', $data); 
				
				
			}
	

	function countNews($type)
		{
		$query=$this->db->query(" select * from news  ");
		return $query->num_rows();		
		}

	function getArticleData($type,$offset,$per_page)
		{
			$query=$this->db->query(" select * from article limit $offset,$per_page ");
			return $query->result_array();
		}
	function getNewsData($type,$offset,$per_page)
		{
			$query=$this->db->query(" select * from news limit $offset,$per_page ");
			return $query->result_array();
		}
	
function getSubCategories($offset,$per_page)
	{
			$query=$this->db->query("Select * from subcategories");
			return $query->result_array();
	}


	
	function countMemberRows()
		{
		$query=$this->db->query(" select * from users ");
		return $query->num_rows();		
		}
	
	function countCommRows()
		{
		$query=$this->db->query(" select * from users as U, audio as A, mcomments as C where C.aID = A.aID and U.userID = C.userID order by ratetime desc  ");
		return $query->num_rows();		
		
		}
	
	function getAllUse($offset,$per_page)
		{
		$query=$this->db->query(" select * from users limit $offset,$per_page ");
		return $query->result_array();
		
		
		}

	
	function GetEmailtemplate($id)
	{
		$query=$this->db->query(" select * from listingsdb as LD, users as U where listingsdb_id = '$id' and LD.listingsdb_id = U.userID ");
		foreach($query->result_array() as $row)
			return $row['emailAdd']; 	
	}
	
	function getUserEmailAddress($uid)
	{
		$query=$this->db->query(" select * from emailtemplate where id = '$id' ");
		return $query->result_array(); 	
	}
		
	function getUserEmailAddress55($userdb_id)
	{
		$query=$this->db->query(" select * from users as U,listingsdb as LD where U.userID = LD.userdb_id and userdb_id = '$userdb_id' ");
		foreach($query->result_array() as $row5)
			return $row5['emailAdd']; 	
	}
		
	function updateRelate($data5)
	{
		$this->db->where('id', '5'); 
		$this->db->update('relateitem', $data5); 
	}
	
	
	
	function insertEmailTemplate($name,$details,$when)
	{
		$query=$this->db->query(" INSERT INTO `emailtemplate` (`id`, `name`, `details`, `sendWhen`) VALUES (NULL, '$name', '$details', '$when'); ");	
	}
	
	function deleteEmailTemplate($id)
	{
		$query=$this->db->query(" delete from emailtemplate where id = '$id' ");	
	}
	
	
	function getAdministratorLoingInfo()
	{
		$query=$this->db->query("Select * from adminsettings");
		return $query->result_array(); 
	}

	function deleteUser($uID)
	{
			$query=$this->db->query("delete from users where userID ='$uID' ");
	}
	
	function check($userID)
	{

		$query=$this->db->query("select * from users where parentID ='$userID' ");
        return $query;
	}
	
	function insertIntoUser($data)
	{
		$this->db->insert('users', $data); 
		$a = $this->db->insert_id();
		return $a;
	}
	

	function	updateIntoPerson($data1)
		{

		$this->db->where('userID', $data1['userID']);
		$this->db->update('users', $data1); 
          }

	
	function searchUserRecord($user,$pass)
	{
			$query=$this->db->query("Select * from users where username ='$user' and password = '$pass' and status = '1' ");
			return $query->result_array();
	}
	
	function checkUser($userID,$userName)
	{
			$query=$this->db->query("Select * from users where userID ='$userID' and userName='$userName' ");
			return $query->result_array();
	}
	
	

	function getUserDetails($cat_id)
	{
		$query=$this->db->query(" Select * from mcategories where cat_id = '$cat_id'");
		return $query->result_array();	
	}
	
	
	
		function allUsers($userID)
	{
		$query=$this->db->query(" Select * from users where userID = '$userID'");
		return $query->result_array();	
	}
	
	
	
	function getUserDetails1($uid)
	{
		$query=$this->db->query(" Select * from users where userID = '$uid'");
		
		
		return $query->result_array();	
	}
	
	
	function counUsersRows()
	{
		$query=$this->db->query(" Select * from users ");
		return $query->num_rows();	
	}

	function getAllUsers1($offset,$per_page)
	{
		$query=$this->db->query(" Select * from mcategories  order by cat_id ");
		return $query->result_array();	
         print_r($result_array);

	}
	
		function getUsers($offset,$per_page,$userID)
	{
	     $userID = $this->input->post('userID');		
		 $query=$this->db->query(" Select * from users where userID='$userID' limit $offset,$per_page");
		 return $query->result_array();	
         print_r($result_array);
	}
	
	function getAllCoupons($offset,$per_page)
	{
		$query=$this->db->query(" Select * from coupons  order by expDate ");
		return $query->result_array();	
         print_r($result_array);

	}
   function getAllUsers($offset,$per_page)
	{  
	   // $userID=$this->input->post($userID);
		 $query=$this->db->query(" Select * from users ");
		 return $query->result_array();	
         print_r($result_array);
	}

	function getAllUsersSortName($offset,$per_page)
	{
		$query=$this->db->query(" Select * from users order by userName limit $offset,$per_page");
		return $query->result_array();	
	}


	function getAllUsersSortStatus($offset,$per_page)
	{
		$query=$this->db->query(" Select * from users  order by status DESC limit $offset,$per_page");
		return $query->result_array();	
	}

	
	function getPendingUserList()
	{
		$query=$this->db->query(" Select * from users as U,pendinguserlist as PU where U.userID = PU.userID group by PU.userID ");
		return $query->result_array();	
	}
	
	function updateUserListingToAccepted($userID)
	{
		$query = $this->db->query("update users set status = '1' where userID = '$userID' ");
		$query = $this->db->query("delete from pendinguserlist where userID = '$userID' ");	
	}
	
	function updateUserListingToDeny($userID)
	{
		$query = $this->db->query("delete from users where userID = '$userID' ");
		$query = $this->db->query("delete from pendinguserlist where userID = '$userID' ");		
	
	}
	//---------  End User Management
	//-----------  Category
	
	function countRows()
	{
			$query = $this->db->query("Select * from category where pID = 0 ");
			return $query->num_rows();
	}


	function getAllCategory($offset,$per_page)
	{
			$query=$this->db->query("Select * from category where pID = 0 order by catName desc limit $offset,$per_page");
			return $query->result_array();
	}
	
	function getAllCategory1($pID,$offset,$per_page)
		{
			$query=$this->db->query("Select * from category where pID = '$pID' order by catName desc limit $offset,$per_page");
			return $query->result_array();
		}
	
	function getpID($catID)
	{
			$query=$this->db->query("Select * from category where catID = '$catID' ");
			foreach($query->result_array() as $row5)
				return $row5['pID'];
	}
	
	function getSubCategory($pID)
	{
			$query=$this->db->query("Select * from category where pID = '$pID' ");
			return $query->result_array();
	
	}

	function getCatDetails($catID)
	{
			$query=$this->db->query("Select * from  category where catID = '$catID'");
			return $query->result_array();
	}
function getSubCatDetails($subCatID)
	{
			$query=$this->db->query("Select * from  subcategories where subCatID = '$subCatID'");
			return $query->result_array();
	}
	function checkCategory($categoryName)
	{
		$query = $this->db->query("SELECT * FROM categories WHERE catName='$categoryName' ");
		return $query->result_array();	
	}
	

	function updateCategory($data5,$catID,$mainCategory)
		{
	
		$this->db->where('catID', $catID);
		$this->db->update('category', $data5);

	}
	

	  
	function updateSubCat($data5,$subCatID,$mainCategory)
		{
	
		$this->db->where('subCatID', $subCatID);
		$this->db->update('subcategories', $data5);

	}
	function getCategoryName($catID)
		{
		$query = $this->db->query("SELECT * FROM category WHERE catID='$catID' ");
		foreach($query->result_array() as $row)
			return $row['English_catName'];
		}
	
	
	function deleteCategory($catID)
	{
			$query=$this->db->query("DELETE FROM category WHERE catID = '$catID'");
	}
	
	function getAllSubCategories($pID)
	{
			//echo "Select * from category as C,catparent as CP WHERE C.catID=CP.catID and CP.pID = '$pID' and org = '1'";
			$query=$this->db->query("Select * from category as C,catparent as CP WHERE C.catID=CP.catID and CP.pID = '$pID' and org = '1'");
			return $query->result_array();
	}
	
	


	function insertCategory($data5,$catID)
	{
	
		$this->db->insert('category', $data5); 
		$insertID = $this->db->insert_id();
	}


	function insertSubCategory($data5)
	{
	
		$this->db->insert('subcategories', $data5); 
		$insertID = $this->db->insert_id();
	
}
	
	
		function getCatURL($id)
			{
			$query=$this->db->query("Select * from category where catID = '$id' ");
		foreach($query->result_array() as $row)
			return $row['catUrl'];
			}	
			
		function searchCategoryID($q)
			{
			$query=$this->db->query("Select * from category where catUrl = '$q' ");
		foreach($query->result_array() as $row)
			return $row['catID'];
			}
	
		function getParentID($catID)	
		{
			$query=$this->db->query("Select * from category as C,catparent as CP WHERE C.catID=CP.catID and C.catID = '$catID'");
		foreach($query->result_array() as $row)
			return $row['pID'];
		}


				
	
		function getCatName($id)
			{
			$LANGUAGE = $this->session->userdata('language');
			$query=$this->db->query("Select * from category where catID = '$id'");
		foreach($query->result_array() as $row)
			return $row[$LANGUAGE.'_catTitle'];
			}


		function getCatName55($id)
			{
			$LANGUAGE = $this->session->userdata('language');
			$query=$this->db->query("Select * from category where catID = '$id'");
		foreach($query->result_array() as $row)
			return $row['English_catName'];
			}

	
	
	function getRelatedCatDetails($catID)
		{
			$query=$this->db->query("Select * from category as C,catparent as CP WHERE C.catID=CP.catID and C.catID = '$catID' group by CP.pID");
			return $query->result_array();
		
		}
	
	//------------  End Category
	
	
	//------------  Sub Category
	
	function countSubCatRows($mainCatID)
	{
			$query=$this->db->query("Select * from category as C,catparent as CP where C.catID=CP.catID and CP.pID = '$mainCatID' group by C.catID");
			return $query->num_rows();
	}

	function getAllSubCategory($mainCatID,$offset,$per_page)
	{
			$query=$this->db->query("Select * from category as C,catparent as CP where C.catID=CP.catID and CP.pID = '$mainCatID' group by C.catID limit $offset,$per_page ");
			return $query->result_array();	
	}
	

	function getAllMainCategories1()
	{
			$query = $this->db->query("Select * from  categories ");
			return $query->result_array();		
	}

	




	function getAllMainSubCategories1()
	{
			$query = $this->db->query("Select * from   subcategories ");
			return $query->result_array();		
	}



	function getAllMainCategories()
	{
			$query=$this->db->query("Select * from category");
			return $query->result_array();
	}	

	function getAllMainCategory($mainCat)
	{
			$LANGUAGE = $this->session->userdata('language');
			$query = $this->db->query("SELECT * FROM category WHERE catID='$mainCat' ");
			foreach($query->result_array() as $row)
				return $row[$LANGUAGE.'_catName'];		
	}

	function checkSubCategory($subCatName,$mainCategory)
	{
			$query=$this->db->query("Select * from subcategories where catID='$mainCategory' and subCatName='$subCatName'");
			return $query->result_array();
	}


	function deleteSubCategory($id)
	{
			$query=$this->db->query("DELETE FROM subcategory WHERE subCatID = '$id'");
	}
	

	
	function updateSubCategory($subCatID,$subCategory,$mainCategory,$catTitle,$catKeyword,$catDescription)
	{
			
			$query=$this->db->query("UPDATE subcategory SET subCatName='$subCategory',subCatTitle = '$catTitle', subCatKeyword = '$catKeyword' , subCatDiscription ='$catDescription' where subCatID='$subCatID'");
	}


	
	//-----------  End Sub Category
	
		
	//------------ Email template

	function countRowsLog()
	{
		$query=$this->db->query("Select * from userlog where userid = 'Administrator' order by dateTime desc ");
		return $query->num_rows();
	}

	function getUserLog($offset,$per_page)
	{
		$query=$this->db->query("Select * from userlog order by dateTime desc limit $offset , $per_page");
		return $query->result_array();
	}

	function enterLogInfo($ip,$post,$status,$agent)
	{
		$dt = date("Y-m-d H:i:s");	
		
		
		$query=$this->db->query(" INSERT INTO `userlog` ( `logid` , `ipaddress` , `userid` , `browser` , `dateTime` , `status` )
VALUES (NULL , '$ip', '$post', '$agent', '$dt', '$status') ");

	}
		
	function countRows9()
	{
		$query=$this->db->query("Select * from emailtemplate");
		return $query->num_rows();
	}

	function showAllEmailTemplates()
	{
		$query=$this->db->query(" SELECT * FROM emailtemplate ");
		return $query->result_array();
	}
	
	function showWebpageById1($id)
	{
		$query=$this->db->query("Select * from emailtemplate WHERE id='$id' ");
		return $query->result_array();
	}

	function editTemplate($id,$details,$when)
	{	
		$query =  $this->db->query("UPDATE `emailtemplate` SET `details` = '$details', sendWhen = '$when' WHERE `id` = '$id' LIMIT 1 ;");
	}
	
	//----------- End Email Template
	
	//---------- Web Pages
	
	

	
	function showWebpageById($id)
	{
		$query=$this->db->query("Select * from webpages WHERE id='$id' ");
		return $query->result_array();
	}

	function showAllwebpages($offset,$per_page)
	{
		$query=$this->db->query("Select * from webpages limit $offset , $per_page");
		return $query->result_array();
	}
	
	
	
	function getExtraPagesList()
	{
		$query=$this->db->query("Select * from webpages ");
		return $query->result_array();
	}

	function countRows8()
	{
		$query=$this->db->query("Select * from webpages");
		return $query->num_rows();
	}

	
	


	function editwebpage($id,$details)
	{
		$query =  $this->db->query("UPDATE webpages SET details='$details' WHERE id='$id'  ");
	//	$query =  $this->db->query("UPDATE webpages SET details='$details' and name='$name' WHERE id='$id'  ");
	}
	
	

	function editSubAdmin($data,$subAdminID)
	{
		$this->db->where('subAdminID',$subAdminID);
		$this->db->update('subadmin',$data);
	}

	function editUserDetails($data,$subAdminID)
	{
		$this->db->where('userID',$subAdminID);
		$this->db->update('users',$data);
	}

	
	function addNewUser($data5)
		{
		$this->db->insert('users', $data5); 
		$ID = $this->db->insert_id();
		$data55['userID'] = $ID;
		$data55['validCode'] = md5($data5['username']);
		$this->db->insert('validateuser', $data55); 
		return $ID;
		}
	
	function getUserName($userID)
	{
		$query=$this->db->query(" Select * from users as U ,validateuser as VU where VU.userID='$userID' and U.userID = VU.userID  ");
		foreach($query->result_array() as $row)
			return $row['username'];		
	}
	
	function userIsValidated($userID)
	{
		$query=$this->db->query(" update users set status = '1' where userID='$userID' ");
		$query=$this->db->query(" delete from validateuser where userID='$userID' ");
	}
	
	function updateOldPassword($userID,$newPass)
		{
		$query=$this->db->query(" update users set password  = '$newPass' where userID='$userID' ");
		}
	
	function checkUsernameEmail($username,$email)
	{
		$query=$this->db->query(" select * from users where username = '$username' and  mailID = '$email' ");
		foreach($query->result_array() as $row)
			return $row['userID'];
		return false;
	}
	
	function checkUsernamePassword($username,$password) //55277 
	{
		$query=$this->db->query(" select * from users where username = '$username' and  password = '$password' and status = '1' ");
		return $query->result_array();
	}
	
	function updateFrontUserUpdate($userID,$data)
	{
		$this->db->where('userID', "$userID"); 
		$this->db->update('users', $data); 
	}

	

	
	function searchAll($q)
		{
		//echo "select * from audio as A,category as C where aName like '%$q%' and C.catID = A.catID order by aTime desc";
		$query=$this->db->query(" select * from category where catName like '%$q%' order by pID desc ");
		return $query->result_array();		
		
		
		}
	
	

	function getCategoryPendingList()
	{
		$query=$this->db->query(" select * from category where status  = '0' ");
		return $query->result_array();			
	}
	
	function updatePendingCateogryList($id)
	{
		$query=$this->db->query(" update category set status = '1' where catID = '$id' ");
	}
	function deletePendingCateogryList($id)
	{
		$query=$this->db->query(" delete from category where catID = '$id' ");
	}
	
           
   function updatename($id,$name)
   {
     $query=$this->db->query("update webpages set name='$name' where id='$id'");
   
   }		   
		   
		   
		   
		   
		   
		   
		   
		   
		   
}	


?>