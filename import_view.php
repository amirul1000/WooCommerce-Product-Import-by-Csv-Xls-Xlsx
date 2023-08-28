<?php
   set_time_limit(0);
   global $wpdb, $post;
  // $sql = "SELECT * FROM wp_posts WHERE post_type LIKE 'surgerymedical' ORDER BY post_title ASC";
   // $result = $wpdb->get_results($sql);

?>

<form method="post" action="admin.php?page=upload_product" enctype="multipart/form-data">

    <?php
	  
	?>
    <!--Category
    
    
    <select  name="category" style="width:500px">
       <option value=""></option>
    </select>
    <br>-->
    <div style="  border: 1px solid black;padding:5px;margin:5px;">
      <h3>Import Product</h3>
    <input type="file" name="file" class="button  mt-4">
    <input type="submit" name="import" value="Import" class="button  mt-4">
    </div>
</form>


<?php
error_reporting( E_ALL );
   
     require dirname(__FILE__) . '/vendor/autoload.php';

	  use PhpOffice\PhpSpreadsheet\Spreadsheet;
	  use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	  use PhpOffice\PhpSpreadsheet\Writer\Xls;
	  use PhpOffice\PhpSpreadsheet\Writer\Csv;
      
   if($_POST['import']){
	   
	   
		$file_picture = "";
		$created_at = date("Y-m-d H:i:s");	 
		$updated_at = "";
		if(isset($_FILES["file"]["name"]))
		{
			$path = $_FILES["file"]["tmp_name"]; 
			
			$arr_file 	= explode('.', $_FILES["file"]["name"]);
			$extension 	= end($arr_file);
			
			
			$reader=NULL;
			if('csv' == $extension) {     
			  $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
			} else if('xls' == $extension) {  
			  $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
			} else{     
			  $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			} 
			
			$spreadsheet 	= $reader->load($path);
			$sheet_data 	= $spreadsheet->getActiveSheet()->toArray(); 
			
			for($row=1; $row<=count($sheet_data); $row++)
		    {
			    $Item_Number	 = isset($sheet_data[$row][0])?$sheet_data[$row][0]:'';
				$Title	 = isset($sheet_data[$row][1])?$sheet_data[$row][1]:'';
				$Description	 = isset($sheet_data[$row][2])?$sheet_data[$row][2]:'';
				$Sell_Price	 = isset($sheet_data[$row][3])?$sheet_data[$row][3]:'';
				$Expr1003	 = isset($sheet_data[$row][4])?$sheet_data[$row][4]:'';
				$Category_Number = isset($sheet_data[$row][5])?$sheet_data[$row][5]:'';
				$Date_Added = isset($sheet_data[$row][6])?$sheet_data[$row][6]:'';
				$Quantity_on_Hand   = isset($sheet_data[$row][7])?$sheet_data[$row][7]:'';
				$Detail_Cat   = isset($sheet_data[$row][8])?$sheet_data[$row][8]:'';
				$Catagory_Detail_ID_Code   = isset($sheet_data[$row][9])?$sheet_data[$row][9]:'';
				$General_Catagory   = isset($sheet_data[$row][10])?$sheet_data[$row][10]:'';
				$General_Catagory_ID_Code   = isset($sheet_data[$row][11])?$sheet_data[$row][11]:'';
				$Nationality   = isset($sheet_data[$row][12])?$sheet_data[$row][12]:'';
				$Nationality_ID_Code   = isset($sheet_data[$row][13])?$sheet_data[$row][13]:'';
				$Date_sold   = isset($sheet_data[$row][14])?$sheet_data[$row][14]:'';
				
				$attributes  = array('Item_Number'=>$Item_Number,
				                    '_regular_price' =>$Sell_Price,
									'_sale_price' =>$Sell_Price,
									'_price' =>$Sell_Price,
									'Expr1003' =>$Expr1003, 
									'Category_Number' =>$Category_Number,
									'Date_Added' =>$Date_Added,
									'Quantity_on_Hand' =>$Quantity_on_Hand,
									'Detail_Cat' =>$Detail_Cat,
									'Catagory_Detail_ID_Code'=>$Catagory_Detail_ID_Code,
									'General_Catagory'=>$General_Catagory,
									'General_Catagory_ID_Code' =>$General_Catagory_ID_Code,
									'Nationality' =>$Nationality,
									'Nationality_ID_Code'=>$Nationality_ID_Code,
									'Date_sold'=>$Date_sold);
									
									
				
				// Create an array of post data for the new post
				$new_post = array(
					'post_title'   => substr($Title,0,30), 
					'post_type'   => 'product',
					'post_content' => $Description, 
					'post_status'  => 'publish', 
					'post_author'  => 1, 
					'post_date'    => date("Y-m-d H:i:s")
				);
				// Insert post into the database
				$post_id = wp_insert_post($new_post, true); // Use $wp_error set to true for error handling
				wcproduct_set_attributes($post_id, $attributes);
				// Check if there was an error during post insertion
				if (is_wp_error($post_id)) {
					// Error occurred while inserting the post
					echo "Error: " . $post_id->get_error_message();
				} else {
					foreach($attributes as $meta_key=>$meta_value){
						 if($meta_key == 'General_Catagory' ){
							 //wp_set_post_terms( $post_id,  'Abc', 'product_cat', false );
							 wp_set_object_terms( $post_id, $meta_value, 'product_cat', false );

							 continue;
						 }
						add_metadata( 'post', $post_id, $meta_key, $meta_value, false );
					}
					// The post was successfully inserted, and $post_id contains the post ID
					echo "Post inserted successfully. New Post ID: " . $post_id;
				}
				
			}
			echo "Insertion has been completed successfully";
		
		}
   }
 
 
 function wcproduct_set_attributes($post_id, $attributes) {
    $i = 0;
    // Loop through the attributes array
    foreach ($attributes as $name => $value) {
        $product_attributes[$i] = array (
            'name' => htmlspecialchars( stripslashes( $name ) ), // set attribute name
            'value' => $value, // set attribute value
            'position' => 1,
            'is_visible' => 1,
            'is_variation' => 1,
            'is_taxonomy' => 0
        );

        $i++;
    }

    // Now update the post with its new attributes
    update_post_meta($post_id, '_product_attributes', $product_attributes);
} 
?>