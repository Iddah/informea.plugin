<?php
if(!class_exists( 'imea_admin_pictures')) {
	require_once(dirname(__FILE__) . '/database.php');
/**
 * Manage database operations for the administration interface.
 */
class imea_admin_pictures extends imea_admin_database {

	function get_pictures($is_slider, $is_highlight_thumbnail, $is_highlight_image) {
		global $wpdb;
		return $wpdb->get_results("SELECT a.*, b.short_title as treaty_title
				FROM imea_pictures a
				LEFT JOIN ai_treaty b on a.id_treaty = b.id
					WHERE a.is_slider=$is_slider AND a.is_highlight_thumbnail=$is_highlight_thumbnail AND a.is_highlight_image=$is_highlight_image
				ORDER BY treaty_title ASC, rec_created DESC");
	}

	function validate_add_picture($PICTURES_DIR, $req_width, $req_height) {
		$this->actioned = TRUE;
		$this->errors = array();
		$ret = TRUE;
		if(!isset($_FILES['picture'])) {
			$ret = FALSE;
			$this->errors['No file'] = "You must attach a file";
		} else {
			$picture = $_FILES['picture'];
			$filename = $picture['name'];
			if(empty($filename)) {
				$ret = FALSE;
				$this->errors['Missing'] = "You must attach a file from your computer";
			} else {
				$path = $PICTURES_DIR . $filename;
				if(file_exists($path)) {
					$ret = FALSE;
					$this->errors['Upload'] = "A picture with this filename already exists. Please rename the file and upload again";
				} else {
					// Check image size
					$size = getimagesize($picture['tmp_name']);
					$width = $size[0];
					$height = $size[1];
					if(!empty($req_width) && $width !== $req_width) {
						$ret = FALSE;
						$this->errors['Width'] = "Image width is not 600 pixels! Actual width is $width pixels. Please resize to 600px x 200px";
					}
					if(!empty($req_height) && $height !== $req_height) {
						$ret = FALSE;
						$this->errors['Height'] = "Image height is not 200 pixels! Actual height is $height pixels. Please resize to 600px x 200px";
					}
				}
			}
		}
		// Validate treaty
		$treaty = get_request_value('id_treaty');
		if(empty($treaty)) {
			$ret = FALSE;
			$this->errors['Treaty'] = "Please select treaty from the list";
		}
		return $ret;
	}

	function add_picture($PICTURES_DIR) {
		global $wpdb;
		global $current_user;
		$user = $current_user->user_login;
		$this->actioned = TRUE;

		$data = array();
		$data['id_treaty'] = get_request_value('id_treaty');
		$data['copyright'] = trim(get_request_value('copyright'));
		$data['title'] = trim(get_request_value('title'));
		$data['keywords'] = trim(get_request_value('keywords'));
		$data['is_slider'] = get_request_value('is_slider', 0);
		$data['is_highlight_thumbnail'] = get_request_value('is_highlight_thumbnail', 0);
		$data['is_highlight_image'] = get_request_value('is_highlight_image', 0);
		$data['rec_created'] = date('Y-m-d H:i:s', strtotime("now"));
		$data['rec_author'] = $user;
		$picture = $_FILES['picture'];
		$filename = $picture['name'];
		$data['filename'] = $picture['name'];
		$dest = $PICTURES_DIR . $filename;
		if(move_uploaded_file($picture['tmp_name'], $dest) == TRUE) {
			$this->success = $wpdb->insert('imea_pictures', $data);
			if($this->success == FALSE) {
				$this->errors = array('add_picture' => "There was an error inserting the row into database for the uploaded file - move_uploaded_file('$filename', '$dest') - {$wpdb->last_error}");
			}
		} else {
			$this->success = FALSE;
			$this->errors = array('add_picture' => "There was an error moving the uploaded file - move_uploaded_file('$filename', '$dest')");
		}
	}

	function delete_pictures($PICTURES_DIR) {
		global $wpdb;
		$pictures = get_request_value('picture');
		if(!empty($pictures)) {
			foreach($pictures as $id) {
				$row = $wpdb->get_row("SELECT * FROM imea_pictures WHERE id=$id");
				$dest = $PICTURES_DIR . $row->filename;
				if(unlink($dest)) {
					$wpdb->query("DELETE FROM imea_pictures WHERE id=$id");
				}
			}
		}
	}
}
}
