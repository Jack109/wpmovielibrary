	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'wpml' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description', 'wpml' ); ?></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
	</p>
	<p>
		<label><input name="<?php echo $this->get_field_name( 'rating_only' ); ?>" type="radio" value="1" <?php checked( $rating_only, 1 ); ?> onclick="document.getElementById('_rating_only_<?php echo $this->get_field_id( 'rating_only' ); ?>').style.display='block';document.getElementById('_movies_only_<?php echo $this->get_field_id( 'rating_only' ); ?>').style.display='none';" /> <?php _e( 'Show Status only', 'wpml' ); ?></label>
		<label><input name="<?php echo $this->get_field_name( 'rating_only' ); ?>" type="radio" value="0" <?php checked( $rating_only, 0 ); ?> onclick="document.getElementById('_rating_only_<?php echo $this->get_field_id( 'rating_only' ); ?>').style.display='none';document.getElementById('_movies_only_<?php echo $this->get_field_id( 'rating_only' ); ?>').style.display='block';" /> <?php _e( 'Show Movies', 'wpml' ); ?></label>
	</p>
	<div id="_rating_only_<?php echo $this->get_field_id( 'rating_only' ); ?>" class="<?php if ( 1 !== $rating_only ) echo 'hide-if-js'; ?>">
		<p>
			<!--<input id="<?php echo $this->get_field_id( 'show_icons' ); ?>" name="<?php echo $this->get_field_name( 'show_icons' ); ?>" type="checkbox" value="1" <?php checked( $show_icons, 1 ); ?> /> 
			<label for="<?php echo $this->get_field_id( 'show_icons' ); ?>"><?php _e( 'Show icons', 'wpml' ); ?></label><br />-->
		</p>
	</div>
	<div id="_movies_only_<?php echo $this->get_field_id( 'rating_only' ); ?>" class="<?php if ( 0 !== $rating_only ) echo 'hide-if-js'; ?>">
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of movies to show', 'wpml' ); ?></label> 
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'display_rating' ); ?>"><?php _e( 'Display movie rating', 'wpml' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'display_rating' ); ?>" name="<?php echo $this->get_field_name( 'display_rating' ); ?>">
				<option value="no" <?php selected( $display_rating, 'no' ); ?>><?php _e( 'No', 'wpml' ); ?></option>
				<option value="below" <?php selected( $display_rating, 'below' ); ?>><?php _e( 'Below Poster', 'wpml' ); ?></option>
				<option value="above" <?php selected( $display_rating, 'above' ); ?>><?php _e( 'Above Poster', 'wpml' ); ?></option>
			</select>
		</p>
	</div>
